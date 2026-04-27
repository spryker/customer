<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Oauth\Strategy;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ResourceOwnerTransfer;
use Spryker\Zed\Customer\Business\Customer\CustomerInterface;

class AcceptOnlyAuthenticationStrategy implements AuthenticationStrategyInterface
{
    public function __construct(protected CustomerInterface $customer)
    {
    }

    public function resolveOauthCustomer(ResourceOwnerTransfer $resourceOwnerTransfer): ?CustomerTransfer
    {
        $email = $resourceOwnerTransfer->getEmail();

        if ($email === null) {
            return null;
        }

        if (!$this->customer->hasEmail($email)) {
            return null;
        }

        $customerTransfer = $this->customer->get(
            (new CustomerTransfer())->setEmail($email),
        );

        if ($customerTransfer->getAnonymizedAt() !== null) {
            return null;
        }

        return $customerTransfer;
    }
}
