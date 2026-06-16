<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Facade;

use Generated\Shared\Transfer\CustomerTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group TryAuthorizeCustomerByEmailAndPasswordTest
 * Add your own group annotations below this line
 */
class TryAuthorizeCustomerByEmailAndPasswordTest extends AbstractCustomerFacadeTest
{
    protected const string RAW_PASSWORD = 'p2cfGyY4';

    /**
     * @dataProvider getAuthorizationData
     *
     * @param bool $isDoubleOptInEnabled
     * @param bool $isConfirmed
     * @param bool $expectedResult
     *
     * @return void
     */
    public function testTryAuthorizeCustomerByEmailAndPassword(
        bool $isDoubleOptInEnabled,
        bool $isConfirmed,
        bool $expectedResult,
    ): void {
        // Arrange
        $this->tester->mockConfigMethod('isDoubleOptInEnabled', $isDoubleOptInEnabled);

        $customerTransfer = $isConfirmed
            ? $this->tester->haveConfirmedCustomer([CustomerTransfer::PASSWORD => static::RAW_PASSWORD])
            : $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::RAW_PASSWORD]);

        $authorizationTransfer = (new CustomerTransfer())
            ->setEmail($customerTransfer->getEmail())
            ->setPassword(static::RAW_PASSWORD);

        // Act
        $result = $this->tester->getCustomerFacade()->tryAuthorizeCustomerByEmailAndPassword($authorizationTransfer);

        // Assert
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function getAuthorizationData(): array
    {
        return [
            'double opt-in disabled, customer not registered' => [false, false, true],
            'double opt-in enabled, customer not registered' => [true, false, false],
            'double opt-in enabled, customer registered' => [true, true, true],
        ];
    }
}
