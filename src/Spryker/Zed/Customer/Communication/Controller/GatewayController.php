<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Customer\Communication\Controller;

use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Business\CustomerFacade;
use Spryker\Zed\Customer\Business\Exception\AddressNotFoundException;
use Spryker\Zed\Customer\Communication\CustomerCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method CustomerFacade getFacade()
 * @method CustomerCommunicationFactory getCommunicationFactory()
 */
class GatewayController extends AbstractGatewayController
{

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerResponseTransfer
     */
    public function registerAction(CustomerTransfer $customerTransfer)
    {
        return $this->getFacade()
            ->registerCustomer($customerTransfer);
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerTransfer
     */
    public function confirmRegistrationAction(CustomerTransfer $customerTransfer)
    {
        return $this->getFacade()
            ->confirmRegistration($customerTransfer);
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerResponseTransfer
     */
    public function forgotPasswordAction(CustomerTransfer $customerTransfer)
    {
        return $this->getFacade()
            ->forgotPassword($customerTransfer);
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerResponseTransfer
     */
    public function restorePasswordAction(CustomerTransfer $customerTransfer)
    {
        return $this->getFacade()
            ->restorePassword($customerTransfer);
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function deleteAction(CustomerTransfer $customerTransfer)
    {
        $result = $this->getFacade()
            ->deleteCustomer($customerTransfer);
        $this->setSuccess($result);
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerResponseTransfer
     */
    public function hasCustomerWithEmailAndPasswordAction(CustomerTransfer $customerTransfer)
    {
        $isAuthorized = $this->getFacade()
            ->tryAuthorizeCustomerByEmailAndPassword($customerTransfer);

        $result = new CustomerResponseTransfer();
        if ($isAuthorized === true) {
            $result->setCustomerTransfer($this->getFacade()->getCustomer($customerTransfer));
        }

        $result->setHasCustomer($isAuthorized);

        $this->setSuccess($isAuthorized);

        return $result;
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerTransfer
     */
    public function customerAction(CustomerTransfer $customerTransfer)
    {
        $result = $this->getFacade()
            ->getCustomer($customerTransfer);

        return $result;
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerResponseTransfer
     */
    public function updateAction(CustomerTransfer $customerTransfer)
    {
        $response = $this->getFacade()
            ->updateCustomer($customerTransfer);
        $this->setSuccess($response->getIsSuccess());

        return $response;
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return CustomerResponseTransfer
     */
    public function updatePasswordAction(CustomerTransfer $customerTransfer)
    {
        $response = $this->getFacade()
            ->updateCustomerPassword($customerTransfer);
        $this->setSuccess($response->getIsSuccess());

        return $response;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return AddressTransfer
     */
    public function addressAction(AddressTransfer $addressTransfer)
    {
        try {
            $addressTransfer = $this->getFacade()
                ->getAddress($addressTransfer);
        } catch (AddressNotFoundException $e) {
            $this->setSuccess(false);
            $addressTransfer = null;
        }

        return $addressTransfer;
    }

    /**
     * @param CustomerTransfer $customerTransfer
     *
     * @return AddressesTransfer|null
     */
    public function addressesAction(CustomerTransfer $customerTransfer)
    {
        $addressesTransfer = $this->getFacade()
            ->getAddresses($customerTransfer);

        if ($addressesTransfer === null) {
            $this->setSuccess(false);

            return null;
        }

        return $addressesTransfer;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return AddressTransfer
     */
    public function updateAddressAction(AddressTransfer $addressTransfer)
    {
        $result = $this->getFacade()
            ->updateAddress($addressTransfer);
        $this->setSuccess($result);

        return $addressTransfer;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return CustomerTransfer
     */
    public function updateAddressAndCustomerDefaultAddressesAction(AddressTransfer $addressTransfer)
    {
        $customerTransfer = $this->getFacade()
            ->updateAddressAndCustomerDefaultAddresses($addressTransfer);

        $isSuccess = ($customerTransfer !== null);
        $this->setSuccess($isSuccess);

        return $customerTransfer;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return CustomerTransfer
     */
    public function createAddressAndUpdateCustomerDefaultAddressesAction(AddressTransfer $addressTransfer)
    {
        $customerTransfer = $this->getFacade()
            ->createAddressAndUpdateCustomerDefaultAddresses($addressTransfer);

        $isSuccess = ($customerTransfer !== null);
        $this->setSuccess($isSuccess);

        return $customerTransfer;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return AddressTransfer
     */
    public function newAddressAction(AddressTransfer $addressTransfer)
    {
        $addressTransfer = $this->getFacade()
            ->createAddress($addressTransfer);
        $this->setSuccess($addressTransfer->getIdCustomerAddress() > 0);

        return $addressTransfer;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return AddressTransfer|null
     */
    public function deleteAddressAction(AddressTransfer $addressTransfer)
    {
        try {
            $this->getFacade()->deleteAddress($addressTransfer);

            return $addressTransfer;
        } catch (AddressNotFoundException $e) {
            $this->setSuccess(false);
        }

        return null;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return AddressTransfer
     */
    public function defaultBillingAddressAction(AddressTransfer $addressTransfer)
    {
        $result = $this->getFacade()
            ->setDefaultBillingAddress($addressTransfer);
        $this->setSuccess($result);

        return $addressTransfer;
    }

    /**
     * @param AddressTransfer $addressTransfer
     *
     * @return AddressTransfer
     */
    public function defaultShippingAddressAction(AddressTransfer $addressTransfer)
    {
        $result = $this->getFacade()
            ->setDefaultShippingAddress($addressTransfer);
        $this->setSuccess($result);

        return $addressTransfer;
    }

}
