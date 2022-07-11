<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Customer;

use Spryker\Client\Customer\Exception\MissingAccessTokenAuthenticationHandlerPluginException;
use Spryker\Client\CustomerExtension\Dependency\Plugin\AccessTokenAuthenticationHandlerPluginInterface;
use Spryker\Client\Kernel\AbstractDependencyProvider;
use Spryker\Client\Kernel\Container;

/**
 * @method \Spryker\Client\Customer\CustomerConfig getConfig()
 */
class CustomerDependencyProvider extends AbstractDependencyProvider
{
    /**
     * @var string
     */
    public const SERVICE_SESSION = 'session service';

    /**
     * @var string
     */
    public const SERVICE_ZED = 'zed service';

    /**
     * @var string
     */
    public const PLUGINS_CUSTOMER_SESSION_GET = 'PLUGINS_CUSTOMER_SESSION_GET';

    /**
     * @var string
     */
    public const PLUGINS_CUSTOMER_SESSION_SET = 'PLUGINS_CUSTOMER_SESSION_SET';

    /**
     * @var string
     */
    public const PLUGINS_DEFAULT_ADDRESS_CHANGE = 'PLUGINS_DEFAULT_ADDRESS_CHANGE';

    /**
     * @var string
     */
    public const PLUGINS_CUSTOMER_SECURED_PATTERN_RULE = 'PLUGINS_CUSTOMER_SECURED_PATTERN_RULE';

    /**
     * @var string
     */
    public const PLUGIN_ACCESS_TOKEN_AUTHENTICATION_HANDLER = 'PLUGIN_ACCESS_TOKEN_AUTHENTICATION_HANDLER';

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function provideServiceLayerDependencies(Container $container)
    {
        $container = $this->addDefaultAddressChangePlugins($container);
        $container = $this->addSessionClient($container);
        $container = $this->addZedRequestClient($container);
        $container = $this->addCustomerSessionGetPlugins($container);
        $container = $this->addCustomerSessionSetPlugins($container);
        $container = $this->addCustomerSecuredPatternRulePlugins($container);
        $container = $this->addAccessTokenAuthenticationHandlerPlugin($container);

        return $container;
    }

    /**
     * @return array<\Spryker\Client\Customer\Dependency\Plugin\CustomerSessionGetPluginInterface>
     */
    protected function getCustomerSessionGetPlugins()
    {
        return [];
    }

    /**
     * @return array<\Spryker\Client\Customer\Dependency\Plugin\CustomerSessionSetPluginInterface>
     */
    protected function getCustomerSessionSetPlugins()
    {
        return [];
    }

    /**
     * @return array<\Spryker\Client\CustomerExtension\Dependency\Plugin\DefaultAddressChangePluginInterface>
     */
    protected function getDefaultAddressChangePlugins()
    {
        return [];
    }

    /**
     * @return array<\Spryker\Client\CustomerExtension\Dependency\Plugin\CustomerSecuredPatternRulePluginInterface>
     */
    protected function getCustomerSecuredPatternRulePlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addDefaultAddressChangePlugins(Container $container)
    {
        $container->set(static::PLUGINS_DEFAULT_ADDRESS_CHANGE, function () {
            return $this->getDefaultAddressChangePlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addSessionClient(Container $container)
    {
        $container->set(static::SERVICE_SESSION, function (Container $container) {
            return $container->getLocator()->session()->client();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addZedRequestClient(Container $container)
    {
        $container->set(static::SERVICE_ZED, function (Container $container) {
            return $container->getLocator()->zedRequest()->client();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addCustomerSessionGetPlugins(Container $container)
    {
        $container->set(static::PLUGINS_CUSTOMER_SESSION_GET, function () {
            return $this->getCustomerSessionGetPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addCustomerSessionSetPlugins(Container $container)
    {
        $container->set(static::PLUGINS_CUSTOMER_SESSION_SET, function () {
            return $this->getCustomerSessionSetPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    public function addCustomerSecuredPatternRulePlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_CUSTOMER_SECURED_PATTERN_RULE, function () {
            return $this->getCustomerSecuredPatternRulePlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Client\Kernel\Container $container
     *
     * @return \Spryker\Client\Kernel\Container
     */
    protected function addAccessTokenAuthenticationHandlerPlugin(Container $container): Container
    {
        $container->set(static::PLUGIN_ACCESS_TOKEN_AUTHENTICATION_HANDLER, function () {
            return $this->getAccessTokenAuthenticationHandlerPlugin();
        });

        return $container;
    }

    /**
     * @throws \Spryker\Client\Customer\Exception\MissingAccessTokenAuthenticationHandlerPluginException
     *
     * @return \Spryker\Client\CustomerExtension\Dependency\Plugin\AccessTokenAuthenticationHandlerPluginInterface
     */
    protected function getAccessTokenAuthenticationHandlerPlugin(): AccessTokenAuthenticationHandlerPluginInterface
    {
        throw new MissingAccessTokenAuthenticationHandlerPluginException(
            sprintf(
                "Missing instance of %s! You need to configure an access token authentication handler plugin
                      in your own CustomerDependencyProvider::getAccessTokenAuthenticationHandlerPlugin() to allow retrieve customer by access token.",
                AccessTokenAuthenticationHandlerPluginInterface::class,
            ),
        );
    }
}
