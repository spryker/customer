<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Generated\Api\Storefront\CustomersConfirmRegistrationStorefrontResource;
use Spryker\Client\Customer\CustomerClientInterface;

/**
 * @implements \ApiPlatform\State\ProviderInterface<\Generated\Api\Storefront\CustomersConfirmRegistrationStorefrontResource>
 */
class CustomersConfirmRegistrationStorefrontProvider implements ProviderInterface
{
    public function __construct(
        protected CustomerClientInterface $customerClient
    ) {
    }

    /**
     * @param \ApiPlatform\Metadata\Operation $operation
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     *
     * @return object|array<object>|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resource = new CustomersConfirmRegistrationStorefrontResource();
        $resource->setCustomerReference($uriVariables['customerReference'] ?? null);

        return $resource;
    }
}
