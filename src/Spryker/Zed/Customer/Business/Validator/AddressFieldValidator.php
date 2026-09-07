<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Validator;

use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerErrorTransfer;
use Spryker\Zed\Customer\CustomerConfig;
use Spryker\Zed\Customer\Persistence\CustomerRepositoryInterface;

class AddressFieldValidator implements AddressFieldValidatorInterface
{
    protected const string ERROR_MESSAGE_FIRST_NAME_MISSING = 'customer.address.validation.first_name_missing';

    protected const string ERROR_MESSAGE_LAST_NAME_MISSING = 'customer.address.validation.last_name_missing';

    protected const string ERROR_MESSAGE_NAME_PATTERN = 'customer.address.validation.name_pattern';

    protected const string ERROR_MESSAGE_SALUTATION_INVALID = 'customer.address.validation.salutation_invalid';

    protected const string ERROR_MESSAGE_FIELD_TOO_LONG = 'customer.address.validation.field_too_long';

    /**
     * @var array<string, int>
     */
    protected const array MAX_ALLOWED_FIELD_LENGTHS = [
        AddressTransfer::FIRST_NAME => 100,
        AddressTransfer::LAST_NAME => 100,
        AddressTransfer::ADDRESS1 => 255,
        AddressTransfer::ADDRESS2 => 255,
        AddressTransfer::ADDRESS3 => 255,
        AddressTransfer::COMPANY => 255,
        AddressTransfer::CITY => 255,
        AddressTransfer::ZIP_CODE => 15,
        AddressTransfer::PHONE => 255,
        AddressTransfer::COMMENT => 255,
    ];

    public function __construct(
        protected CustomerRepositoryInterface $customerRepository,
        protected CustomerConfig $customerConfig
    ) {
    }

    public function validateFields(
        AddressTransfer $addressTransfer,
        AddressResponseTransfer $addressResponseTransfer
    ): AddressResponseTransfer {
        $addressResponseTransfer = $this->validateRequiredFields($addressTransfer, $addressResponseTransfer);
        $addressResponseTransfer = $this->validateNamePatterns($addressTransfer, $addressResponseTransfer);
        $addressResponseTransfer = $this->validateSalutation($addressTransfer, $addressResponseTransfer);

        return $this->validateFieldLengths($addressTransfer, $addressResponseTransfer);
    }

    protected function validateRequiredFields(
        AddressTransfer $addressTransfer,
        AddressResponseTransfer $addressResponseTransfer
    ): AddressResponseTransfer {
        if (!$addressTransfer->getFirstName()) {
            $addressResponseTransfer = $this->addError(
                $addressResponseTransfer,
                static::ERROR_MESSAGE_FIRST_NAME_MISSING,
                AddressTransfer::FIRST_NAME,
            );
        }

        if (!$addressTransfer->getLastName()) {
            $addressResponseTransfer = $this->addError(
                $addressResponseTransfer,
                static::ERROR_MESSAGE_LAST_NAME_MISSING,
                AddressTransfer::LAST_NAME,
            );
        }

        return $addressResponseTransfer;
    }

    protected function validateNamePatterns(
        AddressTransfer $addressTransfer,
        AddressResponseTransfer $addressResponseTransfer
    ): AddressResponseTransfer {
        $pattern = $this->customerConfig->getCustomerNamePattern();

        $names = [
            AddressTransfer::FIRST_NAME => $addressTransfer->getFirstName(),
            AddressTransfer::LAST_NAME => $addressTransfer->getLastName(),
        ];

        foreach ($names as $fieldName => $name) {
            if (!$name || preg_match($pattern, $name) === 1) {
                continue;
            }

            $addressResponseTransfer = $this->addError(
                $addressResponseTransfer,
                static::ERROR_MESSAGE_NAME_PATTERN,
                $fieldName,
            );
        }

        return $addressResponseTransfer;
    }

    protected function validateSalutation(
        AddressTransfer $addressTransfer,
        AddressResponseTransfer $addressResponseTransfer
    ): AddressResponseTransfer {
        $salutation = $addressTransfer->getSalutation();

        if (!$salutation) {
            return $addressResponseTransfer;
        }

        /** @var array<string> $availableSalutations */
        $availableSalutations = $this->customerRepository->getAllSalutations();

        if (in_array($salutation, $availableSalutations, true)) {
            return $addressResponseTransfer;
        }

        return $this->addError(
            $addressResponseTransfer,
            static::ERROR_MESSAGE_SALUTATION_INVALID,
            AddressTransfer::SALUTATION,
        );
    }

    protected function validateFieldLengths(
        AddressTransfer $addressTransfer,
        AddressResponseTransfer $addressResponseTransfer
    ): AddressResponseTransfer {
        $addressData = $addressTransfer->toArray(false, true);

        foreach (static::MAX_ALLOWED_FIELD_LENGTHS as $fieldName => $maxAllowedLength) {
            $value = $addressData[$fieldName] ?? null;

            if (!is_string($value) || mb_strlen($value) <= $maxAllowedLength) {
                continue;
            }

            $addressResponseTransfer = $this->addError(
                $addressResponseTransfer,
                static::ERROR_MESSAGE_FIELD_TOO_LONG,
                $fieldName,
                ['%limit%' => (string)$maxAllowedLength],
            );
        }

        return $addressResponseTransfer;
    }

    /**
     * @param array<string, string> $parameters
     */
    protected function addError(
        AddressResponseTransfer $addressResponseTransfer,
        string $message,
        string $fieldName,
        array $parameters = []
    ): AddressResponseTransfer {
        return $addressResponseTransfer
            ->setIsSuccess(false)
            ->addError(
                (new CustomerErrorTransfer())
                    ->setMessage($message)
                    ->setParameters(['%field%' => $fieldName] + $parameters),
            );
    }
}
