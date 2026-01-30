# Customer Module - Shared Layer

## Overview

The Shared layer provides transfer definitions and constants used across all application layers (Zed, Yves, Client, Glue, Service).

---

## Constants

**Location**: `src/Spryker/Shared/Customer/CustomerConstants.php`

| Constant | Type | Value | Purpose |
|----------|------|-------|---------|
| `CUSTOMER_ANONYMOUS_PATTERN` | string | 'CUSTOMER_ANONYMOUS_PATTERN' | Pattern for identifying anonymous customers |
| `CUSTOMER_SECURED_PATTERN` | string | 'CUSTOMER_SECURED_PATTERN' | Pattern for secured customer routes |
| `BASE_URL_YVES` | string | 'CUSTOMER:BASE_URL_YVES' | Base URL for Yves frontend |
| `NAME_CUSTOMER_REFERENCE` | string | 'CustomerReference' | Customer reference identifier |
| `PARAM_ID_CUSTOMER` | string | 'id-customer' | URL parameter for customer ID |
| `PARAM_ID_CUSTOMER_ADDRESS` | string | 'id-customer-address' | URL parameter for address ID |
| `SHOP_MAIL_FROM_EMAIL_NAME` | string | 'SHOP_MAIL_FROM_EMAIL_NAME' | Sender email name config key |
| `SHOP_MAIL_FROM_EMAIL_ADDRESS` | string | 'SHOP_MAIL_FROM_EMAIL_ADDRESS' | Sender email address config key |
| `SHOP_MAIL_REGISTRATION_TOKEN` | string | 'SHOP_MAIL_REGISTRATION_TOKEN' | Registration email token config key |
| `SHOP_MAIL_REGISTRATION_SUBJECT` | string | 'SHOP_MAIL_REGISTRATION_SUBJECT' | Registration email subject config key |
| `SHOP_MAIL_PASSWORD_RESTORE_TOKEN` | string | 'PASSWORD_RESTORE_TOKEN' | Password restore token config key |
| `SHOP_MAIL_PASSWORD_RESTORE_SUBJECT` | string | 'PASSWORD_RESTORE_SUBJECT' | Password restore email subject config key |
| `SHOP_MAIL_PASSWORD_RESTORED_CONFIRMATION_TOKEN` | string | 'PASSWORD_RESTORED_CONFIRMATION_TOKEN' | Password restored confirmation token config key |
| `SHOP_MAIL_PASSWORD_RESTORED_CONFIRMATION_SUBJECT` | string | 'PASSWORD_RESTORED_CONFIRMATION_SUBJECT' | Password restored confirmation email subject config key |
| `REGISTRATION_CONFIRMATION_TOKEN_URL` | string | 'CUSTOMER:REGISTRATION_CONFIRMATION_TOKEN_URL' | Registration confirmation URL format |

---

## Configuration

**Location**: `src/Spryker/Shared/Customer/CustomerConfig.php`

**Note**: Shared config is available to ALL layers (Zed, Yves, Client, Glue, Service)

### Public Constants

| Constant | Type | Value | Purpose |
|----------|------|-------|---------|
| `ANONYMOUS_SESSION_KEY` | string | 'anonymousID' | Session key for anonymous customers |
| `URL_PARAM_LOCALE` | string | '_locale' | URL parameter for locale |

### Public Methods

| Method | Return Type | Description |
|--------|-------------|-------------|
| `isDoubleOptInEnabled()` | bool | Returns whether double opt-in registration is enabled. Default: false |

---

## Transfer Definitions

**Location**: `src/Spryker/Shared/Customer/Transfer/customer.transfer.xml`

**Total Transfers Defined**: 38

### Core Transfers

#### CustomerTransfer

**Purpose**: Represents a customer entity with complete profile and authentication data

