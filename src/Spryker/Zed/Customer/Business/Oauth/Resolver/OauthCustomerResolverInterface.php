<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Oauth\Resolver;

use Generated\Shared\Transfer\OauthCustomerResolveRequestTransfer;
use Generated\Shared\Transfer\OauthCustomerResolveResponseTransfer;

interface OauthCustomerResolverInterface
{
    public function resolveCustomer(OauthCustomerResolveRequestTransfer $oauthCustomerResolveRequestTransfer): OauthCustomerResolveResponseTransfer;
}
