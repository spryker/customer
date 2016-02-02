<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Customer\Communication\Form\DataProvider;

use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Spryker\Zed\Customer\Communication\Form\CustomerForm;
use Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface;

class CustomerFormDataProvider extends AbstractCustomerDataProvider
{

    /**
     * @var CustomerQueryContainerInterface
     */
    protected $customerQueryContainer;

    /**
     * @param $customerQueryContainer
     */
    public function __construct($customerQueryContainer)
    {
        $this->customerQueryContainer = $customerQueryContainer;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            CustomerForm::OPTION_SALUTATION_CHOICES => $this->getSalutationChoices(),
            CustomerForm::OPTION_GENDER_CHOICES => $this->getGenderChoices(),
        ];
    }

    /**
     * @return array
     */
    protected function getGenderChoices()
    {
        $genderSet = SpyCustomerTableMap::getValueSet(SpyCustomerTableMap::COL_GENDER);

        return array_combine($genderSet, $genderSet);
    }

}