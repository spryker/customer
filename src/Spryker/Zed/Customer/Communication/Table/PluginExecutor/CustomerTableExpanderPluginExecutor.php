<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Table\PluginExecutor;

use Spryker\Service\Container\Attributes\Stack;
use Spryker\Zed\Customer\CustomerDependencyProvider;

/**
 * @method \Spryker\Zed\Customer\Communication\CustomerCommunicationFactory getFactory()
 */
class CustomerTableExpanderPluginExecutor implements CustomerTableExpanderPluginExecutorInterface
{
    /**
     * @param array<\Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerTableActionExpanderPluginInterface> $customerTableActionExpanderPlugins
     *
     * @see \Spryker\Zed\Customer\CustomerDependencyProvider::getCustomerTableActionExpanderPlugins()
     */
    #[Stack(
        dependencyProvider: CustomerDependencyProvider::class,
        dependencyProviderMethod: 'getCustomerTableActionExpanderPlugins',
        provideToArgument: '$customerTableActionExpanderPlugins',
    )]
    public function __construct(protected array $customerTableActionExpanderPlugins)
    {
    }

    /**
     * @return array<\Generated\Shared\Transfer\ButtonTransfer>
     */
    public function executeActionExpanderPlugins(int $idCustomer): array
    {
        $buttons = [];

        foreach ($this->customerTableActionExpanderPlugins as $customerTableActionExpanderPlugin) {
            $buttons = $customerTableActionExpanderPlugin->execute($idCustomer, $buttons);
        }

        return $buttons;
    }
}
