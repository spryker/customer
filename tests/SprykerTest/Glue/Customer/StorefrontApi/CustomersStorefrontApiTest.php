<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\Customer\StorefrontApi;

use SprykerTest\ApiPlatform\Test\StorefrontApiTestCase;
use SprykerTest\Glue\Customer\StorefrontApiTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Glue
 * @group Customer
 * @group StorefrontApi
 * @group CustomersStorefrontApiTest
 * Add your own group annotations below this line
 */
class CustomersStorefrontApiTest extends StorefrontApiTestCase
{
    protected StorefrontApiTester $tester;

    protected const string PASSWORD = '$%uXChange123!@#';

    protected function _before(): void
    {
        parent::_before();

        $this->markTestSkipped('Currently impossible to test this.');
    }

    public function testGivenMissingEmailWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'email']]]);
    }

    public function testGivenMissingSalutationWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'salutation']]]);
    }

    public function testGivenMissingFirstNameWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
            'salutation' => 'Mr',
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'firstName']]]);
    }

    public function testGivenMissingLastNameWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
            'salutation' => 'Mr',
            'firstName' => 'John',
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'lastName']]]);
    }

    public function testGivenMissingGenderWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
            'salutation' => 'Mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'gender']]]);
    }

    public function testGivenMissingPasswordWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
            'salutation' => 'Mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'Male',
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'password']]]);
    }

    public function testGivenMissingConfirmPasswordWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
            'salutation' => 'Mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'Male',
            'password' => static::PASSWORD,
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'confirmPassword']]]);
    }

    public function testGivenValidDataWhenCreatingCustomerViaPostThenCustomerIsCreatedSuccessfully(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
            'salutation' => 'Mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'Male',
            'password' => static::PASSWORD,
            'confirmPassword' => static::PASSWORD,
            'acceptedTerms' => true,
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['email' => $customerData['email']]);
        $this->assertJsonContains(['firstName' => 'John']);
    }

    public function testGivenDuplicateEmailWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $this->tester->haveCustomer(['email' => 'existing@example.com']);

        $customerData = [
            'email' => 'existing@example.com',
            'salutation' => 'Mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'Male',
            'password' => static::PASSWORD,
            'confirmPassword' => static::PASSWORD,
            'acceptedTerms' => true,
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
    }

    public function testGivenWeakPasswordWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
            'salutation' => 'Mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'Male',
            'password' => 'weak',
            'confirmPassword' => 'weak',
            'acceptedTerms' => true,
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'password']]]);
    }

    public function testGivenTermsNotAcceptedWhenCreatingCustomerViaPostThenValidationErrorIsReturned(): void
    {
        // Arrange
        $customerData = [
            'email' => sprintf('john.doe.%s@example.com', time()),
            'salutation' => 'Mr',
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'Male',
            'password' => static::PASSWORD,
            'confirmPassword' => static::PASSWORD,
            'acceptedTerms' => false,
        ];

        // Act
        $this->createClient()->request('POST', '/customers', ['json' => $customerData]);

        // Assert
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['violations' => [['propertyPath' => 'acceptedTerms']]]);
    }

//    public function testGivenAuthenticatedCustomerWhenRetrievingOwnProfileViaGetThenCustomerDataIsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//            'email' => sprintf('test.%s@example.com', time()),
//            'firstName' => 'Test',
//            'lastName' => 'User',
//        ]);
//
//        $token = $this->tester->haveAuthToken($customerTransfer);
//
//        // Act
//        $this->createClient()->request(
//            'GET',
//            sprintf('/customers/%s', $customerTransfer->getCustomerReference()),
//            ['auth_bearer' => $token]
//        );
//
//        // Assert
//        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains(['email' => $customerTransfer->getEmail()]);
//        $this->assertJsonContains(['firstName' => 'Test']);
//    }
//
//    public function testGivenAuthenticatedCustomerWhenAccessingAnotherCustomerProfileViaGetThen403IsReturned(): void
//    {
//        // Arrange
//        $customer1 = $this->tester->haveCustomer(['customerReference' => sprintf('TEST--%s-1', time())]);
//        $customer2 = $this->tester->haveCustomer(['customerReference' => sprintf('TEST--%s-2', time())]);
//
//        $token = $this->tester->haveAuthToken($customer1);
//
//        // Act
//        $this->createClient()->request(
//            'GET',
//            sprintf('/customers/%s', $customer2->getCustomerReference()),
//            ['auth_bearer' => $token]
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(403);
//    }
//
//    public function testGivenAuthenticatedCustomerWhenUpdatingOwnProfileViaPatchThenCustomerIsUpdatedSuccessfully(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//            'firstName' => 'Original',
//            'lastName' => 'Name',
//        ]);
//
//        $token = $this->tester->haveAuthToken($customerTransfer);
//
//        $updateData = [
//            'firstName' => 'Updated',
//            'lastName' => 'Name',
//        ];
//
//        // Act
//        $this->createClient()->request(
//            'PATCH',
//            sprintf('/customers/%s', $customerTransfer->getCustomerReference()),
//            [
//                'auth_bearer' => $token,
//                'json' => $updateData,
//                'headers' => ['Content-Type' => 'application/merge-patch+json'],
//            ]
//        );
//
//        // Assert
//        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains(['firstName' => 'Updated']);
//    }
//
//    public function testGivenAuthenticatedCustomerWhenAnonymizingOwnAccountViaDeleteThenCustomerIsAnonymizedSuccessfully(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer(['customerReference' => sprintf('TEST--%s', time())]);
//        $token = $this->tester->haveAuthToken($customerTransfer);
//
//        // Act
//        $this->createClient()->request(
//            'DELETE',
//            sprintf('/customers/%s', $customerTransfer->getCustomerReference()),
//            ['auth_bearer' => $token]
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(204);
//    }
}
