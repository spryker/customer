<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Persistence\Propel;

use Orm\Zed\Customer\Persistence\Base\SpyCustomer as BaseSpyCustomer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Propel\Runtime\Map\TableMap;

/**
 * Skeleton subclass for representing a row from the 'spy_customer' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements. This class will only be generated as
 * long as it does not already exist in the output directory.
 */
abstract class AbstractSpyCustomer extends BaseSpyCustomer
{
    /**
     * A password may only be created or replaced with a new hash by the dedicated password flows.
     * Nulling an existing hash is always an accident (e.g. hydrating the entity from a transfer
     * that was built from the password-stripped `toArray()`), so such writes are ignored.
     * `fromArray()` funnels into this setter as well, which makes the guard cover all write paths.
     *
     * @param string|null $v
     *
     * @return $this
     */
    public function setPassword($v)
    {
        if ($v === null && !$this->isNew() && $this->getPassword() !== null) {
            return $this;
        }

        return parent::setPassword($v);
    }

    /**
     * @param array<string, mixed> $alreadyDumpedObjects
     * @param array<string> $allowedSensitiveColumns Columns to include despite being sensitive.
     *                                               Pass [SpyCustomerTableMap::COL_PASSWORD] to include the password hash.
     */
    public function toArray(
        string $keyType = TableMap::TYPE_FIELDNAME,
        bool $includeLazyLoadColumns = true,
        array $alreadyDumpedObjects = [],
        bool $includeForeignObjects = false,
        array $allowedSensitiveColumns = [],
    ): array {
        $data = parent::toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, $includeForeignObjects);

        if (in_array(SpyCustomerTableMap::COL_PASSWORD, $allowedSensitiveColumns, true)) {
            return $data;
        }

        $passwordKey = SpyCustomerTableMap::translateFieldName(
            SpyCustomerTableMap::COL_PASSWORD,
            TableMap::TYPE_COLNAME,
            $keyType,
        );

        unset($data[$passwordKey]);

        return $data;
    }
}
