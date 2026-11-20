<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Reader;

use Generated\Shared\Transfer\CustomerCriteriaFilterTransfer;
use Generated\Shared\Transfer\CustomerCriteriaSearchTermsTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Business\CustomerFacadeInterface;
use Spryker\Zed\Customer\CustomerConfig;

class CustomerSuggestionReader implements CustomerSuggestionReaderInterface
{
    public const string KEY_ID = 'id';

    public const string KEY_TEXT = 'text';

    protected const string CUSTOMER_NAME_PATTERN = '%s (%s %s, %s)';

    public function __construct(
        protected CustomerFacadeInterface $customerFacade,
        protected CustomerConfig $customerConfig
    ) {
    }

    /**
     * @return list<array<string, int|string>>
     */
    public function getCustomerSuggestions(string $searchTerm): array
    {
        if ($searchTerm === '') {
            return [];
        }

        $customerCriteriaFilterTransfer = (new CustomerCriteriaFilterTransfer())
            ->setSearchTerms(
                (new CustomerCriteriaSearchTermsTransfer())
                    ->setEmail($searchTerm)
                    ->setFirstName($searchTerm)
                    ->setLastName($searchTerm)
                    ->setCustomerReference($searchTerm),
            )
            ->setLimit($this->customerConfig->getCustomerSuggestionLimit());

        $customerSuggestions = [];

        foreach ($this->customerFacade->getCustomerCollectionByCriteria($customerCriteriaFilterTransfer)->getCustomers() as $customerTransfer) {
            $customerSuggestions[] = [
                static::KEY_ID => $customerTransfer->getIdCustomerOrFail(),
                static::KEY_TEXT => $this->formatCustomerName($customerTransfer),
            ];
        }

        return $customerSuggestions;
    }

    protected function formatCustomerName(CustomerTransfer $customerTransfer): string
    {
        return sprintf(
            static::CUSTOMER_NAME_PATTERN,
            $customerTransfer->getEmail(),
            $customerTransfer->getFirstName(),
            $customerTransfer->getLastName(),
            $customerTransfer->getCustomerReference(),
        );
    }
}
