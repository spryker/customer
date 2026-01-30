# Customer Module - Service Layer

## Overview

The Service layer provides stateless utility services for address key generation, used by all application layers.

---

## Service

**Location**: `src/Spryker/Service/Customer/CustomerService.php`

**Stateless Utility Service** - All methods are pure functions with no side effects.

### Methods

| Method Name | Parameters | Return Type | Description |
|-------------|------------|-------------|-------------|
| `getUniqueAddressKey` | AddressTransfer | string | Generates unique MD5 hash key for address based on whitelisted properties |

**Method Details**:

**`getUniqueAddressKey(AddressTransfer $addressTransfer): string`**

Generates a deterministic unique key for an address by:
1. Extracting whitelisted address properties (names, address lines, location, contact info)
2. Creating a normalized array of these properties
3. JSON encoding the array
4. Generating MD5 hash of the JSON

**Use Cases**:
- Deduplicating addresses in cart/checkout
- Comparing addresses for equality
- Caching address-specific data
- Detecting duplicate address entries

**Properties Included in Key**:
- `salutation`, `firstName`, `lastName`
- `address1`, `address2`, `address3`
- `company`, `city`, `zipCode`
- `phone`, `cellPhone`
- `iso2Code`, `fkCountry`, `fkRegion`
- `isDeleted`, `anonymizedAt`
- `comment`

**Note**: Email and customer-specific IDs are NOT included to allow address reuse across customers.

---

## Configuration

**Location**: `src/Spryker/Service/Customer/CustomerConfig.php`

| Method Name | Return Type | Description |
|-------------|-------------|-------------|
| `getAddressKeyGenerationExcludedFields()` | array\<string\> | DEPRECATED. Returns fields to exclude from key generation |
| `getAddressKeyGenerationWhiteListedFields()` | array\<string\> | Returns fields to include in key generation (16 fields: names, address lines, company, location, contact, deletion status) |

**Whitelisted Fields** (Default):
```php
[
    'salutation',
    'firstName',
    'lastName',
    'address1',
    'address2',
    'address3',
    'company',
    'city',
    'zipCode',
    'phone',
    'cellPhone',
    'iso2Code',
    'fkCountry',
    'fkRegion',
    'isDeleted',
    'anonymizedAt',
    'comment',
]
```

---

## Dependency Provider

**Location**: `src/Spryker/Service/Customer/CustomerDependencyProvider.php`

### Dependencies

| Constant | Service | Purpose |
|----------|---------|---------|
| `SERVICE_UTIL_ENCODING` | UtilEncodingService | JSON encoding for address key generation |

---

## Service Models

**Location**: `src/Spryker/Service/Customer/Model/`

| Class Name | Responsibility | Used By |
|------------|----------------|---------|
| `AddressKeyGenerator` | Generates unique MD5 keys from address data | CustomerService::getUniqueAddressKey() |

---

## Architecture Notes

### Stateless Design

The service is completely stateless:
- No instance variables
- No database access
- No external API calls
- Pure input → output transformation

### Key Generation Algorithm

```
AddressTransfer → Filter Whitelisted Fields → JSON Encode → MD5 Hash → Unique Key
```

**Example**:
```php
Input:  AddressTransfer with firstName='John', lastName='Doe', address1='123 Main St'
Output: 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6' (MD5 hash)
```

### Thread Safety

The service is thread-safe and can be called concurrently:
- No shared state
- Deterministic output
- No race conditions

### Performance

MD5 hashing is fast and suitable for:
- Real-time checkout operations
- High-volume address processing
- Cart operations

### Project Customization

Projects can customize which fields are included in the key by:
1. Extending `CustomerConfig`
2. Overriding `getAddressKeyGenerationWhiteListedFields()`
3. Adding/removing fields as needed

**Example Use Case**: If your project uses custom address fields (e.g., `buildingNumber`), add them to the whitelist to include in key generation.
