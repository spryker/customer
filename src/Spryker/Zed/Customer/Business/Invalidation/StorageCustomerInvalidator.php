<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Invalidation;

use Spryker\Shared\Customer\KeyGenerator\KeyGeneratorInterface;
use Spryker\Zed\Customer\Dependency\Client\CustomerToStorageRedisClientInterface;

class StorageCustomerInvalidator implements CustomerInvalidatorInterface
{
    /**
     * @param \Spryker\Zed\Customer\Dependency\Client\CustomerToStorageRedisClientInterface $storageRedisClient
     * @param \Spryker\Shared\Customer\KeyGenerator\KeyGeneratorInterface $keyGenerator
     */
    public function __construct(
        protected CustomerToStorageRedisClientInterface $storageRedisClient,
        protected KeyGeneratorInterface $keyGenerator,
    ) {
    }

    /**
     * @param array<\Generated\Shared\Transfer\CustomerTransfer> $customerTransfers
     *
     * @return void
     */
    public function invalidate(array $customerTransfers): void
    {
        $invalidationRecords = [];
        foreach ($customerTransfers as $customerTransfer) {
            $key = $this->keyGenerator->generateKey($customerTransfer);
            $invalidationRecords[$key] = true;
        }
        $this->storageRedisClient->setMulti($invalidationRecords);
    }
}
