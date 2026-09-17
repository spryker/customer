<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types = 1);

namespace SprykerTest\Zed\Customer\Persistence\Propel;

use Codeception\Test\Unit;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Orm\Zed\Customer\Persistence\SpyCustomerQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Criterion\BasicCriterion;
use Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Persistence
 * @group Propel
 * @group AbstractSpyCustomerQueryTest
 * Add your own group annotations below this line
 */
class AbstractSpyCustomerQueryTest extends Unit
{
    protected const string EMAIL = 'sonia@spryker.com';

    protected const string EMAIL_TERM = '%sonia%';

    public function testFilterByEmailMatchesCaseInsensitively(): void
    {
        // Arrange
        $query = SpyCustomerQuery::create();

        // Act
        $query->filterByEmail(static::EMAIL);

        // Assert
        $criterion = $query->getCriterion(SpyCustomerTableMap::COL_EMAIL);
        $this->assertInstanceOf(BasicCriterion::class, $criterion);
        $this->assertTrue($criterion->isIgnoreCase());
    }

    public function testFilterByEmailMatchesCaseSensitivelyWhenAsked(): void
    {
        // Arrange
        $query = SpyCustomerQuery::create();

        // Act
        $query->filterByEmail(static::EMAIL, Criteria::EQUAL, false);

        // Assert
        $criterion = $query->getCriterion(SpyCustomerTableMap::COL_EMAIL);
        $this->assertInstanceOf(BasicCriterion::class, $criterion);
        $this->assertFalse($criterion->isIgnoreCase());
    }

    /**
     * An `IN` comparison builds an `InCriterion`, which has no `setIgnoreCase()`. Applying the
     * ignore-case step to it unguarded is a fatal, so `filterByEmail_In()` cannot be called at all.
     */
    public function testFilterByEmailAcceptsAnInComparison(): void
    {
        // Arrange
        $query = SpyCustomerQuery::create();

        // Act
        $query->filterByEmail_In([static::EMAIL]);

        // Assert
        $this->assertNotNull($query->getCriterion(SpyCustomerTableMap::COL_EMAIL));
    }

    /**
     * Reproduces the Backend API `GET /customers?filter[customers.email]=…&q=…` 500: the email filter
     * registers an `InCriterion`, then the search term adds a `LIKE` on the same column. Propel folds
     * the second condition into the *existing* criterion and hands that one back, so the ignore-case
     * step lands on the `InCriterion` again.
     */
    public function testFilterByEmailAcceptsASearchTermWhenAnInFilterIsAlreadyApplied(): void
    {
        // Arrange
        $query = SpyCustomerQuery::create();
        $query->addUsingAlias(SpyCustomerTableMap::COL_EMAIL, [static::EMAIL], Criteria::IN);

        // Act
        $query->filterByEmail(static::EMAIL_TERM, Criteria::LIKE);

        // Assert
        $this->assertNotNull($query->getCriterion(SpyCustomerTableMap::COL_EMAIL));
    }

    public function testFilterByEmailTranslatesTheWildcardOfALikeTerm(): void
    {
        // Arrange
        $query = SpyCustomerQuery::create();

        // Act
        $query->filterByEmail('*sonia*', Criteria::LIKE);

        // Assert
        $this->assertSame(static::EMAIL_TERM, $query->getCriterion(SpyCustomerTableMap::COL_EMAIL)->getValue());
    }

    public function testFilterByEmailRejectsAnArrayWithoutAnArrayComparison(): void
    {
        // Arrange
        $query = SpyCustomerQuery::create();

        // Assert
        $this->expectException(AmbiguousComparisonException::class);

        // Act
        $query->filterByEmail([static::EMAIL], Criteria::EQUAL);
    }
}
