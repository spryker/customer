<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Reader;

interface CustomerSuggestionReaderInterface
{
    /**
     * @return list<array<string, int|string>>
     */
    public function getCustomerSuggestions(string $searchTerm): array;
}
