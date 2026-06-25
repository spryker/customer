<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Facade;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Business\Customer\Customer;
use Spryker\Zed\Customer\Business\CustomerBusinessFactory;
use Spryker\Zed\Customer\Business\CustomerFacade;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group GetCustomerSecureModeTest
 * Add your own group annotations below this line
 */
class GetCustomerSecureModeTest extends AbstractCustomerFacadeTest
{
    protected const string PASSWORD_HASH = '$2y$12$examplehashedpassword';

    public function testGetCustomerStripsPasswordHashByDefault(): void
    {
        $facade = $this->buildFacadeWithSecureAwareCustomerMock();

        $resultTransfer = $facade->getCustomer(new CustomerTransfer());

        $this->assertNull($resultTransfer->getPassword(), 'Password hash must be stripped in secure mode (default).');
    }

    public function testGetCustomerStripsPasswordHashWhenSecureModeExplicit(): void
    {
        $facade = $this->buildFacadeWithSecureAwareCustomerMock();

        $resultTransfer = $facade->getCustomer(new CustomerTransfer(), true);

        $this->assertNull($resultTransfer->getPassword(), 'Password hash must be stripped when isSecure=true.');
    }

    public function testGetCustomerReturnsPasswordHashWhenSecureModeDisabled(): void
    {
        $facade = $this->buildFacadeWithSecureAwareCustomerMock();

        $resultTransfer = $facade->getCustomer(new CustomerTransfer(), false);

        $this->assertSame(
            static::PASSWORD_HASH,
            $resultTransfer->getPassword(),
            'Password hash must be present when isSecure=false (authentication context).',
        );
    }

    protected function buildFacadeWithSecureAwareCustomerMock(): CustomerFacade
    {
        $passwordHash = static::PASSWORD_HASH;

        $customerMock = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $customerMock->method('get')
            ->willReturnCallback(function (CustomerTransfer $customerTransfer, bool $isSecure = true) use ($passwordHash): CustomerTransfer {
                $customerTransfer->setPassword($isSecure ? null : $passwordHash);

                return $customerTransfer;
            });

        $factoryMock = $this->getMockBuilder(CustomerBusinessFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $factoryMock->method('createCustomer')->willReturn($customerMock);

        $facade = new CustomerFacade();
        $facade->setFactory($factoryMock);

        return $facade;
    }
}
