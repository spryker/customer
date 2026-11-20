<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\Customer\Business\CustomerFacadeInterface getFacade()
 * @method \Spryker\Zed\Customer\Communication\CustomerCommunicationFactory getFactory()
 * @method \Spryker\Zed\Customer\Persistence\CustomerQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Customer\Persistence\CustomerRepositoryInterface getRepository()
 */
class SuggestController extends AbstractController
{
    protected const string PARAM_TERM = 'term';

    protected const string KEY_RESULTS = 'results';

    public function indexAction(Request $request): JsonResponse
    {
        $customerSuggestions = $this->getFactory()
            ->createCustomerSuggestionReader()
            ->getCustomerSuggestions(trim((string)$request->query->get(static::PARAM_TERM)));

        return $this->jsonResponse([
            static::KEY_RESULTS => $customerSuggestions,
        ]);
    }
}
