<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerTest\Zed\Customer\Persistence;

use Codeception\Test\Unit;
use Spryker\Zed\Customer\CustomerConfig;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Persistence
 * @group CustomerRepositorySortableFieldsTest
 * Add your own group annotations below this line
 */
class CustomerRepositorySortableFieldsTest extends Unit
{
    protected const string TABLE_CUSTOMER = 'spy_customer.';

    protected const string TABLE_CUSTOMER_ADDRESS = 'spy_customer_address.';

    public function testEveryCustomerSortableFieldResolvesToAQualifiedColumn(): void
    {
        // Arrange
        $sortableFieldMap = (new CustomerConfig())->getCustomerCollectionSortableFieldMap();

        // Assert
        $this->assertNotEmpty($sortableFieldMap);

        foreach ($sortableFieldMap as $field => $column) {
            $this->assertStringStartsWith(
                static::TABLE_CUSTOMER,
                $column,
                sprintf('Sortable field "%s" must resolve to a qualified spy_customer column.', $field),
            );
        }
    }

    public function testEveryAddressSortableFieldResolvesToAQualifiedColumn(): void
    {
        // Arrange
        $sortableFieldMap = (new CustomerConfig())->getAddressCollectionSortableFieldMap();

        // Assert
        $this->assertNotEmpty($sortableFieldMap);

        foreach ($sortableFieldMap as $field => $column) {
            $this->assertStringStartsWith(
                static::TABLE_CUSTOMER_ADDRESS,
                $column,
                sprintf('Sortable field "%s" must resolve to a qualified spy_customer_address column.', $field),
            );
        }
    }
}
