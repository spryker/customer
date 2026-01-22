<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\CustomerExpander;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Service\Container\Attributes\Stack;
use Spryker\Zed\Customer\CustomerDependencyProvider;

class CustomerExpander implements CustomerExpanderInterface
{
    /**
     * @param array<\Spryker\Zed\Customer\Dependency\Plugin\CustomerTransferExpanderPluginInterface> $customerTransferExpanderPlugins
     *
     * @see \Spryker\Zed\Customer\CustomerDependencyProvider::getCustomerTransferExpanderPlugins()
     */
    #[Stack(
        dependencyProvider: CustomerDependencyProvider::class,
        dependencyProviderMethod: 'getCustomerTransferExpanderPlugins',
        provideToArgument: '$customerTransferExpanderPlugins',
    )]
    public function __construct(protected array $customerTransferExpanderPlugins)
    {
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function expand(CustomerTransfer $customerTransfer)
    {
        foreach ($this->customerTransferExpanderPlugins as $customerTransferExpanderPlugin) {
            $customerTransfer = $customerTransferExpanderPlugin->expandTransfer($customerTransfer);
        }

        return $customerTransfer;
    }
}
