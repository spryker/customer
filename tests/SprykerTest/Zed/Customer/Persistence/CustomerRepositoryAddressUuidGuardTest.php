<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerTest\Zed\Customer\Persistence;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressConditionsTransfer;
use Generated\Shared\Transfer\AddressCriteriaTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerAddressTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomerAddressQuery;
use Spryker\Zed\Customer\Persistence\CustomerPersistenceFactory;
use SprykerTest\Zed\Customer\Persistence\Fixtures\AddressUuidGuardProbe;
use SprykerTest\Zed\Customer\Persistence\Fixtures\AddressUuidGuardProbeWithoutUuidColumn;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Persistence
 * @group CustomerRepositoryAddressUuidGuardTest
 * Add your own group annotations below this line
 */
class CustomerRepositoryAddressUuidGuardTest extends Unit
{
    protected const string UUID = '5caa05f5-41f5-5e6c-a254-07d7887fb4e9';

    protected const int ID_CUSTOMER = 1;

    public function testKeepsTheOtherConditionsWhenTheUuidConditionCannotBeApplied(): void
    {
        // Arrange: a uuid the query cannot filter on must not cost the customer scope as well.
        $addressUuidGuardProbe = $this->createProbeWithoutUuidColumn();

        $addressCriteriaTransfer = (new AddressCriteriaTransfer())
            ->setAddressConditions(
                (new AddressConditionsTransfer())
                    ->addUuid(static::UUID)
                    ->addIdCustomer(static::ID_CUSTOMER),
            );

        // Act
        $query = $addressUuidGuardProbe->exposeBuildAddressQueryByConditions($addressCriteriaTransfer);

        // Assert
        $params = [];

        $this->assertStringContainsString(
            SpyCustomerAddressTableMap::COL_FK_CUSTOMER,
            $query->createSelectSql($params),
            'The customer condition must survive a uuid condition that cannot be applied.',
        );
    }

    public function testBuildsTheQueryWhenTheUuidConditionCanBeApplied(): void
    {
        // Arrange
        $addressUuidGuardProbe = $this->createProbe();

        // Act
        $query = $addressUuidGuardProbe->exposeBuildAddressQueryByConditions($this->createCriteriaWithUuid());

        // Assert
        $this->assertInstanceOf(
            SpyCustomerAddressQuery::class,
            $query,
            'This suite runs with the address uuid schema extension installed, so the filter must be usable.',
        );
    }

    public function testBuildsTheQueryForNonUuidConditionsWithoutTheUuidColumn(): void
    {
        // Arrange
        $addressUuidGuardProbe = $this->createProbeWithoutUuidColumn();

        $addressCriteriaTransfer = (new AddressCriteriaTransfer())
            ->setAddressConditions((new AddressConditionsTransfer())->addIdCustomer(static::ID_CUSTOMER));

        // Act
        $query = $addressUuidGuardProbe->exposeBuildAddressQueryByConditions($addressCriteriaTransfer);

        // Assert
        $this->assertInstanceOf(SpyCustomerAddressQuery::class, $query);
    }

    public function testBuildsTheQueryWhenNoConditionsAreSet(): void
    {
        // Arrange
        $addressUuidGuardProbe = $this->createProbeWithoutUuidColumn();

        // Act
        $query = $addressUuidGuardProbe->exposeBuildAddressQueryByConditions(new AddressCriteriaTransfer());

        // Assert
        $this->assertInstanceOf(SpyCustomerAddressQuery::class, $query);
    }

    protected function createCriteriaWithUuid(): AddressCriteriaTransfer
    {
        return (new AddressCriteriaTransfer())
            ->setAddressConditions((new AddressConditionsTransfer())->addUuid(static::UUID));
    }

    protected function createProbe(): AddressUuidGuardProbe
    {
        /** @var \SprykerTest\Zed\Customer\Persistence\Fixtures\AddressUuidGuardProbe $addressUuidGuardProbe */
        $addressUuidGuardProbe = (new AddressUuidGuardProbe())->setFactory(new CustomerPersistenceFactory());

        return $addressUuidGuardProbe;
    }

    protected function createProbeWithoutUuidColumn(): AddressUuidGuardProbeWithoutUuidColumn
    {
        /** @var \SprykerTest\Zed\Customer\Persistence\Fixtures\AddressUuidGuardProbeWithoutUuidColumn $addressUuidGuardProbe */
        $addressUuidGuardProbe = (new AddressUuidGuardProbeWithoutUuidColumn())
            ->setFactory(new CustomerPersistenceFactory());

        return $addressUuidGuardProbe;
    }
}
