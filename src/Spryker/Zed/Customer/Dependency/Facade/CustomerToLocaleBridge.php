<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Customer\Dependency\Facade;

use Generated\Shared\Transfer\LocaleTransfer;

class CustomerToLocaleBridge implements CustomerToLocaleInterface
{
    /**
     * @var \Spryker\Zed\Locale\Business\LocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @param \Spryker\Zed\Locale\Business\LocaleFacadeInterface $localeFacade
     */
    public function __construct($localeFacade)
    {
        $this->localeFacade = $localeFacade;
    }

    /**
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getCurrentLocale(): LocaleTransfer
    {
        return $this->localeFacade->getCurrentLocale();
    }

    /**
     * @return array<\Generated\Shared\Transfer\LocaleTransfer>
     */
    public function getLocaleCollection(): array
    {
        return $this->localeFacade->getLocaleCollection();
    }

    /**
     * @param int $idLocale
     *
     * @return \Generated\Shared\Transfer\LocaleTransfer
     */
    public function getLocaleById(int $idLocale): LocaleTransfer
    {
        return $this->localeFacade->getLocaleById($idLocale);
    }

    /**
     * @return string
     */
    public function getCurrentLocaleName(): string
    {
        return $this->localeFacade->getCurrentLocaleName();
    }
}
