# Customer Module - Zed Layer

## Overview

The Zed layer provides backend administration and business logic for customer management, including CRUD operations, password management, address handling, checkout integration, and GDPR anonymization.

---

## Configuration

**Location**: `src/Spryker/Zed/Customer/CustomerConfig.php`

### Public Constants

| Constant | Type | Value | Description |
|----------|------|-------|-------------|
| `ERROR_CODE_CUSTOMER_ALREADY_REGISTERED` | int | 4001 | Error code for duplicate customer registration |
| `ERROR_CODE_CUSTOMER_INVALID_EMAIL` | int | 4002 | Error code for invalid email format |
| `CUSTOMER_REGISTRATION_MAIL_TYPE` | string | 'customer registration mail' | Mail type for registration email |
| `CUSTOMER_REGISTRATION_WITH_CONFIRMATION_MAIL_TYPE` | string | 'customer registration confirmation mail' | Mail type for confirmation email |
| `GLOSSARY_KEY_CONFIRM_EMAIL_LINK_INVALID_OR_USED` | string | 'customer.error.confirm_email_link.invalid_or_used' | Translation key for invalid/used confirmation link |
| `PATTERN_FIRST_NAME` | string | '/^[^:\/<>]+$/' | Regex for first name validation |
| `PATTERN_LAST_NAME` | string | '/^[^:\/<>]+$/' | Regex for last name validation |

### Public Methods

| Method Name | Return Type | Description |
|-------------|-------------|-------------|
| `getHostYves()` | string | Returns Yves base URL from CustomerConstants::BASE_URL_YVES |
| `isCustomerPasswordResetExpirationEnabled()` | bool | Returns whether password reset expiration is enabled. Default: false |
| `getCustomerPasswordResetExpirationPeriod()` | string | Returns password reset expiration period (PHP DateTime modify compatible). Default: '+2 hours' |
| `getCustomerPasswordRestoreTokenUrl(string $token, ?string $storeName)` | string | Returns password restore URL with token and optional store |
| `getRegisterConfirmTokenUrl(string $token, ?string $storeName)` | string | Returns registration confirmation URL with token and optional store |
| `getCustomerReferenceDefaults(?string $sequenceNumberPrefix)` | SequenceNumberSettingsTransfer | Returns sequence number settings for customer reference generation |
| `getCustomerSequenceNumberPrefix()` | ?string | Returns prefix for customer reference. Default: null |
| `getCustomerPasswordCharacterSet()` | string | Returns regex for password character set validation. Default: '/^.*$/' (any) |
| `getCustomerDetailExternalBlocksUrls()` | array\<string\> | Returns URLs for external blocks in customer detail page. Default: [] |
| `getCustomerPasswordMinLength()` | int | Returns minimum password length. Default: 1 |
| `getCustomerPasswordMaxLength()` | int | Returns maximum password length. Default: 128 |
| `getCustomerPasswordAllowList()` | array\<string\> | Returns passwords that bypass policy validation. Default: [] |
| `getCustomerPasswordDenyList()` | array\<string\> | Returns insecure passwords to reject. Default: [] |
| `getCustomerPasswordSequenceLimit()` | ?int | Returns maximum character repetition limit. Default: null |
| `isRestorePasswordValidationEnabled()` | bool | DEPRECATED. Returns whether password validation is enabled for restorePassword(). Default: false |
| `isDoubleOptInEnabled()` | bool | Returns whether double opt-in registration is enabled (delegates to shared config) |
| `getCustomerInvalidSalutationErrorCode()` | int | Returns error code for invalid salutation. Default: 4003 |
| `isCustomerEmailValidationCaseSensitive()` | bool | Returns whether email validation is case sensitive. Default: false |

---

## Dependency Provider

**Location**: `src/Spryker/Zed/Customer/CustomerDependencyProvider.php`

### Dependency Injection Points

