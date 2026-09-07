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

interface CustomerToCountryInterface
{
    public function getPreferredCountryByName(string $countryName): CountryTransfer;

    public function getAvailableCountries(): CountryCollectionTransfer;

    public function getCountryByIso2Code(string $iso2Code): CountryTransfer;

    public function getRegionCollection(RegionCriteriaTransfer $regionCriteriaTransfer): RegionCollectionTransfer;
}
