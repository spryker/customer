<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Glue\Customer\StorefrontApi;

use Codeception\Stub;
use Generated\Shared\Transfer\CustomerResponseTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Client\Customer\CustomerClientInterface;
use SprykerTest\ApiPlatform\Test\StorefrontApiTestCase;
use SprykerTest\Glue\Customer\StorefrontApiTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group Customer
 * @group StorefrontApi
 * @group CustomersConfirmRegistrationStorefrontApiTest
 * Add your own group annotations below this line
 */
class CustomersConfirmRegistrationStorefrontApiTest extends StorefrontApiTestCase
{
    protected StorefrontApiTester $tester;

    protected function _before(): void
    {
        parent::_before();

        $this->markTestSkipped('Currently impossible to test this.');
    }

    public function testGivenValidRegistrationKeyWhenConfirmingRegistrationViaPostThenCustomerIsConfirmedAndDataIsReturned(): void
    {
        // Arrange
        $customerReference = 'DE--123';
        $registrationKey = 'valid-registration-key-123';

        $customerTransfer = (new CustomerTransfer())
            ->setCustomerReference($customerReference)
            ->setEmail('john.doe@example.com')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setSalutation('Mr')
            ->setGender('Male')
            ->setRegistrationKey($registrationKey)
            ->setCreatedAt('2024-01-15 10:30:00')
            ->setUpdatedAt('2024-01-15 10:30:00');

        $customerResponseTransfer = (new CustomerResponseTransfer())
            ->setHasCustomer(true)
            ->setCustomerTransfer($customerTransfer);

        $confirmedCustomerTransfer = (new CustomerTransfer())
            ->setCustomerReference($customerReference)
            ->setEmail('john.doe@example.com')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setSalutation('Mr')
            ->setGender('Male')
            ->setRegistrationKey(null)
            ->setCreatedAt('2024-01-15 10:30:00')
            ->setUpdatedAt('2024-01-15 10:35:00');

        $confirmationResponse = (new CustomerResponseTransfer())
            ->setIsSuccess(true)
            ->setCustomerTransfer($confirmedCustomerTransfer);

        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
            'findCustomerByReference' => $customerResponseTransfer,
            'confirmCustomerRegistration' => $confirmationResponse,
        ]);

        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);

        // Act
        $this->createClient()->request(
            'POST',
            sprintf('/customers/%s/confirm-registration', $customerReference),
            ['json' => ['registrationKey' => $registrationKey]],
        );

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['customerReference' => $customerReference]);
        $this->assertJsonContains(['email' => 'john.doe@example.com']);
        $this->assertJsonContains(['firstName' => 'John']);
        $this->assertJsonContains(['lastName' => 'Doe']);
    }

    public function testGivenNonExistentCustomerWhenConfirmingRegistrationViaPostThen404IsReturned(): void
    {
        // Arrange
        $customerReference = 'NON-EXISTENT';
        $registrationKey = 'some-key';

        $customerResponseTransfer = (new CustomerResponseTransfer())
            ->setHasCustomer(false);

        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
            'findCustomerByReference' => $customerResponseTransfer,
        ]);

        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);

        // Act
        $this->createClient()->request(
            'POST',
            sprintf('/customers/%s/confirm-registration', $customerReference),
            ['json' => ['registrationKey' => $registrationKey]],
        );

        // Assert
        $this->assertResponseStatusCodeSame(404);
    }

    public function testGivenAlreadyConfirmedCustomerWhenConfirmingRegistrationViaPostThen409IsReturned(): void
    {
        // Arrange
        $customerReference = 'DE--456';
        $registrationKey = 'some-key';

        $customerTransfer = (new CustomerTransfer())
            ->setCustomerReference($customerReference)
            ->setEmail('jane.doe@example.com')
            ->setRegistrationKey(null);

        $customerResponseTransfer = (new CustomerResponseTransfer())
            ->setHasCustomer(true)
            ->setCustomerTransfer($customerTransfer);

        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
            'findCustomerByReference' => $customerResponseTransfer,
        ]);

        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);

        // Act
        $this->createClient()->request(
            'POST',
            sprintf('/customers/%s/confirm-registration', $customerReference),
            ['json' => ['registrationKey' => $registrationKey]],
        );

        // Assert
        $this->assertResponseStatusCodeSame(409);
    }

    public function testGivenInvalidRegistrationKeyWhenConfirmingRegistrationViaPostThen422IsReturned(): void
    {
        // Arrange
        $customerReference = 'DE--789';
        $registrationKey = 'invalid-key';

        $customerTransfer = (new CustomerTransfer())
            ->setCustomerReference($customerReference)
            ->setEmail('test@example.com')
            ->setRegistrationKey('different-valid-key');

        $customerResponseTransfer = (new CustomerResponseTransfer())
            ->setHasCustomer(true)
            ->setCustomerTransfer($customerTransfer);

        $confirmationResponse = (new CustomerResponseTransfer())
            ->setIsSuccess(false);

        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
            'findCustomerByReference' => $customerResponseTransfer,
            'confirmCustomerRegistration' => $confirmationResponse,
        ]);

        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);

        // Act
        $this->createClient()->request(
            'POST',
            sprintf('/customers/%s/confirm-registration', $customerReference),
            ['json' => ['registrationKey' => $registrationKey]],
        );

        // Assert
        $this->assertResponseStatusCodeSame(422);
    }

    public function testGivenMissingRegistrationKeyWhenConfirmingRegistrationViaPostThen422IsReturned(): void
    {
        // Arrange
        $customerReference = 'DE--999';

        // Act
        $this->createClient()->request(
            'POST',
            sprintf('/customers/%s/confirm-registration', $customerReference),
            ['json' => []],
        );

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
    }
}
