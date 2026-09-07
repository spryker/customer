<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Executor;

use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Service\Container\Attributes\Stack;
use Spryker\Zed\Customer\CustomerDependencyProvider;

class CustomerPluginExecutor implements CustomerPluginExecutorInterface
{
    /**
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\PostCustomerRegistrationPluginInterface> $postCustomerRegistrationPlugins
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPostDeletePluginInterface> $customerPostDeletePlugins
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerValidatorPluginInterface> $customerValidatorPlugins
     * @param list<\Spryker\Zed\CustomerExtension\Dependency\Plugin\AddressValidatorPluginInterface> $addressValidatorPlugins
     *
     * @see \Spryker\Zed\Customer\CustomerDependencyProvider::getPostCustomerRegistrationPlugins()
     * @see \Spryker\Zed\Customer\CustomerDependencyProvider::getCustomerPostDeletePlugins()
     * @see \Spryker\Zed\Customer\CustomerDependencyProvider::getCustomerValidatorPlugins()
     * @see \Spryker\Zed\Customer\CustomerDependencyProvider::getAddressValidatorPlugins()
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
    #[Stack(
        dependencyProvider: CustomerDependencyProvider::class,
        dependencyProviderMethod: 'getCustomerValidatorPlugins',
        provideToArgument: '$customerValidatorPlugins',
    )]
    #[Stack(
        dependencyProvider: CustomerDependencyProvider::class,
        dependencyProviderMethod: 'getAddressValidatorPlugins',
        provideToArgument: '$addressValidatorPlugins',
    )]
    public function __construct(
        protected array $postCustomerRegistrationPlugins = [],
        protected array $customerPostDeletePlugins = [],
        protected array $customerValidatorPlugins = [],
        protected array $addressValidatorPlugins = []
    ) {
    }

    public function executePostCustomerRegistrationPlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->postCustomerRegistrationPlugins as $postCustomerRegistrationPlugin) {
            $postCustomerRegistrationPlugin->execute($customerTransfer);
        }
    }

    public function executeCustomerPostDeletePlugins(CustomerTransfer $customerTransfer): void
    {
        foreach ($this->customerPostDeletePlugins as $customerPostDeletePlugin) {
            $customerPostDeletePlugin->execute($customerTransfer);
        }
    }

    public function executeCustomerValidatorPlugins(
        CustomerTransfer $customerTransfer,
        CustomerResponseTransfer $customerResponseTransfer
    ): CustomerResponseTransfer {
        foreach ($this->customerValidatorPlugins as $customerValidatorPlugin) {
            $pluginResponseTransfer = $customerValidatorPlugin->validate($customerTransfer);

            if ($pluginResponseTransfer->getIsSuccess()) {
                continue;
            }

            $customerResponseTransfer->setIsSuccess(false);

            foreach ($pluginResponseTransfer->getErrors() as $customerErrorTransfer) {
                $customerResponseTransfer->addError($customerErrorTransfer);
            }
        }

        return $customerResponseTransfer;
    }

    public function executeAddressValidatorPlugins(
        AddressTransfer $addressTransfer,
        AddressResponseTransfer $addressResponseTransfer
    ): AddressResponseTransfer {
        foreach ($this->addressValidatorPlugins as $addressValidatorPlugin) {
            $pluginResponseTransfer = $addressValidatorPlugin->validate($addressTransfer);

            if ($pluginResponseTransfer->getIsSuccess()) {
                continue;
            }

            $addressResponseTransfer->setIsSuccess(false);

            foreach ($pluginResponseTransfer->getErrors() as $customerErrorTransfer) {
                $addressResponseTransfer->addError($customerErrorTransfer);
            }
        }

        return $addressResponseTransfer;
    }
}
