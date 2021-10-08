<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\CustomerPasswordPolicy;

use Generated\Shared\Transfer\CustomerErrorTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Spryker\Zed\Customer\CustomerConfig;

class CharacterSetCustomerPasswordPolicy implements CustomerPasswordPolicyInterface
{
    /**
     * @var string
     */
    protected const GLOSSARY_KEY_PASSWORD_POLICY_ERROR_CHARACTER_SET = 'customer.password.error.character_set';

    /**
     * @var string
     */
    protected $regularExpression;

    /**
     * @param \Spryker\Zed\Customer\CustomerConfig $customerConfig
     */
    public function __construct(CustomerConfig $customerConfig)
    {
        $this->regularExpression = $customerConfig->getCustomerPasswordCharacterSet();
    }

    /**
     * @param string $password
     * @param \Generated\Shared\Transfer\CustomerResponseTransfer $customerResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerResponseTransfer
     */
    public function validatePassword(string $password, CustomerResponseTransfer $customerResponseTransfer): CustomerResponseTransfer
    {
        if (preg_match($this->regularExpression, $password)) {
            return $customerResponseTransfer;
        }

        $customerErrorTransfer = (new CustomerErrorTransfer())
            ->setMessage(static::GLOSSARY_KEY_PASSWORD_POLICY_ERROR_CHARACTER_SET);
        $customerResponseTransfer->setIsSuccess(false)
            ->addError($customerErrorTransfer);

        return $customerResponseTransfer;
    }
}
