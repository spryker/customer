<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Business\Executor;

use Generated\Shared\Transfer\CustomerTransfer;

interface CustomerPluginExecutorInterface
{
    public function executePostCustomerRegistrationPlugins(CustomerTransfer $customerTransfer): void;

    public function executeCustomerPostDeletePlugins(CustomerTransfer $customerTransfer): void;
}
