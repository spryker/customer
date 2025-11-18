<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Communication\Plugin\CompanyRole;

use Generated\Shared\Transfer\CompanyRoleTransfer;
use Spryker\Zed\CompanyRoleExtension\Dependency\Plugin\CompanyRolePostSavePluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\Customer\Business\CustomerBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\Customer\Persistence\CustomerRepositoryInterface getRepository()
 * @method \Spryker\Zed\Customer\Persistence\CustomerEntityManagerInterface getEntityManager()
 */
class CustomerInvalidationCompanyRolePostSavePlugin extends AbstractPlugin implements CompanyRolePostSavePluginInterface
{
    /**
     * {@inheritDoc}
     * - Invalidates customers associated with the company users of the given company role after it is saved.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CompanyRoleTransfer $companyRoleTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyRoleTransfer
     */
    public function postSave(CompanyRoleTransfer $companyRoleTransfer): CompanyRoleTransfer
    {
        $customerTransfers = [];
        foreach ($companyRoleTransfer->getCompanyUserCollection()->getCompanyUsers() as $companyUserTransfer) {
            $customerTransfer = $companyUserTransfer->getCustomer();
            $customerTransfers[$customerTransfer->getIdCustomer()] = $customerTransfer;
        }

        $this->getBusinessFactory()->createCustomerInvalidator()->invalidate($customerTransfers);

        return $companyRoleTransfer;
    }
}