**Properties** (51 total):
- `email` (string, optional) - Customer email address
- `idCustomer` (int, optional) - Customer primary key
- `customerReference` (string, optional) - Unique customer reference
- `firstName` (string, optional) - Customer first name
- `lastName` (string, optional) - Customer last name
- `company` (string, optional) - Company name
- `gender` (string, optional) - Customer gender
- `dateOfBirth` (string, optional) - Date of birth
- `salutation` (string, optional) - Salutation (Mr, Ms, Dr, etc.)
- `password` (string, optional) - Encrypted password
- `newPassword` (string, optional) - New password for update operations
- `billingAddress` (Address[], optional, singular: billingAddress) - Billing addresses
- `shippingAddress` (Address[], optional, singular: shippingAddress) - Shipping addresses
- `addresses` (Addresses, optional) - All customer addresses
- `defaultBillingAddress` (string, optional) - Default billing address ID
- `defaultShippingAddress` (string, optional) - Default shipping address ID
- `createdAt` (string, optional) - Creation timestamp
- `updatedAt` (string, optional) - Last update timestamp
- `restorePasswordKey` (string, optional) - Password reset token
- `restorePasswordLink` (string, optional) - Password reset URL
- `restorePasswordDate` (string, optional) - Password reset token creation date
- `registrationKey` (string, optional) - Registration confirmation token
- `confirmationLink` (string, optional) - Registration confirmation URL
- `registered` (string, optional) - Registration status
- `message` (string, optional) - Response message
- `sendPasswordToken` (bool, optional) - Whether to send password token email
- `isGuest` (bool, optional) - Guest checkout flag
- `locale` (Locale, optional) - Customer locale
- `anonymizedAt` (string, optional) - Anonymization timestamp (GDPR)
- `fkUser` (int, optional) - Foreign key to user
- `username` (string, optional) - Username
- `phone` (string, optional) - Phone number
- `isDirty` (bool, optional) - Flag for session reload
- `storeName` (string, optional) - Store name
- `isEditedInBackoffice` (bool, optional, strict: true) - Backoffice edit flag

**Associations**:
- References `AddressTransfer` for addresses
- References `LocaleTransfer` for locale

---

#### AddressTransfer

**Purpose**: Represents a customer address with complete location and contact data

**Properties** (32 total):
- `idCustomerAddress` (int, optional) - Address primary key
- `customerId` (string, optional) - Customer ID string
- `fkCustomer` (int, optional) - Foreign key to customer
- `email` (string, optional) - Contact email
- `salutation` (string, optional) - Salutation
- `firstName` (string, optional) - First name
- `lastName` (string, optional) - Last name
- `middleName` (string, optional) - Middle name
- `address1` (string, optional) - Address line 1
- `address2` (string, optional) - Address line 2
- `address3` (string, optional) - Address line 3
- `company` (string, optional) - Company name
- `city` (string, optional) - City
- `zipCode` (string, optional) - Postal code
- `phone` (string, optional) - Phone number
- `cellPhone` (string, optional) - Cell phone number
- `comment` (string, optional) - Address comment
- `state` (string, optional) - State/province
- `region` (string, optional) - Region
- `isDeleted` (bool, optional) - Soft delete flag
- `isDefaultBilling` (bool, optional) - Default billing flag
- `isDefaultShipping` (bool, optional) - Default shipping flag
- `fkCountry` (int, optional) - Foreign key to country
- `fkRegion` (int, optional) - Foreign key to region
- `fkMiscCountry` (int, optional) - Foreign key to misc country
- `iso2Code` (string, optional) - Country ISO2 code
- `anonymizedAt` (string, optional) - Anonymization timestamp (GDPR)
- `country` (Country, optional) - Country transfer
- `isAddressSavingSkipped` (bool, optional) - Skip persistence flag
- `idSalesOrderAddress` (int, optional) - Sales order address ID
- `idCompanyUnitAddress` (int, optional) - Company unit address ID
- `uuid` (string, optional) - Unique identifier
- `key` (string, optional) - Unique address key
- `isFromExternalService` (bool, optional) - External service flag (e.g., PayPal)

**Associations**:
- References `CountryTransfer` for country

---

#### CustomerResponseTransfer

**Purpose**: Response wrapper for customer operations with success/error status

**Properties**:
- `hasCustomer` (bool, optional) - Customer existence flag
- `customerTransfer` (Customer, optional) - Customer entity on success
- `isSuccess` (bool, optional) - Operation success flag
- `message` (Message, optional) - DEPRECATED. Response message
- `messages` (Message[], optional, singular: message) - Response messages
- `errors` (CustomerError[], optional, singular: error) - Validation errors

**Associations**:
- References `CustomerTransfer` for customer data
- References `MessageTransfer` for messages
- References `CustomerErrorTransfer` for errors

---

#### CustomerErrorTransfer

**Purpose**: Validation error message with parameters

**Properties**:
- `message` (string, optional) - Error message
- `parameters` (array, optional, singular: parameters) - Message parameters for translation

---

### Collection Transfers

#### AddressesTransfer

**Purpose**: Collection of customer addresses

**Properties**:
- `addresses` (Address[], optional, singular: address) - Array of address entities

