<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\CustomerCriteriaFilterTransfer;
use Generated\Shared\Transfer\CustomerCriteriaTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Symfony\Component\Console\Output\OutputInterface;

interface CustomerFacadeInterface
{
    /**
     * Specification:
     *  - Retrieves customers from database using filtration and pagination.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerCollectionTransfer $customerCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerCollectionTransfer
     */
    public function getCustomerCollection(CustomerCollectionTransfer $customerCollectionTransfer): CustomerCollectionTransfer;

    /**
     * Specification:
     * - Checks if provided email address exists in persistent storage.
     *
     * @api
     *
     * @param string $email
     *
     * @return bool
     */
    public function hasEmail($email);

    /**
     * Specification:
     * - Validates customer password according configuration.
     * - Validates provided customer email information.
     * - Encrypts provided plain text password.
     * - Assigns current locale to customer if it is not set already.
     * - Generates customer reference for customer.
     * - Stores customer data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function addCustomer(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Validates customer password according configuration.
     * - Validates provided customer email information.
     * - Encrypts provided plain text password.
     * - Assigns current locale to customer if it is not set already.
     * - Generates customer reference for customer.
     * - Stores customer data.
     * - Sends registration confirmation link via email using a freshly generated registration key.
     * - Sends password restoration email if SendPasswordToken property is set in the provided transfer object.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function registerCustomer(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Finds customer registration confirmation by provided registration key.
     * - Sets customer as registered and removes the registration key from persistent storage.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\Customer\Business\CustomerFacadeInterface::confirmCustomerRegistration()} instead.
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function confirmRegistration(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Finds customer registration confirmation by provided registration key.
     * - If found, sets customer as registered and removes the registration key from persistent storage.
     * - Returns response with error messages otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function confirmCustomerRegistration(CustomerTransfer $customerTransfer): CustomerResponseTransfer;

    /**
     * Specification:
     * - Sends password restoration link via email using a freshly generated password restoration key.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function sendPasswordRestoreMail(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Identifies customer by either customer ID, customer email, or password restoration key.
     * - Validates password only if enabled in configuration for backward compatibility reasons.
     * - Encrypts provided plain text password.
     * - Stores new password for customer in persistent storage.
     * - Removes password restoration key from customer.
     * - Sends password restoration confirmation email.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function restorePassword(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Deletes a customer by either customer ID, customer email, or password restoration key.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return bool
     */
    public function deleteCustomer(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Retrieves customer information with customer addresses by customer ID from persistent storage.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function getCustomer(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Retrieves customer information with customer addresses and locale information by customer ID.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function findCustomerById(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Updates password if NewPassword property is set in provided transfer object:
     * - Validates customer password according configuration.
     * - Validates provided current plain text password using persistent storage.
     * - Encrypts provided plain text password before update.
     * - Identifies customer by either customer ID, customer email, or password restoration key.
     * - Validates customer email information.
     * - Updates customer data which is set in provided transfer object (including password property - dismantles newPassword property).
     * - Sends password restoration email if SendPasswordToken property is set in the provided transfer object.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function updateCustomer(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Identifies customer by either customer ID, customer email, or password restoration key.
     * - Validates customer password according configuration.
     * - Validates provided current plain text password using persistent storage.
     * - Encrypts provided plain text password and stores it in persistent storage.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function updateCustomerPassword(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Retrieves an address by customer ID and address ID.
     * - Populates address flags.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function getAddress(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Retrieves provided customer related addresses from persistent storage.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\AddressesTransfer
     */
    public function getAddresses(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Retrieves an address by customer ID and address ID.
     * - Populates address flags.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function updateAddress(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Updates customer address using provided transfer object.
     * - Sets address as default address based on provided default address flags.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function updateAddressAndCustomerDefaultAddresses(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Creates customer address using provided transfer object.
     * - Sets address as default address based on provided default address flags.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function createAddressAndUpdateCustomerDefaultAddresses(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Creates customer address using provided transfer object.
     * - Sets address as default address based on provided default address flags.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function createAddress(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Retrieves customer address by address ID.
     *
     * @api
     *
     * @param int $idCustomerAddress
     *
     * @return \Generated\Shared\Transfer\AddressTransfer|null
     */
    public function findCustomerAddressById(int $idCustomerAddress): ?AddressTransfer;

    /**
     * Specification:
     * - Retrieves customer address by address details.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer|null
     */
    public function findCustomerAddressByAddressData(AddressTransfer $addressTransfer): ?AddressTransfer;

    /**
     * Specification:
     * - Sets provided address as default billing address for the related customer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return bool
     */
    public function setDefaultBillingAddress(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Sets provided address as default shipping address for the related customer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return bool
     */
    public function setDefaultShippingAddress(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Retrieves address as a formatted string for rendering.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return string
     */
    public function renderAddress(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Retrieves default shipping address for customer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function getDefaultShippingAddress(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Retrieves default billing address for customer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function getDefaultBillingAddress(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Deletes address.
     * - Removes references between customer-address entities.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function deleteAddress(AddressTransfer $addressTransfer);

    /**
     * Specification:
     * - Checks if customer exists in persistent storage by provided email and plain text password.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return bool
     */
    public function tryAuthorizeCustomerByEmailAndPassword(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Does nothing if customer is guest.
     * - Validates customer password according configuration.
     * - Registers customer if it does not exist in persistent storage.
     * - Updates customer if it exists in persistent storage.
     * - Updates customer addresses.
     *
     * @api
     *
     * @deprecated Use {@link saveOrderCustomer()} instead.
     *
     * @see CustomerFacadeInterface::registerCustomer()
     * @see CustomerFacadeInterface::updateCustomer()
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function saveCustomerForOrder(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    );

    /**
     * Specification:
     * - Does nothing if customer is guest.
     * - Validates customer password according configuration.
     * - Registers customer if it does not exist in persistent storage.
     * - Updates customer if it exists in persistent storage.
     * - Updates customer addresses.
     * - Processes quote level (BC) or item level shipping addresses.
     *
     * @api
     *
     * @see CustomerFacadeInterface::registerCustomer()
     * @see CustomerFacadeInterface::updateCustomer()
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderCustomer(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer);

    /**
     * Specification:
     * - Checks required fields for an order placement (in a customer in the quote)
     * - Checks if a new customer has a not yet registered email.
     * - Checks if a new customer or a guest user has a valid email address.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkOrderPreSaveConditions(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    );

    /**
     * Specification:
     * - Identifies customer by either customer ID, customer email, or password restoration key.
     * - Applies configured CustomerAnonymizerPluginInterface plugins on customer data.
     * - Validates anonymized customer password according regular password validation configuration.
     * - Anonymizes customer addresses.
     * - Anonymizes customer data.
     * - Updates persistent storage with anonymized data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function anonymizeCustomer(CustomerTransfer $customerTransfer);

    /**
     * Specification:
     * - Retrieves customer information with customer addresses and locale information by customer reference.
     *
     * @api
     *
     * Specification:
     *  - Finds customer by reference
     *  - Returns customer transfer
     *
     * @param string $customerReference
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function findByReference($customerReference);

    /**
     * Specification:
     * - Hydrates Customer transfer object into provided Order transfer object.
     * - Uses Order::customerReference transfer object property to identify customer.
     * - Does nothing if Customer transfer object is already set.
     *
     * @api
     *
     * Specification
     *  - Finds customer by reference
     *  - Hydrates the customer information into an order
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateSalesOrderCustomerInformation(OrderTransfer $orderTransfer);

    /**
     * Specification:
     *  - Finds customer by reference.
     *  - Returns customer response transfer.
     *
     * @api
     *
     * @param string $customerReference
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function findCustomerByReference(string $customerReference): CustomerResponseTransfer;

    /**
     * Specification:
     * - Gets all salutations available in the system.
     *
     * @api
     *
     * @return array
     */
    public function getAllSalutations(): array;

    /**
     * Specification:
     * - Retrieves filtered customers using CustomerCriteriaFilterTransfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerCollectionTransfer
     */
    public function getCustomerCollectionByCriteria(
        CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
    ): CustomerCollectionTransfer;

    /**
     * Specification:
     * - Sends a password restore link via email using a freshly generated password restore key to each customer in the collection.
     * - Displays execution progress if the output is provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerCollectionTransfer $customerCollectionTransfer
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     *
     * @return void
     */
    public function sendPasswordRestoreMailForCustomerCollection(
        CustomerCollectionTransfer $customerCollectionTransfer,
        ?OutputInterface $output = null
    ): void;

    /**
     * Specification:
     * - Finds customer by provided criteria.
     * - Optionally expands customer data with {@link \Spryker\Zed\Customer\Dependency\Plugin\CustomerTransferExpanderPluginInterface} plugins stack.
     * - Returns customer response transfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerCriteriaTransfer $customerCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function getCustomerByCriteria(CustomerCriteriaTransfer $customerCriteriaTransfer): CustomerResponseTransfer;

    /**
     * Specification:
     * - Requires `QuoteTransfer.customer` to be set.
     * - If `QuoteTransfer.customer.salutation` is set, checks if the salutation is present in a list of available salutations.
     * - If the salutation is invalid, sets `CheckoutResponseTransfer.isSuccess` equal to `false` and adds an error.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function validateCustomerCheckoutSalutation(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool;

    /**
     * Specification:
     * - If `QuoteTransfer.billingAddress.salutation` is set, checks if the salutation is present in a list of available salutations.
     * - If the salutation is invalid, sets `CheckoutResponseTransfer.isSuccess` equal to `false` and adds an error.
     * - If `QuoteTransfer.shippingAddress.salutation` is set, checks if the salutation is present in a list of available salutations.
     * - If the salutation is invalid, sets `CheckoutResponseTransfer.isSuccess` equal to `false` and adds an error.
     * - Extracts items from `QuoteTransfer`.
     * - If `ItemTransfer.shipment.shippingAddress.salutation` is set, checks if the salutation is present in a list of available salutations.
     * - If the salutation is invalid, sets `CheckoutResponseTransfer.isSuccess` equal to `false` and adds an error.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function validateCustomerAddressCheckoutSalutation(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool;
}
