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
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\CustomerClientInterface;
use Spryker\Glue\Customer\Api\Storefront\Mapper\CustomersAddressesStorefrontMapper;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @implements \ApiPlatform\State\ProcessorInterface<\Generated\Api\Storefront\CustomersAddressesStorefrontResource, \Generated\Api\Storefront\CustomersAddressesStorefrontResource>
 */
class CustomersAddressesStorefrontProcessor implements ProcessorInterface
{
    public function __construct(
        protected CustomerClientInterface $customerClient,
    ) {
    }

    /**
     * @param mixed $data
     * @param \ApiPlatform\Metadata\Operation $operation
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     *
     * @return object|array<object>|null
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $mapper = new CustomersAddressesStorefrontMapper();

        if ($operation instanceof Post) {
            return $this->processPost($data, $uriVariables, $mapper);
        }

        if ($operation instanceof Patch) {
            return $this->processPatch($data, $uriVariables, $mapper);
        }

        if ($operation instanceof Delete) {
            return $this->processDelete($uriVariables);
        }

        return null;
    }

    /**
     * @param mixed $data
     * @param array<string, mixed> $uriVariables
     * @param \Spryker\Glue\Customer\Api\Storefront\Mapper\CustomersAddressesStorefrontMapper $mapper
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException
     *
     * @return object|null
     */
    protected function processPost(mixed $data, array $uriVariables, CustomersAddressesStorefrontMapper $mapper): ?object
    {
        $customerReference = $uriVariables['customerReference'] ?? null;

        if ($customerReference === null) {
            throw new UnprocessableEntityHttpException('Customer reference is required');
        }

        $customerTransfer = (new CustomerTransfer())->setCustomerReference($customerReference);

        $customerResponse = $this->customerClient->findCustomerByReference($customerTransfer);

        if (!$customerResponse->getIsSuccess() || !$customerResponse->getCustomerTransfer()) {
            throw new UnprocessableEntityHttpException('Customer not found');
        }

        $customer = $customerResponse->getCustomerTransfer();

        $addressTransfer = $mapper->mapResourceToAddressTransfer($data, new AddressTransfer());
        $addressTransfer->setFkCustomer($customer->getIdCustomer());

        $updatedCustomer = $this->customerClient->createAddressAndUpdateCustomerDefaultAddresses($addressTransfer);

        $addresses = $updatedCustomer->getAddresses();

        if ($addresses === null || count($addresses->getAddresses()) === 0) {
            throw new UnprocessableEntityHttpException('Address creation failed');
        }

        $addressesArray = $addresses->getAddresses()->getArrayCopy();
        $createdAddress = end($addressesArray);

        if ($createdAddress === false) {
            throw new UnprocessableEntityHttpException('Address creation failed');
        }

        $resource = $mapper->mapAddressTransferToResource($createdAddress, $updatedCustomer);
        $resource->customerReference = $customerReference;

        return $resource;
    }

    /**
     * @param mixed $data
     * @param array<string, mixed> $uriVariables
     * @param \Spryker\Glue\Customer\Api\Storefront\Mapper\CustomersAddressesStorefrontMapper $mapper
     *
     * @return object|null
     */
    protected function processPatch(mixed $data, array $uriVariables, CustomersAddressesStorefrontMapper $mapper): ?object
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

        $customer = $customerResponse->getCustomerTransfer();
        $addressesTransfer = $customer->getAddresses();

        if ($addressesTransfer === null) {
            return null;
        }

        $existingAddress = null;

        foreach ($addressesTransfer->getAddresses() as $addressTransfer) {
            if ($addressTransfer->getUuid() === $uuid) {
                $existingAddress = clone $addressTransfer;

                break;
            }
        }

        if (!$existingAddress) {
            return null;
        }

        $updatedAddress = $mapper->mapResourceToAddressTransfer($data, $existingAddress);

        $updatedCustomer = $this->customerClient->updateAddressAndCustomerDefaultAddresses($updatedAddress);

        $updatedAddresses = $updatedCustomer->getAddresses();

        if ($updatedAddresses === null) {
            return null;
        }

        foreach ($updatedAddresses->getAddresses() as $addressTransfer) {
            if ($addressTransfer->getUuid() === $uuid) {
                $resource = $mapper->mapAddressTransferToResource($addressTransfer, $updatedCustomer);
                $resource->customerReference = $customerReference;

                return $resource;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $uriVariables
     *
     * @return null
     */
    protected function processDelete(array $uriVariables): ?object
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

        $customer = $customerResponse->getCustomerTransfer();
        $addressesTransfer = $customer->getAddresses();

        if ($addressesTransfer === null) {
            return null;
        }

        $addressToDelete = null;

        foreach ($addressesTransfer->getAddresses() as $addressTransfer) {
            if ($addressTransfer->getUuid() === $uuid) {
                $addressToDelete = $addressTransfer;

                break;
            }
        }

        if (!$addressToDelete) {
            return null;
        }

        $this->customerClient->deleteAddress($addressToDelete);

        return null;
    }
}