---

#### CustomerCollectionTransfer

**Purpose**: Collection of customers with pagination support

**Properties**:
- `customers` (Customer[], optional, singular: customer) - Array of customer entities
- `filter` (Filter, optional) - Filter criteria
- `pagination` (Pagination, optional) - Pagination metadata

**Associations**:
- References `FilterTransfer` for filtering
- References `PaginationTransfer` for pagination

---

### Criteria Transfers

#### CustomerCriteriaFilterTransfer

**Purpose**: Search criteria for customer queries

**Properties**:
- `customerIds` (int[], optional, singular: customerId, strict: true) - Filter by customer IDs
- `hasAnonymizedAt` (bool, optional, strict: true) - Filter anonymized customers
- `restorePasswordKeyExists` (bool, optional) - Filter customers with password reset token
- `passwordExists` (bool, optional) - Filter customers with password set

---

#### AddressCriteriaFilterTransfer

**Purpose**: Search criteria for address queries

**Properties**:
- `idCustomerAddress` (int, optional) - Filter by address ID
- `fkCustomer` (int, optional) - Filter by customer ID

---

#### CustomerCriteriaTransfer

**Purpose**: Advanced customer search criteria

**Properties**:
- `customerReference` (string, optional) - Filter by customer reference
- `idCustomer` (int, optional) - Filter by customer ID
- `withExpanders` (bool, optional) - Include expander plugins

---

### Integration Transfers

#### QuoteTransfer Extensions

**Purpose**: Extends Quote with customer and address data for checkout

**Properties added**:
- `customer` (Customer, optional) - Customer entity
- `billingAddress` (Address, optional) - Billing address
- `shippingAddress` (Address, optional, deprecated) - DEPRECATED. Use items.shipment.shippingAddress
- `billingSameAsShipping` (bool, optional) - Same address flag
- `isAddressSavingSkipped` (bool, optional, deprecated) - DEPRECATED. Use Address.isAddressSavingSkipped
- `items` (Item[], optional, singular: item) - Quote items

---

#### OrderTransfer Extensions

**Purpose**: Extends Order with customer reference

**Properties added**:
- `fkCustomer` (int, optional) - Customer foreign key
- `customer` (Customer, optional) - Customer entity
- `customerReference` (string, optional) - Customer reference

---

#### ItemTransfer Extensions

**Purpose**: Extends Item with shipment data

**Properties added**:
- `shipment` (Shipment, optional) - Shipment information

---

#### ShipmentTransfer

**Purpose**: Shipment information with shipping address

**Properties**:
- `shippingAddress` (Address, optional) - Shipping address

---

### Checkout Transfers

#### CheckoutResponseTransfer

**Purpose**: Checkout response with success status and errors

**Properties**:
- `saveOrder` (SaveOrder, optional) - Saved order data
- `isSuccess` (bool, optional) - Checkout success flag
- `errors` (CheckoutError[], optional, singular: error) - Checkout errors

---

#### CheckoutErrorTransfer

**Purpose**: Checkout validation error

**Properties**:
- `errorCode` (int, optional) - Error code
- `message` (string, optional) - Error message

---

### Mail Transfers

#### MailTransfer

**Purpose**: Email message with customer data and templates

**Properties**:
- `customer` (Customer, optional) - Customer entity
- `type` (string, optional) - Mail type
- `locale` (Locale, optional) - Locale for translation
- `subject` (string, optional) - Email subject
- `templates` (MailTemplate[], optional, singular: template) - Email templates
- `recipients` (MailRecipient[], optional, singular: recipient) - Email recipients
- `storeName` (string, optional) - Store name

---

#### MailRecipientTransfer

**Purpose**: Email recipient information

**Properties**:
- `email` (string, optional) - Recipient email
- `name` (string, optional) - Recipient name

---

#### MailTemplateTransfer

**Purpose**: Email template definition

**Properties**:
- `name` (string, optional) - Template name
- `isHtml` (bool, optional) - HTML format flag

---

### Other Transfers

#### CountryTransfer

**Purpose**: Country entity

**Properties**:
- `idCountry` (int, optional) - Country ID
- `name` (string, optional) - Country name
- `iso2Code` (string, optional) - ISO2 country code

---

#### CountryCollectionTransfer

**Purpose**: Collection of countries

**Properties**:
- `countries` (Country[], optional, singular: country) - Array of countries

---

#### LocaleTransfer

**Purpose**: Locale entity

