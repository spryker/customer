<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Facade;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group FindCustomerByReferencePasswordPreservedTest
 * Add your own group annotations below this line
 */
class FindCustomerByReferencePasswordPreservedTest extends AbstractCustomerFacadeTest
{
    public function testFindCustomerByReferenceReturnsPasswordHash(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer(['password' => static::VALUE_VALID_PASSWORD]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()
            ->findCustomerByReference($customerTransfer->getCustomerReference());

        // Assert – mapper must not strip password; authentication depends on the hash being present
        $this->assertNotNull($customerResponseTransfer->getCustomerTransfer()->getPassword());
    }
}
