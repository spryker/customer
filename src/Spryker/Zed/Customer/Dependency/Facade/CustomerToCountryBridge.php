<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Dependency\Facade;

use Generated\Shared\Transfer\CountryCollectionTransfer;
use Generated\Shared\Transfer\CountryTransfer;
use Generated\Shared\Transfer\RegionCollectionTransfer;
use Generated\Shared\Transfer\RegionCriteriaTransfer;

class CustomerToCountryBridge implements CustomerToCountryInterface
{
    protected const string REGION_ISO_2_CODE_SEPARATOR = '-';

    /**
     * @var \Spryker\Zed\Country\Business\CountryFacadeInterface
     */
    protected $countryFacade;

    /**
     * @param \Spryker\Zed\Country\Business\CountryFacadeInterface $countryFacade
     */
    public function __construct($countryFacade)
    {
        $this->countryFacade = $countryFacade;
    }

    public function getPreferredCountryByName(string $countryName): CountryTransfer
    {
        return $this->countryFacade->getPreferredCountryByName($countryName);
    }

    public function getAvailableCountries(): CountryCollectionTransfer
    {
        return $this->countryFacade->getAvailableCountries();
    }

    public function getCountryByIso2Code(string $iso2Code): CountryTransfer
    {
        return $this->countryFacade->getCountryByIso2Code($iso2Code);
    }

    public function getRegionCollection(RegionCriteriaTransfer $regionCriteriaTransfer): RegionCollectionTransfer
    {
        /** @phpstan-ignore function.alreadyNarrowedType */
        if (method_exists($this->countryFacade, 'getRegionCollection')) {
            return $this->countryFacade->getRegionCollection($regionCriteriaTransfer);
        }

        $regionIso2Codes = $regionCriteriaTransfer->getRegionConditions()?->getIso2Codes() ?? [];

        if (!$regionIso2Codes) {
            return new RegionCollectionTransfer();
        }

        $countryCollectionTransfer = $this->countryFacade->findCountriesByIso2Codes(
            $this->buildCountryCollection($this->extractCountryIso2Codes($regionIso2Codes)),
        );

        return $this->filterRegionsByIso2Codes($countryCollectionTransfer, $regionIso2Codes);
    }

    /**
     * @param array<string> $regionIso2Codes
     *
     * @return array<string>
     */
    protected function extractCountryIso2Codes(array $regionIso2Codes): array
    {
        $countryIso2Codes = [];

        foreach ($regionIso2Codes as $regionIso2Code) {
            $separatorPosition = strpos($regionIso2Code, static::REGION_ISO_2_CODE_SEPARATOR);

            if ($separatorPosition === false) {
                continue;
            }

            $countryIso2Codes[] = substr($regionIso2Code, 0, $separatorPosition);
        }

        return array_unique($countryIso2Codes);
    }

    /**
     * @param array<string> $countryIso2Codes
     */
    protected function buildCountryCollection(array $countryIso2Codes): CountryCollectionTransfer
    {
        $countryCollectionTransfer = new CountryCollectionTransfer();

        foreach ($countryIso2Codes as $countryIso2Code) {
            $countryCollectionTransfer->addCountries((new CountryTransfer())->setIso2Code($countryIso2Code));
        }

        return $countryCollectionTransfer;
    }

    /**
     * @param array<string> $regionIso2Codes
     */
    protected function filterRegionsByIso2Codes(
        CountryCollectionTransfer $countryCollectionTransfer,
        array $regionIso2Codes
    ): RegionCollectionTransfer {
        $regionCollectionTransfer = new RegionCollectionTransfer();

        foreach ($this->extractRegions($countryCollectionTransfer) as $regionTransfer) {
            if (in_array($regionTransfer->getIso2Code(), $regionIso2Codes, true)) {
                $regionCollectionTransfer->addRegion($regionTransfer);
            }
        }

        return $regionCollectionTransfer;
    }

    /**
     * @return array<\Generated\Shared\Transfer\RegionTransfer>
     */
    protected function extractRegions(CountryCollectionTransfer $countryCollectionTransfer): array
    {
        $regionTransfers = [];

        foreach ($countryCollectionTransfer->getCountries() as $countryTransfer) {
            $regionTransfers = array_merge($regionTransfers, $countryTransfer->getRegions()->getArrayCopy());
        }

        return $regionTransfers;
    }
}
