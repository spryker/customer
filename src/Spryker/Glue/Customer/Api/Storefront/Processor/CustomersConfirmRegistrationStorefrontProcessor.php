<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Generated\Api\Storefront\CustomersConfirmRegistrationStorefrontResource;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\CustomerClientInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @implements \ApiPlatform\State\ProcessorInterface<\Generated\Api\Storefront\CustomersConfirmRegistrationStorefrontResource, \Generated\Api\Storefront\CustomersConfirmRegistrationStorefrontResource>
 */
class CustomersConfirmRegistrationStorefrontProcessor implements ProcessorInterface
{
    protected const string ERROR_CODE_CUSTOMER_NOT_FOUND = 'CUSTOMER_NOT_FOUND';

    protected const string ERROR_CODE_ALREADY_CONFIRMED = 'REGISTRATION_ALREADY_CONFIRMED';

    protected const string ERROR_CODE_INVALID_KEY = 'INVALID_REGISTRATION_KEY';

    public function __construct(
        protected CustomerClientInterface $customerClient
    ) {
    }

    /**
     * @param \Generated\Api\Storefront\CustomersConfirmRegistrationStorefrontResource $data
     * @param \ApiPlatform\Metadata\Operation $operation
     * @param array<string, mixed> $uriVariables
     * @param array<string, mixed> $context
     *
     * @throws \Symfony\Component\HttpKernel\Exception\ConflictHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException
     *
     * @return object|null
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ?object
    {
        $customerReference = $uriVariables['customerReference'] ?? null;

        if (!$customerReference) {
            throw new UnprocessableEntityHttpException('Customer reference is required');
        }

        $customerTransfer = (new CustomerTransfer())
            ->setCustomerReference($customerReference);

        $customerResponseTransfer = $this->customerClient->findCustomerByReference($customerTransfer);

        if (!$customerResponseTransfer->getHasCustomer()) {
            throw new NotFoundHttpException(
                sprintf('Customer with reference "%s" not found', $customerReference),
            );
        }

        $existingCustomer = $customerResponseTransfer->getCustomerTransfer();

        if (!$existingCustomer->getRegistrationKey()) {
            throw new ConflictHttpException('Customer registration has already been confirmed');
        }

        $customerTransfer->setRegistrationKey($data->getRegistrationKey());
        $confirmationResponseTransfer = $this->customerClient->confirmCustomerRegistration($customerTransfer);

        if (!$confirmationResponseTransfer->getIsSuccess()) {
            $errorMessage = 'Invalid or expired registration key';

            if ($confirmationResponseTransfer->getErrors()->count() > 0) {
                $errorMessage = $confirmationResponseTransfer->getErrors()->offsetGet(0)->getMessage();
            }

            throw new UnprocessableEntityHttpException($errorMessage);
        }

        return $this->mapCustomerTransferToResource($confirmationResponseTransfer->getCustomerTransfer());
    }

    protected function mapCustomerTransferToResource(CustomerTransfer $customerTransfer): CustomersConfirmRegistrationStorefrontResource
    {
        $resource = new CustomersConfirmRegistrationStorefrontResource();

        $resource->setCustomerReference($customerTransfer->getCustomerReference());
        $resource->setEmail($customerTransfer->getEmail());
        $resource->setFirstName($customerTransfer->getFirstName());
        $resource->setLastName($customerTransfer->getLastName());
        $resource->setSalutation($customerTransfer->getSalutation());
        $resource->setGender($customerTransfer->getGender());
        $resource->setDateOfBirth($customerTransfer->getDateOfBirth());
        $resource->setCreatedAt($customerTransfer->getCreatedAt());
        $resource->setUpdatedAt($customerTransfer->getUpdatedAt());

        return $resource;
    }
}
