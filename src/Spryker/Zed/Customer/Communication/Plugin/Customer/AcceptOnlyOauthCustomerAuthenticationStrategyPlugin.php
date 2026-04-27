<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Plugin\Customer;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ResourceOwnerTransfer;
use Spryker\Zed\CustomerExtension\Dependency\Plugin\OauthCustomerAuthenticationStrategyPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\Customer\Business\CustomerBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\Customer\CustomerConfig getConfig()
 */
class AcceptOnlyOauthCustomerAuthenticationStrategyPlugin extends AbstractPlugin implements OauthCustomerAuthenticationStrategyPluginInterface
{
    /**
     * {@inheritDoc}
     * - Always applicable — handles any resource owner with an email.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ResourceOwnerTransfer $resourceOwnerTransfer
     *
     * @return bool
     */
    public function isApplicable(ResourceOwnerTransfer $resourceOwnerTransfer): bool
    {
        return $resourceOwnerTransfer->getEmail() !== null;
    }

    /**
     * {@inheritDoc}
     * - Finds an existing customer by email.
     * - Returns null if no customer exists or the customer is anonymized.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ResourceOwnerTransfer $resourceOwnerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
    public function resolveOauthCustomer(ResourceOwnerTransfer $resourceOwnerTransfer): ?CustomerTransfer
    {
        return $this->getBusinessFactory()
            ->createAcceptOnlyAuthenticationStrategy()
            ->resolveOauthCustomer($resourceOwnerTransfer);
    }
}
