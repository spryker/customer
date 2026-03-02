<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\AddressCriteriaFilterTransfer;
use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\CustomerCriteriaFilterTransfer;
use Generated\Shared\Transfer\CustomerCriteriaTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomerAddress;
use Orm\Zed\Customer\Persistence\SpyCustomerAddressQuery;
use Orm\Zed\Customer\Persistence\SpyCustomerQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Formatter\ArrayFormatter;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Spryker\Zed\Propel\PropelFilterCriteria;

/**
 * @method \Spryker\Zed\Customer\Persistence\CustomerPersistenceFactory getFactory()
 */
class CustomerRepository extends AbstractRepository implements CustomerRepositoryInterface
{
    public function getCustomerCollection(CustomerCollectionTransfer $customerCollectionTransfer): CustomerCollectionTransfer
    {
        $customerQuery = $this->getFactory()
            ->createSpyCustomerQuery();

        $customerQuery = $this->applyFilterToQuery($customerQuery, $customerCollectionTransfer->getFilter());
        $customerQuery = $this->applyPagination($customerQuery, $customerCollectionTransfer->getPagination());
        $customerQuery->setFormatter(ArrayFormatter::class);
        $this->hydrateCustomerListWithCustomers($customerCollectionTransfer, $customerQuery->find()->getData());

        return $customerCollectionTransfer;
    }

