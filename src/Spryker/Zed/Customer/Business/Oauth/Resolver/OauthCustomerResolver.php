<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Oauth\Resolver;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\OauthCustomerResolveRequestTransfer;
use Generated\Shared\Transfer\OauthCustomerResolveResponseTransfer;
use Generated\Shared\Transfer\OauthCustomerRestrictionRequestTransfer;
use Generated\Shared\Transfer\OauthCustomerRestrictionResponseTransfer;
use Generated\Shared\Transfer\ResourceOwnerTransfer;

class OauthCustomerResolver implements OauthCustomerResolverInterface
{
    /**
     * @param array<\Spryker\Zed\CustomerExtension\Dependency\Plugin\OauthCustomerAuthenticationStrategyPluginInterface> $oauthCustomerAuthenticationStrategyPlugins
     * @param array<\Spryker\Zed\CustomerExtension\Dependency\Plugin\OauthCustomerPostResolvePluginInterface> $oauthCustomerPostResolvePlugins
     * @param array<\Spryker\Zed\CustomerExtension\Dependency\Plugin\OauthCustomerRestrictionPluginInterface> $oauthCustomerRestrictionPlugins
     */
    public function __construct(
        protected array $oauthCustomerAuthenticationStrategyPlugins,
        protected array $oauthCustomerPostResolvePlugins,
        protected array $oauthCustomerRestrictionPlugins,
    ) {
    }

    public function resolveCustomer(OauthCustomerResolveRequestTransfer $oauthCustomerResolveRequestTransfer): OauthCustomerResolveResponseTransfer
    {
        $resourceOwnerTransfer = $oauthCustomerResolveRequestTransfer->getResourceOwnerOrFail();
        $customerTransfer = $this->resolveViaStrategyPlugins($resourceOwnerTransfer);

        if ($customerTransfer === null) {
            return (new OauthCustomerResolveResponseTransfer())->setIsSuccessful(false);
        }

        $this->executePostResolvePlugins($customerTransfer, $resourceOwnerTransfer);

        $restrictionResponseTransfer = $this->executeRestrictionPlugins(
            (new OauthCustomerRestrictionRequestTransfer())
                ->setCustomer($customerTransfer)
                ->setResourceOwner($resourceOwnerTransfer),
        );

        if ($restrictionResponseTransfer->getIsRestricted() === true) {
            return (new OauthCustomerResolveResponseTransfer())
                ->setIsSuccessful(false)
                ->setMessages($restrictionResponseTransfer->getMessages());
        }

        return (new OauthCustomerResolveResponseTransfer())
            ->setIsSuccessful(true)
            ->setCustomer($customerTransfer);
    }

    protected function resolveViaStrategyPlugins(ResourceOwnerTransfer $resourceOwnerTransfer): ?CustomerTransfer
    {
        foreach ($this->oauthCustomerAuthenticationStrategyPlugins as $oauthCustomerAuthenticationStrategyPlugin) {
            if (!$oauthCustomerAuthenticationStrategyPlugin->isApplicable($resourceOwnerTransfer)) {
                continue;
            }

            $customerTransfer = $oauthCustomerAuthenticationStrategyPlugin->resolveOauthCustomer($resourceOwnerTransfer);

            if ($customerTransfer !== null) {
                return $customerTransfer;
            }
        }

        return null;
    }

    protected function executePostResolvePlugins(
        CustomerTransfer $customerTransfer,
        ResourceOwnerTransfer $resourceOwnerTransfer,
    ): void {
        foreach ($this->oauthCustomerPostResolvePlugins as $oauthCustomerPostResolvePlugin) {
            $oauthCustomerPostResolvePlugin->postResolve($customerTransfer, $resourceOwnerTransfer);
        }
    }

    protected function executeRestrictionPlugins(
        OauthCustomerRestrictionRequestTransfer $oauthCustomerRestrictionRequestTransfer,
    ): OauthCustomerRestrictionResponseTransfer {
        $responseTransfer = (new OauthCustomerRestrictionResponseTransfer())->setIsRestricted(false);

        foreach ($this->oauthCustomerRestrictionPlugins as $oauthCustomerRestrictionPlugin) {
            $pluginResponseTransfer = $oauthCustomerRestrictionPlugin->isRestricted($oauthCustomerRestrictionRequestTransfer);

            if ($pluginResponseTransfer->getIsRestricted() === true) {
                return $pluginResponseTransfer;
            }
        }

        return $responseTransfer;
    }
}
