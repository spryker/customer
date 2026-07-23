<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\Customer\Session;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\Session\CustomerSession;
use Spryker\Client\Session\SessionClientInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group Customer
 * @group Session
 * @group CustomerSessionTest
 * Add your own group annotations below this line
 */
class CustomerSessionTest extends Unit
{
    public function testSetCustomerNeverStoresPassword(): void
    {
        // Arrange
        $customerTransfer = $this->createCustomerTransfer();
        $storedValue = null;
        $customerSession = new CustomerSession($this->createSessionClientMock($storedValue));

        // Act
        $customerSession->setCustomer($customerTransfer);

        // Assert
        $this->assertArrayNotHasKey(CustomerTransfer::PASSWORD, $storedValue->modifiedToArray(true, true));
        $this->assertSame('sonia@acme.com', $storedValue->getEmail());
    }

    public function testSetCustomerRawDataNeverStoresPassword(): void
    {
        // Arrange
        $customerTransfer = $this->createCustomerTransfer();
        $storedValue = null;
        $customerSession = new CustomerSession($this->createSessionClientMock($storedValue));

        // Act
        $customerSession->setCustomerRawData($customerTransfer);

        // Assert
        $this->assertNull($storedValue->getPassword());
        $this->assertSame('sonia@acme.com', $storedValue->getEmail());
    }

    public function testSetCustomerReturnsOriginalTransfer(): void
    {
        // Arrange
        $customerTransfer = $this->createCustomerTransfer();
        $storedValue = null;
        $customerSession = new CustomerSession($this->createSessionClientMock($storedValue));

        // Act
        $result = $customerSession->setCustomer($customerTransfer);

        // Assert
        $this->assertSame($customerTransfer, $result);
        $this->assertNotSame($customerTransfer, $storedValue);
    }

    protected function createSessionClientMock(mixed &$storedValue): SessionClientInterface
    {
        $sessionClientMock = $this->createMock(SessionClientInterface::class);
        $sessionClientMock->method('set')->willReturnCallback(function (string $key, mixed $value) use (&$storedValue): void {
            $storedValue = $value;
        });

        return $sessionClientMock;
    }

    protected function createCustomerTransfer(): CustomerTransfer
    {
        return (new CustomerTransfer())->fromArray([
            CustomerTransfer::EMAIL => 'sonia@acme.com',
            CustomerTransfer::CUSTOMER_REFERENCE => 'DE--21',
            CustomerTransfer::PASSWORD => 'secret-hash',
            CustomerTransfer::NEW_PASSWORD => 'transient-value',
        ], true);
    }
}
