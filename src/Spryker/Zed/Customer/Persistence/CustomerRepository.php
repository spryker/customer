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
use Generated\Shared\Transfer\CustomerConditionsTransfer;
use Generated\Shared\Transfer\CustomerCriteriaFilterTransfer;
use Generated\Shared\Transfer\CustomerCriteriaTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\SortCollectionTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomerAddressQuery;
use Orm\Zed\Customer\Persistence\SpyCustomerQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Formatter\ArrayFormatter;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\Customer\Persistence\CustomerPersistenceFactory getFactory()
 */
class CustomerRepository extends AbstractRepository implements CustomerRepositoryInterface
{
    /**
     * @var array
     */
    protected const SORT_KEYS_MAP = [
        'firstName' => SpyCustomerTableMap::COL_FIRST_NAME,
        'lastName' => SpyCustomerTableMap::COL_LAST_NAME,
        'email' => SpyCustomerTableMap::COL_EMAIL,
        'customerReference' => SpyCustomerTableMap::COL_CUSTOMER_REFERENCE,
        'salutation' => SpyCustomerTableMap::COL_SALUTATION,
        'registered' => SpyCustomerTableMap::COL_REGISTERED,
    ];

    /**
     * @param \Generated\Shared\Transfer\CustomerCriteriaTransfer $customerCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerCollectionTransfer
     */
    public function getCustomerCollection(CustomerCriteriaTransfer $customerCriteriaTransfer): CustomerCollectionTransfer
    {
        $customerQuery = $this->getFactory()
            ->createSpyCustomerQuery();

        $customerCollectionTransfer = new CustomerCollectionTransfer();

        $customerQuery = $this->applyCriteriaToQuery($customerQuery, $customerCriteriaTransfer->getCustomerConditions());
        $customerQuery = $this->applySortToQuery($customerQuery, $customerCriteriaTransfer->getSortCollection());
        $customerQuery = $this->applyPagination($customerQuery, $customerCriteriaTransfer->getPagination());
        $customerQuery->setFormatter(ArrayFormatter::class);
        $this->hydrateCustomerListWithCustomers($customerCollectionTransfer, $customerQuery->find()->getData());

        return $customerCollectionTransfer;
    }

