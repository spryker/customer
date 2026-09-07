<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Customer;

use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Spryker\Zed\Customer\Business\Executor\CustomerPluginExecutorInterface;
use Spryker\Zed\Customer\Business\Validator\AddressFieldValidatorInterface;

class AddressValidator implements AddressValidatorInterface
{
    public function __construct(
        protected AddressFieldValidatorInterface $addressFieldValidator,
        protected CustomerPluginExecutorInterface $customerPluginExecutor
    ) {
    }

    public function validateAddress(AddressTransfer $addressTransfer): AddressResponseTransfer
    {
        $addressResponseTransfer = (new AddressResponseTransfer())
            ->setAddress($addressTransfer)
            ->setIsSuccess(true);

        $addressResponseTransfer = $this->addressFieldValidator->validateFields(
            $addressTransfer,
            $addressResponseTransfer,
        );

        return $this->customerPluginExecutor->executeAddressValidatorPlugins(
            $addressTransfer,
            $addressResponseTransfer,
        );
    }
}
