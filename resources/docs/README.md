# Customer Module

## Overview

The Customer module provides comprehensive customer management functionality across all Spryker application layers. It handles customer registration, authentication, profile management, address management, password policies, anonymization (GDPR compliance), and session management.

**Key Capabilities**:
- Customer CRUD operations with validation
- Email/password authentication with configurable policies
- Single and double opt-in registration flows
- Multi-address management with default billing/shipping
- Password reset and restoration with expiration
- GDPR-compliant anonymization
- Session-based customer tracking
- Anonymous user identification
- Checkout integration
- Admin backend UI

---

## Application Layers

- [Zed Layer](./ZED.md) - Backend business logic and administration
- [Yves Layer](./YVES.md) - Storefront utility layer (session and logging)
- [Client Layer](./CLIENT.md) - Frontend API client for session and Zed communication
- [Shared Layer](./SHARED.md) - Transfer definitions and constants shared across layers
- [Service Layer](./SERVICE.md) - Utility services for address key generation
- **Glue Layer** - Not present in this module

---

## Architecture Summary

### Design Patterns Used

1. **Facade Pattern**: CustomerFacade, CustomerClient expose simplified interfaces
2. **Factory Pattern**: CustomerBusinessFactory, CustomerFactory for object creation
3. **Repository Pattern**: CustomerRepository for read operations
4. **Bridge Pattern**: Dependency bridges decouple from external services
5. **Strategy Pattern**: OrderSaverStrategyResolver for checkout variations
6. **Plugin Pattern**: Extensive plugin system for extensibility
7. **Policy Pattern**: Password policy validators
8. **Gateway Pattern**: GatewayController for inter-module communication
9. **Decorator Pattern**: CustomerExpander for data enrichment
10. **Transfer Object Pattern**: All data passed as transfer objects

### Extension Points

**Business Logic Extensions**:
- CustomerAnonymizerPluginInterface
- CustomerTransferExpanderPluginInterface
- PostCustomerRegistrationPluginInterface
- CustomerPostDeletePluginInterface
- CustomerPreUpdatePluginInterface
- CustomerPasswordPolicyInterface

**UI Extensions**:
- CustomerTableActionExpanderPluginInterface

**Checkout Extensions**:
- CheckoutPreConditionPluginInterface
- CheckoutDoSaveOrderInterface

**Session & Security Extensions**:
- CustomerSessionGetPluginInterface
- CustomerSessionSetPluginInterface
- DefaultAddressChangePluginInterface
- CustomerSecuredPatternRulePluginInterface
- AccessTokenAuthenticationHandlerPluginInterface

**Mail Extensions**:
- MailTypeBuilderPluginInterface

**Authorization Extensions**:
- AuthorizationStrategyPluginInterface

**Company Integration Extensions**:
- CompanyRolePostSavePluginInterface
- CompanyUserPostUpdatePluginInterface

**Event & Logging Extensions**:
- EventDispatcherPluginInterface
- LogProcessorPluginInterface

### Key Features

1. **Customer Management**: Complete CRUD operations for customers and addresses
2. **Authentication**: Email/password login, access token support
3. **Password Security**: Configurable password policies, reset/restore workflows, expiration
4. **Registration**: Single opt-in or double opt-in with email confirmation
5. **Session Management**: Client-side session storage with plugin hooks
6. **Anonymization**: GDPR-compliant customer anonymization with plugin system
7. **Address Management**: Multiple addresses per customer with default billing/shipping
8. **Validation**: Email format/uniqueness, salutation, password strength, checkout preconditions
9. **Mail Integration**: Registration, password restore, confirmation emails with templates
10. **Cache Invalidation**: Redis-based cache invalidation on company role/user changes
11. **Logging**: Customer context enrichment in application logs
12. **Anonymous Tracking**: Unique anonymous IDs for non-registered users
13. **Checkout Integration**: Customer saving, address validation, order hydration
14. **Admin UI**: Complete Zed backend with customer list, forms, tables, CSV export
15. **Extensibility**: 15+ plugin interfaces for customization

---

## Dependencies

### Required Modules (Composer)

**Core Dependencies**:
- spryker/kernel (^3.72.0)
- spryker/transfer (^3.27.0)
- spryker/propel (^3.0.0)
- spryker/propel-orm (^1.16.0)

**Integration Dependencies**:
- spryker/country (^3.1.0 || ^4.0.0)
- spryker/locale (^3.0.0 || ^4.0.0)
- spryker/mail (^4.6.0)
- spryker/sequence-number (^3.0.0)
- spryker/session (^3.0.0 || ^4.0.0)
- spryker/store (^1.19.0)
- spryker/router (^1.12.0)
- spryker/storage-redis (^1.0.0)
- spryker/zed-request (^3.0.0)

**Extension Points**:
- spryker/customer-extension (^1.6.0)
- spryker/checkout-extension (^1.2.0)
- spryker/mail-extension (^1.0.0)
- spryker/authorization-extension (^1.0.0)
- spryker/acl-merchant-portal-extension (^1.0.0)
- spryker/company-role-extension (^1.0.0)
- spryker/company-user-extension (^1.3.0)
- spryker/event-dispatcher-extension (^1.0.0)
- spryker/tax-product-connector-extension (^1.0.0)
- spryker-shop/session-customer-validation-page-extension (^1.0.0)

**UI & Framework**:
- spryker/gui (^3.39.0 || ^4.0.0)
- spryker/symfony (^3.0.0)

**Utility Services**:
- spryker/util-date-time (^1.0.0)
- spryker/util-encoding (^2.0.0)
- spryker/util-sanitize (^2.0.0)
- spryker/util-text (^1.1.0)
- spryker/util-validate (^1.0.0)

### Optional Integrations (Suggested)

- **spryker/checkout**: For checkout plugins
- **spryker/event-dispatcher**: For EventDispatcher plugins
- **spryker/log**: For log processor plugins
- **spryker/sales**: For customer information in sales orders

---

## Testing

**Location**: `tests/SprykerTest/`

The module includes comprehensive test coverage:
- Unit tests for business logic
- Functional tests for persistence operations
- Integration tests for facades and clients
- Helper classes for test support

---

## Version

**Current Version**: 7.0.x (from composer.json branch-alias)

---

## License

Proprietary - Spryker Systems GmbH

Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
Use of this software requires acceptance of the Evaluation License Agreement.
