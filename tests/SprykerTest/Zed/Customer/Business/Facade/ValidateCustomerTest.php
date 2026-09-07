<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Facade;

use Generated\Shared\Transfer\CustomerErrorTransfer;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Orm\Zed\Customer\Persistence\SpyCustomerQuery;
use Spryker\Zed\Customer\CustomerDependencyProvider;
use Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerValidatorPluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group ValidateCustomerTest
 * Add your own group annotations below this line
 */
class ValidateCustomerTest extends AbstractCustomerFacadeTest
{
    /**
     * @var string
     */
    protected const PLUGIN_ERROR_MESSAGE = 'some.module.rejected.this.customer';

    public function testRegisterCustomerAcceptsAPasswordTokenRequestWithoutAStore(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomerTransfer()->setSendPasswordToken(true);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->registerCustomer($customerTransfer);

        // Assert
        $this->assertTrue(
            $customerResponseTransfer->getIsSuccess(),
            'A store is optional here because CustomerConfig::getCustomerPasswordRestoreTokenUrl() has a '
            . 'store-less format, and callers such as CompanyPage and ManualOrderEntryGui request a password '
            . 'token without naming one.',
        );
    }

    public function testRegisterCustomerAcceptsAPasswordTokenRequestThatNamesAStore(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomerTransfer()
            ->setSendPasswordToken(true)
            ->setStoreName($this->tester->haveStore()->getNameOrFail());

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->registerCustomer($customerTransfer);

        // Assert
        $this->assertTrue($customerResponseTransfer->getIsSuccess());
    }

    public function testRegisterCustomerSurfacesAnErrorRaisedByAValidatorPlugin(): void
    {
        // Arrange
        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_CUSTOMER_VALIDATOR,
            [$this->createRejectingValidatorPlugin()],
        );
        $customerTransfer = $this->tester->createTestCustomerTransfer();

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->registerCustomer($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertSame(
            static::PLUGIN_ERROR_MESSAGE,
            $customerResponseTransfer->getErrors()->offsetGet(0)->getMessage(),
        );
    }

    public function testRegisterCustomerDoesNotPersistWhenAValidatorPluginRejects(): void
    {
        // Arrange
        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_CUSTOMER_VALIDATOR,
            [$this->createRejectingValidatorPlugin()],
        );
        $customerTransfer = $this->tester->createTestCustomerTransfer();

        // Act
        $this->tester->getCustomerFacade()->registerCustomer($customerTransfer);

        // Assert: a rejected customer must not reach the database.
        $this->assertSame(
            0,
            SpyCustomerQuery::create()->filterByEmail($customerTransfer->getEmailOrFail())->count(),
            'A customer a validator plugin rejected must not be persisted.',
        );
    }

    public function testUpdateCustomerRunsTheValidatorPlugins(): void
    {
        // Arrange
        $customerResponseTransfer = $this->tester->getCustomerFacade()
            ->registerCustomer($this->tester->createTestCustomerTransfer());
        $customerTransfer = $customerResponseTransfer->getCustomerTransferOrFail();

        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_CUSTOMER_VALIDATOR,
            [$this->createRejectingValidatorPlugin()],
        );

        // Act
        $updateResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertFalse($updateResponseTransfer->getIsSuccess());
    }

    public function testRegisterCustomerCollectsErrorsFromEveryValidatorPlugin(): void
    {
        // Arrange
        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_CUSTOMER_VALIDATOR,
            [$this->createRejectingValidatorPlugin(), $this->createRejectingValidatorPlugin()],
        );

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()
            ->registerCustomer($this->tester->createTestCustomerTransfer());

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertCount(2, $customerResponseTransfer->getErrors());
    }

    protected function createRejectingValidatorPlugin(): CustomerValidatorPluginInterface
    {
        $customerValidatorPluginMock = $this->getMockBuilder(CustomerValidatorPluginInterface::class)->getMock();
        $customerValidatorPluginMock
            ->method('validate')
            ->willReturn(
                (new CustomerResponseTransfer())
                    ->setIsSuccess(false)
                    ->addError((new CustomerErrorTransfer())->setMessage(static::PLUGIN_ERROR_MESSAGE)),
            );

        return $customerValidatorPluginMock;
    }
}