| Method Name | Return Type | Purpose | Required |
|-------------|-------------|---------|----------|
| `addSequenceNumberFacade()` | Container | Provides SequenceNumberFacade for customer reference generation | Yes |
| `addCountryFacade()` | Container | Provides CountryFacade for country operations | Yes |
| `addLocaleFacade()` | Container | Provides LocaleFacade for locale operations | Yes |
| `addMailFacade()` | Container | Provides MailFacade for sending emails | Yes |
| `addRouterFacade()` | Container | Provides RouterFacade for URL generation | Yes |
| `addStoreFacade()` | Container | Provides StoreFacade for store operations | Yes |
| `addUtilValidateService()` | Container | Provides UtilValidateService for validation | Yes |
| `addUtilSanitizeService()` | Container | Provides UtilSanitizeService for XSS protection | Yes |
| `addUtilDateTimeService()` | Container | Provides UtilDateTimeService for date formatting | Yes |
| `addCustomerService()` | Container | Provides CustomerService for address key generation | Yes |
| `addStorageRedisClient()` | Container | Provides StorageRedisClient for cache invalidation | Yes |
| `addPropelQueryLocale()` | Container | Provides SpyLocaleQuery for database queries | Yes |
| `addSubRequestHandler()` | Container | Provides Symfony sub-request handler | Yes |
| `addCustomerAnonymizerPlugins()` | Container | Plugin stack for customer anonymization | No |
| `addCustomerTransferExpanderPlugins()` | Container | Plugin stack for expanding customer transfer | No |
| `addPostCustomerRegistrationPlugins()` | Container | Plugin stack executed after registration | No |
| `addCustomerTableActionPlugins()` | Container | Plugin stack for customer table action buttons | No |
| `addCustomerPostDeletePlugins()` | Container | Plugin stack executed after deletion | No |
| `addCustomerPreUpdatePlugins()` | Container | Plugin stack executed before update | No |

### Constants

| Constant | Value | Purpose |
|----------|-------|---------|
| `FACADE_SEQUENCE_NUMBER` | 'FACADE_SEQUENCE_NUMBER' | Dependency key for SequenceNumberFacade |
| `FACADE_COUNTRY` | 'FACADE_COUNTRY' | Dependency key for CountryFacade |
| `FACADE_LOCALE` | 'FACADE_LOCALE' | Dependency key for LocaleFacade |
| `FACADE_MAIL` | 'FACADE_MAIL' | Dependency key for MailFacade |
| `FACADE_ROUTER` | 'FACADE_ROUTER' | Dependency key for RouterFacade |
| `FACADE_STORE` | 'FACADE_STORE' | Dependency key for StoreFacade |
| `SERVICE_UTIL_VALIDATE` | 'SERVICE_UTIL_VALIDATE' | Dependency key for UtilValidateService |
| `SERVICE_UTIL_SANITIZE` | 'SERVICE_UTIL_SANITIZE' | Dependency key for UtilSanitizeService |
| `SERVICE_UTIL_DATE_TIME` | 'SERVICE_UTIL_DATE_TIME' | Dependency key for UtilDateTimeService |
| `SERVICE_CUSTOMER` | 'SERVICE_CUSTOMER' | Dependency key for CustomerService |
| `CLIENT_STORAGE_REDIS` | 'CLIENT_STORAGE_REDIS' | Dependency key for StorageRedisClient |
| `PROPEL_QUERY_LOCALE` | 'PROPEL_QUERY_LOCALE' | Dependency key for SpyLocaleQuery |
| `SUB_REQUEST_HANDLER` | 'SUB_REQUEST_HANDLER' | Dependency key for sub-request handler |
| `PLUGINS_CUSTOMER_ANONYMIZER` | 'PLUGINS_CUSTOMER_ANONYMIZER' | Plugin stack key for anonymizers |
| `PLUGINS_CUSTOMER_TRANSFER_EXPANDER` | 'PLUGINS_CUSTOMER_TRANSFER_EXPANDER' | Plugin stack key for expanders |
| `PLUGINS_POST_CUSTOMER_REGISTRATION` | 'PLUGINS_POST_CUSTOMER_REGISTRATION' | Plugin stack key for post-registration |
| `PLUGINS_CUSTOMER_TABLE_ACTION_EXPANDER` | 'PLUGINS_CUSTOMER_TABLE_ACTION_EXPANDER' | Plugin stack key for table actions |
| `PLUGINS_CUSTOMER_POST_DELETE` | 'PLUGINS_CUSTOMER_POST_DELETE' | Plugin stack key for post-delete |
| `PLUGINS_CUSTOMER_PRE_UPDATE` | 'PLUGINS_CUSTOMER_PRE_UPDATE' | Plugin stack key for pre-update |

