<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Customer\Invalidation;

use Generated\Shared\Transfer\CustomerTransfer;

interface InvalidationRecordCheckerInterface
{
    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return string|null
     */
    public function findInvalidationRecord(CustomerTransfer $customerTransfer): ?string;
}
