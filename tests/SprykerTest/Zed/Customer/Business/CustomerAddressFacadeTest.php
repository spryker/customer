<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Business\CustomerBusinessFactory;
use Spryker\Zed\Customer\Business\CustomerFacade;
use Spryker\Zed\Customer\Business\Exception\AddressNotFoundException;
use Spryker\Zed\Customer\CustomerDependencyProvider;
use Spryker\Zed\Customer\Dependency\Facade\CustomerToMailInterface;
use Spryker\Zed\Kernel\Container;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group CustomerAddressFacadeTest
 * Add your own group annotations below this line
 */
class CustomerAddressFacadeTest extends Unit
{
    /**
     * @var string
     */
    protected const TESTER_EMAIL = 'tester@spryker.com';

    /**
     * @var string
     */
    protected const TESTER_PASSWORD = 'testpassworD1$';

    /**
     * @var string
     */
    protected const TESTER_NAME = 'Tester';

    /**
     * @var string
     */
    protected const TESTER_CITY = 'Testcity';

    /**
     * @var string
     */
    protected const TESTER_ADDRESS1 = 'Testerstreet 23';

    /**
     * @var string
     */
    protected const TESTER_ZIP_CODE = '42';

    /**
     * @var int
     */
    protected const TESTER_FK_COUNTRY_GERMANY = 60;

    /**
     * @var \SprykerTest\Zed\Customer\CustomerBusinessTester
     */
    protected $tester;