---

## Business Layer

### Facade

**Location**: `src/Spryker/Zed/Customer/Business/CustomerFacade.php`

The Facade provides the public API for all customer business operations.

#### Customer Management Methods

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `hasEmail(string $email)` | string | bool | Checks if email exists in system |
| `addCustomer(CustomerTransfer)` | CustomerTransfer | CustomerResponseTransfer | Adds new customer with validation and password encryption |
| `registerCustomer(CustomerTransfer)` | CustomerTransfer | CustomerResponseTransfer | Registers customer with confirmation email |
| `confirmRegistration(CustomerTransfer)` | CustomerTransfer | CustomerTransfer | DEPRECATED. Confirms registration |
| `confirmCustomerRegistration(CustomerTransfer)` | CustomerTransfer | CustomerResponseTransfer | Confirms customer registration with error handling |
| `getCustomer(CustomerTransfer)` | CustomerTransfer | CustomerTransfer | Retrieves customer with addresses and locale |
| `findCustomerById(CustomerTransfer)` | CustomerTransfer | ?CustomerTransfer | Finds customer by ID (nullable) |
| `updateCustomer(CustomerTransfer)` | CustomerTransfer | CustomerResponseTransfer | Updates customer data and password if provided |
| `deleteCustomer(CustomerTransfer)` | CustomerTransfer | bool | Deletes customer and executes post-delete plugins |
| `findByReference(string)` | string | ?CustomerTransfer | Finds customer by reference (nullable) |
| `findCustomerByReference(string)` | string | CustomerResponseTransfer | Finds customer by reference with response object |
| `getCustomerCollection(CustomerCollectionTransfer)` | CustomerCollectionTransfer | CustomerCollectionTransfer | Retrieves filtered customers with pagination |
| `getCustomerCollectionByCriteria(CustomerCriteriaFilterTransfer)` | CustomerCriteriaFilterTransfer | CustomerCollectionTransfer | Retrieves customers by filter criteria |
| `getCustomerByCriteria(CustomerCriteriaTransfer)` | CustomerCriteriaTransfer | CustomerResponseTransfer | Finds customer by criteria with optional expanders |

#### Password Management Methods

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `sendPasswordRestoreMail(CustomerTransfer)` | CustomerTransfer | CustomerResponseTransfer | Sends password reset email |
| `restorePassword(CustomerTransfer)` | CustomerTransfer | CustomerResponseTransfer | Restores password with validation and confirmation |
| `updateCustomerPassword(CustomerTransfer)` | CustomerTransfer | CustomerResponseTransfer | Updates customer password with current password validation |
| `tryAuthorizeCustomerByEmailAndPassword(CustomerTransfer)` | CustomerTransfer | bool | Authenticates customer by email and password |
| `sendPasswordRestoreMailForCustomerCollection(CustomerCollectionTransfer, ?OutputInterface)` | CustomerCollectionTransfer, ?OutputInterface | void | Batch sends password reset emails |

