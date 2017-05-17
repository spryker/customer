<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Controller;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Shared\Customer\CustomerConstants;
use Spryker\Zed\Customer\Business\Exception\CustomerNotFoundException;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\Customer\Business\CustomerFacade getFacade()
 * @method \Spryker\Zed\Customer\Communication\CustomerCommunicationFactory getFactory()
 */
class DeleteController extends AbstractController
{

    public function indexAction(Request $request)
    {
        $idCustomer = $this->castId($request->query->get(CustomerConstants::PARAM_ID_CUSTOMER));

        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setIdCustomer($idCustomer);

        try {
            $customerTransfer = $this->getFacade()->getCustomer($customerTransfer);
        } catch (CustomerNotFoundException $exception) {
            $this->addErrorMessage('Customer does not exist');
            return $this->redirectResponse('/customer');
        }

        return $this->viewResponse([
            'idCustomer' => $customerTransfer->getIdCustomer(),
        ]);
    }

    public function confirmAction(Request $request)
    {
        $idCustomer = $this->castId($request->query->get(CustomerConstants::PARAM_ID_CUSTOMER));

        $customerTransfer = new CustomerTransfer();
        $customerTransfer->setIdCustomer($idCustomer);

        try {
            $customerTransfer = $this->getFacade()->getCustomer($customerTransfer);
        } catch (CustomerNotFoundException $exception) {
            $this->addErrorMessage('Customer does not exist');
            return $this->redirectResponse('/customer');
        }

        $isSuccessful = $this->getFacade()->anonymizeCustomer($customerTransfer);

        if ($isSuccessful) {
            $this->addSuccessMessage('Customer successfully deleted');
        } else {
            $this->addSuccessMessage('Customer anonymization is failed');
        }

        return $this->redirectResponse('/customer');
    }

}