    /**
     * @var \Spryker\Zed\Customer\Business\CustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * @var \Spryker\Zed\Kernel\Container
     */
    protected $businessLayerDependencies;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->customerFacade = new CustomerFacade();
        $this->customerFacade->setFactory($this->getBusinessFactory());
    }

    /**
     * @return void
     */
    public function testGetAddressesHasCountry(): void
    {
        // Assign
        $customerTransfer = $this->tester->haveCustomer();
        $this->customerFacade->createAddressAndUpdateCustomerDefaultAddresses($customerTransfer->getShippingAddress()[0]);

        // Act
        $addressesTransfer = $this->customerFacade->getAddresses($customerTransfer);
        $addressTransfer = $addressesTransfer->getAddresses()[0];

        // Assert
        $this->assertSame(static::TESTER_FK_COUNTRY_GERMANY, $addressTransfer->getCountry()->getIdCountry());
    }

    /**
     * @return void
     */
    public function testDeleteAddress(): void
    {
        $customerTransfer = $this->createCustomerWithAddress();

        $addresses = $customerTransfer->getAddresses()->getAddresses();
        $addressTransfer = $addresses[0];

        $deletedAddress = $this->customerFacade->deleteAddress($addressTransfer);
        $this->assertNotNull($deletedAddress);
    }

    /**
     * @return void
     */
    public function testDeleteDefaultAddress(): void
    {
        $customerTransfer = $this->createCustomerWithAddress();

        $addresses = $customerTransfer->getAddresses()->getAddresses();
        $addressTransfer = $addresses[0];

        $this->customerFacade->setDefaultBillingAddress($addressTransfer);

        $deletedAddress = $this->customerFacade->deleteAddress($addressTransfer);
        $this->assertNotNull($deletedAddress);

        $customerTransfer = $this->getTestCustomerTransfer($customerTransfer);
        $this->assertNull($customerTransfer->getDefaultBillingAddress());
    }

    /**
     * @return void
     */
    public function testSetDefaultBillingAddress(): void
    {
        $customerTransfer = $this->createTestCustomer();
        $addressTransfer = new AddressTransfer();
        $addressTransfer->setEmail($customerTransfer->getEmail());
        $addressTransfer->setFirstName(static::TESTER_NAME);
        $addressTransfer->setLastName(static::TESTER_NAME);
        $addressTransfer->setFkCustomer($customerTransfer->getIdCustomer());
        $addressTransfer = $this->customerFacade->createAddress($addressTransfer);
        $this->assertNotNull($addressTransfer);
        $customerTransfer = $this->getTestCustomerTransfer($customerTransfer);

        $addresses = $customerTransfer->getAddresses()->getAddresses();
        $addressTransfer = $addresses[0];

        $isSuccess = $this->customerFacade->setDefaultBillingAddress($addressTransfer);
        $this->assertTrue($isSuccess);
    }

    /**
     * @return void
     */
    public function testGetDefaultShippingAddress(): void
    {
        $customerTransfer = $this->createTestCustomer();
        $this->createTestAddress($customerTransfer);
        $addressTransfer = $this->customerFacade->getDefaultShippingAddress($customerTransfer);
        $this->assertNotNull($addressTransfer);
    }

    /**
     * @return void
     */
    public function testGetDefaultBillingAddress(): void
    {
        $customerTransfer = $this->createTestCustomer();
        $this->createTestAddress($customerTransfer);
        $addressTransfer = $this->customerFacade->getDefaultBillingAddress($customerTransfer);
        $this->assertNotNull($addressTransfer);
    }

    /**
     * @return void
     */
    public function testRenderAddress(): void
    {
        $customerTransfer = $this->createTestCustomer();
        $addressTransfer = $this->createTestAddress($customerTransfer);
        $addressTransfer = $this->customerFacade->getAddress($addressTransfer);
        $renderedAddress = $this->customerFacade->renderAddress($addressTransfer);
        $this->assertNotNull($renderedAddress);
    }

    /**
     * @return void
     */
    public function testNewAddress(): void
    {
        $customerTransfer = $this->createTestCustomer();
        $addressTransfer = new AddressTransfer();
        $addressTransfer->setEmail($customerTransfer->getEmail());
        $addressTransfer->setFirstName(static::TESTER_NAME);
        $addressTransfer->setLastName(static::TESTER_NAME);
        $addressTransfer = $this->customerFacade->createAddress($addressTransfer);
        $this->assertNotNull($addressTransfer);
    }

    /**
     * @return void
     */
    public function testUpdateAddress(): void
    {
        $customerTransfer = $this->createTestCustomer();
        $addressTransfer = new AddressTransfer();
        $addressTransfer->setEmail($customerTransfer->getEmail());
        $addressTransfer->setFirstName(static::TESTER_NAME);
        $addressTransfer->setLastName(static::TESTER_NAME);
        $addressTransfer = $this->customerFacade->createAddress($addressTransfer);
        $this->assertNotNull($addressTransfer);

        $customerTransfer = $this->getTestCustomerTransfer($customerTransfer);

        $addresses = $customerTransfer->getAddresses()->getAddresses();
        $addressTransfer = $addresses[0];

        $addressTransfer->setCity(static::TESTER_CITY);
        $addressTransfer->setFkCustomer($customerTransfer->getIdCustomer());
        $addressTransfer = $this->customerFacade->updateAddress($addressTransfer);
        $this->assertNotNull($addressTransfer);
        $this->assertSame(static::TESTER_CITY, $addressTransfer->getCity());
    }

    /**
     * @return void
     */
    public function testSetDefaultShippingAddress(): void
    {
        $customerTransfer = $this->createTestCustomer();
        $addressTransfer = new AddressTransfer();
        $addressTransfer->setEmail($customerTransfer->getEmail());
        $addressTransfer->setFirstName(static::TESTER_NAME);
        $addressTransfer->setLastName(static::TESTER_NAME);
        $addressTransfer->setFkCustomer($customerTransfer->getIdCustomer());
        $addressTransfer = $this->customerFacade->createAddress($addressTransfer);
        $this->assertNotNull($addressTransfer);
        $customerTransfer = $this->getTestCustomerTransfer($customerTransfer);

        $addresses = $customerTransfer->getAddresses()->getAddresses();
        $addressTransfer = $addresses[0];

        $isSuccess = $this->customerFacade->setDefaultShippingAddress($addressTransfer);
        $this->assertTrue($isSuccess);
    }

    /**
     * @return void
     */
    public function testDeleteCustomerWithDefaultAddresses(): void
    {
        // Arrange
        $customerTransfer = $this->createCustomerWithAddress();

        $addresses = $customerTransfer->getAddresses()->getAddresses();
        $addressTransfer = $addresses[0];

        $this->customerFacade->setDefaultBillingAddress($addressTransfer);
        $this->customerFacade->setDefaultShippingAddress($addressTransfer);

        // Assert
        $this->expectException(AddressNotFoundException::class);

        // Act
        $this->customerFacade->deleteCustomer($customerTransfer);
        $this->customerFacade->getAddress($addressTransfer);
    }

    /**
     * @return void
     */
    public function testCheckAddressExistsByIdCustomerAddressShouldReturnTrueOnExistedAddress(): void
    {
        $customerTransfer = $this->createCustomerWithAddress();

        $addresses = $customerTransfer->getAddresses()->getAddresses();
        $addressTransfer = $addresses[0];
        $idCustomerAddress = $addressTransfer->getIdCustomerAddress();

        $result = $this->customerFacade->findCustomerAddressById($idCustomerAddress);

        $this->assertNotNull($result);
    }

    /**
     * @return void
     */
    public function testCheckAddressExistsByIdCustomerAddressShouldReturnFalseOnNonExistedAddress(): void
    {
        $customerTransfer = $this->createCustomerWithAddress();

        $addresses = $customerTransfer->getAddresses()->getAddresses();
        $addressTransfer = $addresses[0];
        $idCustomerAddress = $addressTransfer->getIdCustomerAddress();
        $this->customerFacade->deleteAddress($addressTransfer);

        $result = $this->customerFacade->findCustomerAddressById($idCustomerAddress);

        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function testGetAddressReturnsAddressByProvidedAddressIdWhenCustomerIdNotSpecified(): void
    {
        // Arrange
        $customerTransfer = $this->createTestCustomer();
        $addressTransfer = $this->createTestAddress($customerTransfer);
        $addressTransfer->setCustomerId(null);

        // Act
        $addressTransfer = $this->customerFacade->getAddress($addressTransfer);

        // Assert
        $this->assertSame($customerTransfer->getIdCustomer(), $addressTransfer->getFkCustomer());
    }

    /**
     * @return void
     */
    public function testGetAddressReturnsFirstAddressOfCustomerWhenNoDefaultAddress(): void
    {
        // Arrange
        $customerTransfer = $this->createTestCustomer();
        $addressTransfer = $this->createTestAddress($customerTransfer);

        // Act
        $addressTransfer = $this->customerFacade->getAddress($addressTransfer);

        // Assert
        $this->assertSame($customerTransfer->getIdCustomer(), $addressTransfer->getFkCustomer());
    }

    /**
     * @return void
     */
    public function testGetAddressTrowsAddressNotFoundExceptionWhenAddressIdAndCustomerIdNotPassed(): void
    {
        // Arrange
        $addressTransfer = new AddressTransfer();

        // Assert
        $this->expectException(AddressNotFoundException::class);
        $this->expectExceptionMessage('Address not found for ID `` (and optional customer ID ``).');

        // Act
        $this->customerFacade->getAddress($addressTransfer);
    }

    /**
     * @return void
     */
    public function testGetDefaultBillingAddressReturnsFirstAddressOfCustomerWhenNoDefaultAddress(): void
    {
        // Arrange
        $customerTransfer = $this->createCustomerWithAddress();

        // Act
        $addressTransfer = $this->customerFacade->getDefaultBillingAddress($customerTransfer);

        // Assert
        $this->assertSame($customerTransfer->getIdCustomer(), $addressTransfer->getFkCustomer());
    }

    /**
     * @return void
     */
    public function testGetDefaultBillingAddressTrowsAddressNotFoundExceptionWhenCustomerDontHaveAddress(): void
    {
        // Arrange
        $customerTransfer = $this->createTestCustomer();

        // Assert
        $this->expectException(AddressNotFoundException::class);
        $this->expectExceptionMessage("Address not found for ID `` (and optional customer ID `{$customerTransfer->getIdCustomer()}`).");

        // Act
        $this->customerFacade->getDefaultBillingAddress($customerTransfer);
    }

    /**
     * @return void
     */
    public function testGetDefaultShippingAddressReturnsFirstAddressOfCustomerWhenNoDefaultAddress(): void
    {
        // Arrange
        $customerTransfer = $this->createCustomerWithAddress();

        // Act
        $addressTransfer = $this->customerFacade->getDefaultShippingAddress($customerTransfer);

        // Assert
        $this->assertSame($customerTransfer->getIdCustomer(), $addressTransfer->getFkCustomer());
    }

    /**
     * @return void
     */
    public function testGetDefaultShippingAddressTrowsAddressNotFoundExceptionWhenCustomerDontHaveAddress(): void
    {
        // Arrange
        $customerTransfer = $this->createTestCustomer();

        // Assert
        $this->expectException(AddressNotFoundException::class);
        $this->expectExceptionMessage("Address not found for ID `` (and optional customer ID `{$customerTransfer->getIdCustomer()}`).");

        // Act
        $this->customerFacade->getDefaultShippingAddress($customerTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function createCustomerWithAddress(): CustomerTransfer
    {
        $customerTransfer = $this->createTestCustomer();
        $addressTransfer = new AddressTransfer();
        $addressTransfer->setEmail($customerTransfer->getEmail());
        $addressTransfer->setFirstName(static::TESTER_NAME);
        $addressTransfer->setLastName(static::TESTER_NAME);
        $addressTransfer->setFkCustomer($customerTransfer->getIdCustomer());
        $addressTransfer = $this->customerFacade->createAddress($addressTransfer);
        $this->assertNotNull($addressTransfer);

        return $this->getTestCustomerTransfer($customerTransfer);
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function createTestCustomer(): CustomerTransfer
    {
        $customerTransfer = $this->createTestCustomerTransfer();
        $customerResponseTransfer = $this->customerFacade->registerCustomer($customerTransfer);
        $customerTransfer = $this->customerFacade->confirmRegistration($customerResponseTransfer->getCustomerTransfer());

        return $customerTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function createTestCustomerTransfer(): CustomerTransfer
    {
        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setEmail(static::TESTER_EMAIL);
        $customerTransfer->setPassword(static::TESTER_PASSWORD);

        return $customerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function getTestCustomerTransfer(CustomerTransfer $customerTransfer): CustomerTransfer
    {
        $customerTransfer = $this->customerFacade->getCustomer($customerTransfer);

        return $customerTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function createTestAddress(CustomerTransfer $customerTransfer): AddressTransfer
    {
        $addressTransfer = $this->createTestAddressTransfer($customerTransfer);
        $addressTransfer = $this->customerFacade->createAddress($addressTransfer);

        return $addressTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function createTestAddressTransfer(CustomerTransfer $customerTransfer): AddressTransfer
    {
        $addressTransfer = new AddressTransfer();
        $addressTransfer->setFkCustomer($customerTransfer->getIdCustomer());
        $addressTransfer->setEmail(static::TESTER_EMAIL);
        $addressTransfer->setFirstName(static::TESTER_NAME);
        $addressTransfer->setLastName(static::TESTER_NAME);
        $addressTransfer->setAddress1(static::TESTER_ADDRESS1);
        $addressTransfer->setCity(static::TESTER_CITY);
        $addressTransfer->setZipCode(static::TESTER_ZIP_CODE);

        return $addressTransfer;
    }

    /**
     * @return \Spryker\Zed\Customer\Business\CustomerBusinessFactory
     */
    protected function getBusinessFactory(): CustomerBusinessFactory
    {
        $customerBusinessFactory = new CustomerBusinessFactory();
        $customerBusinessFactory->setContainer($this->getContainer());

        return $customerBusinessFactory;
    }

    /**
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function getContainer(): Container
    {
        $dependencyProvider = new CustomerDependencyProvider();
        $this->businessLayerDependencies = new Container();

        $dependencyProvider->provideBusinessLayerDependencies($this->businessLayerDependencies);

        $this->businessLayerDependencies[CustomerDependencyProvider::FACADE_MAIL] = $this->getMockBuilder(CustomerToMailInterface::class)->getMock();

        return $this->businessLayerDependencies;
    }
}
