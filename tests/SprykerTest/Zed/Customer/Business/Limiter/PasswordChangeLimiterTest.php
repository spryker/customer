<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Limiter;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\SecurityCheckAttemptContextTransfer;
use Generated\Shared\Transfer\SecurityCheckAttemptResponseTransfer;
use Spryker\Client\SecurityBlocker\SecurityBlockerClientInterface;
use Spryker\Zed\Customer\Business\Limiter\PasswordChangeLimiter;
use Spryker\Zed\Customer\CustomerConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Limiter
 * @group PasswordChangeLimiterTest
 * Add your own group annotations below this line
 */
class PasswordChangeLimiterTest extends Unit
{
    protected const int ID_CUSTOMER = 7;

    protected const int OTHER_ID_CUSTOMER = 8;

    protected const int TTL = 900;

    protected const string SECURITY_CHECK_TYPE = 'customer:password_change';

    protected const string IDENTIFIER_CUSTOMER = 'customer:7';

    public function testReserveAttemptReturnsFalseWhenAttemptLimitIsExceeded(): void
    {
        // Arrange
        $passwordChangeLimiter = $this->createPasswordChangeLimiter(2, $this->createCountingSecurityBlockerClient());

        // Act
        $areAttemptsReserved = [$passwordChangeLimiter->reserveAttempt(static::ID_CUSTOMER), $passwordChangeLimiter->reserveAttempt(static::ID_CUSTOMER), $passwordChangeLimiter->reserveAttempt(static::ID_CUSTOMER)];

        // Assert
        $this->assertSame([true, true, false], $areAttemptsReserved);
    }

    public function testReserveAttemptReturnsTrueForOtherCustomerWhenAttemptLimitIsExceededForCustomer(): void
    {
        // Arrange
        $passwordChangeLimiter = $this->createPasswordChangeLimiter(1, $this->createCountingSecurityBlockerClient());
        $passwordChangeLimiter->reserveAttempt(static::ID_CUSTOMER);
        $passwordChangeLimiter->reserveAttempt(static::ID_CUSTOMER);

        // Act
        $isAttemptReserved = $passwordChangeLimiter->reserveAttempt(static::OTHER_ID_CUSTOMER);

        // Assert
        $this->assertTrue($isAttemptReserved);
    }

    public function testReserveAttemptPassesTypeIdentifierAndTtlToSecurityBlockerClient(): void
    {
        // Arrange
        $securityBlockerClientMock = $this->createMock(SecurityBlockerClientInterface::class);
        $passwordChangeLimiter = $this->createPasswordChangeLimiter(1, $securityBlockerClientMock);

        // Expect
        $securityBlockerClientMock->expects($this->once())
            ->method('incrementAttemptCount')
            ->with($this->callback(function (SecurityCheckAttemptContextTransfer $securityCheckAttemptContextTransfer): bool {
                return $securityCheckAttemptContextTransfer->getType() === static::SECURITY_CHECK_TYPE
                    && $securityCheckAttemptContextTransfer->getIdentifier() === static::IDENTIFIER_CUSTOMER
                    && $securityCheckAttemptContextTransfer->getTtl() === static::TTL;
            }))
            ->willReturn((new SecurityCheckAttemptResponseTransfer())->setNumberOfAttempts(1));

        // Act
        $passwordChangeLimiter->reserveAttempt(static::ID_CUSTOMER);
    }

    /**
     * @dataProvider getDisabledPasswordChangeAttemptLimitDataProvider
     */
    public function testReserveAttemptReturnsTrueAndDoesNotCountAttemptWhenAttemptLimitIsDisabled(int $passwordChangeAttemptLimit): void
    {
        // Arrange
        $securityBlockerClientMock = $this->createMock(SecurityBlockerClientInterface::class);
        $securityBlockerClientMock->expects($this->never())->method('incrementAttemptCount');
        $passwordChangeLimiter = $this->createPasswordChangeLimiter($passwordChangeAttemptLimit, $securityBlockerClientMock);

        // Act
        $isAttemptReserved = $passwordChangeLimiter->reserveAttempt(static::ID_CUSTOMER);

        // Assert
        $this->assertTrue($isAttemptReserved);
    }

    /**
     * @return array<string, array<int>>
     */
    public function getDisabledPasswordChangeAttemptLimitDataProvider(): array
    {
        return [
            'zero limit' => [0],
            'negative limit' => [-1],
        ];
    }

    protected function createPasswordChangeLimiter(
        int $passwordChangeAttemptLimit,
        SecurityBlockerClientInterface $securityBlockerClient
    ): PasswordChangeLimiter {
        $customerConfigMock = $this->createMock(CustomerConfig::class);
        $customerConfigMock->method('getPasswordChangeAttemptLimit')->willReturn($passwordChangeAttemptLimit);
        $customerConfigMock->method('getPasswordChangeAttemptLimitTtl')->willReturn(static::TTL);

        return new PasswordChangeLimiter($securityBlockerClient, $customerConfigMock);
    }

    protected function createCountingSecurityBlockerClient(): SecurityBlockerClientInterface
    {
        $numberOfAttemptsByIdentifier = [];
        $securityBlockerClientMock = $this->createMock(SecurityBlockerClientInterface::class);
        $securityBlockerClientMock->method('incrementAttemptCount')->willReturnCallback(
            function (SecurityCheckAttemptContextTransfer $securityCheckAttemptContextTransfer) use (&$numberOfAttemptsByIdentifier): SecurityCheckAttemptResponseTransfer {
                $identifier = $securityCheckAttemptContextTransfer->getIdentifierOrFail();
                $numberOfAttemptsByIdentifier[$identifier] = ($numberOfAttemptsByIdentifier[$identifier] ?? 0) + 1;

                return (new SecurityCheckAttemptResponseTransfer())->setNumberOfAttempts($numberOfAttemptsByIdentifier[$identifier]);
            },
        );

        return $securityBlockerClientMock;
    }
}