#### Address Management Methods

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `getAddress(AddressTransfer)` | AddressTransfer | AddressTransfer | Retrieves address with default flags |
| `getAddresses(CustomerTransfer)` | CustomerTransfer | AddressesTransfer | Gets all addresses for customer |
| `createAddress(AddressTransfer)` | AddressTransfer | AddressTransfer | Creates new customer address |
| `updateAddress(AddressTransfer)` | AddressTransfer | AddressTransfer | Updates customer address |
| `updateAddressAndCustomerDefaultAddresses(AddressTransfer)` | AddressTransfer | CustomerTransfer | Updates address and sets defaults |
| `createAddressAndUpdateCustomerDefaultAddresses(AddressTransfer)` | AddressTransfer | CustomerTransfer | Creates address and sets defaults |
| `findCustomerAddressById(int)` | int | ?AddressTransfer | Finds address by ID (nullable) |
| `findCustomerAddressByAddressData(AddressTransfer)` | AddressTransfer | ?AddressTransfer | Finds address by address details (nullable) |
| `setDefaultBillingAddress(AddressTransfer)` | AddressTransfer | bool | Sets default billing address |
| `setDefaultShippingAddress(AddressTransfer)` | AddressTransfer | bool | Sets default shipping address |
| `getDefaultBillingAddress(CustomerTransfer)` | CustomerTransfer | AddressTransfer | Retrieves customer's default billing address |
| `getDefaultShippingAddress(CustomerTransfer)` | CustomerTransfer | AddressTransfer | Retrieves customer's default shipping address |
| `deleteAddress(AddressTransfer)` | AddressTransfer | AddressTransfer | Deletes customer address |
| `renderAddress(AddressTransfer)` | AddressTransfer | string | Returns formatted address string |

#### Checkout & Order Methods

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `checkOrderPreSaveConditions(QuoteTransfer, CheckoutResponseTransfer)` | QuoteTransfer, CheckoutResponseTransfer | bool | Pre-checkout validation |
| `saveOrderCustomer(QuoteTransfer, SaveOrderTransfer)` | QuoteTransfer, SaveOrderTransfer | void | Saves customer during checkout |
| `saveCustomerForOrder(QuoteTransfer, CheckoutResponseTransfer)` | QuoteTransfer, CheckoutResponseTransfer | void | DEPRECATED. Saves customer for order |
| `hydrateSalesOrderCustomerInformation(OrderTransfer)` | OrderTransfer | OrderTransfer | Populates customer data in order |

#### Validation Methods

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `validateCustomerCheckoutSalutation(QuoteTransfer, CheckoutResponseTransfer)` | QuoteTransfer, CheckoutResponseTransfer | bool | Validates customer salutation at checkout |
| `validateCustomerAddressCheckoutSalutation(QuoteTransfer, CheckoutResponseTransfer)` | QuoteTransfer, CheckoutResponseTransfer | bool | Validates address salutations at checkout |

#### Other Methods

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `anonymizeCustomer(CustomerTransfer)` | CustomerTransfer | void | Anonymizes customer and addresses for GDPR |
| `getAllSalutations()` | - | array | Returns all available salutation values |

---

### Model Classes

**Location**: `src/Spryker/Zed/Customer/Business/`

#### Core Customer Operations

| Class | Responsibility | Implements | Key Methods |
|-------|----------------|------------|-------------|
| `Customer` | Core customer CRUD, password management, registration | CustomerInterface | add(), register(), confirmCustomerRegistration(), sendPasswordRestoreMail(), restorePassword(), update(), updatePassword(), delete(), get(), findById(), findByReference() |
| `Address` | Address CRUD, default address management, formatting | AddressInterface | createAddress(), getAddress(), updateAddress(), deleteAddress(), setDefaultBillingAddress(), setDefaultShippingAddress(), getFormattedAddressString() |
| `CustomerReader` | Customer retrieval with filtering and pagination | CustomerReaderInterface | getCustomerCollection(), findCustomerByReference(), getCustomerByCriteria() |
| `EmailValidator` | Email format and uniqueness validation | EmailValidatorInterface | isFormatValid(), isEmailAvailableForCustomer(), isEmailLengthValid() |

#### Checkout Operations

