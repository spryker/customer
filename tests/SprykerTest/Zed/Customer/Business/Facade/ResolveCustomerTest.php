<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Customer\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\OauthCustomerResolveRequestTransfer;
use Generated\Shared\Transfer\OauthCustomerRestrictionResponseTransfer;
use Generated\Shared\Transfer\ResourceOwnerTransfer;
use Spryker\Zed\Customer\Communication\Plugin\Customer\AcceptOnlyOauthCustomerAuthenticationStrategyPlugin;
use Spryker\Zed\Customer\Communication\Plugin\Customer\CreateCustomerOauthCustomerAuthenticationStrategyPlugin;
use Spryker\Zed\Customer\CustomerDependencyProvider;
use Spryker\Zed\CustomerExtension\Dependency\Plugin\OauthCustomerRestrictionPluginInterface;
use SprykerTest\Zed\Customer\CustomerBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group ResolveCustomerTest
 * Add your own group annotations below this line
 */
class ResolveCustomerTest extends Unit
{
    protected const string TEST_EMAIL = 'resolve-customer-test@spryker.com';

    protected const string TEST_PROVIDER = 'test-provider';

    protected CustomerBusinessTester $tester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_POST_RESOLVE, []);
        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_RESTRICTION, []);
    }

    public function testResolveCustomerReturnsSuccessWhenExistingCustomerFoundByEmail(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([CustomerTransfer::EMAIL => static::TEST_EMAIL]);

        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_AUTHENTICATION_STRATEGY,
            [new AcceptOnlyOauthCustomerAuthenticationStrategyPlugin()],
        );

        $requestTransfer = $this->buildResolveRequest(static::TEST_EMAIL);

        // Act
        $responseTransfer = $this->tester->getFacade()->resolveCustomer($requestTransfer);

        // Assert
        $this->assertTrue($responseTransfer->getIsSuccessful(), 'Expected successful resolution for an existing customer.');
        $this->assertSame($customerTransfer->getIdCustomer(), $responseTransfer->getCustomerOrFail()->getIdCustomer());
    }

    public function testResolveCustomerReturnsFailureWhenNoCustomerExistsForEmail(): void
    {
        // Arrange
        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_AUTHENTICATION_STRATEGY,
            [new AcceptOnlyOauthCustomerAuthenticationStrategyPlugin()],
        );

        $requestTransfer = $this->buildResolveRequest('unknown@spryker.com');

        // Act
        $responseTransfer = $this->tester->getFacade()->resolveCustomer($requestTransfer);

        // Assert
        $this->assertFalse($responseTransfer->getIsSuccessful(), 'Expected failure when no customer exists for that email.');
    }

    public function testResolveCustomerReturnsFailureWhenCustomerIsAnonymized(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([CustomerTransfer::EMAIL => static::TEST_EMAIL]);
        $this->tester->getFacade()->anonymizeCustomer($customerTransfer);

        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_AUTHENTICATION_STRATEGY,
            [new AcceptOnlyOauthCustomerAuthenticationStrategyPlugin()],
        );

        $requestTransfer = $this->buildResolveRequest(static::TEST_EMAIL);

        // Act
        $responseTransfer = $this->tester->getFacade()->resolveCustomer($requestTransfer);

        // Assert
        $this->assertFalse($responseTransfer->getIsSuccessful(), 'Expected failure when customer is anonymized.');
    }

    public function testResolveCustomerCreatesNewCustomerWhenStrategyIsCreateOnFirstLogin(): void
    {
        // Arrange
        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_AUTHENTICATION_STRATEGY,
            [new CreateCustomerOauthCustomerAuthenticationStrategyPlugin()],
        );

        $requestTransfer = $this->buildResolveRequest(static::TEST_EMAIL);

        // Act
        $responseTransfer = $this->tester->getFacade()->resolveCustomer($requestTransfer);

        // Assert
        $this->assertTrue($responseTransfer->getIsSuccessful(), 'Expected successful resolution when customer is created on first login.');
        $this->assertSame(static::TEST_EMAIL, $responseTransfer->getCustomerOrFail()->getEmail());
    }

    public function testResolveCustomerReturnsFailureWhenNoStrategyPluginsAreWired(): void
    {
        // Arrange
        $this->tester->haveCustomer([CustomerTransfer::EMAIL => static::TEST_EMAIL]);

        // Wire empty plugin stack — graceful degradation
        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_AUTHENTICATION_STRATEGY, []);

        $requestTransfer = $this->buildResolveRequest(static::TEST_EMAIL);

        // Act
        $responseTransfer = $this->tester->getFacade()->resolveCustomer($requestTransfer);

        // Assert
        $this->assertFalse($responseTransfer->getIsSuccessful(), 'Expected failure when no authentication strategy plugins are wired.');
    }

    public function testResolveCustomerReturnsRestrictedWhenRestrictionPluginBlocksLogin(): void
    {
        // Arrange
        $this->tester->haveCustomer([CustomerTransfer::EMAIL => static::TEST_EMAIL]);

        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_AUTHENTICATION_STRATEGY,
            [new AcceptOnlyOauthCustomerAuthenticationStrategyPlugin()],
        );

        $restrictionPlugin = $this->createRestrictionPluginMock(true, 'Access denied by policy.');
        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_RESTRICTION, [$restrictionPlugin]);

        $requestTransfer = $this->buildResolveRequest(static::TEST_EMAIL);

        // Act
        $responseTransfer = $this->tester->getFacade()->resolveCustomer($requestTransfer);

        // Assert
        $this->assertFalse($responseTransfer->getIsSuccessful(), 'Expected failure when restriction plugin blocks login.');
        $this->assertNotEmpty($responseTransfer->getMessages(), 'Expected restriction messages to be populated.');
    }

    public function testResolveCustomerSucceedsWhenRestrictionPluginAllowsLogin(): void
    {
        // Arrange
        $this->tester->haveCustomer([CustomerTransfer::EMAIL => static::TEST_EMAIL]);

        $this->tester->setDependency(
            CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_AUTHENTICATION_STRATEGY,
            [new AcceptOnlyOauthCustomerAuthenticationStrategyPlugin()],
        );

        $restrictionPlugin = $this->createRestrictionPluginMock(false);
        $this->tester->setDependency(CustomerDependencyProvider::PLUGINS_OAUTH_CUSTOMER_RESTRICTION, [$restrictionPlugin]);

        $requestTransfer = $this->buildResolveRequest(static::TEST_EMAIL);

        // Act
        $responseTransfer = $this->tester->getFacade()->resolveCustomer($requestTransfer);

        // Assert
        $this->assertTrue($responseTransfer->getIsSuccessful(), 'Expected success when restriction plugin allows login.');
    }

    protected function buildResolveRequest(string $email): OauthCustomerResolveRequestTransfer
    {
        return (new OauthCustomerResolveRequestTransfer())
            ->setResourceOwner(
                (new ResourceOwnerTransfer())
                    ->setEmail($email)
                    ->setProvider(static::TEST_PROVIDER)
                    ->setId('external-123'),
            );
    }

    protected function createRestrictionPluginMock(
        bool $isRestricted,
        ?string $message = null,
    ): OauthCustomerRestrictionPluginInterface {
        $mock = $this->getMockBuilder(OauthCustomerRestrictionPluginInterface::class)->getMock();

        $responseTransfer = (new OauthCustomerRestrictionResponseTransfer())->setIsRestricted($isRestricted);

        if ($isRestricted && $message !== null) {
            $responseTransfer->addMessage(
                (new MessageTransfer())->setValue($message),
            );
        }

        $mock->method('isRestricted')->willReturn($responseTransfer);

        return $mock;
    }
}
