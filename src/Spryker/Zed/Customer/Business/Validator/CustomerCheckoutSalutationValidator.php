<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Validator;

use Generated\Shared\Transfer\CheckoutErrorTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Customer\CustomerConfig;
use Spryker\Zed\Customer\Persistence\CustomerRepositoryInterface;

class CustomerCheckoutSalutationValidator implements CustomerCheckoutSalutationValidatorInterface
{
    /**
     * @var string
     */
    protected const GLOSSARY_KEY_CUSTOMER_SALUTATION_INVALID = 'customer.salutation.invalid';

    /**
     * @var \Spryker\Zed\Customer\Persistence\CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @var \Spryker\Zed\Customer\CustomerConfig
     */
    protected CustomerConfig $customerConfig;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerConfig $customerConfig
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerConfig = $customerConfig;
    }

    public function validate(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer): bool
    {
        if ($this->customerSalutationExists($quoteTransfer)) {
            return true;
        }

        $checkoutResponseTransfer->setIsSuccess(false)
            ->addError(
                (new CheckoutErrorTransfer())
                    ->setErrorCode($this->customerConfig->getCustomerInvalidSalutationErrorCode())
                    ->setMessage(static::GLOSSARY_KEY_CUSTOMER_SALUTATION_INVALID),
            );

        return false;
    }

    protected function customerSalutationExists(QuoteTransfer $quoteTransfer): bool
    {
        return !$quoteTransfer->getCustomerOrFail()->getSalutation()
            || in_array($quoteTransfer->getCustomerOrFail()->getSalutationOrFail(), $this->customerRepository->getAllSalutations(), true);
    }
}
