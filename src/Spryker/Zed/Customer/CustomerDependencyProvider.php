<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Customer;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Customer\Dependency\Facade\CustomerToCountryBridge;
use Spryker\Zed\Customer\Dependency\Facade\CustomerToLocaleBridge;

class CustomerDependencyProvider extends AbstractBundleDependencyProvider
{

    const REGISTRATION_TOKEN_SENDERS = 'Registration Token Senders';
    const PASSWORD_RESTORE_TOKEN_SENDERS = 'Password Restore TokenSenders';
    const PASSWORD_RESTORED_CONFIRMATION_SENDERS = 'Password RestoredConfirmation Senders';
    const SENDER_PLUGINS = 'sender plugins';
    const FACADE_SEQUENCE_NUMBER = 'FACADE_SEQUENCE_NUMBER';
    const FACADE_COUNTRY = 'FACADE_COUNTRY';
    const FACADE_LOCALE = 'locale facade';

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[self::SENDER_PLUGINS] = function (Container $container) {
            return $this->getSenderPlugins($container);
        };

        $container[self::FACADE_SEQUENCE_NUMBER] = function (Container $container) {
            return $container->getLocator()->sequenceNumber()->facade();
        };

        $container[self::FACADE_COUNTRY] = function (Container $container) {
            return new CustomerToCountryBridge($container->getLocator()->country()->facade());
        };
        $container[self::FACADE_LOCALE] = function (Container $container) {
            return new CustomerToLocaleBridge($container->getLocator()->locale()->facade());
        };

        return $container;
    }

    /**
     * @param Container $container
     *
     * @return Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[self::FACADE_COUNTRY] = function (Container $container) {
            return new CustomerToCountryBridge($container->getLocator()->country()->facade());
        };

        return $container;
    }

    /**
     * Overwrite in project
     *
     * @param Container $container
     *
     * @return mixed[]
     */
    protected function getSenderPlugins(Container $container)
    {
        return [
            self::REGISTRATION_TOKEN_SENDERS => [],
            self::PASSWORD_RESTORE_TOKEN_SENDERS => [],
            self::PASSWORD_RESTORED_CONFIRMATION_SENDERS => [],
        ];
    }

}