| Class | Responsibility | Implements |
|-------|----------------|------------|
| `CustomerOrderSaver` | DEPRECATED. Legacy checkout customer saving | CustomerOrderSaverInterface |
| `CustomerOrderSaverWithMultiShippingAddress` | Enhanced checkout saving with multi-shipment support | CustomerOrderSaverInterface |
| `PreConditionChecker` | Pre-checkout validation (email, uniqueness) | PreConditionCheckerInterface |
| `CustomerOrderHydrator` | Populates customer data into order | CustomerOrderHydratorInterface |

#### Password & Reference Management

| Class | Responsibility | Implements |
|-------|----------------|------------|
| `CustomerPasswordPolicyValidator` | Validates passwords against multiple policies | CustomerPasswordPolicyValidatorInterface |
| `LengthCustomerPasswordPolicy` | Validates minimum password length | CustomerPasswordPolicyInterface |
| `CharacterSetCustomerPasswordPolicy` | Validates character set requirements | CustomerPasswordPolicyInterface |
| `SequenceCustomerPasswordPolicy` | Validates against sequential characters | CustomerPasswordPolicyInterface |
| `DenyListCustomerPasswordPolicy` | Checks password against deny list | CustomerPasswordPolicyInterface |
| `PasswordResetExpirationChecker` | Validates password reset token expiration | PasswordResetExpirationCheckerInterface |
| `CustomerReferenceGenerator` | Generates unique customer references | CustomerReferenceGeneratorInterface |

#### Plugin Execution & Data Enrichment

| Class | Responsibility | Implements |
|-------|----------------|------------|
| `CustomerPluginExecutor` | Executes customer lifecycle plugins | CustomerPluginExecutorInterface |
| `CustomerExpander` | Applies transfer expander plugins | CustomerExpanderInterface |
| `CustomerAnonymizer` | Anonymizes customer for GDPR compliance | CustomerAnonymizerInterface |

#### Validators

| Class | Responsibility | Implements |
|-------|----------------|------------|
| `CustomerCheckoutSalutationValidator` | Validates customer salutation during checkout | CustomerCheckoutSalutationValidatorInterface |
| `CustomerAddressCheckoutSalutationValidator` | Validates address salutations at checkout | CustomerAddressCheckoutSalutationValidatorInterface |

#### Cache Management

| Class | Responsibility | Implements |
|-------|----------------|------------|
| `StorageCustomerInvalidator` | Invalidates customer cache in Redis storage | CustomerInvalidatorInterface |

---

## Communication Layer

### Controllers

**Location**: `src/Spryker/Zed/Customer/Communication/Controller/`

| Controller | Actions | Purpose |
|------------|---------|---------|
| `IndexController` | indexAction(), tableAction() | Lists all customers with data table; tableAction() returns JSON |
| `AddController` | indexAction() | Displays customer creation form and handles submission |
| `EditController` | indexAction() | Displays customer edit form with address management |
| `ViewController` | indexAction(), addressTableAction() | Shows customer details with addresses and external blocks |
| `DeleteController` | indexAction(), confirmAction() | Deletion confirmation page and processing |
| `AddressController` | addAction(), editAction() | Add and edit customer addresses |
| `DownloadController` | indexAction() | Streams customer data as CSV export |
| `GatewayController` | 21 action methods | Inter-module communication gateway for customer operations |

**GatewayController Actions**: registerAction(), confirmCustomerRegistrationAction(), sendPasswordRestoreMailAction(), restorePasswordAction(), deleteAction(), hasCustomerWithEmailAndPasswordAction(), customerAction(), updateAction(), updatePasswordAction(), addressAction(), addressesAction(), updateAddressAction(), newAddressAction(), deleteAddressAction(), defaultBillingAddressAction(), defaultShippingAddressAction(), anonymizeCustomerAction(), findCustomerByReferenceAction(), updateAddressAndCustomerDefaultAddressesAction(), createAddressAndUpdateCustomerDefaultAddressesAction(), confirmRegistrationAction() (deprecated)

---

### Plugins Provided to Other Modules

**Location**: `src/Spryker/Zed/Customer/Communication/Plugin/`

