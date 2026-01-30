# Customer Module - Client Layer

## Overview

The Client layer provides the frontend API for customer operations, session management, and Zed communication. It acts as the bridge between Yves/Glue and the backend Zed layer.

---

## Client

**Location**: `src/Spryker/Client/Customer/CustomerClient.php`

The CustomerClient provides 34 public methods for customer operations.

### Customer Management

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `registerCustomer` | CustomerTransfer | CustomerResponseTransfer | Validates email, encrypts password, generates reference, sends confirmation |
| `findCustomerWithEmailAndPassword` | CustomerTransfer | ?CustomerTransfer | Checks if customer exists by email and password |
| `confirmRegistration` | CustomerTransfer | CustomerTransfer | DEPRECATED. Confirms registration by key |
| `confirmCustomerRegistration` | CustomerTransfer | CustomerResponseTransfer | Confirms registration with error handling |
| `getCustomer` | - | ?CustomerTransfer | Returns customer from session |
| `getCustomerById` | int | CustomerTransfer | Retrieves customer by ID with addresses |
| `findCustomerById` | CustomerTransfer | ?CustomerTransfer | Retrieves customer by ID, returns null if not found |
| `getCustomerByEmail` | CustomerTransfer | CustomerTransfer | Retrieves customer by ID, email, or password restoration key |
| `updateCustomer` | CustomerTransfer | CustomerResponseTransfer | Updates customer data, handles password update if set |
| `updateCustomerPassword` | CustomerTransfer | CustomerResponseTransfer | Updates customer password only |
| `deleteCustomer` | CustomerTransfer | Response | Deletes customer entity |
| `findCustomerByReference` | CustomerTransfer | CustomerResponseTransfer | Retrieves customer by reference |
| `getCustomerByAccessToken` | string | CustomerResponseTransfer | Retrieves customer by access token |
| `anonymizeCustomer` | CustomerTransfer | CustomerTransfer | Anonymizes customer data using plugins |

### Session Management

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `setCustomer` | CustomerTransfer | CustomerTransfer | Stores customer in session with plugin execution |
| `setCustomerRawData` | CustomerTransfer | CustomerTransfer | Stores customer in session without plugin execution |
| `findCustomerRawData` | - | ?CustomerTransfer | Retrieves customer from session without plugins |
| `login` | CustomerTransfer | ?CustomerTransfer | Authenticates customer and stores in session |
| `logout` | - | void | Removes customer from session |
| `isLoggedIn` | - | bool | Checks if customer is in session |
| `markCustomerAsDirty` | - | void | Marks customer for reloading from Zed |
| `getUserIdentifier` | - | string | Returns logged-in customer identifier or anonymous |

### Password Management

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `sendPasswordRestoreMail` | CustomerTransfer | CustomerResponseTransfer | Sends password restoration link via email |
| `restorePassword` | CustomerTransfer | CustomerResponseTransfer | Restores password with new encrypted value |

### Address Management

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `getAddresses` | CustomerTransfer | AddressesTransfer | Retrieves all customer addresses |
| `getAddress` | AddressTransfer | AddressTransfer | Retrieves single address with flags |
| `updateAddress` | AddressTransfer | AddressTransfer | Updates customer address |
| `updateAddressAndCustomerDefaultAddresses` | AddressTransfer | CustomerTransfer | Updates address and default address flags |
| `createAddressAndUpdateCustomerDefaultAddresses` | AddressTransfer | CustomerTransfer | Creates address and sets defaults |
| `createAddress` | AddressTransfer | AddressTransfer | Creates new customer address |
| `deleteAddress` | AddressTransfer | AddressTransfer | Deletes address and removes customer-address references |
| `setDefaultShippingAddress` | AddressTransfer | AddressTransfer | Sets address as default shipping |
| `setDefaultBillingAddress` | AddressTransfer | AddressTransfer | Sets address as default billing |
| `updateCustomerAddresses` | CustomerTransfer | void | Updates customer addresses in session |

