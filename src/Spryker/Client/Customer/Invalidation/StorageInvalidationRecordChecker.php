<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Customer\Invalidation;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\Dependency\Client\CustomerToStorageRedisClientInterface;
use Spryker\Shared\Customer\KeyGenerator\KeyGeneratorInterface;

class StorageInvalidationRecordChecker implements InvalidationRecordCheckerInterface
{
    public function __construct(
        protected CustomerToStorageRedisClientInterface $storageRedisClient,
        protected KeyGeneratorInterface $keyGenerator,
    ) {
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerTransfer $customerTransfer
     *
     * @return string|null
     */
    public function findInvalidationRecord(CustomerTransfer $customerTransfer): ?string
    {
        $key = $this->keyGenerator->generateKey($customerTransfer);
        $invalidationRecord = $this->storageRedisClient->get($key);
        if ($invalidationRecord) {
            $this->storageRedisClient->delete($key);

            return $invalidationRecord;
        }

        return null;
    }
}