#### For AclMerchantPortal Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `CustomerMerchantAclEntityRuleExpanderPlugin` | MerchantAclEntityRuleExpanderPluginInterface | Adds inherited read-only access to SpyCustomer entity for merchant portal | AclMerchantPortalDependencyProvider::getAclEntityRuleExpanderPlugins() |

#### For Checkout Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `CustomerAddressSalutationCheckoutPreConditionPlugin` | CheckoutPreConditionPluginInterface | Validates address salutations during checkout | CheckoutDependencyProvider::getPreConditionPlugins() |
| `CustomerOrderSavePlugin` | CheckoutDoSaveOrderInterface | Saves customer order relationship during checkout | CheckoutDependencyProvider::getOrderSavers() |
| `CustomerSalutationCheckoutPreConditionPlugin` | CheckoutPreConditionPluginInterface | Validates customer salutation during checkout | CheckoutDependencyProvider::getPreConditionPlugins() |
| `CustomerPreConditionCheckerPlugin` | CheckoutPreConditionInterface | Checks order pre-save conditions | CheckoutDependencyProvider::getCheckoutPreConditions() |
| `OrderCustomerSavePlugin` | CheckoutSaveOrderInterface | DEPRECATED. Saves customer for order | CheckoutDependencyProvider::getCheckoutOrderSavers() |

#### For CompanyRole Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `CustomerInvalidationCompanyRolePostSavePlugin` | CompanyRolePostSavePluginInterface | Invalidates customers associated with company users after role save | CompanyRoleDependencyProvider::getCompanyRolePostSavePlugins() |

#### For CompanyUser Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `CustomerInvalidationCompanyUserPostUpdatePlugin` | CompanyUserPostUpdatePluginInterface | Invalidates customer cache after company user update | CompanyUserDependencyProvider::getCompanyUserPostUpdatePlugins() |

#### For Mail Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `CustomerRegistrationMailTypeBuilderPlugin` | MailTypeBuilderPluginInterface | Builds customer registration email | MailDependencyProvider::getMailTypeBuilderPlugins() |
| `CustomerRegistrationConfirmationMailTypeBuilderPlugin` | MailTypeBuilderPluginInterface | Builds double opt-in registration confirmation email | MailDependencyProvider::getMailTypeBuilderPlugins() |
| `CustomerRestorePasswordMailTypeBuilderPlugin` | MailTypeBuilderPluginInterface | Builds password restore email | MailDependencyProvider::getMailTypeBuilderPlugins() |
| `CustomerRestoredPasswordConfirmationMailTypeBuilderPlugin` | MailTypeBuilderPluginInterface | Builds password reset confirmation email | MailDependencyProvider::getMailTypeBuilderPlugins() |
| `CustomerRegistrationMailTypePlugin` | MailTypePluginInterface | DEPRECATED. Use CustomerRegistrationMailTypeBuilderPlugin | - |
| `CustomerRegistrationConfirmationMailTypePlugin` | MailTypePluginInterface | DEPRECATED. Use CustomerRegistrationConfirmationMailTypeBuilderPlugin | - |
| `CustomerRestorePasswordMailTypePlugin` | MailTypePluginInterface | DEPRECATED. Use CustomerRestorePasswordMailTypeBuilderPlugin | - |
| `CustomerRestoredPasswordConfirmationMailTypePlugin` | MailTypePluginInterface | DEPRECATED. Use CustomerRestoredPasswordConfirmationMailTypeBuilderPlugin | - |

#### For Sales Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `CustomerOrderHydratePlugin` | HydrateOrderPluginInterface | Hydrates order transfer with customer information | SalesDependencyProvider::getOrderHydrationPlugins() |

#### For TaxProductConnector Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `CustomerAddressShippingAddressValidatorPlugin` | ShippingAddressValidatorPluginInterface | DEPRECATED. Validates shipping address | TaxProductConnectorDependencyProvider::getShippingAddressValidatorPlugins() |

---

### Forms

**Location**: `src/Spryker/Zed/Customer/Communication/Form/`

