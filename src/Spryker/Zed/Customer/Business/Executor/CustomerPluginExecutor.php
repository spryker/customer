<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Executor;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Service\Container\Attributes\Stack;
use Spryker\Zed\Customer\CustomerDependencyProvider;

class CustomerPluginExecutor implements CustomerPluginExecutorInterface
{
    /**
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\PostCustomerRegistrationPluginInterface> $postCustomerRegistrationPlugins
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPostDeletePluginInterface> $customerPostDeletePlugins
     *
     * @see \Spryker\Zed\Customer\CustomerDependencyProvider::getPostCustomerRegistrationPlugins()
     * @see \Spryker\Zed\Customer\CustomerDependencyProvider::getCustomerPostDeletePlugins()
     */
    #[Stack(
        dependencyProvider: CustomerDependencyProvider::class,
        dependencyProviderMethod: 'getPostCustomerRegistrationPlugins',
        provideToArgument: '$postCustomerRegistrationPlugins',
    )]
    #[Stack(
        dependencyProvider: CustomerDependencyProvider::class,
        dependencyProviderMethod: 'getCustomerPostDeletePlugins',
        provideToArgument: '$customerPostDeletePlugins',
    )]
    public function __construct(
        protected array $postCustomerRegistrationPlugins = [],
        protected array $customerPostDeletePlugins = []
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function executePostCustomerRegistrationPlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->postCustomerRegistrationPlugins as $postCustomerRegistrationPlugin) {
            $postCustomerRegistrationPlugin->execute($customerTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return void
     */
    public function executeCustomerPostDeletePlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->customerPostDeletePlugins as $customerPostDeletePlugin) {
            $customerPostDeletePlugin->execute($customerTransfer);
        }
    }
}
