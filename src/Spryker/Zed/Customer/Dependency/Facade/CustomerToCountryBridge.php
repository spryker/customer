<?php
/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Spryker\Zed\Customer\Dependency\Facade;

use Spryker\Zed\Country\Business\CountryFacade;

class CustomerToCountryBridge implements CustomerToCountryInterface
{

    /**
     * @var CountryFacade
     */
    protected $countryFacade;

    /**
     * CustomerToCountryBridge constructor.
     *
     * @param CountryFacade $countryFacade
     */
    public function __construct($countryFacade)
    {
        $this->countryFacade = $countryFacade;
    }

    /**
     * @param string $iso2Code
     *
     * @return int
     */
    public function getIdCountryByIso2Code($iso2Code)
    {
        return $this->countryFacade->getIdCountryByIso2Code($iso2Code);
    }

}
