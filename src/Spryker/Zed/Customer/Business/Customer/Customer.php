<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Customer\Business\Customer;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerErrorTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Exception\PropelException;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use Spryker\Shared\Customer\Code\Messages;
use Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException;
use Spryker\Zed\Customer\Business\Exception\CustomerNotUpdatedException;
use Spryker\Zed\Customer\Business\ReferenceGenerator\CustomerReferenceGeneratorInterface;
use Spryker\Zed\Customer\CustomerConfig;
use Spryker\Zed\Customer\Dependency\Plugin\PasswordRestoredConfirmationSenderPluginInterface;
use Spryker\Zed\Customer\Dependency\Plugin\PasswordRestoreTokenSenderPluginInterface;
use Spryker\Zed\Customer\Dependency\Plugin\RegistrationTokenSenderPluginInterface;
use Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Orm\Zed\Customer\Persistence\SpyCustomerAddress;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

class Customer
{

    const BCRYPT_FACTOR = 12;
    const BCRYPT_SALT = '';

    /**
     * @var CustomerQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var CustomerReferenceGeneratorInterface
     */
    protected $customerReferenceGenerator;

    /**
     * @var PasswordRestoredConfirmationSenderPluginInterface[]
     */
    protected $passwordRestoredConfirmationSender = [];

    /**
     * @var PasswordRestoreTokenSenderPluginInterface[]
     */
    protected $passwordRestoreTokenSender = [];

    /**
     * @var RegistrationTokenSenderPluginInterface[]
     */
    protected $registrationTokenSender = [];

    /**
     * @var CustomerConfig
     */
    protected $customerConfig;

    /**
     * @param \Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Customer\Business\ReferenceGenerator\CustomerReferenceGeneratorInterface $customerReferenceGenerator
     * @param \Spryker\Zed\Customer\CustomerConfig $customerConfig
     */
    public function __construct(QueryContainerInterface $queryContainer, CustomerReferenceGeneratorInterface $customerReferenceGenerator, CustomerConfig $customerConfig)
    {
        $this->queryContainer = $queryContainer;
        $this->customerReferenceGenerator = $customerReferenceGenerator;
        $this->customerConfig = $customerConfig;
    }

    /**
     * @param string $email
     *
     * @return bool
     */
    public function hasEmail($email)
    {
        $customerCount = $this->queryContainer
            ->queryCustomerByEmail($email)
            ->count();

        return ($customerCount > 0);
    }

    /**
     * @param \Spryker\Zed\Customer\Dependency\Plugin\PasswordRestoredConfirmationSenderPluginInterface $sender
     *
     * @return void
     */
    public function addPasswordRestoredConfirmationSender(PasswordRestoredConfirmationSenderPluginInterface $sender)
    {
        $this->passwordRestoredConfirmationSender[] = $sender;
    }

    /**
     * @param \Spryker\Zed\Customer\Dependency\Plugin\PasswordRestoreTokenSenderPluginInterface $sender
     *
     * @return void
     */
    public function addPasswordRestoreTokenSender(PasswordRestoreTokenSenderPluginInterface $sender)
    {
        $this->passwordRestoreTokenSender[] = $sender;
    }

