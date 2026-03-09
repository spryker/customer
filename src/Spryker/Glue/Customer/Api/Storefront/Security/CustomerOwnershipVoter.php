<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\Customer\Api\Storefront\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants access when the authenticated customer's reference matches the
 * customerReference from the request URI.
 *
 * Used in YAML security expressions:
 *   is_granted('CUSTOMER_OWNER', request.attributes.get('customerReference'))
 *
 * @extends \Symfony\Component\Security\Core\Authorization\Voter\Voter<string, string>
 */
class CustomerOwnershipVoter extends Voter
{
    protected const string ATTRIBUTE_CUSTOMER_OWNER = 'CUSTOMER_OWNER';

    public function __construct(
        protected readonly CustomerReferenceResolverInterface $customerReferenceResolver,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === static::ATTRIBUTE_CUSTOMER_OWNER && is_string($subject);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($user === null) {
            return false;
        }

        $authenticatedCustomerReference = $this->customerReferenceResolver->resolveCustomerReference($user);

        if ($authenticatedCustomerReference === null) {
            return false;
        }

        return $authenticatedCustomerReference === $subject;
    }
}
