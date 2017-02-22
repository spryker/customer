<?php

/**
 * (c) Spryker Systems GmbH copyright protected
 */

namespace Customer\Module;

use Codeception\Module;
use Codeception\TestCase;
use Propel\Runtime\Propel;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TwigServiceProvider as SilexTwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Spryker\Service\UtilDateTime\ServiceProvider\DateTimeFormatterServiceProvider;
use Spryker\Shared\Application\ServiceProvider\FormFactoryServiceProvider;
use Spryker\Shared\Kernel\Communication\Application;
use Spryker\Zed\Assertion\Communication\Plugin\ServiceProvider\AssertionServiceProvider;
use Spryker\Zed\Country\Business\CountryFacade;
use Spryker\Zed\Kernel\Communication\Plugin\Pimple;
use Spryker\Zed\Locale\Business\LocaleFacade;
use Spryker\Zed\Propel\Communication\Plugin\ServiceProvider\PropelServiceProvider;
use Spryker\Zed\Twig\Communication\Plugin\ServiceProvider\TwigServiceProvider;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Functional extends Module
{

    /**
     * @param array|null $config
     */
    public function __construct($config = null)
    {
        parent::__construct($config);

        $this->registerServiceProvider();
        $this->runInstaller();
    }

    /**
     * @return void
     */
    private function registerServiceProvider()
    {
        $application = new Application();
        $application->register(new AssertionServiceProvider());
        $application->register(new SilexTwigServiceProvider());
        $application->register(new ValidatorServiceProvider());
        $application->register(new FormServiceProvider());
        $application->register(new TwigServiceProvider());
        $application->register(new DateTimeFormatterServiceProvider());
        $application->register(new FormFactoryServiceProvider());

        $propelServiceProvider = new PropelServiceProvider();
        $propelServiceProvider->boot($application);

        $pimple = new Pimple();
        $pimple->setApplication($application);
    }

    /**
     * @return void
     */
    private function runInstaller()
    {
        $messenger = $this->getMessenger();

        $localeFacade = new LocaleFacade();
        $localeFacade->install($messenger);

        $countryFacade = new CountryFacade();
        $countryFacade->install($messenger);
    }

    /**
     * @return \Symfony\Component\Console\Logger\ConsoleLogger
     */
    protected function getMessenger()
    {
        $messenger = new ConsoleLogger(
            new ConsoleOutput(OutputInterface::VERBOSITY_QUIET)
        );

        return $messenger;
    }

    /**
     * @param \Codeception\TestCase $test
     *
     * @return void
     */
    public function _before(TestCase $test)
    {
        parent::_before($test);

        Propel::getWriteConnection('zed')->beginTransaction();
    }

    /**
     * @param \Codeception\TestCase $test
     *
     * @return void
     */
    public function _after(TestCase $test)
    {
        parent::_after($test);

        Propel::getWriteConnection('zed')->rollBack();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * @param \Codeception\TestCase $test
     * @param bool $fail
     *
     * @return void
     */
    public function _failed(TestCase $test, $fail)
    {
        parent::_failed($test, $fail);

        Propel::getWriteConnection('zed')->rollBack();

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

}
