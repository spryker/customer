<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\Customer;

use Spryker\Service\UtilText\UtilTextServiceInterface;
use Spryker\Yves\Customer\Processor\CurrentCustomerDataRequestLogProcessor;
use Spryker\Yves\Customer\Processor\CurrentCustomerDataRequestLogProcessorInterface;
use Spryker\Yves\Customer\Session\AnonymousIdProvider;
use Spryker\Yves\Customer\Session\AnonymousIdProviderInterface;
use Spryker\Yves\Kernel\AbstractFactory;
use Symfony\Component\HttpFoundation\RequestStack;

class CustomerFactory extends AbstractFactory
{
    public function createCurrentCustomerDataRequestLogProcessor(): CurrentCustomerDataRequestLogProcessorInterface
    {
        return new CurrentCustomerDataRequestLogProcessor($this->getRequestStackService());
    }

    public function getRequestStackService(): RequestStack
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::SERVICE_REQUEST_STACK);
    }

    public function createAnonymousIdProvider(): AnonymousIdProviderInterface
    {
        return new AnonymousIdProvider($this->getUtilTextService());
    }

    public function getUtilTextService(): UtilTextServiceInterface
    {
        return $this->getProvidedDependency(CustomerDependencyProvider::SERVICE_UTIL_TEXT);
    }
}
