<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Plugin\CompanyUser;

use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Spryker\Zed\CompanyUserExtension\Dependency\Plugin\CompanyUserPostUpdatePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\Customer\Business\CustomerBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\Customer\Persistence\CustomerRepositoryInterface getRepository()
 * @method \Spryker\Zed\Customer\Persistence\CustomerEntityManagerInterface getEntityManager()
 */
class CustomerInvalidationCompanyUserPostUpdatePlugin extends AbstractPlugin implements CompanyUserPostUpdatePluginInterface
{
    /**
     * {@inheritDoc}
     * - Invalidates customers associated with the given company user after it is saved.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyUserResponseTransfer $companyUserResponseTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    public function postUpdate(CompanyUserResponseTransfer $companyUserResponseTransfer): CompanyUserResponseTransfer
    {
        $customerTransfer = $companyUserResponseTransfer->getCompanyUser()->getCustomer();
        if ($customerTransfer === null) {
            return $companyUserResponseTransfer;
        }

        $this->getBusinessFactory()->createCustomerInvalidator()->invalidate([$customerTransfer]);

        return $companyUserResponseTransfer;
    }
}
