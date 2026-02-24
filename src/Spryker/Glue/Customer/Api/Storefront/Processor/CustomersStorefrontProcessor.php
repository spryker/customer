<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Processor;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use Generated\Api\Storefront\CustomersStorefrontResource;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\CustomerClientInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @implements \ApiPlatform\State\ProcessorInterface<\Generated\Api\Storefront\CustomersStorefrontResource, \Generated\Api\Storefront\CustomersStorefrontResource>
 */
final class CustomersStorefrontProcessor implements ProcessorInterface
{
    public function __construct(
        protected CustomerClientInterface $customerClient,
    ) {
    }

    /**
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        if ($operation instanceof Post) {
            return $this->registerCustomer($data);
        }

        if ($operation instanceof Patch) {
            return $this->updateCustomer($data, $uriVariables);
        }

        if ($operation instanceof Delete) {
            return $this->anonymizeCustomer($uriVariables);
        }

        return null;
    }

    protected function registerCustomer(CustomersStorefrontResource $resource): CustomersStorefrontResource
    {
        $customerTransfer = $this->mapResourceToCustomerTransfer($resource);
        $customerResponseTransfer = $this->customerClient->registerCustomer($customerTransfer);

        $this->validateCustomerResponse($customerResponseTransfer);

        return $this->mapCustomerTransferToResource($customerResponseTransfer->getCustomerTransfer());
    }

    /**
     * @param array<string, mixed> $uriVariables
     */
    protected function updateCustomer(CustomersStorefrontResource $resource, array $uriVariables): CustomersStorefrontResource
    {
        $customerTransfer = $this->mapResourceToCustomerTransfer($resource);
        $customerTransfer->setCustomerReference($uriVariables['customerReference']);

        $customerResponseTransfer = $this->customerClient->updateCustomer($customerTransfer);

        $this->validateCustomerResponse($customerResponseTransfer);

        return $this->mapCustomerTransferToResource($customerResponseTransfer->getCustomerTransfer());
    }

    /**
     * @param array<string, mixed> $uriVariables
     */
    protected function anonymizeCustomer(array $uriVariables): null
    {
        $customerTransfer = (new CustomerTransfer())->setCustomerReference($uriVariables['customerReference']);
        $this->customerClient->anonymizeCustomer($customerTransfer);

        return null;
    }

    protected function validateCustomerResponse(CustomerResponseTransfer $customerResponseTransfer): void
    {
        if (!$customerResponseTransfer->getIsSuccess()) {
            $errorMessages = [];

            foreach ($customerResponseTransfer->getErrors() as $customerError) {
                $message = $customerError->getMessage();

                if ($message !== null && $message !== '') {
                    $errorMessages[] = $message;
                }
            }

            $errorMessage = $errorMessages !== []
                ? implode('; ', $errorMessages)
                : 'Failed to process customer request';

            throw new UnprocessableEntityHttpException($errorMessage);
        }
    }

    protected function mapResourceToCustomerTransfer(CustomersStorefrontResource $resource): CustomerTransfer
    {
        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail($resource->email);
        $customerTransfer->setSalutation($resource->salutation);
        $customerTransfer->setFirstName($resource->firstName);
        $customerTransfer->setLastName($resource->lastName);
        $customerTransfer->setGender($resource->gender);
        $customerTransfer->setDateOfBirth($resource->dateOfBirth);
        $customerTransfer->setPhone($resource->phone);
        $customerTransfer->setPassword($resource->password);

        return $customerTransfer;
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
