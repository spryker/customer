<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerTest\Zed\Customer\Business\Facade;

use Generated\Shared\Transfer\CustomerCollectionCriteriaTransfer;
use Generated\Shared\Transfer\CustomerCollectionTransfer;
use Generated\Shared\Transfer\CustomerConditionsTransfer;
use Generated\Shared\Transfer\CustomerCriteriaSearchTermsTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaginationTransfer;
use Generated\Shared\Transfer\SortTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group GetCustomerCollectionByCollectionCriteriaTest
 * Add your own group annotations below this line
 */
class GetCustomerCollectionByCollectionCriteriaTest extends AbstractCustomerFacadeTest
{
    protected const string SORT_FIELD_EMAIL = 'email';

    protected const string SORT_FIELD_UNKNOWN = 'id_customer; DROP TABLE spy_customer; --';

    public function testGetCustomerCollectionByCollectionCriteriaFiltersByCustomerReferences(): void
    {
        // Arrange
        $expectedCustomerTransfer = $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);
        $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions(
                (new CustomerConditionsTransfer())->addCustomerReference($expectedCustomerTransfer->getCustomerReferenceOrFail()),
            );

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(1, $customerCollectionTransfer->getCustomers()->count());
        $this->assertSame(
            $expectedCustomerTransfer->getCustomerReferenceOrFail(),
            $customerCollectionTransfer->getCustomers()->offsetGet(0)->getCustomerReferenceOrFail(),
        );
    }

    public function testGetCustomerCollectionByCollectionCriteriaFiltersByEmails(): void
    {
        // Arrange
        $expectedCustomerTransfer = $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);
        $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions(
                (new CustomerConditionsTransfer())->addEmail($expectedCustomerTransfer->getEmailOrFail()),
            );

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(1, $customerCollectionTransfer->getCustomers()->count());
        $this->assertSame(
            $expectedCustomerTransfer->getEmailOrFail(),
            $customerCollectionTransfer->getCustomers()->offsetGet(0)->getEmailOrFail(),
        );
    }

    public function testGetCustomerCollectionByCollectionCriteriaFiltersByCustomerIds(): void
    {
        // Arrange
        $expectedCustomerTransfer = $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);
        $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions(
                (new CustomerConditionsTransfer())->addIdCustomer($expectedCustomerTransfer->getIdCustomerOrFail()),
            );

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(1, $customerCollectionTransfer->getCustomers()->count());
        $this->assertSame(
            $expectedCustomerTransfer->getIdCustomerOrFail(),
            $customerCollectionTransfer->getCustomers()->offsetGet(0)->getIdCustomerOrFail(),
        );
    }

    public function testGetCustomerCollectionByCollectionCriteriaAppliesSearchTermsWithOrLogic(): void
    {
        // Arrange
        $customerMatchedByEmail = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
            CustomerTransfer::EMAIL => 'unique_ccc_or_email@example.com',
            CustomerTransfer::FIRST_NAME => 'RegularCccFirst',
        ]);
        $customerMatchedByFirstName = $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
            CustomerTransfer::EMAIL => 'regular_ccc_or@example.com',
            CustomerTransfer::FIRST_NAME => 'UniqueCccOrFirst',
        ]);
        $this->tester->haveCustomer([
            CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
            CustomerTransfer::EMAIL => 'unrelated_ccc_or@example.com',
            CustomerTransfer::FIRST_NAME => 'UnrelatedCccFirst',
        ]);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions(
                (new CustomerConditionsTransfer())->setSearchTerms(
                    (new CustomerCriteriaSearchTermsTransfer())
                        ->setEmail('unique_ccc_or_email')
                        ->setFirstName('UniqueCccOrFirst'),
                ),
            );

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(2, $customerCollectionTransfer->getCustomers()->count());
        $customerReferences = $this->extractCustomerReferences($customerCollectionTransfer);
        $this->assertContains($customerMatchedByEmail->getCustomerReferenceOrFail(), $customerReferences);
        $this->assertContains($customerMatchedByFirstName->getCustomerReferenceOrFail(), $customerReferences);
    }

    public function testGetCustomerCollectionByCollectionCriteriaExcludesAnonymizedCustomersByDefault(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);
        $this->tester->getCustomerFacade()->anonymizeCustomer($customerTransfer);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions(
                (new CustomerConditionsTransfer())->addCustomerReference($customerTransfer->getCustomerReferenceOrFail()),
            );

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(0, $customerCollectionTransfer->getCustomers()->count());
    }

    public function testGetCustomerCollectionByCollectionCriteriaIncludesAnonymizedCustomersWhenRequested(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);
        $this->tester->getCustomerFacade()->anonymizeCustomer($customerTransfer);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions(
                (new CustomerConditionsTransfer())
                    ->addCustomerReference($customerTransfer->getCustomerReferenceOrFail())
                    ->setHasAnonymizedAt(true),
            );

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(1, $customerCollectionTransfer->getCustomers()->count());
        $this->assertNotNull($customerCollectionTransfer->getCustomers()->offsetGet(0)->getAnonymizedAt());
    }

    public function testGetCustomerCollectionByCollectionCriteriaExpandsPaginationWithTotals(): void
    {
        // Arrange
        $customerReferences = $this->haveCustomersWithReferences(3);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions((new CustomerConditionsTransfer())->setCustomerReferences($customerReferences))
            ->setPagination((new PaginationTransfer())->setPage(1)->setMaxPerPage(2));

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $paginationTransfer = $customerCollectionTransfer->getPagination();
        $this->assertNotNull($paginationTransfer);
        $this->assertSame(2, $customerCollectionTransfer->getCustomers()->count(), 'Page size was not applied.');
        $this->assertSame(3, $paginationTransfer->getNbResults(), 'Total result count is wrong.');
        $this->assertSame(2, $paginationTransfer->getLastPage(), 'Last page was not calculated.');
        $this->assertSame(2, $paginationTransfer->getNextPage(), 'Next page was not calculated.');
    }

    public function testGetCustomerCollectionByCollectionCriteriaReturnsRemainderOnLastPage(): void
    {
        // Arrange
        $customerReferences = $this->haveCustomersWithReferences(3);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions((new CustomerConditionsTransfer())->setCustomerReferences($customerReferences))
            ->setPagination((new PaginationTransfer())->setPage(2)->setMaxPerPage(2));

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(1, $customerCollectionTransfer->getCustomers()->count());
        $this->assertSame(3, $customerCollectionTransfer->getPagination()->getNbResults());
    }

    public function testGetCustomerCollectionByCollectionCriteriaSortsByWhitelistedFieldDescending(): void
    {
        // Arrange
        $customerReferences = $this->haveCustomersWithEmails([
            'ccc_sort_a@example.com',
            'ccc_sort_b@example.com',
            'ccc_sort_c@example.com',
        ]);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions((new CustomerConditionsTransfer())->setCustomerReferences($customerReferences))
            ->addSort((new SortTransfer())->setField(static::SORT_FIELD_EMAIL)->setIsAscending(false));

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(
            ['ccc_sort_c@example.com', 'ccc_sort_b@example.com', 'ccc_sort_a@example.com'],
            $this->extractEmails($customerCollectionTransfer),
        );
    }

    public function testGetCustomerCollectionByCollectionCriteriaSortsByWhitelistedFieldAscending(): void
    {
        // Arrange
        $customerReferences = $this->haveCustomersWithEmails([
            'ccc_asc_b@example.com',
            'ccc_asc_c@example.com',
            'ccc_asc_a@example.com',
        ]);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions((new CustomerConditionsTransfer())->setCustomerReferences($customerReferences))
            ->addSort((new SortTransfer())->setField(static::SORT_FIELD_EMAIL)->setIsAscending(true));

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(
            ['ccc_asc_a@example.com', 'ccc_asc_b@example.com', 'ccc_asc_c@example.com'],
            $this->extractEmails($customerCollectionTransfer),
        );
    }

    public function testGetCustomerCollectionByCollectionCriteriaIgnoresSortFieldOutsideTheWhitelist(): void
    {
        // Arrange
        $customerReferences = $this->haveCustomersWithReferences(2);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions((new CustomerConditionsTransfer())->setCustomerReferences($customerReferences))
            ->addSort((new SortTransfer())->setField(static::SORT_FIELD_UNKNOWN)->setIsAscending(true));

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(
            2,
            $customerCollectionTransfer->getCustomers()->count(),
            'The unknown sort field should have been ignored, leaving the result set intact.',
        );
    }

    public function testGetCustomerCollectionByCollectionCriteriaReturnsEmptyCollectionWhenNothingMatches(): void
    {
        // Arrange
        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions(
                (new CustomerConditionsTransfer())->addCustomerReference('nonexistent-ccc-reference-12345'),
            );

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertSame(0, $customerCollectionTransfer->getCustomers()->count());
    }

    public function testGetCustomerCollectionByCollectionCriteriaDoesNotExpandCustomersWithAddresses(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD]);

        $customerCollectionCriteriaTransfer = (new CustomerCollectionCriteriaTransfer())
            ->setCustomerConditions(
                (new CustomerConditionsTransfer())->addCustomerReference($customerTransfer->getCustomerReferenceOrFail()),
            );

        // Act
        $customerCollectionTransfer = $this->tester->getCustomerFacade()->getCustomerCollectionByCollectionCriteria(
            $customerCollectionCriteriaTransfer,
        );

        // Assert
        $this->assertNull($customerCollectionTransfer->getCustomers()->offsetGet(0)->getAddresses());
    }

    /**
     * @return array<int, string>
     */
    protected function haveCustomersWithReferences(int $numberOfCustomers): array
    {
        $customerReferences = [];

        for ($i = 0; $i < $numberOfCustomers; $i++) {
            $customerReferences[] = $this->tester->haveCustomer([
                CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
            ])->getCustomerReferenceOrFail();
        }

        return $customerReferences;
    }

    /**
     * @param array<int, string> $emails
     *
     * @return array<int, string>
     */
    protected function haveCustomersWithEmails(array $emails): array
    {
        $customerReferences = [];

        foreach ($emails as $email) {
            $customerReferences[] = $this->tester->haveCustomer([
                CustomerTransfer::PASSWORD => static::VALUE_VALID_PASSWORD,
                CustomerTransfer::EMAIL => $email,
            ])->getCustomerReferenceOrFail();
        }

        return $customerReferences;
    }

    /**
     * @return array<int, string>
     */
    protected function extractCustomerReferences(CustomerCollectionTransfer $customerCollectionTransfer): array
    {
        return array_map(
            static fn (CustomerTransfer $customerTransfer): string => $customerTransfer->getCustomerReferenceOrFail(),
            $customerCollectionTransfer->getCustomers()->getArrayCopy(),
        );
    }

    /**
     * @return array<int, string>
     */
    protected function extractEmails(CustomerCollectionTransfer $customerCollectionTransfer): array
    {
        return array_map(
            static fn (CustomerTransfer $customerTransfer): string => $customerTransfer->getEmailOrFail(),
            $customerCollectionTransfer->getCustomers()->getArrayCopy(),
        );
    }
}
