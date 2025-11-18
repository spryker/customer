<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Dependency\Client;

class CustomerToStorageRedisClientBridge implements CustomerToStorageRedisClientInterface
{
    /**
     * @var \Spryker\Client\StorageRedis\StorageRedisClientInterface
     */
    protected $storageRedisClient;

    /**
     * @param \Spryker\Client\StorageRedis\StorageRedisClientInterface $storageRedisClient
     */
    public function __construct($storageRedisClient)
    {
        $this->storageRedisClient = $storageRedisClient;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->storageRedisClient->get($key);
    }

    /**
     * @param array $items
     *
     * @return void
     */
    public function setMulti(array $items): void
    {
        $this->storageRedisClient->setMulti($items);
    }
}
