<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Customer\Persistence;

use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;

/**
 * @method CustomerDependencyContainer getDependencyContainer()
 */
class CustomerQueryContainer extends AbstractQueryContainer implements CustomerQueryContainerInterface
{

    /**
     * @inheritdoc
     */
    public function queryCustomerByEmail($email)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query->filterByEmail($email);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryCustomerByEmailApartFromIdCustomer($email, $exceptIdCustomer)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query
            ->filterByEmail($email)
            ->filterByIdCustomer($exceptIdCustomer, Criteria::NOT_EQUAL);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryCustomerById($id)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query->filterByIdCustomer($id);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryCustomerByRegistrationKey($token)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query->filterByRegistrationKey($token);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryCustomerByRestorePasswordKey($token)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();
        $query->filterByRestorePasswordKey($token);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryAddressForCustomer($idAddress, $email)
    {
        $customer = $this->queryCustomerByEmail($email)->findOne();

        $query = $this->getDependencyContainer()->createSpyCustomerAddressQuery();
        $query->filterByIdCustomerAddress($idAddress);
        $query->filterByCustomer($customer);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryAddressByIdCustomer($idCustomer)
    {
        return $this
            ->getDependencyContainer()
            ->createSpyCustomerAddressQuery()
            ->filterByFkCustomer($idCustomer);
    }

    /**
     * @inheritdoc
     */
    public function queryAddress($idAddress)
    {
        $query = $this->getDependencyContainer()->createSpyCustomerAddressQuery();
        $query->joinWithCountry();
        $query->filterByIdCustomerAddress($idAddress);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryAddressesForCustomer($email)
    {
        $customer = $this->queryCustomerByEmail($email)->findOne();

        $query = $this->getDependencyContainer()->createSpyCustomerAddressQuery();
        $query->filterByCustomer($customer);

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryAddresses()
    {
        $query = $this->getDependencyContainer()->createSpyCustomerAddressQuery();

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function queryCustomers()
    {
        $query = $this->getDependencyContainer()->createSpyCustomerQuery();

        return $query;
    }

}
