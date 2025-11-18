<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Customer\KeyGenerator;

use Generated\Shared\Transfer\CustomerTransfer;

class CustomerInvalidationKeyGenerator implements KeyGeneratorInterface
{
    /**
     * @var string
     */
    protected const KEY_PATTERN = 'customer:invalidated:%s';

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return string
     */
    public function generateKey(CustomerTransfer $customerTransfer): string
    {
        return sprintf(static::KEY_PATTERN, $customerTransfer->getIdCustomer());
    }
}
