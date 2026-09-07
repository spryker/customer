<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerTest\Zed\Customer\Persistence\Fixtures;

use Generated\Shared\Transfer\AddressCriteriaTransfer;
use Orm\Zed\Customer\Persistence\SpyCustomerAddressQuery;
use Spryker\Zed\Customer\Persistence\CustomerRepository;

class AddressUuidGuardProbe extends CustomerRepository
{
    public function exposeBuildAddressQueryByConditions(
        AddressCriteriaTransfer $addressCriteriaTransfer
    ): SpyCustomerAddressQuery {
        return $this->buildAddressQueryByConditions($addressCriteriaTransfer);
    }
}
