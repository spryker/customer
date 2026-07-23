<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerTest\Zed\Customer\Persistence\Propel;

use Codeception\Test\Unit;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Propel\Runtime\Map\TableMap;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Persistence
 * @group Propel
 * @group AbstractSpyCustomerTest
 * Add your own group annotations below this line
 */
class AbstractSpyCustomerTest extends Unit
{
    protected const string PASSWORD_HASH = '$2y$10$examplehashvalue1234567890';

    protected const string EMAIL = 'persistence.test@spryker.com';

    /**
     * @dataProvider keyTypeDataProvider
     */
    public function testToArrayRemovesPasswordByDefault(string $keyType): void
    {
        // Arrange
        $customerEntity = $this->createCustomerEntity();

        // Act
        $data = $customerEntity->toArray($keyType);

        // Assert
        $this->assertNotContains(static::PASSWORD_HASH, $data);
        $this->assertContains(static::EMAIL, $data);
        $this->assertCount(count(SpyCustomerTableMap::getFieldNames($keyType)) - 1, $data);
    }

    /**
     * @dataProvider keyTypeDataProvider
     */
    public function testToArrayKeepsPasswordWhenExplicitlyAllowed(string $keyType): void
    {
        // Arrange
        $customerEntity = $this->createCustomerEntity();

        // Act
        $data = $customerEntity->toArray($keyType, true, [], false, [SpyCustomerTableMap::COL_PASSWORD]);

        // Assert
        $this->assertContains(static::PASSWORD_HASH, $data);
        $this->assertContains(static::EMAIL, $data);
        $this->assertCount(count(SpyCustomerTableMap::getFieldNames($keyType)), $data);
    }

    public function testSetPasswordIgnoresNullOnPersistedCustomer(): void
    {
        // Arrange
        $customerEntity = $this->createCustomerEntity();
        $customerEntity->setNew(false);
        $customerEntity->resetModified();

        // Act
        $customerEntity->setPassword(null);

        // Assert
        $this->assertSame(static::PASSWORD_HASH, $customerEntity->toArray(allowedSensitiveColumns: [SpyCustomerTableMap::COL_PASSWORD])['password']);
        $this->assertFalse($customerEntity->isColumnModified(SpyCustomerTableMap::COL_PASSWORD));
    }

    public function testFromArrayCannotNullPasswordOnPersistedCustomer(): void
    {
        // Arrange
        $customerEntity = $this->createCustomerEntity();
        $customerEntity->setNew(false);
        $customerEntity->resetModified();

        // Act
        $customerEntity->fromArray(['password' => null], TableMap::TYPE_FIELDNAME);

        // Assert
        $this->assertSame(static::PASSWORD_HASH, $customerEntity->toArray(allowedSensitiveColumns: [SpyCustomerTableMap::COL_PASSWORD])['password']);
    }

    public function testSetPasswordAllowsNewHashOnPersistedCustomer(): void
    {
        // Arrange
        $customerEntity = $this->createCustomerEntity();
        $customerEntity->setNew(false);

        // Act
        $customerEntity->setPassword('$2y$10$anotherhashvalue0987654321');

        // Assert
        $this->assertSame('$2y$10$anotherhashvalue0987654321', $customerEntity->toArray(allowedSensitiveColumns: [SpyCustomerTableMap::COL_PASSWORD])['password']);
    }

    /**
     * @return array<string, array<string>>
     */
    public static function keyTypeDataProvider(): array
    {
        return [
            'phpName' => [TableMap::TYPE_PHPNAME],
            'camelName' => [TableMap::TYPE_CAMELNAME],
            'colName' => [TableMap::TYPE_COLNAME],
            'fieldName' => [TableMap::TYPE_FIELDNAME],
            'num' => [TableMap::TYPE_NUM],
        ];
    }

    protected function createCustomerEntity(): SpyCustomer
    {
        $customerEntity = new SpyCustomer();
        $customerEntity->setCustomerReference('DE--PERSISTENCE-TEST');
        $customerEntity->setEmail(static::EMAIL);
        $customerEntity->setPassword(static::PASSWORD_HASH);

        return $customerEntity;
    }
}
