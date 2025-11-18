<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Customer;

use Spryker\Client\Customer\CustomerAddress\CustomerAddress;
use Spryker\Client\Customer\CustomerSecuredPattern\CustomerSecuredPattern;
use Spryker\Client\Customer\CustomerSecuredPattern\CustomerSecuredPatternInterface;
use Spryker\Client\Customer\Dependency\Client\CustomerToStorageRedisClientInterface;
use Spryker\Client\Customer\Invalidation\InvalidationRecordCheckerInterface;
use Spryker\Client\Customer\Invalidation\StorageInvalidationRecordChecker;
use Spryker\Client\Customer\Reader\CustomerAccessTokenReader;
use Spryker\Client\Customer\Reader\CustomerAccessTokenReaderInterface;
use Spryker\Client\Customer\Session\CustomerSession;
use Spryker\Client\Customer\Updater\CustomerAddressUpdater;
use Spryker\Client\Customer\Updater\CustomerAddressUpdaterInterface;
use Spryker\Client\Customer\Zed\CustomerStub;
use Spryker\Client\CustomerExtension\Dependency\Plugin\AccessTokenAuthenticationHandlerPluginInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Shared\Customer\KeyGenerator\CustomerInvalidationKeyGenerator;
use Spryker\Shared\Customer\KeyGenerator\KeyGeneratorInterface;

/**
 * @method \Spryker\Client\Customer\CustomerConfig getConfig()
 */
class CustomerFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\Customer\Zed\CustomerStubInterface
     */
    public function createZedCustomerStub()
    {
        return new CustomerStub($this->getProvidedDependency(CustomerDependencyProvider::SERVICE_ZED));
    }

    /**
     * @return \Spryker\Client\Customer\CustomerAddress\CustomerAddressInterface
     */
    public function createCustomerAddress()
    {
        return new CustomerAddress(
            $this->createZedCustomerStub(),
            $this->getDefaultAddressChangePlugins(),
        );
    }

    /**
     * @return \Spryker\Client\Customer\Session\CustomerSessionInterface
     */
    public function createSessionCustomerSession()
    {
        return new CustomerSession(
            $this->getSessionClient(),
            $this->getCustomerSessionGetPlugins(),
            $this->getCustomerSessionSetPlugin(),
        );
    }

    /**
     * @return \Spryker\Client\Customer\Reader\CustomerAccessTokenReaderInterface
     */
    public function createCustomerAccessTokenReader(): CustomerAccessTokenReaderInterface
    {
        return new CustomerAccessTokenReader(
            $this->getAccessTokenAuthenticationHandlerPlugin(),
        );
    }

    /**
     * @return \Spryker\Client\Customer\Updater\CustomerAddressUpdaterInterface
     */
    public function createCustomerAddressUpdater(): CustomerAddressUpdaterInterface
    {
        return new CustomerAddressUpdater(
            $this->createSessionCustomerSession(),
        );
    }

    /**
     * @return array<\Spryker\Client\Customer\Dependency\Plugin\CustomerSessionGetPluginInterface>
     */
    public function getCustomerSessionGetPlugins()
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::PLUGINS_CUSTOMER_SESSION_GET);
    }

    /**
     * @return array<\Spryker\Client\Customer\Dependency\Plugin\CustomerSessionSetPluginInterface>
     */
    public function getCustomerSessionSetPlugin()
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::PLUGINS_CUSTOMER_SESSION_SET);
    }

    /**
     * @return array<\Spryker\Client\CustomerExtension\Dependency\Plugin\DefaultAddressChangePluginInterface>
     */
    public function getDefaultAddressChangePlugins()
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::PLUGINS_DEFAULT_ADDRESS_CHANGE);
    }

    /**
     * @return \Spryker\Client\Customer\CustomerSecuredPattern\CustomerSecuredPatternInterface
     */
    public function createCustomerSecuredPattern(): CustomerSecuredPatternInterface
    {
        return new CustomerSecuredPattern($this->getConfig(), $this->getCustomerSecuredPatternRulePlugins());
    }

    /**
     * @return array<\Spryker\Client\CustomerExtension\Dependency\Plugin\CustomerSecuredPatternRulePluginInterface>
     */
    public function getCustomerSecuredPatternRulePlugins(): array
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::PLUGINS_CUSTOMER_SECURED_PATTERN_RULE);
    }

    /**
     * @return \Spryker\Client\Session\SessionClientInterface
     */
    public function getSessionClient()
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::SERVICE_SESSION);
    }

    /**
     * @return \Spryker\Client\CustomerExtension\Dependency\Plugin\AccessTokenAuthenticationHandlerPluginInterface
     */
    public function getAccessTokenAuthenticationHandlerPlugin(): AccessTokenAuthenticationHandlerPluginInterface
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::PLUGIN_ACCESS_TOKEN_AUTHENTICATION_HANDLER);
    }

    /**
     * @return \Spryker\Client\Customer\Dependency\Client\CustomerToStorageRedisClientInterface
     */
    public function getStorageRedisClient(): CustomerToStorageRedisClientInterface
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::CLIENT_STORAGE_REDIS);
    }

    /**
     * @return \Spryker\Client\Customer\Invalidation\InvalidationRecordCheckerInterface
     */
    public function createInvalidationRecordChecker(): InvalidationRecordCheckerInterface
    {
        return new StorageInvalidationRecordChecker(
            $this->getStorageRedisClient(),
            $this->createInvalidationRecordKeyGenerator(),
        );
    }

    /**
     * @return \Spryker\Shared\Customer\KeyGenerator\KeyGeneratorInterface
     */
    public function createInvalidationRecordKeyGenerator(): KeyGeneratorInterface
    {
        return new CustomerInvalidationKeyGenerator();
    }
}
