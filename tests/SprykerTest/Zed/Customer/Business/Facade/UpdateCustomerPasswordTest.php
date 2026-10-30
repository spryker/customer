<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Facade;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\SecurityBlocker\Exception\SecurityBlockerException;
use Spryker\Client\SecurityBlocker\SecurityBlockerClientInterface;
use Spryker\Client\SecurityBlocker\SecurityBlockerDependencyProvider;
use Spryker\Client\SecurityBlocker\SecurityBlockerFactory;
use Spryker\Shared\Customer\Code\Messages;
use Spryker\Zed\Customer\Business\CustomerBusinessFactory;
use Spryker\Zed\Customer\CustomerDependencyProvider;
use SprykerTest\Client\SecurityBlocker\Helper\InMemorySecurityBlockerRedisClient;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group UpdateCustomerPasswordTest
 * Add your own group annotations below this line
 */
class UpdateCustomerPasswordTest extends AbstractCustomerFacadeTest
{
    protected const string GLOSSARY_KEY_PASSWORD_CHANGE_LIMIT_EXCEEDED = 'customer.password.change_limit_exceeded';

    protected const string VALUE_WRONG_PASSWORD = 'wR0ngPwd';

    public function setUp(): void
    {
        parent::setUp();

        $this->tester->setDependency(SecurityBlockerDependencyProvider::CLIENT_REDIS, new InMemorySecurityBlockerRedisClient(), SecurityBlockerFactory::class);
    }

