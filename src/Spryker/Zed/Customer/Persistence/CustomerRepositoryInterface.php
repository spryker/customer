<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Persistence;

use Generated\Shared\Transfer\AddressCriteriaFilterTransfer;
use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\CustomerCriteriaFilterTransfer;
use Generated\Shared\Transfer\CustomerCriteriaTransfer;
use Generated\Shared\Transfer\CustomerTransfer;

interface CustomerRepositoryInterface
{
    public function getCustomerCollection(CustomerCollectionTransfer $customerCollectionTransfer): CustomerCollectionTransfer;

    public function findCustomerByReference(string $customerReference): ?CustomerTransfer;

    public function findCustomerAddressById(int $idCustomerAddress): ?AddressTransfer;

    public function findAddressByAddressData(AddressTransfer $addressTransfer): ?AddressTransfer;

    public function getAllSalutations(): array;

    public function getCustomerCollectionByCriteria(
        CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
    ): CustomerCollectionTransfer;

    public function findAddressByCriteria(AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer): ?AddressTransfer;

    public function getAddressesByCriteria(AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer): AddressesTransfer;

    public function findCustomerByCriteria(CustomerCriteriaTransfer $customerCriteriaTransfer): ?CustomerTransfer;

    public function isEmailAvailableForCustomer(string $email, ?int $exceptIdCustomer): bool;
}
