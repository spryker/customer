<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Customer\Updater;

use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\Session\CustomerSessionInterface;

class CustomerAddressUpdater implements CustomerAddressUpdaterInterface
{
    /**
     * @var \Spryker\Client\Customer\Session\CustomerSessionInterface
     */
    protected CustomerSessionInterface $customerSession;

    public function __construct(CustomerSessionInterface $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function updateCustomerAddresses(CustomerTransfer $customerTransfer): void
    {
        $currentCustomerTransfer = $this->customerSession->getCustomer();

        if ($currentCustomerTransfer === null) {
            return;
        }

        $currentCustomerTransfer->setAddresses($customerTransfer->getAddresses());
        $this->customerSession->setCustomer($currentCustomerTransfer);
    }
}
