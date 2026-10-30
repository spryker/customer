<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Limiter;

use Generated\Shared\Transfer\SecurityCheckAttemptContextTransfer;
use Spryker\Client\SecurityBlocker\SecurityBlockerClientInterface;
use Spryker\Zed\Customer\CustomerConfig;

class PasswordChangeLimiter implements PasswordChangeLimiterInterface
{
    protected const string SECURITY_CHECK_TYPE = 'customer:password_change';

    protected const string IDENTIFIER_PATTERN_CUSTOMER = 'customer:%d';

    public function __construct(
        protected SecurityBlockerClientInterface $securityBlockerClient,
        protected CustomerConfig $customerConfig
    ) {
    }

    public function reserveAttempt(int $idCustomer): bool
    {
        $limit = $this->customerConfig->getPasswordChangeAttemptLimit();

        if ($limit <= 0) {
            return true;
        }

        $securityCheckAttemptContextTransfer = (new SecurityCheckAttemptContextTransfer())
            ->setType(static::SECURITY_CHECK_TYPE)
            ->setIdentifier(sprintf(static::IDENTIFIER_PATTERN_CUSTOMER, $idCustomer))
            ->setTtl($this->customerConfig->getPasswordChangeAttemptLimitTtl());

        return $this->securityBlockerClient
            ->incrementAttemptCount($securityCheckAttemptContextTransfer)
            ->getNumberOfAttemptsOrFail() <= $limit;
    }
}
