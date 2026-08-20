<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerTest\Zed\Customer\Helper;

use Codeception\Module;
use Codeception\Stub;
use Codeception\TestInterface;
use Spryker\Zed\Customer\Business\CustomerBusinessFactory;
use Spryker\Zed\Customer\CustomerDependencyProvider;
use Spryker\Zed\Customer\Dependency\Client\CustomerToStorageRedisClientInterface;
use SprykerTest\Shared\Testify\Helper\DependencyHelperTrait;

/**
 * Stands in for Redis on the customer-invalidation write path.
 *
 * Creating a company creates its default company role, and
 * {@see \Spryker\Zed\Customer\Communication\Plugin\CompanyRole\CustomerInvalidationCompanyRolePostSavePlugin}
 * then marks every affected customer stale through
 * {@see \Spryker\Zed\Customer\Business\Invalidation\StorageCustomerInvalidator}. That invalidator
 * talks to the StorageRedis client directly rather than through the Storage client, so the host
 * lane's `StorageDatabasePlugin` never sees it and the arrange dies on `Class "Redis" not found`.
 *
 * Nothing in these suites reads the invalidation marker back — it exists for a Yves customer cache
 * that the API lane does not run — so discarding the write costs no coverage.
 *
 * The binding is scoped to the Customer business factory: an unscoped `CLIENT_STORAGE_REDIS` would
 * be handed to every module declaring a key of that name.
 */
class CustomerInvalidationStorageStubHelper extends Module
{
    use DependencyHelperTrait;

    public function _before(TestInterface $test): void // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    {
        $this->getDependencyHelper()->setDependency(
            CustomerDependencyProvider::CLIENT_STORAGE_REDIS,
            Stub::makeEmpty(CustomerToStorageRedisClientInterface::class),
            CustomerBusinessFactory::class,
        );
    }
}