| Form Class | Used By | Purpose | Key Fields |
|------------|---------|---------|------------|
| `CustomerForm` | AddController::indexAction() | Customer creation | email, salutation, first_name, last_name, gender, date_of_birth, phone, company, locale, send_password_token, store_name |
| `CustomerUpdateForm` | EditController::indexAction() | Customer editing with address defaults | Extends CustomerForm; adds default_billing_address, default_shipping_address |
| `CustomerDeleteForm` | DeleteController::indexAction() | Deletion confirmation | id_customer (hidden) |
| `AddressForm` | AddressController::addAction(), editAction() | Address creation/editing with XSS sanitization | salutation, first_name, last_name, address1, address2, address3, company, city, zip_code, fk_country, phone, comment |

---

### Tables

**Location**: `src/Spryker/Zed/Customer/Communication/Table/`

| Table Class | Used By | Purpose | Key Columns |
|-------------|---------|---------|-------------|
| `CustomerTable` | IndexController::indexAction() | Customer list with sorting, filtering, CSV export, plugin-expandable actions | id_customer, created_at, email, first_name, last_name, registered (Verified/Unverified), Actions |
| `AddressTable` | ViewController::addressTableAction() | Customer addresses with billing/shipping badges | id_customer_address, salutation, first_name, last_name, address1-3, company, zip_code, city, Country, Actions |

---

### Console Commands

**Location**: `src/Spryker/Zed/Customer/Communication/Console/`

| Command Class | Command Name | Purpose | Options |
|---------------|--------------|---------|---------|
| `CustomerPasswordResetConsole` | customer:password:reset | Sends password reset email to all customers matching filter criteria | --force / -f, --no-token |
| `CustomerPasswordSetConsole` | customer:password:set | Sends password restore email to customers with empty/null password | --force / -f, --no-token |

---

## Persistence Layer

### Repository

**Location**: `src/Spryker/Zed/Customer/Persistence/CustomerRepository.php`

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `getCustomerCollection` | CustomerCollectionTransfer | CustomerCollectionTransfer | Retrieves filtered customers with pagination |
| `findCustomerByReference` | string | ?CustomerTransfer | Finds customer by reference (nullable) |
| `findAddressByAddressData` | AddressTransfer | ?AddressTransfer | Finds address by address data comparison |
| `findCustomerAddressById` | int | ?AddressTransfer | Finds address by ID (nullable) |
| `getAllSalutations` | - | array | Returns all available salutations |
| `getCustomerCollectionByCriteria` | CustomerCriteriaFilterTransfer | CustomerCollectionTransfer | Retrieves customers by filter criteria |
| `findAddressByCriteria` | AddressCriteriaFilterTransfer | ?AddressTransfer | Finds address by criteria (nullable) |
| `getAddressesByCriteria` | AddressCriteriaFilterTransfer | AddressesTransfer | Gets addresses by criteria |
| `findCustomerByCriteria` | CustomerCriteriaTransfer | ?CustomerTransfer | Finds customer by criteria with optional expanders |
| `isEmailAvailableForCustomer` | string, ?int | bool | Checks email uniqueness (with optional exclusion ID) |
| `hydrateCustomerListWithCustomers` | CustomerCollectionTransfer, array | void | Hydrates customer list with customer data |

---

### Entity Manager

**Location**: `src/Spryker/Zed/Customer/Persistence/CustomerEntityManager.php`

**Status**: Currently empty. All write operations are handled through QueryContainer (legacy pattern).

---

### Mapper

**Location**: `src/Spryker/Zed/Customer/Persistence/Mapper/CustomerMapper.php`

| Method Name | Transformation | Description |
|-------------|----------------|-------------|
| `mapCustomerEntityToCustomer` | Array → CustomerTransfer | Maps customer entity array to transfer |
| `mapCustomerAddressEntityToAddressTransfer` | SpyCustomerAddress → AddressTransfer | Maps address entity to transfer |
| `mapCountryEntityToCountryTransfer` | SpyCountry → CountryTransfer | Maps country entity to transfer |
| `mapCustomerAddressEntityToTransfer` | SpyCustomerAddress → AddressTransfer | DEPRECATED. Use mapCustomerAddressEntityToAddressTransfer() |