**Properties**:
- `localeName` (string, optional) - Locale name (e.g., en_US)
- `idLocale` (int, optional) - Locale ID

---

#### StoreTransfer

**Purpose**: Store entity with countries

**Properties**:
- `name` (string, optional) - Store name
- `countries` (string[], optional, singular: country) - Available countries

---

#### MessageTransfer

**Purpose**: Generic message with translation parameters

**Properties**:
- `value` (string, optional) - Message value
- `parameters` (array, optional, singular: parameters) - Translation parameters

---

#### ButtonTransfer

**Purpose**: UI button configuration

**Properties**:
- `url` (string, optional) - Button URL
- `title` (string, optional) - Button title
- `defaultOptions` (array, optional, singular: defaultOptions) - Default options
- `customOptions` (array, optional, singular: customOptions) - Custom options

---

#### SequenceNumberSettingsTransfer

**Purpose**: Sequence number generation settings

**Properties**:
- `name` (string, optional) - Sequence name
- `prefix` (string, optional) - Number prefix

---

#### PaginationTransfer

**Purpose**: Pagination metadata

**Properties**:
- `page` (int, optional) - Current page
- `maxPerPage` (int, optional) - Items per page
- `nbResults` (int, optional) - Total results
- `firstIndex` (int, optional) - First item index
- `lastIndex` (int, optional) - Last item index
- `firstPage` (int, optional) - First page number
- `lastPage` (int, optional) - Last page number
- `nextPage` (int, optional) - Next page number
- `previousPage` (int, optional) - Previous page number

---

#### FilterTransfer

**Purpose**: Generic filter criteria

**Properties**: (empty - used as base for specific filters)

---

#### SaveOrderTransfer

**Purpose**: Saved order metadata

**Properties**: (empty - populated by checkout process)

---

### ACL Transfers

#### AclEntityRuleTransfer

**Purpose**: ACL entity rule for merchant portal

**Properties**:
- `entity` (string, optional) - Entity class name
- `scope` (string, optional) - Access scope
- `permissionMask` (int, optional) - Permission bitmask

---

### Company Integration Transfers

#### CompanyRoleTransfer

**Purpose**: Company role with user collection

**Properties**:
- `companyUserCollection` (CompanyUserCollection, optional) - Associated company users

---

#### CompanyUserTransfer

**Purpose**: Company user entity

**Properties**:
- `customer` (Customer, optional) - Associated customer

---

#### CompanyUserCollectionTransfer

**Purpose**: Collection of company users

**Properties**:
- `companyUsers` (CompanyUser[], optional, singular: companyUser) - Array of company users

---

#### CompanyUserResponseTransfer

**Purpose**: Company user operation response

**Properties**:
- `companyUser` (CompanyUser, optional) - Company user entity

---

### Session Validation Transfers

#### SessionEntityRequestTransfer

**Purpose**: Session entity validation request

**Properties**:
- `idEntity` (int, optional) - Entity ID to validate

---

#### SessionEntityResponseTransfer

**Purpose**: Session entity validation response

**Properties**:
- `isSuccessfull` (bool, optional) - Validation success flag

---

### Authorization Transfers

#### AuthorizationRequestTransfer

**Purpose**: Authorization request with identity and entity

**Properties**:
- `identity` (AuthorizationIdentity, optional) - Authorization identity
- `entity` (AuthorizationEntity, optional) - Entity to authorize

---

#### AuthorizationIdentityTransfer

**Purpose**: Authorization identity

**Properties**:
- `identifier` (string, optional) - Identity identifier

---

#### AuthorizationEntityTransfer

**Purpose**: Authorization entity

**Properties**:
- `identifier` (string, optional) - Entity identifier

---

## Architecture Notes

### Transfer Object Pattern

All transfers follow Spryker's Transfer Object pattern:

- **Strict mode**: Properties marked as `strict: true` must be set before use
- **Type safety**: All properties are strongly typed
- **Associations**: Transfers reference other transfers for complex data structures
- **Collections**: Array properties use `singular` attribute for item naming

### Cross-Layer Communication

Transfers serve as the data contract between layers:

- **Zed → Client**: Gateway methods accept/return transfers
- **Client → Yves**: Client methods use transfers
- **Database → Business**: Mappers convert entities to transfers
- **Business → Persistence**: Transfers passed to Repository/EntityManager

### Extension Pattern

Transfers can be extended at project level:

- Add new properties without modifying core
- Extend existing transfers with additional data
- Maintain backward compatibility
