<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Provider;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\CustomerClientInterface;
use Spryker\Glue\Customer\Api\Storefront\Mapper\CustomersAddressesStorefrontMapper;

/**
 * @implements \ApiPlatform\State\ProviderInterface<\Generated\Api\Storefront\CustomersAddressesStorefrontResource>
 */
class CustomersAddressesStorefrontProvider implements ProviderInterface
{
    public function __construct(
        protected CustomerClientInterface $customerClient,
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
        $mapper = new CustomersAddressesStorefrontMapper();

        if ($operation instanceof GetCollection) {
            return $this->provideCollection($uriVariables, $mapper);
        }

        if ($operation instanceof Get) {
            return $this->provideItem($uriVariables, $mapper);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param \Spryker\Glue\Customer\Api\Storefront\Mapper\CustomersAddressesStorefrontMapper $mapper
     *
     * @return array<object>
     */
    protected function provideCollection(array $uriVariables, CustomersAddressesStorefrontMapper $mapper): array
    {
        $customerReference = $uriVariables['customerReference'] ?? null;

        if ($customerReference === null) {
            return [];
        }

        $customerTransfer = (new CustomerTransfer())->setCustomerReference($customerReference);

        $customerResponse = $this->customerClient->findCustomerByReference($customerTransfer);

        if (!$customerResponse->getIsSuccess() || !$customerResponse->getCustomerTransfer()) {
            return [];
        }

        $customerTransfer = $customerResponse->getCustomerTransfer();
        $addressesTransfer = $customerTransfer->getAddresses();

        if ($addressesTransfer === null) {
            return [];
        }

        $resources = $mapper->mapAddressesTransferToResourceArray($addressesTransfer, $customerTransfer);

        foreach ($resources as $resource) {
            $resource->customerReference = $customerReference;
        }

        return $resources;
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param \Spryker\Glue\Customer\Api\Storefront\Mapper\CustomersAddressesStorefrontMapper $mapper
     *
     * @return object|null
     */
    protected function provideItem(array $uriVariables, CustomersAddressesStorefrontMapper $mapper): ?object
    {
        $customerReference = $uriVariables['customerReference'] ?? null;
        $uuid = $uriVariables['uuid'] ?? null;

        if ($customerReference === null || $uuid === null) {
            return null;
        }

        $customerTransfer = (new CustomerTransfer())->setCustomerReference($customerReference);

        $customerResponse = $this->customerClient->findCustomerByReference($customerTransfer);

        if (!$customerResponse->getIsSuccess() || !$customerResponse->getCustomerTransfer()) {
            return null;
        }

        $customerTransfer = $customerResponse->getCustomerTransfer();
        $addressesTransfer = $customerTransfer->getAddresses();

        if ($addressesTransfer === null) {
            return null;
        }

        foreach ($addressesTransfer->getAddresses() as $addressTransfer) {
            if ($addressTransfer->getUuid() === $uuid) {
                $resource = $mapper->mapAddressTransferToResource($addressTransfer, $customerTransfer);
                $resource->customerReference = $customerReference;

                return $resource;
            }
        }

        return null;
    }
}
