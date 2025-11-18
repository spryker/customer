<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Customer\Plugin\SessionCustomerValidationPage;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\SessionEntityRequestTransfer;
use Generated\Shared\Transfer\SessionEntityResponseTransfer;
use Spryker\Client\Kernel\AbstractPlugin;
use SprykerShop\Yves\SessionCustomerValidationPageExtension\Dependency\Plugin\CustomerSessionValidatorPluginInterface;

/**
 * @method \Spryker\Client\Customer\CustomerFactory getFactory()
 */
class StorageInvalidationRecordCustomerSessionValidatorPlugin extends AbstractPlugin implements CustomerSessionValidatorPluginInterface
{
    /**
     * {@inheritDoc}
     * - Validates customer session by checking for invalidation records in storage (e.g., Redis).
     * - If an invalidation record is found for the customer, it indicates that the session should be invalidated.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\SessionEntityRequestTransfer $sessionEntityRequestTransfer
     *
     * @return \Generated\Shared\Transfer\SessionEntityResponseTransfer
     */
    public function validate(SessionEntityRequestTransfer $sessionEntityRequestTransfer): SessionEntityResponseTransfer
    {
        $customerTransfer = (new CustomerTransfer())->setIdCustomer($sessionEntityRequestTransfer->getIdEntity());
        $record = $this->getFactory()->createInvalidationRecordChecker()->findInvalidationRecord($customerTransfer);

        $sessionEntityResponseTransfer = (new SessionEntityResponseTransfer())
            ->setIsSuccessfull(true);
        if ($record !== null) {
            $sessionEntityResponseTransfer->setIsSuccessfull(false);
        }

        return $sessionEntityResponseTransfer;
    }
}
