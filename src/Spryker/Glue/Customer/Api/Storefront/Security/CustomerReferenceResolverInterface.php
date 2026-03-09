<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Security;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Extracts the customer reference from an authenticated API user's identifier.
 *
 * The user identifier for customer tokens is a JSON-encoded string from
 * CustomerIdentifierTransfer::toArray() containing the customer_reference key.
 */
interface CustomerReferenceResolverInterface
{
    /**
     * Resolves the customer reference from the user's identifier payload.
     *
     * @return string|null The customer reference (e.g. "DE--123"), or null if not resolvable.
     */
    public function resolveCustomerReference(UserInterface $user): ?string;
}
