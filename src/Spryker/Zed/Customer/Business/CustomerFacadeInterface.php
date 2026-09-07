<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business;

use Generated\Shared\Transfer\AddressCollectionTransfer;
use Generated\Shared\Transfer\AddressCriteriaTransfer;
use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\CustomerCollectionCriteriaTransfer;
use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\CustomerCriteriaFilterTransfer;
use Generated\Shared\Transfer\CustomerCriteriaTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OauthCustomerResolveRequestTransfer;
use Generated\Shared\Transfer\OauthCustomerResolveResponseTransfer;
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
     * - Retrieves a collection of customers.
     * - Uses `CustomerCollectionCriteriaTransfer.customerConditions.customerIds` to filter by customer ids.
     * - Uses `CustomerCollectionCriteriaTransfer.customerConditions.customerReferences` to filter by customer references.
     * - Uses `CustomerCollectionCriteriaTransfer.customerConditions.emails` to filter by emails.
     * - Combines all conditions with AND.
     * - Uses `CustomerCollectionCriteriaTransfer.customerConditions.searchTerms.{email, firstName, lastName}` to
     *   filter by a partial match, combining the search terms with OR.
     * - Excludes anonymized customers unless `CustomerCollectionCriteriaTransfer.customerConditions.hasAnonymizedAt`
     *   is set to `true`.
     * - Uses `CustomerCollectionCriteriaTransfer.sortCollection.sort.field` to set the 'order by' field.
     * - Uses `CustomerCollectionCriteriaTransfer.sortCollection.sort.isAscending` to set ascending/descending order.
     * - Supported sort fields are `customerReference`, `createdAt`, `email`, `firstName`, `lastName`, `registered`;
     *   unsupported fields are ignored.
     * - Uses `CustomerCollectionCriteriaTransfer.pagination.{page, maxPerPage}` to paginate results.
     * - Requires `pagination.page` and `pagination.maxPerPage` when `pagination` is set.
     * - Returns `CustomerCollectionTransfer` filled with found customers and populated pagination.
     * - Does not populate the addresses of the returned customers; use `findCustomerByReference()` for that.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerCollectionCriteriaTransfer $customerCollectionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerCollectionTransfer
     */
    public function getCustomerCollectionByCollectionCriteria(
        CustomerCollectionCriteriaTransfer $customerCollectionCriteriaTransfer
    ): CustomerCollectionTransfer;

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
     * - Executes {@link \Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerValidatorPluginInterface} plugin stack.
     * - Returns `CustomerResponseTransfer` with `isSuccess = false` and the collected errors without storing anything when a plugin rejects the customer.
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
     * - Executes {@link \Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerValidatorPluginInterface} plugin stack.
     * - Returns `CustomerResponseTransfer` with `isSuccess = false` and the collected errors without storing anything when a plugin rejects the customer.
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
     * - Executes {@link \Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPostDeletePluginInterface} plugin stack.
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
     * - Excludes credentials (password hash) from the returned transfer when `$isSecure` is true (default).
     * - Pass `$isSecure = false` only in authentication contexts that require credential validation.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function getCustomer(CustomerTransfer $customerTransfer, bool $isSecure = true);

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
     * - Executes {@link \Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerValidatorPluginInterface} plugin stack.
     * - Returns `CustomerResponseTransfer` with `isSuccess = false` and the collected errors without updating anything when a plugin rejects the customer.
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
     * - Does not execute the {@link \Spryker\Zed\CustomerExtension\Dependency\Plugin\AddressValidatorPluginInterface}
     *   plugin stack; expects {@link \Spryker\Zed\Customer\Business\CustomerFacadeInterface::validateAddress()} to be executed beforehand.
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
     * - Does not execute the {@link \Spryker\Zed\CustomerExtension\Dependency\Plugin\AddressValidatorPluginInterface}
     *   plugin stack, expects {@link \Spryker\Zed\Customer\Business\CustomerFacadeInterface::validateAddress()} to be executed beforehand.
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
     * - Does not execute the {@link \Spryker\Zed\CustomerExtension\Dependency\Plugin\AddressValidatorPluginInterface}
     *   plugin stack, expects {@link \Spryker\Zed\Customer\Business\CustomerFacadeInterface::validateAddress()} to be executed beforehand.
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
     * - Retrieves a collection of customer addresses.
     * - Uses `AddressCriteriaTransfer.addressConditions.uuids` to filter by address uuids.
     * - Uses `AddressCriteriaTransfer.addressConditions.addressIds` to filter by address ids.
     * - Uses `AddressCriteriaTransfer.addressConditions.customerIds` to filter by owning customer ids.
     * - Combines all conditions with AND.
     * - Uses `AddressCriteriaTransfer.sortCollection.sort.field` to set the 'order by' field.
     * - Uses `AddressCriteriaTransfer.sortCollection.sort.isAscending` to set ascending/descending order.
     * - Supported sort fields are `createdAt`, `updatedAt`, `firstName`, `lastName`, `city`, `zipCode`;
     *   unsupported fields are ignored.
     * - Uses `AddressCriteriaTransfer.pagination.{page, maxPerPage}` to paginate results.
     * - Returns `AddressCollectionTransfer` filled with found addresses and populated pagination.
     * - Does not populate the default billing and shipping address flags on the returned addresses.
     * - Returns an empty collection when `addressConditions.uuids` is set but `spy_customer_address.uuid`
     *   is absent, which is the case unless a module contributing the address uuid schema extension is
     *   installed; filtering by uuid requires that extension.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressCriteriaTransfer $addressCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\AddressCollectionTransfer
     */
    public function getAddressCollection(AddressCriteriaTransfer $addressCriteriaTransfer): AddressCollectionTransfer;

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
     * @return array<string>
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

    /**
     * Specification:
     * - Resolves or creates a customer based on the OAuth resource owner data via `OauthCustomerResolveRequest.resourceOwner`.
     * - Applies the configured authentication strategy plugins (accept_only or create_customer_on_first_login).
     * - Runs restriction plugins after successful resolution.
     * - Returns `OauthCustomerResolveResponse.isSuccessful=false` if the customer cannot be resolved or is anonymized.
     * - Returns `OauthCustomerResolveResponse.isRestricted=true` with messages if a restriction plugin blocks login.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OauthCustomerResolveRequestTransfer $oauthCustomerResolveRequestTransfer
     *
     * @return \Generated\Shared\Transfer\OauthCustomerResolveResponseTransfer
     */
    public function resolveCustomer(OauthCustomerResolveRequestTransfer $oauthCustomerResolveRequestTransfer): OauthCustomerResolveResponseTransfer;

    /**
     * Specification:
     * - Requires `AddressTransfer.firstName` to be set.
     * - Requires `AddressTransfer.lastName` to be set.
     * - Validates customer address first name pattern using `CustomerConfig::getCustomerNamePattern()`.
     * - Validates customer address last name pattern using `CustomerConfig::getCustomerNamePattern()`.
     * - Validates customer address salutation against the `salutation` column value set when `AddressTransfer.salutation` is set.
     * - Validates customer address first name, last name, address1, address2, address3, company, city,
     *   zip code, phone and comment lengths against the `spy_customer_address` column widths.
     * - Executes {@link \Spryker\Zed\CustomerExtension\Dependency\Plugin\AddressValidatorPluginInterface} plugin stack.
     * - Executes every check regardless of preceding failures and collects all errors.
     * - Does not persist anything.
     * - Expects to be executed before {@link \Spryker\Zed\Customer\Business\CustomerFacadeInterface::createAddress()}
     *   or {@link \Spryker\Zed\Customer\Business\CustomerFacadeInterface::updateAddressAndCustomerDefaultAddresses()},
     *   neither of which reports validation errors.
     * - Returns `AddressResponseTransfer` with `isSuccess = true` and no errors when every check passes.
     * - Returns `AddressResponseTransfer` with `isSuccess = false` and `CustomerError` entries holding
     *   glossary keys as messages otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressResponseTransfer
     */
    public function validateAddress(AddressTransfer $addressTransfer): AddressResponseTransfer;
}
