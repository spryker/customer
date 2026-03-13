<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class CustomerReferenceResolver implements CustomerReferenceResolverInterface
{
    protected const string CUSTOMER_REFERENCE_KEY = 'customer_reference';

    public function resolveCustomerReference(UserInterface $user): ?string
    {
        $userIdentifier = $user->getUserIdentifier();
        $userData = json_decode($userIdentifier, true);

        if (!is_array($userData) || !isset($userData[static::CUSTOMER_REFERENCE_KEY])) {
            return null;
        }

        return (string)$userData[static::CUSTOMER_REFERENCE_KEY];
    }
}