    /**
     * @param \Spryker\Zed\Customer\Dependency\Plugin\RegistrationTokenSenderPluginInterface $sender
     *
     * @return void
     */
    public function addRegistrationTokenSender(RegistrationTokenSenderPluginInterface $sender)
    {
        $this->registrationTokenSender[] = $sender;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function get(CustomerTransfer $customerTransfer)
    {
        $customerEntity = $this->getCustomer($customerTransfer);
        $customerTransfer->fromArray($customerEntity->toArray());
        $addresses = $customerEntity->getAddresses();
        if ($addresses) {
            $customerTransfer->setAddresses($this->entityCollectionToTransferCollection($addresses, $customerEntity));
        }

        return $customerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function register(CustomerTransfer $customerTransfer)
    {
        $customerTransfer = $this->encryptPassword($customerTransfer);

        $customerEntity = new SpyCustomer();
        $customerEntity->fromArray($customerTransfer->toArray());

        if (!$this->isEmailAvailableForCustomer($customerEntity)) {
            $customerResponseTransfer = $this->createCustomerEmailAlreadyUsedResponse();

            return $customerResponseTransfer;
        }

        $customerResponseTransfer = $this->createCustomerResponseTransfer();

        $customerEntity->setCustomerReference($this->customerReferenceGenerator->generateCustomerReference($customerTransfer));
        $customerEntity->setRegistrationKey($this->generateKey());

        $customerEntity->save();

        $customerTransfer->setIdCustomer($customerEntity->getPrimaryKey());
        $customerTransfer->setCustomerReference($customerEntity->getCustomerReference());
        $customerTransfer->setRegistrationKey($customerEntity->getRegistrationKey());

        $this->sendRegistrationToken($customerTransfer);

        $customerResponseTransfer
            ->setIsSuccess(true)
            ->setCustomerTransfer($customerTransfer);

        return $customerResponseTransfer;
    }

    /**
     * @return string
     */
    protected function generateKey()
    {
        return uniqid();
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    protected function sendPasswordRestoreToken(CustomerTransfer $customerTransfer)
    {
        $customerTransfer = $this->get($customerTransfer);
        $confirmationLink = $this->customerConfig
            ->getCustomerPasswordRestoreTokenUrl($customerTransfer->getRestorePasswordKey());
        foreach ($this->passwordRestoreTokenSender as $sender) {
            $sender->send($customerTransfer->getEmail(), $confirmationLink);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return bool
     */
    protected function sendRegistrationToken(CustomerTransfer $customerTransfer)
    {
        if (!$customerTransfer->getSendPasswordToken()) {
            return false;
        }
        $confirmationLink = $this->customerConfig
            ->getRegisterConfirmTokenUrl($customerTransfer->getRegistrationKey());
        foreach ($this->registrationTokenSender as $sender) {
            $sender->send($customerTransfer->getEmail(), $confirmationLink);
        }

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    protected function sendPasswordRestoreConfirmation(CustomerTransfer $customerTransfer)
    {
        foreach ($this->passwordRestoredConfirmationSender as $sender) {
            $sender->send($customerTransfer->getEmail());
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function confirmRegistration(CustomerTransfer $customerTransfer)
    {
        $customerEntity = $this->queryContainer->queryCustomerByRegistrationKey($customerTransfer->getRegistrationKey())
            ->findOne();
        if ($customerEntity === null) {
            throw new CustomerNotFoundException('Customer not found.');
        }

        $customerEntity->setRegistered(new \DateTime());
        $customerEntity->setRegistrationKey(null);

        $customerEntity->save();
        $customerTransfer->fromArray($customerEntity->toArray());

        return $customerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function sendPasswordRestoreMail(CustomerTransfer $customerTransfer)
    {
        $customerResponseTransfer = $this->createCustomerResponseTransfer();

        try {
            $customerEntity = $this->getCustomer($customerTransfer);
        } catch (CustomerNotFoundException $e) {
            $customerError = new CustomerErrorTransfer();
            $customerError->setMessage(Messages::CUSTOMER_EMAIL_INVALID);

            $customerResponseTransfer
                ->setIsSuccess(false)
                ->addError($customerError);

            return $customerResponseTransfer;
        }

        $customerEntity->setRestorePasswordDate(new \DateTime());
        $customerEntity->setRestorePasswordKey($this->generateKey());

        $customerEntity->save();

        $customerTransfer->fromArray($customerEntity->toArray(), true);
        $this->sendPasswordRestoreToken($customerTransfer);

        $customerResponseTransfer->setCustomerTransfer($customerTransfer);

        return $customerResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function restorePassword(CustomerTransfer $customerTransfer)
    {
        $customerTransfer = $this->encryptPassword($customerTransfer);

        $customerResponseTransfer = $this->createCustomerResponseTransfer();

        try {
            $customerEntity = $this->getCustomer($customerTransfer);
        } catch (CustomerNotFoundException $e) {
            $customerError = new CustomerErrorTransfer();
            $customerError->setMessage(Messages::CUSTOMER_TOKEN_INVALID);

            $customerResponseTransfer
                ->setIsSuccess(false)
                ->addError($customerError);

            return $customerResponseTransfer;
        }

        $customerEntity->setRestorePasswordDate(null);
        $customerEntity->setRestorePasswordKey(null);

        $customerEntity->setPassword($customerTransfer->getPassword());

        $customerEntity->save();
        $customerTransfer->fromArray($customerEntity->toArray(), true);
        $this->sendPasswordRestoreConfirmation($customerTransfer);

        $customerResponseTransfer->setCustomerTransfer($customerTransfer);

        return $customerResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     *
     * @return bool
     */
    public function delete(CustomerTransfer $customerTransfer)
    {
        $customerEntity = $this->getCustomer($customerTransfer);
        $customerEntity->delete();

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotUpdatedException
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function update(CustomerTransfer $customerTransfer)
    {
        $customerTransfer = $this->encryptPassword($customerTransfer);

        $customerEntity = $this->getCustomer($customerTransfer);
        $customerEntity->fromArray($customerTransfer->modifiedToArray());

        if (!$this->isEmailAvailableForCustomer($customerEntity)) {
            $customerResponseTransfer = $this->createCustomerEmailAlreadyUsedResponse();

            return $customerResponseTransfer;
        }

        $customerResponseTransfer = $this->createCustomerResponseTransfer();

        $changedRows = $customerEntity->save();

        $customerResponseTransfer
            ->setIsSuccess($changedRows > 0)
            ->setCustomerTransfer($customerTransfer);

        $this->sendRegistrationToken($customerTransfer);

        return $customerResponseTransfer;
    }

    /**
     * @param bool $isSuccess
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    protected function createCustomerResponseTransfer($isSuccess = true)
    {
        $customerResponseTransfer = new CustomerResponseTransfer();
        $customerResponseTransfer->setIsSuccess($isSuccess);

        return $customerResponseTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    protected function createCustomerEmailAlreadyUsedResponse()
    {
        $customerErrorTransfer = new CustomerErrorTransfer();
        $customerErrorTransfer->setMessage(Messages::CUSTOMER_EMAIL_ALREADY_USED);

        $customerResponseTransfer = $this->createCustomerResponseTransfer(false);
        $customerResponseTransfer->addError($customerErrorTransfer);

        return $customerResponseTransfer;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customerEntity
     *
     * @return bool
     */
    protected function isEmailAvailableForCustomer(SpyCustomer $customerEntity)
    {
        $count = $this->queryContainer
            ->queryCustomerByEmailApartFromIdCustomer($customerEntity->getEmail(), $customerEntity->getIdCustomer())
            ->count();

        return ($count === 0);
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotUpdatedException
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function updatePassword(CustomerTransfer $customerTransfer)
    {
        $customerEntity = $this->getCustomer($customerTransfer);

        $customerResponseTransfer = $this->getCustomerPasswordInvalidResponse($customerEntity, $customerTransfer);
        if (!$customerResponseTransfer->getIsSuccess()) {
            return $customerResponseTransfer;
        }

        $customerTransfer = $this->encryptNewPassword($customerTransfer);

        $customerEntity->setPassword($customerTransfer->getNewPassword());

        $changedRows = $customerEntity->save();

        $customerTransfer->fromArray($customerEntity->toArray(), true);

        $customerResponseTransfer
            ->setIsSuccess($changedRows > 0)
            ->setCustomerTransfer($customerTransfer);

        return $customerResponseTransfer;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customerEntity
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    protected function getCustomerPasswordInvalidResponse(SpyCustomer $customerEntity, CustomerTransfer $customerTransfer)
    {
        $customerResponseTransfer = new CustomerResponseTransfer();
        $customerResponseTransfer->setIsSuccess(true);

        if (!$this->isValidPassword($customerEntity->getPassword(), $customerTransfer->getPassword())) {
            $customerErrorTransfer = new CustomerErrorTransfer();
            $customerErrorTransfer
                ->setMessage(Messages::CUSTOMER_PASSWORD_INVALID);
            $customerResponseTransfer
                ->setIsSuccess(false)
                ->addError($customerErrorTransfer);
        }

        return $customerResponseTransfer;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomerAddress $customer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function entityToTransfer(SpyCustomerAddress $customer)
    {
        $entity = new AddressTransfer();

        return $entity->fromArray($customer->toArray(), true);
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $entities
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customer
     *
     * @return \Generated\Shared\Transfer\AddressesTransfer
     */
    protected function entityCollectionToTransferCollection(ObjectCollection $entities, SpyCustomer $customer)
    {
        $addressCollection = new AddressesTransfer();
        foreach ($entities->getData() as $address) {
            $addressTransfer = $this->entityToTransfer($address);

            if ($customer->getDefaultBillingAddress() === $address->getIdCustomerAddress()) {
                $addressTransfer->setIsDefaultBilling(true);
            }
            if ($customer->getDefaultShippingAddress() === $address->getIdCustomerAddress()) {
                $addressTransfer->setIsDefaultShipping(true);
            }

            $addressCollection->addAddress($addressTransfer);
        }

        return $addressCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomer
     */
    protected function getCustomer(CustomerTransfer $customerTransfer)
    {
        $customerEntity = null;

        if ($customerTransfer->getIdCustomer()) {
            $customerEntity = $this->queryContainer->queryCustomerById($customerTransfer->getIdCustomer())
                ->findOne();
        } elseif ($customerTransfer->getEmail()) {
            $customerEntity = $this->queryContainer->queryCustomerByEmail($customerTransfer->getEmail())
                ->findOne();
        } elseif ($customerTransfer->getRestorePasswordKey()) {
            $customerEntity = $this->queryContainer->queryCustomerByRestorePasswordKey($customerTransfer->getRestorePasswordKey())
                ->findOne();
        }

        if ($customerEntity !== null) {
            return $customerEntity;
        }

        throw new CustomerNotFoundException();
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return bool
     */
    protected function hasCustomer(CustomerTransfer $customerTransfer)
    {
        $result = false;
        $customerEntity = null;

        if ($customerTransfer->getIdCustomer()) {
            $customerEntity = $this->queryContainer
                ->queryCustomerById($customerTransfer->getIdCustomer())
                ->findOne();
        } elseif ($customerTransfer->getEmail()) {
            $customerEntity = $this->queryContainer
                ->queryCustomerByEmail($customerTransfer->getEmail())
                ->findOne();
        }

        if ($customerEntity !== null) {
            $result = true;
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return bool
     */
    public function tryAuthorizeCustomerByEmailAndPassword(CustomerTransfer $customerTransfer)
    {
        $result = false;

        $customerEntity = $this->queryContainer->queryCustomerByEmail($customerTransfer->getEmail())
            ->findOne();

        if ($customerEntity !== null) {
            $result = $this->isValidPassword($customerEntity->getPassword(), $customerTransfer->getPassword());
        }

        return $result;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function encryptPassword(CustomerTransfer $customerTransfer)
    {
        $currentPassword = $customerTransfer->getPassword();
        $customerTransfer->setPassword($this->getEncodedPassword($currentPassword));

        return $customerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function encryptNewPassword(CustomerTransfer $customerTransfer)
    {
        $currentPassword = $customerTransfer->getNewPassword();
        $customerTransfer->setNewPassword($this->getEncodedPassword($currentPassword));

        return $customerTransfer;
    }

    /**
     * @param $currentPassword
     *
     * @return string
     */
    protected function getEncodedPassword($currentPassword)
    {
        $newPassword = $currentPassword;

        if (mb_substr($currentPassword, 0, 2) !== '$2') {
            $encoder = new BCryptPasswordEncoder(self::BCRYPT_FACTOR);

            $newPassword = $encoder->encodePassword($currentPassword, self::BCRYPT_SALT);
        }

        return $newPassword;
    }

    /**
     * @param string $hash
     * @param string $rawPassword
     *
     * @return bool
     */
    protected function isValidPassword($hash, $rawPassword)
    {
        $encoder = new BCryptPasswordEncoder(self::BCRYPT_FACTOR);

        return $encoder->isPasswordValid($hash, $rawPassword, self::BCRYPT_SALT);
    }

}
