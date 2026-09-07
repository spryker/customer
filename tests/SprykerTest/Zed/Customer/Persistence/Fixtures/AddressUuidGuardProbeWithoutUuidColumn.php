<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerTest\Zed\Customer\Persistence\Fixtures;

class AddressUuidGuardProbeWithoutUuidColumn extends AddressUuidGuardProbe
{
    protected const string ADDRESS_UUID_FILTER_METHOD = 'filterByUuidColumnThatDoesNotExist_In';
}
