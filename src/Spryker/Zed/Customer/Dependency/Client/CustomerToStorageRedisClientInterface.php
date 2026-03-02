<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Dependency\Client;

interface CustomerToStorageRedisClientInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);

    public function setMulti(array $items): void;
}
