<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Validator;

use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;

interface AddressFieldValidatorInterface
{
    public function validateFields(
        AddressTransfer $addressTransfer,
        AddressResponseTransfer $addressResponseTransfer
    ): AddressResponseTransfer;
}
