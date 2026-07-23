<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Business\CustomerFacade;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group GetCustomerForAuthenticationTest
 * Add your own group annotations below this line
 */
class GetCustomerForAuthenticationTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\Customer\CustomerBusinessTester
     */
    protected $tester;

    public function testGetCustomerInSecureModeStripsPasswordHash(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer(['password' => 'change123']);

        // Act
        $resultCustomerTransfer = (new CustomerFacade())->getCustomer(
            (new CustomerTransfer())->setEmail($customerTransfer->getEmail()),
        );

        // Assert
        $this->assertNull($resultCustomerTransfer->getPassword());
    }

    public function testGetCustomerInAuthenticationModeProvidesPasswordHash(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer(['password' => 'change123']);

        // Act
        $resultCustomerTransfer = (new CustomerFacade())->getCustomer(
            (new CustomerTransfer())->setEmail($customerTransfer->getEmail()),
            false,
        );

        // Assert
        $this->assertNotNull($resultCustomerTransfer->getPassword());
        // A password hash is provided, never the plain password.
        $this->assertNotSame('change123', $resultCustomerTransfer->getPassword());
    }
}
