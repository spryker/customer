<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Persistence;

use ArrayObject;
use Generated\Shared\Transfer\AddressCollectionTransfer;
use Generated\Shared\Transfer\AddressCriteriaFilterTransfer;
use Generated\Shared\Transfer\AddressCriteriaTransfer;
use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CustomerCollectionCriteriaTransfer;
use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\CustomerCriteriaFilterTransfer;
use Generated\Shared\Transfer\CustomerCriteriaSearchTermsTransfer;
use Generated\Shared\Transfer\CustomerCriteriaTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerAddressTableMap;
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
    protected const string ADDRESS_UUID_FILTER_METHOD = 'filterByUuid_In';

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

    /**
     * @module Country
     */
    public function findAddressByAddressData(AddressTransfer $addressTransfer): ?AddressTransfer
    {
        /** @var \Orm\Zed\Customer\Persistence\SpyCustomerAddressQuery $customerAddressQuery */
        $customerAddressQuery = $this->getFactory()
            ->createSpyCustomerAddressQuery()
            ->filterByFkCustomer($addressTransfer->getFkCustomer())
            ->joinWithCountry()
            ->leftJoinWithRegion();

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

    /**
     * @param array<array<string, mixed>> $customers
     */
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

    /**
     * @module Country
     */
    public function findCustomerAddressById(int $idCustomerAddress): ?AddressTransfer
    {
        $customerAddressEntity = $this->getFactory()
            ->createSpyCustomerAddressQuery()
            ->filterByIdCustomerAddress($idCustomerAddress)
            ->joinWithCountry()
            ->leftJoinWithRegion()
            ->findOne();

        if (!$customerAddressEntity) {
            return null;
        }

        return $this->getFactory()
            ->createCustomerMapper()
            ->mapCustomerAddressEntityToAddressTransfer($customerAddressEntity, new AddressTransfer());
    }

    /**
     * @return array<string>
     */
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

    /**
     * @module Country
     */
    protected function buildAddressConditionsByCriteria(
        AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer
    ): SpyCustomerAddressQuery {
        $addressQuery = $this->getFactory()
            ->createSpyCustomerAddressQuery()
            ->joinWithCountry()
            ->leftJoinWithRegion();
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

        if ($customerCriteriaFilterTransfer->getSearchTerms()) {
            $query = $this->applySearchTermsToQuery($query, $customerCriteriaFilterTransfer->getSearchTerms());
        }

        return $query;
    }

    /**
     * The terms are OR-combined: a customer matches when ANY of the provided terms matches.
     */
    protected function applySearchTermsToQuery(
        SpyCustomerQuery $query,
        CustomerCriteriaSearchTermsTransfer $customerCriteriaSearchTermsTransfer
    ): SpyCustomerQuery {
        $orNeeded = false;

        if ($customerCriteriaSearchTermsTransfer->getEmail()) {
            $query->filterByEmail(sprintf('%%%s%%', $customerCriteriaSearchTermsTransfer->getEmail()), Criteria::LIKE);
            $orNeeded = true;
        }

        if ($customerCriteriaSearchTermsTransfer->getFirstName()) {
            if ($orNeeded) {
                $query->_or();
            }
            $query->filterByFirstName(sprintf('%%%s%%', $customerCriteriaSearchTermsTransfer->getFirstName()), Criteria::LIKE);
            $orNeeded = true;
        }

        if ($customerCriteriaSearchTermsTransfer->getLastName()) {
            if ($orNeeded) {
                $query->_or();
            }
            $query->filterByLastName(sprintf('%%%s%%', $customerCriteriaSearchTermsTransfer->getLastName()), Criteria::LIKE);
        }

        return $query;
    }

    public function getCustomerCollectionByCollectionCriteria(
        CustomerCollectionCriteriaTransfer $customerCollectionCriteriaTransfer
    ): CustomerCollectionTransfer {
        $customerCollectionTransfer = new CustomerCollectionTransfer();
        $paginationTransfer = $customerCollectionCriteriaTransfer->getPagination();

        $query = $this->buildCustomerQueryByConditions($customerCollectionCriteriaTransfer);
        $query = $this->applyCustomerSortToQuery($query, $customerCollectionCriteriaTransfer->getSortCollection());
        $query = $this->applyPagination($query, $paginationTransfer);
        $query->setFormatter(ArrayFormatter::class);

        $this->hydrateCustomerListWithCustomers($customerCollectionTransfer, $query->find()->getData());

        return $customerCollectionTransfer->setPagination($paginationTransfer);
    }

    protected function buildCustomerQueryByConditions(
        CustomerCollectionCriteriaTransfer $customerCollectionCriteriaTransfer
    ): SpyCustomerQuery {
        $customerConditionsTransfer = $customerCollectionCriteriaTransfer->getCustomerConditions();

        $query = $this->getFactory()->createSpyCustomerQuery(
            null,
            null,
            $customerConditionsTransfer?->getHasAnonymizedAt() ?? false,
        );

        if ($customerConditionsTransfer === null) {
            return $query;
        }

        if ($customerConditionsTransfer->getCustomerIds()) {
            $query->filterByIdCustomer_In($customerConditionsTransfer->getCustomerIds());
        }

        if ($customerConditionsTransfer->getCustomerReferences()) {
            $query->filterByCustomerReference_In($customerConditionsTransfer->getCustomerReferences());
        }

        if ($customerConditionsTransfer->getEmails()) {
            $query->addUsingAlias(SpyCustomerTableMap::COL_EMAIL, $customerConditionsTransfer->getEmails(), Criteria::IN);
        }

        if ($customerConditionsTransfer->getSearchTerms()) {
            $query = $this->applySearchTermsToQuery($query, $customerConditionsTransfer->getSearchTerms());
        }

        return $query;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\SortTransfer> $sortCollection
     */
    protected function applyCustomerSortToQuery(SpyCustomerQuery $query, ArrayObject $sortCollection): SpyCustomerQuery
    {
        $sortableFieldMap = $this->getFactory()->getConfig()->getCustomerCollectionSortableFieldMap();

        foreach ($sortCollection as $sortTransfer) {
            $column = $sortableFieldMap[$sortTransfer->getField()] ?? null;

            if ($column === null) {
                continue;
            }

            $query->orderBy($column, $sortTransfer->getIsAscending() === false ? Criteria::DESC : Criteria::ASC);
        }

        return $query;
    }

    public function getAddressCollection(AddressCriteriaTransfer $addressCriteriaTransfer): AddressCollectionTransfer
    {
        $addressCollectionTransfer = new AddressCollectionTransfer();
        $paginationTransfer = $addressCriteriaTransfer->getPagination();

        $query = $this->buildAddressQueryByConditions($addressCriteriaTransfer);
        $query = $this->applyAddressSortToQuery($query, $addressCriteriaTransfer->getSortCollection());
        $query = $this->applyAddressPagination($query, $paginationTransfer);

        $customerMapper = $this->getFactory()->createCustomerMapper();

        foreach ($query->find() as $addressEntity) {
            $addressCollectionTransfer->addAddress(
                $customerMapper->mapCustomerAddressEntityToAddressTransfer($addressEntity, new AddressTransfer()),
            );
        }

        return $addressCollectionTransfer->setPagination($paginationTransfer);
    }

    /**
     * @module Country
     */
    protected function buildAddressQueryByConditions(AddressCriteriaTransfer $addressCriteriaTransfer): SpyCustomerAddressQuery
    {
        $query = $this->getFactory()
            ->createSpyCustomerAddressQuery()
            ->joinWithCountry()
            ->leftJoinWithRegion();
        $addressConditionsTransfer = $addressCriteriaTransfer->getAddressConditions();

        if ($addressConditionsTransfer === null) {
            return $query;
        }

        if ($addressConditionsTransfer->getUuids() && method_exists($query, static::ADDRESS_UUID_FILTER_METHOD)) {
            $query->filterByUuid_In($addressConditionsTransfer->getUuids());
        }

        if ($addressConditionsTransfer->getAddressIds()) {
            $query->filterByIdCustomerAddress_In($addressConditionsTransfer->getAddressIds());
        }

        if ($addressConditionsTransfer->getCustomerIds()) {
            $query->filterByFkCustomer_In($addressConditionsTransfer->getCustomerIds());
        }

        return $query;
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\SortTransfer> $sortCollection
     */
    protected function applyAddressSortToQuery(
        SpyCustomerAddressQuery $query,
        ArrayObject $sortCollection
    ): SpyCustomerAddressQuery {
        $sortableFieldMap = $this->getFactory()->getConfig()->getAddressCollectionSortableFieldMap();
        $tiebreakerDirection = Criteria::ASC;

        foreach ($sortCollection as $sortTransfer) {
            $column = $sortableFieldMap[$sortTransfer->getField()] ?? null;

            if ($column === null) {
                continue;
            }

            $direction = $sortTransfer->getIsAscending() === false ? Criteria::DESC : Criteria::ASC;

            $query->orderBy($column, $direction);
            $tiebreakerDirection = $direction;
        }

        return $query->orderBy(SpyCustomerAddressTableMap::COL_ID_CUSTOMER_ADDRESS, $tiebreakerDirection);
    }

    protected function applyAddressPagination(
        SpyCustomerAddressQuery $query,
        ?PaginationTransfer $paginationTransfer = null
    ): SpyCustomerAddressQuery {
        if (!$paginationTransfer) {
            return $query;
        }

        $paginationModel = $query->paginate(
            $paginationTransfer->requirePage()->getPage(),
            $paginationTransfer->requireMaxPerPage()->getMaxPerPage(),
        );

        $paginationTransfer->setNbResults($paginationModel->getNbResults());
        $paginationTransfer->setFirstIndex($paginationModel->getFirstIndex());
        $paginationTransfer->setLastIndex($paginationModel->getLastIndex());
        $paginationTransfer->setFirstPage($paginationModel->getFirstPage());
        $paginationTransfer->setLastPage($paginationModel->getLastPage());
        $paginationTransfer->setNextPage($paginationModel->getNextPage());
        $paginationTransfer->setPreviousPage($paginationModel->getPreviousPage());

        /** @var \Orm\Zed\Customer\Persistence\SpyCustomerAddressQuery $paginatedQuery */
        $paginatedQuery = $paginationModel->getQuery();

        return $paginatedQuery;
    }
}
