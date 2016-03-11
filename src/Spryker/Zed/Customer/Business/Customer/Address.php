<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Customer;

use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Orm\Zed\Customer\Persistence\SpyCustomerAddress;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Customer\Business\Exception\AddressNotFoundException;
use Spryker\Zed\Customer\Business\Exception\CountryNotFoundException;
use Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException;
use Spryker\Zed\Customer\Dependency\Facade\CustomerToCountryInterface;
use Spryker\Zed\Customer\Dependency\Facade\CustomerToLocaleInterface;
use Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface;

class Address
{

    /**
     * @var \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @var \Spryker\Zed\Customer\Dependency\Facade\CustomerToCountryInterface
     */
    protected $countryFacade;

    /**
     * @var \Spryker\Zed\Customer\Dependency\Facade\CustomerToLocaleInterface
     */
    protected $localeFacade;

    /**
     * @param \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface $queryContainer
     * @param \Spryker\Zed\Customer\Dependency\Facade\CustomerToCountryInterface $countryFacade
     * @param \Spryker\Zed\Customer\Dependency\Facade\CustomerToLocaleInterface $localeFacade
     */
    public function __construct(CustomerQueryContainerInterface $queryContainer, CustomerToCountryInterface $countryFacade, CustomerToLocaleInterface $localeFacade)
    {
        $this->queryContainer = $queryContainer;
        $this->countryFacade = $countryFacade;
        $this->localeFacade = $localeFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function createAddress(AddressTransfer $addressTransfer)
    {
        $customerEntity = $this->getCustomerFromAddressTransfer($addressTransfer);

        $addressEntity = $this->createCustomerAddress($addressTransfer, $customerEntity);

        $this->updateCustomerDefaultAddresses($addressTransfer, $customerEntity, $addressEntity);

        return $this->entityToAddressTransfer($addressEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function getAddress(AddressTransfer $addressTransfer)
    {
        $idCustomer = $addressTransfer->getFkCustomer();

        $addressTransfer = $this->getAddressTransferById($addressTransfer->getIdCustomerAddress(), $idCustomer);

        $this->setDefaultAddressFlags($addressTransfer);

        return $addressTransfer;
    }

    /**
     * @param int $idAddress
     * @param int|null $idCustomer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function getAddressTransferById($idAddress, $idCustomer = null)
    {
        $addressQuery = $this->queryContainer->queryAddress($idAddress);
        if ($idCustomer !== null) {
            $addressQuery->filterByFkCustomer($idCustomer);
        }
        $addressEntity = $addressQuery->findOne();

        if ($addressEntity === null) {
            throw new AddressNotFoundException();
        }

        $addressTransfer = $this->entityToAddressTransfer($addressEntity);

        return $addressTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return void
     */
    protected function setDefaultAddressFlags(AddressTransfer $addressTransfer)
    {
        $customerEntity = $this->getCustomerFromAddressTransfer($addressTransfer);

        if ($addressTransfer !== null) {
            $addressTransfer->setIsDefaultBilling(
                $this->isDefaultAddress($addressTransfer->getIdCustomerAddress(), $customerEntity->getDefaultBillingAddress())
            );
            $addressTransfer->setIsDefaultShipping(
                $this->isDefaultAddress($addressTransfer->getIdCustomerAddress(), $customerEntity->getDefaultShippingAddress())
            );
        }
    }

    /**
     * @param int $idCustomerAddress
     * @param int $idDefaultAddress
     *
     * @return bool
     */
    protected function isDefaultAddress($idCustomerAddress, $idDefaultAddress)
    {
        return ((int)$idCustomerAddress === (int)$idDefaultAddress);
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\AddressesTransfer
     */
    public function getAddresses(CustomerTransfer $customerTransfer)
    {
        $entities = $this->queryContainer
            ->queryAddresses()
            ->joinCountry()
            ->filterByFkCustomer($customerTransfer->getIdCustomer())
            ->find();

        return $this->entityCollectionToTransferCollection($entities);
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function updateAddress(AddressTransfer $addressTransfer)
    {
        $customer = $this->getCustomerFromAddressTransfer($addressTransfer);

        $addressEntity = $this->updateCustomerAddress($addressTransfer, $customer);

        return $this->entityToAddressTransfer($addressEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return bool
     */
    public function setDefaultShippingAddress(AddressTransfer $addressTransfer)
    {
        $customer = $this->getCustomerFromAddressTransfer($addressTransfer);

        $entity = $this->queryContainer->queryAddressForCustomer($addressTransfer->getIdCustomerAddress(), $customer->getEmail())
            ->findOne();

        if (!$entity) {
            throw new AddressNotFoundException();
        }

        $customer->setDefaultShippingAddress($addressTransfer->getIdCustomerAddress());
        $customer->save();

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return bool
     */
    public function setDefaultBillingAddress(AddressTransfer $addressTransfer)
    {
        $customer = $this->getCustomerFromAddressTransfer($addressTransfer);

        $entity = $this->queryContainer->queryAddressForCustomer($addressTransfer->getIdCustomerAddress(), $customer->getEmail())
            ->findOne();

        if (!$entity) {
            throw new AddressNotFoundException();
        }

        $customer->setDefaultBillingAddress($addressTransfer->getIdCustomerAddress());
        $customer->save();

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return string
     */
    public function getFormattedAddressString(AddressTransfer $addressTransfer)
    {
        return implode("\n", $this->getFormattedAddressArray($addressTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return array
     */
    public function getFormattedAddressArray(AddressTransfer $addressTransfer)
    {
        $address = [];

        if (count($addressTransfer->getCompany()) > 0) {
            $address[] = $addressTransfer->getCompany();
        }

        $address[] = sprintf('%s %s %s', $addressTransfer->getSalutation(), $addressTransfer->getFirstName(), $addressTransfer->getLastName());

        if (count($addressTransfer->getAddress1()) > 0) {
            $address[] = $addressTransfer->getAddress1();
        }
        if (count($addressTransfer->getAddress2()) > 0) {
            $address[] = $addressTransfer->getAddress2();
        }
        if (count($addressTransfer->getAddress3()) > 0) {
            $address[] = $addressTransfer->getAddress3();
        }

        $address[] = sprintf('%s %s', $addressTransfer->getZipCode(), $addressTransfer->getCity());

        return $address;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomerAddress $entity
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function entityToAddressTransfer(SpyCustomerAddress $entity)
    {
        $addressTransfer = new AddressTransfer();
        $addressTransfer->fromArray($entity->toArray(), true);
        $addressTransfer->setIso2Code($entity->getCountry()->getIso2Code());

        return $addressTransfer;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $entities
     *
     * @return \Generated\Shared\Transfer\AddressesTransfer
     */
    protected function entityCollectionToTransferCollection(ObjectCollection $entities)
    {
        $addressTransferCollection = new AddressesTransfer();
        foreach ($entities->getData() as $entity) {
            $addressTransferCollection->addAddress($this->entityToAddressTransfer($entity));
        }

        return $addressTransferCollection;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomer
     */
    protected function getCustomerFromAddressTransfer(AddressTransfer $addressTransfer)
    {
        if ($addressTransfer->getEmail()) {
            $customer = $this->queryContainer->queryCustomerByEmail($addressTransfer->getEmail())
                ->findOne();
        } elseif ($addressTransfer->getFkCustomer()) {
            $customer = $this->queryContainer->queryCustomerById($addressTransfer->getFkCustomer())
                ->findOne();
        }

        if (!isset($customer) || $customer === null) {
            throw new CustomerNotFoundException();
        }

        return $customer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomer
     */
    protected function getCustomerFromCustomerTransfer(CustomerTransfer $customerTransfer)
    {
        if ($customerTransfer->getEmail()) {
            $customer = $this->queryContainer->queryCustomerByEmail($customerTransfer->getEmail())
                ->findOne();
        } elseif ($customerTransfer->getIdCustomer()) {
            $customer = $this->queryContainer->queryCustomerById($customerTransfer->getIdCustomer())
                ->findOne();
        }

        if (!isset($customer) || $customer === null) {
            throw new CustomerNotFoundException();
        }

        return $customer;
    }

    /**
     * @throws \Spryker\Zed\Customer\Business\Exception\CountryNotFoundException
     *
     * @return int
     */
    protected function getCustomerCountryId()
    {
        $idCountry = $this->countryFacade->getIdCountryByIso2Code($this->getIsoCode());

        if ($idCountry === null) {
            throw new CountryNotFoundException();
        }

        return $idCountry;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function getDefaultShippingAddress(CustomerTransfer $customerTransfer)
    {
        $customerEntity = $this->getCustomerFromCustomerTransfer($customerTransfer);
        $idAddress = $customerEntity->getDefaultShippingAddress();

        return $this->getAddressTransferById($idAddress);
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function getDefaultBillingAddress(CustomerTransfer $customerTransfer)
    {
        $customerEntity = $this->getCustomerFromCustomerTransfer($customerTransfer);
        $idAddress = $customerEntity->getDefaultBillingAddress();

        return $this->getAddressTransferById($idAddress);
    }

    /**
     * @return string
     */
    private function getIsoCode()
    {
        $localeName = $this->localeFacade->getCurrentLocale()
            ->getLocaleName();

        return explode('_', $localeName)[1];
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     * @throws \Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function deleteAddress(AddressTransfer $addressTransfer)
    {
        $customer = $this->getCustomerFromAddressTransfer($addressTransfer);

        $entity = $this->queryContainer
            ->queryAddressForCustomer(
                $addressTransfer->getIdCustomerAddress(),
                $customer->getEmail()
            )
            ->findOne();

        if (!$entity) {
            throw new AddressNotFoundException();
        }

        $wasDefault = false;
        if ($customer->getDefaultShippingAddress() === $entity->getIdCustomerAddress()) {
            $customer->setDefaultShippingAddress(null);
            $wasDefault = true;
        }
        if ($customer->getDefaultBillingAddress() === $entity->getIdCustomerAddress()) {
            $customer->setDefaultBillingAddress(null);
            $wasDefault = true;
        }
        if ($wasDefault) {
            $customer->save();
        }

        $oldAddressTransfer = $this->entityToAddressTransfer($entity);
        $oldAddressTransfer->setIdCustomerAddress(null);

        $entity->delete();

        return $oldAddressTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\CountryNotFoundException
     *
     * @return int
     */
    protected function retrieveFkCountry(AddressTransfer $addressTransfer)
    {
        $fkCountry = $addressTransfer->getFkCountry();
        if (empty($fkCountry)) {
            $iso2Code = $addressTransfer->getIso2Code();
            if (empty($iso2Code) === false) {
                $fkCountry = $this->countryFacade->getIdCountryByIso2Code($iso2Code);
            } else {
                $fkCountry = $this->getCustomerCountryId();
            }
        }

        return $fkCountry;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Exception
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function updateAddressAndCustomerDefaultAddresses(AddressTransfer $addressTransfer)
    {
        $connection = $this->queryContainer->getConnection();
        $connection->beginTransaction();

        try {
            $customerEntity = $this->getCustomerFromAddressTransfer($addressTransfer);

            $addressEntity = $this->updateCustomerAddress($addressTransfer, $customerEntity);

            $this->updateCustomerDefaultAddresses($addressTransfer, $customerEntity, $addressEntity);

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $customerTransfer = $this->entityToCustomerTransfer($customerEntity);
        $customerTransfer->setAddresses($this->getAddresses($customerTransfer));

        return $customerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @throws \Exception
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function createAddressAndUpdateCustomerDefaultAddresses(AddressTransfer $addressTransfer)
    {
        $connection = $this->queryContainer->getConnection();
        $connection->beginTransaction();

        try {
            $customerEntity = $this->getCustomerFromAddressTransfer($addressTransfer);

            $addressEntity = $this->createCustomerAddress($addressTransfer, $customerEntity);

            $this->updateCustomerDefaultAddresses($addressTransfer, $customerEntity, $addressEntity);

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $customerTransfer = $this->entityToCustomerTransfer($customerEntity);
        $customerTransfer->setAddresses($this->getAddresses($customerTransfer));

        return $customerTransfer;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $entity
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function entityToCustomerTransfer(SpyCustomer $entity)
    {
        $addressTransfer = new CustomerTransfer();

        return $addressTransfer->fromArray($entity->toArray(), true);
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customer
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerAddress
     */
    protected function createCustomerAddress(AddressTransfer $addressTransfer, SpyCustomer $customer)
    {
        $addressEntity = new SpyCustomerAddress();
        $addressEntity->fromArray($addressTransfer->toArray());

        $fkCountry = $this->retrieveFkCountry($addressTransfer);
        $addressEntity->setFkCountry($fkCountry);

        $addressEntity->setCustomer($customer);
        $addressEntity->save();

        return $addressEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customer
     *
     * @throws \Spryker\Zed\Customer\Business\Exception\AddressNotFoundException
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerAddress
     */
    protected function updateCustomerAddress(AddressTransfer $addressTransfer, SpyCustomer $customer)
    {
        $addressEntity = $this->queryContainer->queryAddressForCustomer($addressTransfer->getIdCustomerAddress(), $customer->getEmail())
            ->findOne();

        if (!$addressEntity) {
            throw new AddressNotFoundException();
        }

        $fkCountry = $this->retrieveFkCountry($addressTransfer);

        $addressEntity->fromArray($addressTransfer->modifiedToArray());
        $addressEntity->setCustomer($customer);
        $addressEntity->setFkCountry($fkCountry);
        $addressEntity->save();

        return $addressEntity;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customerEntity
     * @param \Orm\Zed\Customer\Persistence\SpyCustomerAddress $entity
     *
     * @return void
     */
    protected function updateCustomerDefaultAddresses(AddressTransfer $addressTransfer, SpyCustomer $customerEntity, SpyCustomerAddress $entity)
    {
        if ($customerEntity->getDefaultBillingAddress() === null || $addressTransfer->getIsDefaultBilling()) {
            $customerEntity->setDefaultBillingAddress($entity->getIdCustomerAddress());
        }

        if ($customerEntity->getDefaultShippingAddress() === null || $addressTransfer->getIsDefaultShipping()) {
            $customerEntity->setDefaultShippingAddress($entity->getIdCustomerAddress());
        }

        $customerEntity->save();
    }

}
