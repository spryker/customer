<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Communication\Controller;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Zed\Customer\Communication\Controller\SuggestController;
use Spryker\Zed\Customer\Communication\CustomerCommunicationFactory;
use Spryker\Zed\Customer\CustomerConfig;
use SprykerTest\Zed\Customer\CustomerCommunicationTester;
use Symfony\Component\HttpFoundation\Request;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Communication
 * @group Controller
 * @group SuggestControllerTest
 * Add your own group annotations below this line
 */
class SuggestControllerTest extends Unit
{
    protected const string SEARCH_TERM = 'customer_suggest_unit_';

    protected const string KEY_RESULTS = 'results';

    protected const string KEY_ID = 'id';

    protected const string KEY_TEXT = 'text';

    protected CustomerCommunicationTester $tester;

    public function testIndexActionSuggestsCustomersWhoseEmailMatchesTheTerm(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([CustomerTransfer::EMAIL => static::SEARCH_TERM . 'match@example.com']);

        // Act
        $results = $this->requestSuggestions(static::SEARCH_TERM, $this->createSuggestController());

        // Assert
        $this->assertSame([$customerTransfer->getIdCustomerOrFail()], array_column($results, static::KEY_ID));
        $this->assertStringContainsString($customerTransfer->getEmailOrFail(), $results[0][static::KEY_TEXT]);
    }

    public function testIndexActionSuggestsTheCustomerWhoseReferenceMatchesTheTerm(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer();

        // Act
        $results = $this->requestSuggestions($customerTransfer->getCustomerReferenceOrFail(), $this->createSuggestController());

        // Assert
        $resultsById = array_column($results, static::KEY_TEXT, static::KEY_ID);
        $this->assertArrayHasKey($customerTransfer->getIdCustomerOrFail(), $resultsById);
        $this->assertStringContainsString(
            $customerTransfer->getCustomerReferenceOrFail(),
            $resultsById[$customerTransfer->getIdCustomerOrFail()],
        );
    }

    public function testIndexActionReturnsAtMostTheSuggestionLimit(): void
    {
        // Arrange
        $this->tester->haveCustomer([CustomerTransfer::EMAIL => static::SEARCH_TERM . 'first@example.com']);
        $this->tester->haveCustomer([CustomerTransfer::EMAIL => static::SEARCH_TERM . 'second@example.com']);

        // Act
        $results = $this->requestSuggestions(static::SEARCH_TERM, $this->createSuggestController(1));

        // Assert
        $this->assertCount(1, $results);
    }

    public function testIndexActionReturnsNoSuggestionsForAnEmptyTerm(): void
    {
        // Act
        $results = $this->requestSuggestions('  ', $this->createSuggestController());

        // Assert
        $this->assertSame([], $results);
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function requestSuggestions(string $term, SuggestController $suggestController): array
    {
        $response = $suggestController->indexAction(new Request(['term' => $term]));

        return json_decode((string)$response->getContent(), true)[static::KEY_RESULTS];
    }

    protected function createSuggestController(?int $suggestionLimit = null): SuggestController
    {
        $customerCommunicationFactory = new CustomerCommunicationFactory();

        if ($suggestionLimit !== null) {
            $customerCommunicationFactory->setConfig($this->createConfigWithSuggestionLimit($suggestionLimit));
        }

        return new class ($customerCommunicationFactory) extends SuggestController {
            public function __construct(CustomerCommunicationFactory $customerCommunicationFactory)
            {
                $this->factory = $customerCommunicationFactory;
            }
        };
    }

    protected function createConfigWithSuggestionLimit(int $suggestionLimit): CustomerConfig
    {
        return new class ($suggestionLimit) extends CustomerConfig {
            public function __construct(protected int $suggestionLimit)
            {
            }

            public function getCustomerSuggestionLimit(): int
            {
                return $this->suggestionLimit;
            }
        };
    }
}