    /**
     * @param string $customerReference
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
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
     * @param \Generated\Shared\Transfer\AddressTransfer $addressTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer|null
     */
    public function findAddressByAddressData(AddressTransfer $addressTransfer): ?AddressTransfer
    {
        $addressEntity = $this->getFactory()
            ->createSpyCustomerAddressQuery()
            ->filterByFkCustomer($addressTransfer->getFkCustomer())
            ->filterByFirstName($addressTransfer->getFirstName())
            ->filterByLastName($addressTransfer->getLastName())
            ->filterByAddress1($addressTransfer->getAddress1())
            ->filterByAddress2($addressTransfer->getAddress2())
            ->filterByAddress3($addressTransfer->getAddress3())
            ->filterByZipCode($addressTransfer->getZipCode())
            ->filterByCity($addressTransfer->getCity())
            ->filterByPhone($addressTransfer->getPhone())
            ->useCountryQuery()
            ->filterByIso2Code($addressTransfer->getIso2Code())
            ->endUse()
            ->findOne();
        if ($addressEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createCustomerMapper()
            ->mapCustomerAddressEntityToAddressTransfer($addressEntity, new AddressTransfer());
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomerQuery $spyCustomerQuery
     * @param \Generated\Shared\Transfer\SortCollectionTransfer|null $sortCollectionTransfer
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerQuery
     */
    protected function applySortToQuery(SpyCustomerQuery $spyCustomerQuery, ?SortCollectionTransfer $sortCollectionTransfer): SpyCustomerQuery
    {
        if ($sortCollectionTransfer !== null) {
            foreach ($sortCollectionTransfer->getSorts() as $sort) {
                if (isset(static::SORT_KEYS_MAP[$sort->getField()])) {
                    if ($sort->getOrderByAsc()) {
                        $spyCustomerQuery->addAscendingOrderByColumn(static::SORT_KEYS_MAP[$sort->getField()]);

                        continue;
                    }

                    $spyCustomerQuery->addDescendingOrderByColumn(static::SORT_KEYS_MAP[$sort->getField()]);
                }
            }
        }

        return $spyCustomerQuery;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomerQuery $spyCustomerQuery
     * @param \Generated\Shared\Transfer\PaginationTransfer|null $paginationTransfer
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerQuery
     */
    protected function applyPagination(SpyCustomerQuery $spyCustomerQuery, ?PaginationTransfer $paginationTransfer = null): SpyCustomerQuery
    {
        if ($paginationTransfer === null) {
            return $spyCustomerQuery;
        }

        if($paginationTransfer->getLimit()) {
            $spyCustomerQuery->setLimit($paginationTransfer->getLimit());
        }

        if($paginationTransfer->getOffset()) {
            $spyCustomerQuery->setOffset($paginationTransfer->getOffset());
        }

        return $spyCustomerQuery;
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerCollectionTransfer $customerListTransfer
     * @param array $customers
     *
     * @return void
     */
    public function hydrateCustomerListWithCustomers(CustomerCollectionTransfer $customerListTransfer, array $customers): void
    {
        $customerCollection = new ArrayObject();

        foreach ($customers as $customer) {
            $customerCollection->append(
                $this->getFactory()
                    ->createCustomerMapper()
                    ->mapCustomerEntityToCustomer($customer)
            );
        }

        $customerListTransfer->setCustomers($customerCollection);
    }

    /**
     * @param int $idCustomerAddress
     *
     * @return \Generated\Shared\Transfer\AddressTransfer|null
     */
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

    /**
     * @return array
     */
    public function getAllSalutations(): array
    {
        return SpyCustomerTableMap::getValueSet(SpyCustomerTableMap::COL_SALUTATION);
    }

    /**
     * @param \Generated\Shared\Transfer\CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerCollectionTransfer
     */
    public function getCustomerCollectionByCriteria(
        CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
    ): CustomerCollectionTransfer {
        $customerCollectionTransfer = new CustomerCollectionTransfer();

        $this->hydrateCustomerListWithCustomers(
            $customerCollectionTransfer,
            $this->queryCustomersByCriteria($customerCriteriaFilterTransfer)->find()->toArray()
        );

        return $customerCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer|null
     */
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

    /**
     * @param \Generated\Shared\Transfer\AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\AddressesTransfer
     */
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

    /**
     * @param \Generated\Shared\Transfer\CustomerCriteriaTransfer $customerCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer|null
     */
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

    /**
     * @param \Generated\Shared\Transfer\AddressCriteriaFilterTransfer $addressCriteriaFilterTransfer
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerAddressQuery
     */
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

    /**
     * @param \Generated\Shared\Transfer\CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerQuery
     */
    protected function queryCustomersByCriteria(
        CustomerCriteriaFilterTransfer $customerCriteriaFilterTransfer
    ): SpyCustomerQuery {
        $query = $this->getFactory()->createSpyCustomerQuery();
        if (!$customerCriteriaFilterTransfer->getRestorePasswordKeyExists()) {
            $query->filterByRestorePasswordKey(null, Criteria::ISNULL);
        }
        if (!$customerCriteriaFilterTransfer->getPasswordExists() && $customerCriteriaFilterTransfer->getPasswordExists() !== null) {
            $query->filterByPassword(null, Criteria::ISNULL)
                ->addOr($query->getNewCriterion(SpyCustomerTableMap::COL_PASSWORD, '', Criteria::EQUAL));
        }

        return $query;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomerQuery $customerQuery
     * @param \Generated\Shared\Transfer\CustomerConditionsTransfer|null $customerConditionsTransfer
     *
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerQuery
     */
    protected function applyCriteriaToQuery(SpyCustomerQuery $customerQuery, ?CustomerConditionsTransfer $customerConditionsTransfer): SpyCustomerQuery
    {
        if (!$customerConditionsTransfer) {
            return $customerQuery;
        }

        if ($customerConditionsTransfer->getGenders()) {
            $customerQuery->filterByGender_In($customerConditionsTransfer->getGenders());
        }

        if ($customerConditionsTransfer->getIds()) {
            $customerQuery->filterByIdCustomer_In(array_map('intval', $customerConditionsTransfer->getIds()));
        }

        return $customerQuery;
    }
}