    public function findCustomerByReference(string $customerReference): ?CustomerTransfer
    {
        $customerEntity = $this->getFactory()->createSpyCustomerQuery()->findOneByCustomerReference($customerReference);

        if ($customerEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createCustomerMapper()
            ->mapCustomerEntityToCustomer($customerEntity->toArray());
    }

    public function findAddressByAddressData(AddressTransfer $addressTransfer): ?AddressTransfer
    {
        /** @var \Orm\Zed\Customer\Persistence\SpyCustomerAddressQuery $customerAddressQuery */
        $customerAddressQuery = $this->getFactory()
            ->createSpyCustomerAddressQuery()
            ->filterByFkCustomer($addressTransfer->getFkCustomer())
            ->joinWithCountry();

        $customerAddressEntities = $customerAddressQuery->find();

        $customerAddressEntity = $this->getExistingAddressByAddress($customerAddressEntities, $addressTransfer);

        if ($customerAddressEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createCustomerMapper()
            ->mapCustomerAddressEntityToAddressTransfer($customerAddressEntity, new AddressTransfer());
    }

    protected function getExistingAddressByAddress(Collection $customerAddressEntities, AddressTransfer $addressTransfer): ?SpyCustomerAddress
    {
        /** @var \Orm\Zed\Customer\Persistence\SpyCustomerAddress $customerAddressEntity */
        foreach ($customerAddressEntities as $customerAddressEntity) {
            if (
                $customerAddressEntity->getFirstName() !== $addressTransfer->getFirstName()
                || $customerAddressEntity->getLastName() !== $addressTransfer->getLastName()
                || $customerAddressEntity->getAddress1() !== $addressTransfer->getAddress1()
                || $customerAddressEntity->getAddress2() !== $addressTransfer->getAddress2()
                || $customerAddressEntity->getAddress3() !== $addressTransfer->getAddress3()
                || $customerAddressEntity->getZipCode() !== $addressTransfer->getZipCode()
                || $customerAddressEntity->getCity() !== $addressTransfer->getCity()
                || $customerAddressEntity->getPhone() !== $addressTransfer->getPhone()
                || $customerAddressEntity->getCountry()->getIso2Code() !== $addressTransfer->getIso2Code()
            ) {
                continue;
            }

            return $customerAddressEntity;
        }

        return null;
    }

    protected function applyFilterToQuery(SpyCustomerQuery $customerQuery, ?FilterTransfer $filterTransfer): SpyCustomerQuery
    {
        $criteria = new Criteria();
        if ($filterTransfer !== null) {
            $criteria = (new PropelFilterCriteria($filterTransfer))
                ->toCriteria();
        }

        $customerQuery->mergeWith($criteria);

        return $customerQuery;
    }

    protected function applyPagination(SpyCustomerQuery $customerQuery, ?PaginationTransfer $paginationTransfer = null): SpyCustomerQuery
    {
        if (!$paginationTransfer) {
            return $customerQuery;
        }

        $page = $paginationTransfer
            ->requirePage()
            ->getPage();

        $maxPerPage = $paginationTransfer
            ->requireMaxPerPage()
            ->getMaxPerPage();

        $paginationModel = $customerQuery->paginate($page, $maxPerPage);

        $paginationTransfer->setNbResults($paginationModel->getNbResults());
        $paginationTransfer->setFirstIndex($paginationModel->getFirstIndex());
        $paginationTransfer->setLastIndex($paginationModel->getLastIndex());
        $paginationTransfer->setFirstPage($paginationModel->getFirstPage());
        $paginationTransfer->setLastPage($paginationModel->getLastPage());
        $paginationTransfer->setNextPage($paginationModel->getNextPage());
        $paginationTransfer->setPreviousPage($paginationModel->getPreviousPage());

        /** @var \Orm\Zed\Customer\Persistence\SpyCustomerQuery $paginatedCustomerQuery */
        $paginatedCustomerQuery = $paginationModel->getQuery();

        return $paginatedCustomerQuery;
    }

    public function hydrateCustomerListWithCustomers(CustomerCollectionTransfer $customerListTransfer, array $customers): void
    {
        $customerCollection = new ArrayObject();

        foreach ($customers as $customer) {
            $customerCollection->append(
                $this->getFactory()
                    ->createCustomerMapper()
                    ->mapCustomerEntityToCustomer($customer),
            );
        }

        $customerListTransfer->setCustomers($customerCollection);
    }

    public function findCustomerAddressById(int $idCustomerAddress): ?AddressTransfer
    {
        $customerAddressEntity = $this->getFactory()
            ->createSpyCustomerAddressQuery()
            ->filterByIdCustomerAddress($idCustomerAddress)
            ->findOne();

        if (!$customerAddressEntity) {
            return null;
        }

        return $this->getFactory()
            ->createCustomerMapper()
            ->mapCustomerAddressEntityToAddressTransfer($customerAddressEntity, new AddressTransfer());
    }

    public function getAllSalutations(): array
    {
        return SpyCustomerTableMap::getValueSet(SpyCustomerTableMap::COL_SALUTATION);
    }

    public function getCustomerCollectionByCriteria(
        CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
    ): CustomerCollectionTransfer {
        $customerCollectionTransfer = new CustomerCollectionTransfer();
        /** @var \Propel\Runtime\Collection\ObjectCollection $customerCollection */
        $customerCollection = $this->queryCustomersByCriteria($customerCriteriaFilterTransfer)->find();

        $this->hydrateCustomerListWithCustomers(
            $customerCollectionTransfer,
            $customerCollection->toArray(),
        );

        return $customerCollectionTransfer;
    }

    public function findAddressByCriteria(AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer): ?AddressTransfer
    {
        $addressQuery = $this->buildAddressConditionsByCriteria($addressCriteriaFilterTransfer);
        $addressEntity = $addressQuery->findOne();

        if (!$addressEntity) {
            return null;
        }

        return $this->getFactory()
            ->createCustomerMapper()
            ->mapCustomerAddressEntityToAddressTransfer($addressEntity, new AddressTransfer());
    }

    public function getAddressesByCriteria(AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer): AddressesTransfer
    {
        $addressQuery = $this->buildAddressConditionsByCriteria($addressCriteriaFilterTransfer);
        $addressEntities = $addressQuery->find();

        $addressTransfers = [];
        $customerMapper = $this->getFactory()
            ->createCustomerMapper();
        foreach ($addressEntities as $addressEntity) {
            $addressTransfers[] = $customerMapper->mapCustomerAddressEntityToAddressTransfer($addressEntity, new AddressTransfer());
        }

        return (new AddressesTransfer())->setAddresses(new ArrayObject($addressTransfers));
    }

    public function findCustomerByCriteria(CustomerCriteriaTransfer $customerCriteriaTransfer): ?CustomerTransfer
    {
        $customerQuery = $this->getFactory()->createSpyCustomerQuery();

        if ($customerCriteriaTransfer->getCustomerReference()) {
            $customerQuery->filterByCustomerReference($customerCriteriaTransfer->getCustomerReference());
        }

        if ($customerCriteriaTransfer->getIdCustomer()) {
            $customerQuery->filterByIdCustomer($customerCriteriaTransfer->getIdCustomer());
        }

        $customerEntity = $customerQuery->findOne();

        if ($customerEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createCustomerMapper()
            ->mapCustomerEntityToCustomer($customerEntity->toArray());
    }

    public function isEmailAvailableForCustomer(string $email, ?int $exceptIdCustomer): bool
    {
        return $this->getFactory()
            ->createSpyCustomerQuery()
            ->filterByEmail($email, Criteria::EQUAL, $this->getFactory()->getConfig()->isCustomerEmailValidationCaseSensitive())
            ->filterByIdCustomer($exceptIdCustomer, Criteria::NOT_EQUAL)
            ->count() === 0;
    }

    protected function buildAddressConditionsByCriteria(
        AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer
    ): SpyCustomerAddressQuery {
        $addressQuery = $this->getFactory()->createSpyCustomerAddressQuery()->joinWithCountry();
        if ($addressCriteriaFilterTransfer->getIdCustomerAddress()) {
            $addressQuery->filterByIdCustomerAddress($addressCriteriaFilterTransfer->getIdCustomerAddress());
        }

        if ($addressCriteriaFilterTransfer->getFkCustomer()) {
            $addressQuery->filterByFkCustomer($addressCriteriaFilterTransfer->getFkCustomer());
        }

        return $addressQuery;
    }

    protected function queryCustomersByCriteria(
        CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
    ): SpyCustomerQuery {
        $query = $this->getFactory()->createSpyCustomerQuery(
            null,
            null,
            $customerCriteriaFilterTransfer->getHasAnonymizedAt() ?? false,
        );

        if ($customerCriteriaFilterTransfer->getRestorePasswordKeyExists() === false) {
            $query->filterByRestorePasswordKey(null, Criteria::ISNULL);
        }
        if (!$customerCriteriaFilterTransfer->getPasswordExists() && $customerCriteriaFilterTransfer->getPasswordExists() !== null) {
            $query->filterByPassword(null, Criteria::ISNULL)
                ->addOr($query->getNewCriterion(SpyCustomerTableMap::COL_PASSWORD, '', Criteria::EQUAL));
        }

        if ($customerCriteriaFilterTransfer->getCustomerIds()) {
            $query->filterByIdCustomer_In(
                $customerCriteriaFilterTransfer->getCustomerIds(),
            );
        }

        return $query;
    }
}
