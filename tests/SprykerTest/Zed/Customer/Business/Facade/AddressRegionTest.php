<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\Customer\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressConditionsTransfer;
use Generated\Shared\Transfer\AddressCriteriaTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\RegionTransfer;
use Spryker\Zed\Customer\Business\CustomerFacadeInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group AddressRegionTest
 * Add your own group annotations below this line
 */
class AddressRegionTest extends Unit
{
    protected const string ISO_2_CODE = 'DE';

    protected const string REGION_CODE = 'DE-BE';

    protected const string REGION_CODE_SAME_COUNTRY = 'DE-BY';

    /**
     * @var \SprykerTest\Zed\Customer\CustomerBusinessTester
     */
    protected $tester;

    public function testCreateAddressStoresTheRegionReferenceForTheGivenRegionCode(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $regionTransfer = $this->haveRegion(static::REGION_CODE, static::ISO_2_CODE);

        // Act
        $addressTransfer = $this->getFacade()->createAddress($this->createAddressTransfer($customerTransfer));

        // Assert
        $this->assertSame(
            $regionTransfer->getIdRegionOrFail(),
            $addressTransfer->getFkRegion(),
            'The region code has to reach the row as fk_region, which is the only region the row stores.',
        );
    }

    public function testUpdateAddressStoresTheRegionReferenceForTheGivenRegionCode(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $this->haveRegion(static::REGION_CODE, static::ISO_2_CODE);
        $regionTransfer = $this->haveRegion(static::REGION_CODE_SAME_COUNTRY, static::ISO_2_CODE);

        $addressTransfer = $this->getFacade()->createAddress($this->createAddressTransfer($customerTransfer));

        // Act
        $updatedAddressTransfer = $this->getFacade()->updateAddress(
            $addressTransfer->setRegion(static::REGION_CODE_SAME_COUNTRY),
        );

        // Assert
        $this->assertSame(
            $regionTransfer->getIdRegionOrFail(),
            $updatedAddressTransfer->getFkRegion(),
            'An update has to move the row to the region the new code names.',
        );
    }

    public function testGetAddressCollectionReadsTheRegionCodeBackFromTheStoredReference(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();
        $this->haveRegion(static::REGION_CODE, static::ISO_2_CODE);

        $createdAddressTransfer = $this->getFacade()->createAddress($this->createAddressTransfer($customerTransfer));

        // Act
        $addressCollectionTransfer = $this->getFacade()->getAddressCollection(
            (new AddressCriteriaTransfer())->setAddressConditions(
                (new AddressConditionsTransfer())->addIdCustomerAddress($createdAddressTransfer->getIdCustomerAddressOrFail()),
            ),
        );

        // Assert
        $this->assertSame(
            static::REGION_CODE,
            $addressCollectionTransfer->getAddresses()->offsetGet(0)->getRegion(),
            'A read must report the region by the same code a write accepts, otherwise it cannot round-trip.',
        );
    }

    public function testGetAddressCollectionLeavesTheRegionEmptyForAnAddressWithoutOne(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();

        $createdAddressTransfer = $this->getFacade()->createAddress(
            $this->createAddressTransfer($customerTransfer)->setRegion(null),
        );

        // Act
        $addressCollectionTransfer = $this->getFacade()->getAddressCollection(
            (new AddressCriteriaTransfer())->setAddressConditions(
                (new AddressConditionsTransfer())->addIdCustomerAddress($createdAddressTransfer->getIdCustomerAddressOrFail()),
            ),
        );

        // Assert
        $this->assertNull($addressCollectionTransfer->getAddresses()->offsetGet(0)->getRegion());
    }

    public function testValidateAddressAcceptsAnAddressWhenNoPluginIsRegistered(): void
    {
        // Arrange
        $addressTransfer = (new AddressTransfer())
            ->setIso2Code(static::ISO_2_CODE)
            ->setFirstName('Sonia')
            ->setLastName('Wagner');

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
        $this->assertSame($addressTransfer, $addressResponseTransfer->getAddress());
    }

    protected function haveRegion(string $regionCode, string $countryIso2Code): RegionTransfer
    {
        $countryTransfer = $this->tester->getLocator()->country()->facade()
            ->getCountryByIso2Code($countryIso2Code);

        return $this->tester->haveRegion([
            RegionTransfer::ISO2_CODE => $regionCode,
            RegionTransfer::FK_COUNTRY => $countryTransfer->getIdCountryOrFail(),
        ]);
    }

    protected function createAddressTransfer(CustomerTransfer $customerTransfer): AddressTransfer
    {
        return (new AddressTransfer())
            ->setFkCustomer($customerTransfer->getIdCustomerOrFail())
            ->setEmail($customerTransfer->getEmailOrFail())
            ->setFirstName('Spencor')
            ->setLastName('Hopkin')
            ->setIso2Code(static::ISO_2_CODE)
            ->setRegion(static::REGION_CODE);
    }

    protected function getFacade(): CustomerFacadeInterface
    {
        /** @var \Spryker\Zed\Customer\Business\CustomerFacadeInterface $customerFacade */
        $customerFacade = $this->tester->getLocator()->customer()->facade();

        return $customerFacade;
    }
}
