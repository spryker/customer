<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Table;

use Orm\Zed\Customer\Persistence\Map\SpyCustomerAddressTableMap;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Shared\Customer\CustomerConstants;
use Spryker\Zed\Customer\Dependency\Service\CustomerToUtilSanitizeServiceInterface;
use Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;

class AddressTable extends AbstractTable
{
    /**
     * @var string
     */
    public const ACTIONS = 'Actions';

    /**
     * @var string
     */
    public const DEFAULT_BILLING_ADDRESS = 'default_billing_address';

    /**
     * @var string
     */
    public const DEFAULT_SHIPPING_ADDRESS = 'default_shipping_address';

    /**
     * @var string
     */
    public const COL_COMPANY = 'Company';

    /**
     * @var string
     */
    protected const URL_EDIT_CUSTOMER_ADDRESS = '/customer/address/edit';

    /**
     * @var \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface
     */
    protected $customerQueryContainer;

    /**
     * @var int
     */
    protected $idCustomer;

    /**
     * @var \Spryker\Zed\Customer\Dependency\Service\CustomerToUtilSanitizeServiceInterface
     */
    protected $utilSanitize;

    /**
     * @param \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface $customerQueryContainer
     * @param int $idCustomer
     * @param \Spryker\Zed\Customer\Dependency\Service\CustomerToUtilSanitizeServiceInterface $utilSanitize
     */
    public function __construct(
        CustomerQueryContainerInterface $customerQueryContainer,
        $idCustomer,
        CustomerToUtilSanitizeServiceInterface $utilSanitize
    ) {
        $this->customerQueryContainer = $customerQueryContainer;
        $this->idCustomer = $idCustomer;
        $this->utilSanitize = $utilSanitize;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config)
    {
        $config->setHeader([
            SpyCustomerAddressTableMap::COL_ID_CUSTOMER_ADDRESS => '#',
            SpyCustomerAddressTableMap::COL_SALUTATION => 'Salutation',
            SpyCustomerAddressTableMap::COL_FIRST_NAME => 'First Name',
            SpyCustomerAddressTableMap::COL_LAST_NAME => 'Last Name',
            SpyCustomerAddressTableMap::COL_ADDRESS1 => 'Address ',
            SpyCustomerAddressTableMap::COL_ADDRESS2 => 'Address (2nd line)',
            SpyCustomerAddressTableMap::COL_ADDRESS3 => 'Address (3rd line)',
            SpyCustomerAddressTableMap::COL_COMPANY => 'Company',
            SpyCustomerAddressTableMap::COL_ZIP_CODE => 'Zip Code',
            SpyCustomerAddressTableMap::COL_CITY => 'City',
            static::COL_COMPANY => 'Country',
            static::ACTIONS => 'Actions',
        ]);

        $config->addRawColumn(static::ACTIONS);
        $config->addRawColumn(SpyCustomerAddressTableMap::COL_ADDRESS1);

        $config->setSortable([
            SpyCustomerAddressTableMap::COL_ID_CUSTOMER_ADDRESS,
            SpyCustomerAddressTableMap::COL_FIRST_NAME,
            SpyCustomerAddressTableMap::COL_LAST_NAME,
            SpyCustomerAddressTableMap::COL_ZIP_CODE,
            SpyCustomerAddressTableMap::COL_FK_COUNTRY,
        ]);

        $config->setSearchable([
            SpyCustomerAddressTableMap::COL_ID_CUSTOMER_ADDRESS,
            SpyCustomerAddressTableMap::COL_LAST_NAME,
            SpyCustomerAddressTableMap::COL_FIRST_NAME,
            SpyCustomerAddressTableMap::COL_ADDRESS1,
            SpyCustomerAddressTableMap::COL_ADDRESS2,
            SpyCustomerAddressTableMap::COL_ADDRESS3,
            SpyCustomerAddressTableMap::COL_ZIP_CODE,
        ]);

        $config->setUrl(sprintf('address-table?id-customer=%d', $this->idCustomer));

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Propel\Runtime\Collection\ObjectCollection|array
     */
    protected function prepareData(TableConfiguration $config)
    {
        $query = $this->customerQueryContainer->queryAddresses()
            ->filterByFkCustomer($this->idCustomer)
            ->leftJoinCountry('country')
            ->withColumn('country.name', static::COL_COMPANY);
        $lines = $this->runQuery($query, $config);

        $customer = $this->customerQueryContainer->queryCustomers()->findOneByIdCustomer($this->idCustomer);

        $defaultBillingAddress = $defaultShippingAddress = false;
        if ($customer !== null) {
            $customer = $customer->toArray();

            $defaultBillingAddress = !empty($customer[static::DEFAULT_BILLING_ADDRESS]) ? $customer[static::DEFAULT_BILLING_ADDRESS] : false;
            $defaultShippingAddress = !empty($customer[static::DEFAULT_SHIPPING_ADDRESS]) ? $customer[static::DEFAULT_SHIPPING_ADDRESS] : false;
        }

        if ($lines) {
            foreach ($lines as $key => $value) {
                $id = !empty($value[SpyCustomerAddressTableMap::COL_ID_CUSTOMER_ADDRESS]) ? $value[SpyCustomerAddressTableMap::COL_ID_CUSTOMER_ADDRESS] : false;

                $tags = [];
                if ((is_bool($id) === false) && ($id === $defaultBillingAddress)) {
                    $tags[] = $this->generateLabel('BILLING', 'label-danger');
                }
                if ((is_bool($id) === false) && ($id === $defaultShippingAddress)) {
                    $tags[] = $this->generateLabel('SHIPPING', 'label-danger');
                }

                $address = $this->utilSanitize->escapeHtml($lines[$key][SpyCustomerAddressTableMap::COL_ADDRESS1]);
                $lines[$key][SpyCustomerAddressTableMap::COL_ADDRESS1] = ($tags ? implode('&nbsp;', $tags) . '&nbsp;' : '') . $address;

                $lines[$key][static::ACTIONS] = $this->buildLinks($value);
            }
        }

        return $lines;
    }

    /**
     * @param array $details
     *
     * @return string
     */
    protected function buildLinks(array $details)
    {
        $buttons = [];

        $idCustomerAddress = !empty($details[SpyCustomerAddressTableMap::COL_ID_CUSTOMER_ADDRESS])
            ? $details[SpyCustomerAddressTableMap::COL_ID_CUSTOMER_ADDRESS]
            : null;

        if ($idCustomerAddress !== null) {
            $buttons[] = $this->generateEditButton(
                Url::generate(static::URL_EDIT_CUSTOMER_ADDRESS, [
                    CustomerConstants::PARAM_ID_CUSTOMER_ADDRESS => $idCustomerAddress,
                    CustomerConstants::PARAM_ID_CUSTOMER => $this->idCustomer,
                ]),
                'Edit',
            );
        }

        return implode(' ', $buttons);
    }
}
