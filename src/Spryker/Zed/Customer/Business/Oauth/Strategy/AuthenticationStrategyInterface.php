<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Oauth\Strategy;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ResourceOwnerTransfer;

interface AuthenticationStrategyInterface
{
    public function resolveOauthCustomer(ResourceOwnerTransfer $resourceOwnerTransfer): ?CustomerTransfer;
}
