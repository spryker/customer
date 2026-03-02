<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Customer\Reader;

use Generated\Shared\Transfer\CustomerResponseTransfer;

interface CustomerAccessTokenReaderInterface
{
    public function getCustomerByAccessToken(string $accessToken): CustomerResponseTransfer;
}