---

### Propel Schemas

**Location**: `src/Spryker/Zed/Customer/Persistence/Propel/Schema/`

#### spy_customer.schema.xml

Defines the core customer and address database tables.

**Tables Defined**: `spy_customer`, `spy_customer_address`

**spy_customer Table**:
- **Primary Key**: `id_customer` (INTEGER, auto-increment)
- **Unique Constraints**: `email`, `customer_reference`
- **Indices**: `first_name`, `last_name`, `anonymized_at`
- **Foreign Keys**: `fk_locale` (Locale), `default_billing_address` (CustomerAddress), `default_shipping_address` (CustomerAddress)
- **Key Columns**: customer_reference, email, salutation, first_name, last_name, company, gender, date_of_birth, password, restore_password_key, restore_password_date, registered, registration_key, phone, anonymized_at
- **Behaviors**: Timestampable (created_at, updated_at), Event (spy_customer_created, spy_customer_updated)

**spy_customer_address Table**:
- **Primary Key**: `id_customer_address` (INTEGER, auto-increment)
- **Foreign Keys**: `fk_customer` (Customer, CASCADE), `fk_country` (Country), `fk_region` (Region)
- **Index**: `fk_customer`
- **Key Columns**: salutation, first_name, last_name, address1, address2, address3, company, city, zip_code, phone, comment, deleted_at, anonymized_at
- **Behaviors**: Timestampable (created_at, updated_at), Event (spy_customer_address_created, spy_customer_address_updated)

#### spy_sales.schema.xml

Extends the Sales module's order table with customer reference.

**Extends Table**: `spy_sales_order`
- **Added Column**: `customer_reference` (VARCHAR(255))
- **Added Index**: `customer_reference`

---

## Presentation Layer

**Location**: `src/Spryker/Zed/Customer/Presentation/`

### Web Templates

| Template Path | Controller::Action | Purpose | Extends | Key Variables |
|---------------|-------------------|---------|---------|---------------|
| Add/index.twig | AddController::indexAction | Customer registration form | @Gui/Layout/layout.twig | form (CustomerForm) |
| Edit/index.twig | EditController::indexAction | Customer edit form | @Gui/Layout/layout.twig | form (CustomerUpdateForm) |
| Delete/index.twig | DeleteController::indexAction | Deletion confirmation | @Gui/Layout/layout.twig | form (CustomerDeleteForm) |
| Index/index.twig | IndexController::indexAction | Customer list dashboard | @Gui/Layout/layout.twig | customerTable (GuiTable) |
| View/index.twig | ViewController::indexAction | Customer detail view | @Gui/Layout/layout.twig | customer (CustomerTransfer), addressTable, externalBlocks |
| Address/add.twig | AddressController::addAction | New address form | @Gui/Layout/layout.twig | form (AddressForm) |
| Address/edit.twig | AddressController::editAction | Address edit form | @Gui/Layout/layout.twig | form (AddressForm) |
| Address/index.twig | AddressController::indexAction | Address management | @Gui/Layout/layout.twig | addressTable |
| Address/view.twig | (Not mapped to action) | Single address details | @Gui/Layout/layout.twig | address (AddressTransfer) |

### Mail Templates

| Template Path | Format | Purpose |
|---------------|--------|---------|
| Mail/customer_registration.html.twig | HTML | Registration confirmation email |
| Mail/customer_registration.text.twig | Plain Text | Registration confirmation email |
| Mail/customer_registration_token.html.twig | HTML | Registration with token email |
| Mail/customer_registration_token.text.twig | Plain Text | Registration with token email |
| Mail/customer_restore_password.html.twig | HTML | Password reset request email |
| Mail/customer_restore_password.text.twig | Plain Text | Password reset request email |
| Mail/customer_reset_password_confirmation.html.twig | HTML | Password reset confirmation email |
| Mail/customer_reset_password_confirmation.text.twig | Plain Text | Password reset confirmation email |
