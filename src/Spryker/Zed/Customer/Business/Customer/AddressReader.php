<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Customer;

use Generated\Shared\Transfer\AddressCollectionTransfer;
use Generated\Shared\Transfer\AddressCriteriaTransfer;
use Spryker\Zed\Customer\Persistence\CustomerRepositoryInterface;

class AddressReader implements AddressReaderInterface
{
    public function __construct(protected CustomerRepositoryInterface $customerRepository)
    {
    }

    public function getAddressCollection(AddressCriteriaTransfer $addressCriteriaTransfer): AddressCollectionTransfer
    {
        return $this->customerRepository->getAddressCollection($addressCriteriaTransfer);
    }
}
