<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Mapper;

use Generated\Api\Storefront\CustomersAddressesStorefrontResource;
use Generated\Api\Storefront\CustomersStorefrontResource;
use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerTransfer;

class CustomersAddressesStorefrontMapper
{
    /**
     * @return \Generated\Api\Storefront\CustomersAddressesStorefrontResource
     */
    public function mapAddressTransferToResource(AddressTransfer $addressTransfer, CustomerTransfer $customerTransfer): CustomersAddressesStorefrontResource
    {
        $customersStorefrontResource = new CustomersStorefrontResource();
        $customersStorefrontResource->fromArray($customerTransfer->toArray());

        $customersAddressesStorefrontResource = new CustomersAddressesStorefrontResource();

        $customersAddressesStorefrontResource->setCustomer($customersStorefrontResource);
        $customersAddressesStorefrontResource->setUuid($addressTransfer->getUuid());
        $customersAddressesStorefrontResource->setSalutation($addressTransfer->getSalutation());
        $customersAddressesStorefrontResource->setFirstName($addressTransfer->getFirstName());
        $customersAddressesStorefrontResource->setLastName($addressTransfer->getLastName());
        $customersAddressesStorefrontResource->setAddress1($addressTransfer->getAddress1());
        $customersAddressesStorefrontResource->setAddress2($addressTransfer->getAddress2());
        $customersAddressesStorefrontResource->setAddress3($addressTransfer->getAddress3());
        $customersAddressesStorefrontResource->setCompany($addressTransfer->getCompany());
        $customersAddressesStorefrontResource->setCity($addressTransfer->getCity());
        $customersAddressesStorefrontResource->setZipCode($addressTransfer->getZipCode());
        $customersAddressesStorefrontResource->setPhone($addressTransfer->getPhone());
        $customersAddressesStorefrontResource->setComment($addressTransfer->getComment());
        $customersAddressesStorefrontResource->setIsDefaultShipping($addressTransfer->getIsDefaultShipping());
        $customersAddressesStorefrontResource->setIsDefaultBilling($addressTransfer->getIsDefaultBilling());

        if ($addressTransfer->getCountry()) {
            $customersAddressesStorefrontResource->setCountry($addressTransfer->getCountry()->getName());
            $customersAddressesStorefrontResource->setIso2Code($addressTransfer->getCountry()->getIso2Code());
        }

        if ($addressTransfer->getRegion()) {
            $customersAddressesStorefrontResource->setRegion($addressTransfer->getRegion());
        }

        return $customersAddressesStorefrontResource;
    }

    /**
     * @return array<\Generated\Api\Storefront\CustomersAddressesStorefrontResource>
     */
    public function mapAddressesTransferToResourceArray(AddressesTransfer $addressesTransfer, CustomerTransfer $customerTransfer): array
    {
        $resources = [];

        foreach ($addressesTransfer->getAddresses() as $addressTransfer) {
            $resources[] = $this->mapAddressTransferToResource($addressTransfer, $customerTransfer);
        }

        return $resources;
    }

    /**
     * @param \Generated\Api\Storefront\CustomersAddressesStorefrontResource $customersAddressesStorefrontResource
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    public function mapResourceToAddressTransfer(
        CustomersAddressesStorefrontResource $customersAddressesStorefrontResource,
        AddressTransfer $addressTransfer
    ): AddressTransfer {
        return $addressTransfer->fromArray($customersAddressesStorefrontResource->toArray(), true);
    }
}
