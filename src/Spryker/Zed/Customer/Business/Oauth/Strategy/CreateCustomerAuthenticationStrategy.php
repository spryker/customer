<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Oauth\Strategy;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ResourceOwnerTransfer;
use Spryker\Zed\Customer\Business\Customer\CustomerInterface;

class CreateCustomerAuthenticationStrategy implements AuthenticationStrategyInterface
{
    protected const string DEFAULF_FIRST_NAME = 'OAuth';

    protected const string DEFAULT_LAST_NAME = 'User';

    public function __construct(protected CustomerInterface $customer)
    {
    }

    public function resolveOauthCustomer(ResourceOwnerTransfer $resourceOwnerTransfer): ?CustomerTransfer
    {
        $email = $resourceOwnerTransfer->getEmail();

        if ($email === null) {
            return null;
        }

        if ($this->customer->hasEmail($email)) {
            $customerTransfer = $this->customer->get(
                (new CustomerTransfer())->setEmail($email),
            );

            if ($customerTransfer->getAnonymizedAt() !== null) {
                return null;
            }

            return $customerTransfer;
        }

        return $this->createCustomer($resourceOwnerTransfer, $email);
    }

    protected function createCustomer(ResourceOwnerTransfer $resourceOwnerTransfer, string $email): ?CustomerTransfer
    {
        $customerTransfer = (new CustomerTransfer())
            ->setEmail($email)
            ->setFirstName($resourceOwnerTransfer->getFirstName() ?? static::DEFAULF_FIRST_NAME)
            ->setLastName($resourceOwnerTransfer->getLastName() ?? static::DEFAULT_LAST_NAME)
            ->setSkipSendingRegistrationToken(true)
            ->setSendPasswordToken(false);

        $customerResponseTransfer = $this->customer->register($customerTransfer);

        if (!$customerResponseTransfer->getIsSuccess()) {
            return null;
        }

        $confirmationResponseTransfer = $this->customer->confirmCustomerRegistration(
            $customerResponseTransfer->getCustomerTransferOrFail(),
        );

        if (!$confirmationResponseTransfer->getIsSuccess()) {
            return null;
        }

        return $confirmationResponseTransfer->getCustomerTransfer();
    }
}