    public function testUpdateCustomerPasswordNotUpdatesCustomerPasswordWhenItLessThanMinLength(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_SHORT_PASSWORD);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasMessageInCustomerResponseTransfer(
            static::GLOSSARY_KEY_MIN_LENGTH_ERROR,
            $customerResponseTransfer,
        ));
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(
            static::GLOSSARY_KEY_MIN_LENGTH_ERROR,
            $customerResponseTransfer,
        ));
    }

    public function testUpdateCustomerPasswordShouldNotUpdateCustomerPasswordWhenItLongerThanMaxLength(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_LONG_PASSWORD);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasMessageInCustomerResponseTransfer(
            static::GLOSSARY_KEY_MAX_LENGTH_ERROR,
            $customerResponseTransfer,
        ));
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(
            static::GLOSSARY_KEY_MAX_LENGTH_ERROR,
            $customerResponseTransfer,
        ));
    }

    public function testUpdateCustomerPasswordShouldUpdateCustomerPasswordWhenItHasCorrectLength(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_NEW_PASSWORD);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($customerTransfer);

        // Assert
        $this->assertTrue($customerResponseTransfer->getIsSuccess());
        $this->assertNotNull($customerResponseTransfer->getCustomerTransfer());
    }

    /**
     * @uses \Spryker\Zed\Customer\CustomerConfig::getCustomerPasswordAllowList()
     *
     * @return void
     */
    public function testUpdateCustomerPasswordUpdatesCustomerPasswordWhenPasswordInAllowList(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_SHORT_PASSWORD);
        $this->tester->mockConfigMethod('getCustomerPasswordAllowList', [static::VALUE_SHORT_PASSWORD]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($customerTransfer);

        // Assert
        $this->assertTrue($customerResponseTransfer->getIsSuccess());
        $this->assertNotNull($customerResponseTransfer->getCustomerTransfer());
    }

    /**
     * @uses \Spryker\Zed\Customer\CustomerConfig::getCustomerPasswordSequenceLimit()
     *
     * @return void
     */
    public function testUpdateCustomerPasswordNotUpdatesCustomerPasswordWhenPasswordHasTooLongSequence(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_SEQUENCE_TOO_LONG_PASSWORD);
        $this->tester->mockConfigMethod('getCustomerPasswordSequenceLimit', static::SEQUENCE_LIMIT_CUSTOMER_PASSWORD);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(
            static::GLOSSARY_KEY_PASSWORD_POLICY_ERROR_SEQUENCE,
            $customerResponseTransfer,
        ));
    }

    /**
     * @uses \Spryker\Zed\Customer\CustomerConfig::getCustomerPasswordDenyList()
     *
     * @return void
     */
    public function testUpdateCustomerPasswordNotUpdatesCustomerPasswordWhenPasswordInDenyList(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_DENY_LIST_PASSWORD);
        $this->tester->mockConfigMethod('getCustomerPasswordDenyList', [static::VALUE_DENY_LIST_PASSWORD]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(
            static::GLOSSARY_KEY_PASSWORD_POLICY_ERROR_DENY_LIST,
            $customerResponseTransfer,
        ));
    }

    public function testUpdateCustomerPasswordReturnsLimitExceededErrorAndKeepsPasswordWhenAttemptLimitIsExceeded(): void
    {
        // Arrange
        $this->setUpPasswordChangeAttemptLimit(1);
        $customerTransfer = $this->haveCustomerWithFailedPasswordChangeAttempt();

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($this->createPasswordChangeCustomerTransfer($customerTransfer, static::VALUE_VALID_PASSWORD));

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(static::GLOSSARY_KEY_PASSWORD_CHANGE_LIMIT_EXCEEDED, $customerResponseTransfer));
        $this->assertTrue($this->isCurrentPassword($customerTransfer, static::VALUE_VALID_PASSWORD));
    }

    public function testUpdateCustomerPasswordUpdatesPasswordAfterFailedAttemptWhenAttemptLimitIsDisabled(): void
    {
        // Arrange
        $this->setUpPasswordChangeAttemptLimit(0);
        $customerTransfer = $this->haveCustomerWithFailedPasswordChangeAttempt();

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($this->createPasswordChangeCustomerTransfer($customerTransfer, static::VALUE_VALID_PASSWORD));

        // Assert
        $this->assertTrue($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->isCurrentPassword($customerTransfer, static::VALUE_NEW_PASSWORD));
    }

    public function testUpdateCustomerPasswordReturnsLimitExceededErrorAndNoInvalidPasswordErrorWhenBlockedCallHasWrongPassword(): void
    {
        // Arrange
        $this->setUpPasswordChangeAttemptLimit(1);
        $customerTransfer = $this->haveCustomerWithFailedPasswordChangeAttempt();

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomerPassword($this->createPasswordChangeCustomerTransfer($customerTransfer, static::VALUE_WRONG_PASSWORD));

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(static::GLOSSARY_KEY_PASSWORD_CHANGE_LIMIT_EXCEEDED, $customerResponseTransfer));
        $this->assertFalse($this->hasErrorInCustomerResponseTransfer(Messages::CUSTOMER_PASSWORD_INVALID, $customerResponseTransfer));
        $this->assertTrue($this->isCurrentPassword($customerTransfer, static::VALUE_VALID_PASSWORD));
    }

    public function testUpdateCustomerReturnsLimitExceededErrorAndKeepsPasswordWhenAttemptLimitIsExceeded(): void
    {
        // Arrange
        $this->setUpPasswordChangeAttemptLimit(1);
        $customerTransfer = $this->haveCustomerWithFailedPasswordChangeAttempt();

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($this->createPasswordChangeCustomerTransfer($customerTransfer, static::VALUE_VALID_PASSWORD));

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(static::GLOSSARY_KEY_PASSWORD_CHANGE_LIMIT_EXCEEDED, $customerResponseTransfer));
        $this->assertTrue($this->isCurrentPassword($customerTransfer, static::VALUE_VALID_PASSWORD));
    }

    public function testUpdateCustomerPasswordThrowsExceptionAndKeepsPasswordWhenSecurityBlockerClientFails(): void
    {
        // Arrange
        $this->setUpPasswordChangeAttemptLimit(1);
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $securityBlockerClientMock = $this->createMock(SecurityBlockerClientInterface::class);
        $securityBlockerClientMock->method('incrementAttemptCount')->willThrowException(new SecurityBlockerException());
        $this->tester->setDependency(CustomerDependencyProvider::CLIENT_SECURITY_BLOCKER, $securityBlockerClientMock, CustomerBusinessFactory::class);

        $isSecurityBlockerExceptionThrown = false;

        // Act
        try {
            $this->tester->getCustomerFacade()->updateCustomerPassword($this->createPasswordChangeCustomerTransfer($customerTransfer, static::VALUE_VALID_PASSWORD));
        } catch (SecurityBlockerException) {
            $isSecurityBlockerExceptionThrown = true;
        }

        // Assert
        $this->assertTrue($isSecurityBlockerExceptionThrown);
        $this->assertTrue($this->isCurrentPassword($customerTransfer, static::VALUE_VALID_PASSWORD));
    }

    protected function setUpPasswordChangeAttemptLimit(int $passwordChangeAttemptLimit): void
    {
        $this->tester->mockConfigMethod('getPasswordChangeAttemptLimit', $passwordChangeAttemptLimit);
    }

    protected function haveCustomerWithFailedPasswordChangeAttempt(): CustomerTransfer
    {
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $this->tester->getCustomerFacade()->updateCustomerPassword($this->createPasswordChangeCustomerTransfer($customerTransfer, static::VALUE_WRONG_PASSWORD));

        return $customerTransfer;
    }

    protected function createPasswordChangeCustomerTransfer(CustomerTransfer $customerTransfer, string $currentPassword): CustomerTransfer
    {
        return (new CustomerTransfer())
            ->setIdCustomer($customerTransfer->getIdCustomerOrFail())
            ->setEmail($customerTransfer->getEmailOrFail())
            ->setPassword($currentPassword)
            ->setNewPassword(static::VALUE_NEW_PASSWORD);
    }

    protected function isCurrentPassword(CustomerTransfer $customerTransfer, string $password): bool
    {
        return $this->tester->getCustomerFacade()->tryAuthorizeCustomerByEmailAndPassword(
            (new CustomerTransfer())
                ->setEmail($customerTransfer->getEmailOrFail())
                ->setPassword($password),
        );
    }
}