### Security

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `getCustomerSecuredPattern` | - | string | Returns secured pattern with applied access rules |

---

## Configuration

**Location**: `src/Spryker/Client/Customer/CustomerConfig.php`

| Method | Return Type | Description |
|--------|-------------|-------------|
| `getCustomerSecuredPattern()` | string | Returns secured pattern with applied access rules |

---

## Dependency Provider

**Location**: `src/Spryker/Client/Customer/CustomerDependencyProvider.php`

### Service Dependencies

| Constant | Value | Purpose |
|----------|-------|---------|
| `SERVICE_SESSION` | 'session service' | Session client for customer data storage |
| `SERVICE_ZED` | 'zed service' | Zed request client for gateway communication |
| `CLIENT_STORAGE_REDIS` | 'CLIENT_STORAGE_REDIS' | Storage Redis client for cache operations |

### Plugin Dependencies

| Constant | Plugin Interface | Purpose |
|----------|------------------|---------|
| `PLUGINS_CUSTOMER_SESSION_GET` | CustomerSessionGetPluginInterface[] | Executed when retrieving customer from session |
| `PLUGINS_CUSTOMER_SESSION_SET` | CustomerSessionSetPluginInterface[] | Executed when setting customer in session |
| `PLUGINS_DEFAULT_ADDRESS_CHANGE` | DefaultAddressChangePluginInterface[] | Executed when default address changes |
| `PLUGINS_CUSTOMER_SECURED_PATTERN_RULE` | CustomerSecuredPatternRulePluginInterface[] | Modifies secured pattern based on rules |
| `PLUGIN_ACCESS_TOKEN_AUTHENTICATION_HANDLER` | AccessTokenAuthenticationHandlerPluginInterface | Authenticates customer via access token |

---

## Plugins Provided to Other Modules

**Location**: `src/Spryker/Client/Customer/Plugin/`

| Plugin Class | Target Module | Interface | Purpose |
|--------------|---------------|-----------|---------|
| `CustomerTransferSessionRefreshPlugin` | SessionCustomerValidationPage | CustomerSessionRefreshPluginInterface | Refreshes customer transfer on session validation |
| `CustomerTransferRefreshPlugin` | SessionCustomerValidationPage | CustomerSessionRefreshPluginInterface | Refreshes customer transfer data |
| `CustomerAddressSessionUpdatePlugin` | Customer (internal) | DefaultAddressChangePluginInterface | Updates session on address changes |
| `CustomerAddressDefaultAddressChangePlugin` | Customer (internal) | DefaultAddressChangePluginInterface | Handles default address changes |
| `CustomerReferenceMatchingEntityIdAuthorizationStrategyPlugin` | Authorization | AuthorizationStrategyPluginInterface | Authorization strategy for customer reference |
| `StorageInvalidationRecordCustomerSessionValidatorPlugin` | SessionCustomerValidationPage | CustomerSessionValidatorPluginInterface | Validates customer session via storage invalidation |

---

## Architecture Notes

### Session Storage

The Client layer manages customer data in session storage:

- **Full customer data**: Stored via `setCustomer()` with plugin execution
- **Raw data**: Stored via `setCustomerRawData()` without plugins
- **Dirty flag**: Marks customer for reload from Zed on next request

### Zed Communication

All data operations communicate with Zed via Gateway:

- Uses `ZedStub` for RPC calls
- Calls `GatewayController` actions in Zed
- Passes Transfer Objects over the wire
- Returns Transfer Objects with success/error status

### Plugin System

Extensibility through multiple plugin stacks:

- **Session plugins**: Modify customer data on get/set
- **Address change plugins**: React to default address changes
- **Security plugins**: Customize access patterns
- **Authentication plugins**: Support alternative auth methods (access tokens)

### Anonymous Users

Non-logged-in users are tracked with:

- Anonymous ID generated by Yves layer
- `getUserIdentifier()` returns 'anonymous' for non-logged-in users
- Session preserved across requests
