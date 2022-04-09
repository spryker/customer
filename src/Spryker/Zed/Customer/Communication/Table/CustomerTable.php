<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Table;

use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomer;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\ObjectCollection;
use Spryker\Zed\Customer\Communication\Table\PluginExecutor\CustomerTableExpanderPluginExecutorInterface;
use Spryker\Zed\Customer\Dependency\Service\CustomerToUtilDateTimeServiceInterface;
use Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

class CustomerTable extends AbstractTable
{
    /**
     * @var string
     */
    public const ACTIONS = 'Actions';

    /**
     * @var string
     */
    public const COL_CREATED_AT = 'created_at';

    /**
     * @var string
     */
    public const COL_ID_CUSTOMER = 'id_customer';

    /**
     * @var string
     */
    public const COL_EMAIL = 'email';

    /**
     * @var string
     */
    public const COL_FIRST_NAME = 'first_name';

    /**
     * @var string
     */
    public const COL_LAST_NAME = 'last_name';

    /**
     * @var string
     */
    public const COL_STATUS = 'registered';

    /**
     * @var \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface
     */
    protected $customerQueryContainer;

    /**
     * @var \Spryker\Zed\Customer\Dependency\Service\CustomerToUtilDateTimeServiceInterface
     */
    protected $utilDateTimeService;

    /**
     * @var \Spryker\Zed\Customer\Communication\Table\PluginExecutor\CustomerTableExpanderPluginExecutorInterface
     */
    protected $customerTableExpanderPluginExecutor;

    /**
     * @param \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface $customerQueryContainer
     * @param \Spryker\Zed\Customer\Dependency\Service\CustomerToUtilDateTimeServiceInterface $utilDateTimeService
     * @param \Spryker\Zed\Customer\Communication\Table\PluginExecutor\CustomerTableExpanderPluginExecutorInterface $customerTableExpanderPluginExecutor
     */
    public function __construct(
        CustomerQueryContainerInterface $customerQueryContainer,
        CustomerToUtilDateTimeServiceInterface $utilDateTimeService,
        CustomerTableExpanderPluginExecutorInterface $customerTableExpanderPluginExecutor
    ) {
        $this->customerQueryContainer = $customerQueryContainer;
        $this->utilDateTimeService = $utilDateTimeService;
        $this->customerTableExpanderPluginExecutor = $customerTableExpanderPluginExecutor;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            static::COL_ID_CUSTOMER => '#',
            static::COL_CREATED_AT => 'Registration Date',
            static::COL_EMAIL => 'Email',
            static::COL_LAST_NAME => 'Last Name',
            static::COL_FIRST_NAME => 'First Name',
            static::COL_STATUS => 'Status',
            static::ACTIONS => static::ACTIONS,
        ]);

        $config->addRawColumn(static::ACTIONS);
        $config->addRawColumn(static::COL_STATUS);

        $config->setSortable([
            static::COL_ID_CUSTOMER,
            static::COL_CREATED_AT,
            static::COL_EMAIL,
            static::COL_LAST_NAME,
            static::COL_FIRST_NAME,
            static::COL_STATUS,
        ]);

        $config->setUrl('table');

        $config->setSearchable([
            SpyCustomerTableMap::COL_ID_CUSTOMER,
            SpyCustomerTableMap::COL_EMAIL,
            SpyCustomerTableMap::COL_CREATED_AT,
            SpyCustomerTableMap::COL_FIRST_NAME,
            SpyCustomerTableMap::COL_LAST_NAME,
        ]);

        return $config;
    }

    /**
     * @return array<string>
     */
    protected function getCsvHeaders(): array
    {
        return [
            static::COL_ID_CUSTOMER => '#',
            static::COL_CREATED_AT => 'Registration Date',
            static::COL_EMAIL => 'Email',
            static::COL_LAST_NAME => 'Last Name',
            static::COL_FIRST_NAME => 'First Name',
            static::COL_STATUS => 'Status',
        ];
    }

    /**
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerQuery
     */
    protected function getDownloadQuery(): ModelCriteria
    {
        $customerQuery = $this->prepareQuery();
        $customerQuery->orderBy(SpyCustomerTableMap::COL_ID_CUSTOMER, Criteria::DESC);

        return $customerQuery;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $entity
     *
     * @return array
     */
    protected function formatCsvRow(ActiveRecordInterface $entity): array
    {
        $customerRow = $entity->toArray();

        $customerRow[static::COL_CREATED_AT] = $this->utilDateTimeService->formatDateTime($entity->getCreatedAt());
        $customerRow[static::COL_STATUS] = $entity->getRegistered() ? 'Verified' : 'Unverified';

        return $customerRow;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->prepareQuery();

        /** @var \Propel\Runtime\Collection\ObjectCollection<\Orm\Zed\Customer\Persistence\SpyCustomer> $customersCollection */
        $customersCollection = $this->runQuery($query, $config, true);

        if ($customersCollection->count() < 1) {
            return [];
        }

        return $this->formatCustomerCollection($customersCollection);
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer|null $customer
     *
     * @return string
     */
    protected function buildLinks(?SpyCustomer $customer = null)
    {
        if ($customer === null) {
            return '';
        }

        $buttons = [];
        $buttons[] = $this->generateViewButton('/customer/view?id-customer=' . $customer->getIdCustomer(), 'View');
        $buttons[] = $this->generateEditButton('/customer/edit?id-customer=' . $customer->getIdCustomer(), 'Edit');

        $buttons = $this->expandLinks($buttons, $customer);

        return implode(' ', $buttons);
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection $customersCollection
     *
     * @return array
     */
    protected function formatCustomerCollection(ObjectCollection $customersCollection)
    {
        $customersList = [];

        foreach ($customersCollection as $customer) {
            $customersList[] = $this->hydrateCustomerListRow($customer);
        }

        return $customersList;
    }

    /**
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customer
     *
     * @return array
     */
    protected function hydrateCustomerListRow(SpyCustomer $customer)
    {
        $customerRow = $customer->toArray();

        $customerRow[static::COL_CREATED_AT] = $this->utilDateTimeService->formatDateTime($customer->getCreatedAt());
        $customerRow[static::ACTIONS] = $this->buildLinks($customer);
        $customerRow[static::COL_STATUS] = $customer->getRegistered()
            ? $this->generateLabel('Verified', 'label-info')
            : $this->generateLabel('Unverified', 'label-danger');

        return $customerRow;
    }

    /**
     * @return \Orm\Zed\Customer\Persistence\SpyCustomerQuery
     */
    protected function prepareQuery()
    {
        $query = $this->customerQueryContainer
            ->queryCustomers();

        return $query;
    }

    /**
     * @param array<string> $buttons
     * @param \Orm\Zed\Customer\Persistence\SpyCustomer $customer
     *
     * @return array<string>
     */
    protected function expandLinks(array $buttons, SpyCustomer $customer): array
    {
        $expandedButtons = $this->customerTableExpanderPluginExecutor
            ->executeActionExpanderPlugins($customer->getIdCustomer());

        foreach ($expandedButtons as $button) {
            $buttons[] = $this->generateButton(
                $button->getUrl(),
                $button->getTitle(),
                $button->getDefaultOptions(),
                $button->getCustomOptions(),
            );
        }

        return $buttons;
    }
}
