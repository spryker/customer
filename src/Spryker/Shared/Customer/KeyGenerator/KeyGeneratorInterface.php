<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Customer\KeyGenerator;

use Generated\Shared\Transfer\CustomerTransfer;

interface KeyGeneratorInterface
{
    public function generateKey(CustomerTransfer $customerTransfer): string;
}
