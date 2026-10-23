<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Facade;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\CustomerDependencyProvider;
use Spryker\Zed\CustomerExtension\Dependency\Plugin\CustomerPreUpdatePluginInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group UpdateCustomerTest
 * Add your own group annotations below this line
 */
class UpdateCustomerTest extends AbstractCustomerFacadeTest
{
    /**
     * @var string
     */
    protected const TESTER_PASSWORD = '$2tester';

    /**
     * @var string
     */
    protected const TESTER_NEW_PASSWORD = '$3tester';

    /**
     * @var string
     */
    protected const TESTER_NAME = 'Tester';

    protected const string PRE_UPDATE_MESSAGE = 'customer.test.pre_update_message';

    protected const string PRE_UPDATE_MESSAGE_TYPE = 'info';

    protected const string PRE_UPDATE_MESSAGE_TYPE_ERROR = 'error';

    public function testUpdateCustomerNotUpdatesCustomerWhenPasswordLessThanMinLength(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_SHORT_PASSWORD);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

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

    public function testUpdateCustomerShouldNotUpdateCustomerWhenPasswordLongerThanMaxLength(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_LONG_PASSWORD);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

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

    /**
     * @uses \Spryker\Zed\Customer\CustomerConfig::getCustomerPasswordSequenceLimit()
     *
     * @return void
     */
    public function testUpdateCustomerNotUpdatesCustomerWhenPasswordHasTooLongSequence(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_SEQUENCE_TOO_LONG_PASSWORD);
        $this->tester->mockConfigMethod('getCustomerPasswordSequenceLimit', static::SEQUENCE_LIMIT_CUSTOMER_PASSWORD);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

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
    public function testUpdateCustomerNotUpdatesCustomerWhenPasswordInDenyList(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_DENY_LIST_PASSWORD);
        $this->tester->mockConfigMethod('getCustomerPasswordDenyList', [static::VALUE_DENY_LIST_PASSWORD]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(
            static::GLOSSARY_KEY_PASSWORD_POLICY_ERROR_DENY_LIST,
            $customerResponseTransfer,
        ));
    }

    /**
     * @uses \Spryker\Zed\Customer\CustomerConfig::getCustomerPasswordCharacterSet()
     *
     * @return void
     */
    public function testUpdateCustomerNotUpdatesCustomerWhenPasswordCharacterSetWrong(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_CHARACTER_SET_WRONG_PASSWORD);
        $this->tester->mockConfigMethod('getCustomerPasswordCharacterSet', static::CHARACTER_SET_REGEXP);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(
            static::GLOSSARY_KEY_PASSWORD_POLICY_ERROR_CHARACTER_SET,
            $customerResponseTransfer,
        ));
    }

    public function testUpdateCustomerShouldUpdateCustomerWhenPasswordHasCorrectLength(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_NEW_PASSWORD);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertTrue($customerResponseTransfer->getIsSuccess());
        $this->assertNotNull($customerResponseTransfer->getCustomerTransfer());
    }

    /**
     * @uses \Spryker\Zed\Customer\CustomerConfig::getCustomerPasswordAllowList()
     *
     * @return void
     */
    public function testUpdateCustomerUpdatesCustomerWhenPasswordInAllowList(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_SHORT_PASSWORD);
        $this->tester->mockConfigMethod('getCustomerPasswordAllowList', [static::VALUE_SHORT_PASSWORD]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertTrue($customerResponseTransfer->getIsSuccess());
        $this->assertNotNull($customerResponseTransfer->getCustomerTransfer());
    }

    /**
     * @uses \Spryker\Zed\Customer\CustomerConfig::getCustomerPasswordCharacterSet()
     *
     * @return void
     */
    public function testUpdateCustomerPasswordNotUpdatesCustomerPasswordWhenPasswordCharacterSetWrong(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
        ]);
        $customerTransfer->setPassword(static::VALUE_VALID_PASSWORD)
            ->setNewPassword(static::VALUE_CHARACTER_SET_WRONG_PASSWORD);
        $this->tester->mockConfigMethod('getCustomerPasswordCharacterSet', static::CHARACTER_SET_REGEXP);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(
            static::GLOSSARY_KEY_PASSWORD_POLICY_ERROR_CHARACTER_SET,
            $customerResponseTransfer,
        ));
    }

    public function testUpdateCustomer(): void
    {
        $customerTransfer = $this->tester->createTestCustomer();
        $customerTransfer->setPassword(null);
        $customerTransfer->setLastName(static::TESTER_NAME);
        $customerResponse = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);
        $this->assertNotNull($customerResponse);
        $this->assertTrue($customerResponse->getIsSuccess());
        $customerTransfer = $customerResponse->getCustomerTransfer();
        $this->assertSame(static::TESTER_NAME, $customerTransfer->getLastName());
    }

    public function testUpdateCustomerAddsPreUpdatePluginMessageWithMessageType(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomer();
        $customerTransfer->setPassword(null);

        $customerPreUpdatePluginMock = $this->getMockBuilder(CustomerPreUpdatePluginInterface::class)->getMock();
        $customerPreUpdatePluginMock
            ->method('preUpdate')
            ->willReturnCallback(fn (CustomerTransfer $customerTransfer): CustomerTransfer => $customerTransfer
                ->setMessage(static::PRE_UPDATE_MESSAGE)
                ->setMessageType(static::PRE_UPDATE_MESSAGE_TYPE));
        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_CUSTOMER_PRE_UPDATE, [$customerPreUpdatePluginMock]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertCount(1, $customerResponseTransfer->getMessages());
        $this->assertSame(static::PRE_UPDATE_MESSAGE, $customerResponseTransfer->getMessages()->offsetGet(0)->getValue());
        $this->assertSame(static::PRE_UPDATE_MESSAGE_TYPE, $customerResponseTransfer->getMessages()->offsetGet(0)->getType());
    }

    public function testUpdateCustomerDoesNotRepeatPreUpdatePluginMessageForNextPluginWithoutMessage(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomer();
        $customerTransfer->setPassword(null);

        $customerPreUpdatePluginWithMessageMock = $this->getMockBuilder(CustomerPreUpdatePluginInterface::class)->getMock();
        $customerPreUpdatePluginWithMessageMock
            ->method('preUpdate')
            ->willReturnCallback(fn (CustomerTransfer $customerTransfer): CustomerTransfer => $customerTransfer
                ->setMessage(static::PRE_UPDATE_MESSAGE)
                ->setMessageType(static::PRE_UPDATE_MESSAGE_TYPE));
        $customerPreUpdatePluginWithoutMessageMock = $this->getMockBuilder(CustomerPreUpdatePluginInterface::class)->getMock();
        $customerPreUpdatePluginWithoutMessageMock
            ->method('preUpdate')
            ->willReturnArgument(0);
        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_CUSTOMER_PRE_UPDATE, [
            $customerPreUpdatePluginWithMessageMock,
            $customerPreUpdatePluginWithoutMessageMock,
        ]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertCount(1, $customerResponseTransfer->getMessages());
    }

    public function testUpdateCustomerReturnsErrorAndDoesNotSaveCustomerWhenPreUpdatePluginSetsErrorMessage(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomer();
        $customerTransfer
            ->setPassword(null)
            ->setLastName(static::TESTER_NAME);

        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_CUSTOMER_PRE_UPDATE, [
            $this->createPreUpdatePluginMock(static::PRE_UPDATE_MESSAGE, static::PRE_UPDATE_MESSAGE_TYPE_ERROR),
        ]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertCount(1, $customerResponseTransfer->getErrors());
        $this->assertTrue($this->hasErrorInCustomerResponseTransfer(static::PRE_UPDATE_MESSAGE, $customerResponseTransfer));
        $this->assertCount(0, $customerResponseTransfer->getMessages());
        $this->assertNotSame(
            static::TESTER_NAME,
            $this->tester->getCustomerFacade()->getCustomer((new CustomerTransfer())->setIdCustomer($customerTransfer->getIdCustomer()))->getLastName(),
        );
    }

    public function testUpdateCustomerExecutesNextPreUpdatePluginWhenPreUpdatePluginSetsErrorMessage(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomer();
        $customerTransfer->setPassword(null);

        $customerPreUpdatePluginAfterErrorMock = $this->getMockBuilder(CustomerPreUpdatePluginInterface::class)->getMock();
        $customerPreUpdatePluginAfterErrorMock
            ->expects($this->once())
            ->method('preUpdate')
            ->willReturnArgument(0);
        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_CUSTOMER_PRE_UPDATE, [
            $this->createPreUpdatePluginMock(static::PRE_UPDATE_MESSAGE, static::PRE_UPDATE_MESSAGE_TYPE_ERROR),
            $customerPreUpdatePluginAfterErrorMock,
        ]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertCount(1, $customerResponseTransfer->getErrors());
    }

    public function testUpdateCustomerReturnsFailureWithoutErrorWhenPreUpdatePluginSetsErrorTypeWithoutMessage(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomer();
        $customerTransfer
            ->setPassword(null)
            ->setLastName(static::TESTER_NAME);

        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_CUSTOMER_PRE_UPDATE, [
            $this->createPreUpdatePluginMock(null, static::PRE_UPDATE_MESSAGE_TYPE_ERROR),
        ]);

        // Act
        $customerResponseTransfer = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertFalse($customerResponseTransfer->getIsSuccess());
        $this->assertCount(0, $customerResponseTransfer->getErrors());
        $this->assertCount(0, $customerResponseTransfer->getMessages());
        $this->assertNotSame(
            static::TESTER_NAME,
            $this->tester->getCustomerFacade()->getCustomer((new CustomerTransfer())->setIdCustomer($customerTransfer->getIdCustomer()))->getLastName(),
        );
    }

    public function testUpdateCustomerFailsWhenInvalidEmailFormatIsProvided(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomer();
        $this->mockUtilValidateService(false);

        // Act
        $customerResponse = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertFalse($customerResponse->getIsSuccess());
    }

    public function testUpdateCustomerUpdatesValidEmail(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomer();
        $customerTransfer->setPassword('other password');
        $this->mockUtilValidateService(true);

        // Act
        $customerResponse = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);

        // Assert
        $this->assertTrue($customerResponse->getIsSuccess());
    }

    public function testUpdateCustomerWithProvidedPasswordShouldSuccessWhenPasswordAreProvided(): void
    {
        // Arrange
        $customerTransfer = $this->tester->createTestCustomer();
        $customerTransfer->setNewPassword(static::TESTER_NEW_PASSWORD);
        $customerTransfer->setPassword(static::TESTER_PASSWORD);
        $customerTransfer->setLastName(static::TESTER_NAME);

        // Act
        $customerResponse = $this->tester->getCustomerFacade()->updateCustomer($customerTransfer);
        $customerTransfer = $customerResponse->getCustomerTransfer();

        // Assert
        $this->assertTrue($customerResponse->getIsSuccess(), 'Customer response must be successful.');
        $this->assertSame(static::TESTER_NAME, $customerTransfer->getLastName(), 'Last name was not saved.');

        // Password is not returned in the update response to avoid exposing password hashes.
        // Reload the customer from persistence with isSecure=false to verify the new password hash.
        $customerWithPassword = $this->tester->getCustomerFacade()->getCustomer($customerTransfer, false);
        $this->tester->assertPasswordsEqual($customerWithPassword->getPassword(), static::TESTER_NEW_PASSWORD);
    }

    protected function createPreUpdatePluginMock(?string $message, string $messageType): CustomerPreUpdatePluginInterface
    {
        $customerPreUpdatePluginMock = $this->getMockBuilder(CustomerPreUpdatePluginInterface::class)->getMock();
        $customerPreUpdatePluginMock
            ->method('preUpdate')
            ->willReturnCallback(fn (CustomerTransfer $customerTransfer): CustomerTransfer => $customerTransfer
                ->setMessage($message)
                ->setMessageType($messageType));

        return $customerPreUpdatePluginMock;
    }
}
