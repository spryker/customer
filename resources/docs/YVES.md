# Customer Module - Yves Layer

## Overview

The Yves layer provides session management and logging enhancement utilities for the storefront. It does NOT contain controllers, forms, or views - only utility services for session tracking and log enrichment.

---

## Configuration

**Location**: `src/Spryker/Yves/Customer/CustomerConfig.php`

**Status**: Empty configuration class extending AbstractBundleConfig. Ready for project-level extension.

---

## Dependency Provider

**Location**: `src/Spryker/Yves/Customer/CustomerDependencyProvider.php`

### Dependencies

| Constant | Service Name | Purpose |
|----------|--------------|---------|
| `SERVICE_REQUEST_STACK` | 'request_stack' | Symfony RequestStack for retrieving current request |
| `SERVICE_UTIL_TEXT` | 'SERVICE_UTIL_TEXT' | UtilText service for random string generation |

---

## Plugins Provided to Other Modules

**Location**: `src/Spryker/Yves/Customer/Plugin/`

### For EventDispatcher Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `AnonymousIdSessionAssignEventDispatcherPlugin` | EventDispatcherPluginInterface | Assigns unique anonymous ID to session on kernel request (priority 8) | EventDispatcherDependencyProvider::getEventDispatcherPlugins() |

### For Log Module

| Plugin Class | Interface | Description | Registration |
|--------------|-----------|-------------|--------------|
| `CurrentCustomerDataRequestProcessorPlugin` | LogProcessorPluginInterface | Enriches log records with customer email and reference | LogDependencyProvider::getLogProcessorPlugins() |

---

## Session Management

### AnonymousIdProvider

**Location**: `src/Spryker/Yves/Customer/Session/AnonymousIdProvider.php`

| Method | Return Type | Description |
|--------|-------------|-------------|
| `generateUniqueId()` | string | Generates unique anonymous identifier with format 'anonymous-{16-char-random}' |

**Purpose**: Provides unique session identifiers for non-authenticated users to enable tracking and analytics without requiring login.

---

## Log Processing

### CurrentCustomerDataRequestLogProcessor

**Location**: `src/Spryker/Yves/Customer/Processor/CurrentCustomerDataRequestLogProcessor.php`

| Method | Return Type | Description |
|--------|-------------|-------------|
| `__invoke(array)` | array | Adds customer email (as 'username') and customer reference to log record |

**Purpose**: Enriches application logs with customer context for debugging and monitoring. Automatically adds customer identification to all log entries when a customer is logged in.

---

## Architecture Notes

### No Frontend Components

Unlike most Yves modules, the Customer Yves layer does NOT provide:
- Controllers or routes
- Forms
- Twig templates or views
- Theme components

This is intentional - customer-facing UI is handled by the CustomerPage module in SprykerShop.

### Utility Focus

The Customer Yves layer focuses exclusively on:
1. **Session tracking**: Anonymous ID assignment for non-logged-in users
2. **Log enrichment**: Adding customer context to application logs

### Integration Points

**Session Management**:
- `AnonymousIdSessionAssignEventDispatcherPlugin` hooks into Symfony's kernel request event
- Checks if session has anonymous ID
- Generates and stores ID if missing
- Enables tracking of anonymous user journeys

**Logging**:
- `CurrentCustomerDataRequestLogProcessor` is invoked by Monolog
- Reads customer from session
- Adds customer email and reference to log context
- Helps with customer support and debugging
