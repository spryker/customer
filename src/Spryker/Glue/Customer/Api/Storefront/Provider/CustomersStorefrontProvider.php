<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Generated\Api\Storefront\CustomersStorefrontResource;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\CustomerClientInterface;

/**
 * @implements \ApiPlatform\State\ProviderInterface<\Generated\Api\Storefront\CustomersStorefrontResource>
 */
class CustomersStorefrontProvider implements ProviderInterface
{
    public function __construct(
        protected CustomerClientInterface $customerClient,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     *
     * @return \Generated\Api\Storefront\CustomersStorefrontResource|array<\Generated\Api\Storefront\CustomersStorefrontResource>|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (isset($uriVariables['customerReference'])) {
            return $this->getCustomerByReference($uriVariables['customerReference']);
        }

        return null;
    }

    protected function getCustomerByReference(string $customerReference): ?CustomersStorefrontResource
    {
        $customerTransfer = (new CustomerTransfer())->setCustomerReference($customerReference);
        $customerResponseTransfer = $this->customerClient->findCustomerByReference($customerTransfer);

        if (!$customerResponseTransfer->getHasCustomer()) {
            return null;
        }

        return $this->mapCustomerTransferToResource($customerResponseTransfer->getCustomerTransfer());
    }

    protected function mapCustomerTransferToResource(CustomerTransfer $customerTransfer): CustomersStorefrontResource
    {
        $resource = new CustomersStorefrontResource();
        $resource->customerReference = $customerTransfer->getCustomerReference();
        $resource->email = $customerTransfer->getEmail();
        $resource->salutation = $customerTransfer->getSalutation();
        $resource->firstName = $customerTransfer->getFirstName();
        $resource->lastName = $customerTransfer->getLastName();
        $resource->gender = $customerTransfer->getGender();
        $resource->dateOfBirth = $customerTransfer->getDateOfBirth();
        $resource->phone = $customerTransfer->getPhone();
        $resource->createdAt = $customerTransfer->getCreatedAt();
        $resource->updatedAt = $customerTransfer->getUpdatedAt();
        $resource->anonymizedAt = $customerTransfer->getAnonymizedAt();

        return $resource;
    }
}
