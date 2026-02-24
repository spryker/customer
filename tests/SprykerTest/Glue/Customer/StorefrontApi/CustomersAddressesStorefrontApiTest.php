<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Glue\Customer\StorefrontApi;

use Codeception\Stub;
use Generated\Shared\Transfer\AddressesTransfer;
use Generated\Shared\Transfer\AddressTransfer;
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
 * @group CustomersAddressesStorefrontApiTest
 * Add your own group annotations below this line
 */
class CustomersAddressesStorefrontApiTest extends StorefrontApiTestCase
{
    protected StorefrontApiTester $tester;

    protected const string PASSWORD = '$%uXChange123!@#';

    protected function _before(): void
    {
        parent::_before();

        $this->markTestSkipped('Currently impossible to test this.');
    }

    public function testGivenAuthenticatedCustomerWithAddressesWhenRetrievingCollectionViaGetThenAllAddressesAreReturned(): void
    {
        // Arrange
        $customerTransfer = $this->tester->haveCustomer([
            'customerReference' => sprintf('TEST--%s', time()),
        ]);

        $addressesTransfer = (new AddressesTransfer())
            ->addAddress(
                (new AddressTransfer())
                    ->setUuid('address-uuid-1')
                    ->setFirstName('John')
                    ->setLastName('Doe')
                    ->setAddress1('Street 1')
                    ->setAddress2('Apt 1')
                    ->setCity('Hamburg')
                    ->setZipCode('20095')
                    ->setIso2Code('DE')
                    ->setSalutation('Mr'),
            )
            ->addAddress(
                (new AddressTransfer())
                    ->setUuid('address-uuid-2')
                    ->setFirstName('Jane')
                    ->setLastName('Doe')
                    ->setAddress1('Street 2')
                    ->setAddress2('Apt 2')
                    ->setCity('Berlin')
                    ->setZipCode('10115')
                    ->setIso2Code('DE')
                    ->setSalutation('Mrs'),
            );

        $customerWithAddresses = (new CustomerTransfer())
            ->setCustomerReference($customerTransfer->getCustomerReference())
            ->setIdCustomer(1)
            ->setAddresses($addressesTransfer);

        $customerResponse = (new CustomerResponseTransfer())
            ->setCustomerTransfer($customerWithAddresses)
            ->setIsSuccess(true)
            ->setHasCustomer(true);

        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
            'findCustomerByReference' => $customerResponse,
        ]);

        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);

        // Act
        $response = $this->createClient()->request(
            'GET',
            sprintf('/customers/%s/addresses', $customerTransfer->getCustomerReference()),
        );

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['@type' => 'Collection']);

        $responseData = $response->toArray();
        $this->assertCount(2, $responseData['member']);
        $this->assertArrayHasKey('uuid', $responseData['member'][0]);
    }

//    public function testGivenAuthenticatedCustomerWithoutAddressesWhenRetrievingCollectionViaGetThenEmptyCollectionIsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $customerWithNoAddresses = (new CustomerTransfer())
//            ->setCustomerReference($customerTransfer->getCustomerReference())
//            ->setIdCustomer(1)
//            ->setAddresses(new AddressesTransfer());
//
//        $customerResponse = (new CustomerResponseTransfer())
//            ->setCustomerTransfer($customerWithNoAddresses)
//            ->setIsSuccess(true)
//            ->setHasCustomer(true);
//
//        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
//            'findCustomerByReference' => $customerResponse,
//        ]);
//
//        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);
//
//        // Act
//        $this->createClient()->request(
//            'GET',
//            sprintf('/customers/%s/addresses', $customerTransfer->getCustomerReference())
//        );
//
//        // Assert
//        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains(['@type' => 'Collection']);
//        $this->assertJsonContains(['member' => []]);
//    }
//
//    public function testGivenExistingAddressWhenRetrievingViaGetThenAddressDataIsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $addressUuid = 'test-address-uuid';
//
//        $addressesTransfer = (new AddressesTransfer())
//            ->addAddress(
//                (new AddressTransfer())
//                    ->setUuid($addressUuid)
//                    ->setFirstName('John')
//                    ->setLastName('Doe')
//                    ->setAddress1('Kieler Str. 75')
//                    ->setAddress2('Building A')
//                    ->setCity('Hamburg')
//                    ->setZipCode('20095')
//                    ->setIso2Code('DE')
//                    ->setSalutation('Mr')
//            );
//
//        $customerWithAddresses = (new CustomerTransfer())
//            ->setCustomerReference($customerTransfer->getCustomerReference())
//            ->setIdCustomer(1)
//            ->setAddresses($addressesTransfer);
//
//        $customerResponse = (new CustomerResponseTransfer())
//            ->setCustomerTransfer($customerWithAddresses)
//            ->setIsSuccess(true)
//            ->setHasCustomer(true);
//
//        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
//            'findCustomerByReference' => $customerResponse,
//        ]);
//
//        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);
//
//        // Act
//        $this->createClient()->request(
//            'GET',
//            sprintf('/customers/%s/addresses/%s', $customerTransfer->getCustomerReference(), $addressUuid)
//        );
//
//        // Assert
//        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains(['uuid' => $addressUuid]);
//        $this->assertJsonContains(['firstName' => 'John']);
//        $this->assertJsonContains(['city' => 'Hamburg']);
//    }
//
//    public function testGivenNonExistentAddressWhenRetrievingViaGetThen404IsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $customerWithNoAddresses = (new CustomerTransfer())
//            ->setCustomerReference($customerTransfer->getCustomerReference())
//            ->setIdCustomer(1)
//            ->setAddresses(new AddressesTransfer());
//
//        $customerResponse = (new CustomerResponseTransfer())
//            ->setCustomerTransfer($customerWithNoAddresses)
//            ->setIsSuccess(true)
//            ->setHasCustomer(true);
//
//        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
//            'findCustomerByReference' => $customerResponse,
//        ]);
//
//        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);
//
//        // Act
//        $this->createClient()->request(
//            'GET',
//            sprintf('/customers/%s/addresses/NON-EXISTENT-UUID', $customerTransfer->getCustomerReference())
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(404);
//    }
//
//    public function testGivenValidDataWhenCreatingAddressViaPostThenAddressIsCreatedSuccessfully(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $addressData = [
//            'salutation' => 'Mr',
//            'firstName' => 'John',
//            'lastName' => 'Doe',
//            'address1' => 'Kieler Str. 75',
//            'address2' => 'Building A',
//            'city' => 'Hamburg',
//            'zipCode' => '20095',
//            'iso2Code' => 'DE',
//            'isDefaultShipping' => true,
//            'isDefaultBilling' => false,
//        ];
//
//        $customerResponse = (new CustomerResponseTransfer())
//            ->setCustomerTransfer(
//                (new CustomerTransfer())
//                    ->setCustomerReference($customerTransfer->getCustomerReference())
//                    ->setIdCustomer(1)
//            )
//            ->setIsSuccess(true)
//            ->setHasCustomer(true);
//
//        $createdAddress = (new AddressTransfer())
//            ->fromArray($addressData, true)
//            ->setUuid('generated-uuid-123')
//            ->setIdCustomerAddress(1);
//
//        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
//            'findCustomerByReference' => $customerResponse,
//            'createAddressAndUpdateCustomerDefaultAddresses' => (new CustomerTransfer())
//                ->setCustomerReference($customerTransfer->getCustomerReference())
//                ->setAddresses(
//                    (new AddressesTransfer())->addAddress($createdAddress)
//                ),
//        ]);
//
//        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);
//
//        // Act
//        $this->createClient()->request(
//            'POST',
//            sprintf('/customers/%s/addresses', $customerTransfer->getCustomerReference()),
//            ['json' => $addressData]
//        );
//
//        // Assert
//        $this->assertResponseIsSuccessful();
//        $this->assertResponseStatusCodeSame(201);
//        $this->assertJsonContains(['firstName' => 'John']);
//        $this->assertJsonContains(['city' => 'Hamburg']);
//        $this->assertJsonContains(['uuid' => 'generated-uuid-123']);
//    }
//
//    public function testGivenMissingSalutationWhenCreatingAddressViaPostThenValidationErrorIsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $addressData = [
//            'firstName' => 'John',
//            'lastName' => 'Doe',
//            'address1' => 'Street 1',
//            'address2' => 'Apt 1',
//            'city' => 'Hamburg',
//            'zipCode' => '20095',
//            'iso2Code' => 'DE',
//        ];
//
//        // Act
//        $this->createClient()->request(
//            'POST',
//            sprintf('/customers/%s/addresses', $customerTransfer->getCustomerReference()),
//            ['json' => $addressData]
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(422);
//        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
//    }
//
//    public function testGivenMissingFirstNameWhenCreatingAddressViaPostThenValidationErrorIsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $addressData = [
//            'salutation' => 'Mr',
//            'lastName' => 'Doe',
//            'address1' => 'Street 1',
//            'address2' => 'Apt 1',
//            'city' => 'Hamburg',
//            'zipCode' => '20095',
//            'iso2Code' => 'DE',
//        ];
//
//        // Act
//        $this->createClient()->request(
//            'POST',
//            sprintf('/customers/%s/addresses', $customerTransfer->getCustomerReference()),
//            ['json' => $addressData]
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(422);
//        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
//    }
//
//    public function testGivenInvalidSalutationWhenCreatingAddressViaPostThenValidationErrorIsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $addressData = [
//            'salutation' => 'Invalid',
//            'firstName' => 'John',
//            'lastName' => 'Doe',
//            'address1' => 'Street 1',
//            'address2' => 'Apt 1',
//            'city' => 'Hamburg',
//            'zipCode' => '20095',
//            'iso2Code' => 'DE',
//        ];
//
//        // Act
//        $this->createClient()->request(
//            'POST',
//            sprintf('/customers/%s/addresses', $customerTransfer->getCustomerReference()),
//            ['json' => $addressData]
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(422);
//        $this->assertJsonContains(['@type' => 'ConstraintViolation']);
//    }
//
//    public function testGivenExistingAddressWhenUpdatingViaPatchThenAddressIsUpdatedSuccessfully(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $addressUuid = 'test-address-uuid';
//        $existingAddress = (new AddressTransfer())
//            ->setUuid($addressUuid)
//            ->setFirstName('Original')
//            ->setLastName('Name')
//            ->setAddress1('Old Street')
//            ->setAddress2('Old Building')
//            ->setCity('Hamburg')
//            ->setZipCode('20095')
//            ->setIso2Code('DE')
//            ->setSalutation('Mr');
//
//        $updatedAddress = (new AddressTransfer())
//            ->fromArray($existingAddress->toArray(), true)
//            ->setCity('Berlin')
//            ->setZipCode('10115');
//
//        $customerResponse = (new CustomerResponseTransfer())
//            ->setCustomerTransfer(
//                (new CustomerTransfer())
//                    ->setCustomerReference($customerTransfer->getCustomerReference())
//                    ->setIdCustomer(1)
//                    ->setAddresses((new AddressesTransfer())->addAddress($existingAddress))
//            )
//            ->setIsSuccess(true)
//            ->setHasCustomer(true);
//
//        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
//            'findCustomerByReference' => $customerResponse,
//            'updateAddressAndCustomerDefaultAddresses' => (new CustomerTransfer())
//                ->setCustomerReference($customerTransfer->getCustomerReference())
//                ->setAddresses((new AddressesTransfer())->addAddress($updatedAddress)),
//        ]);
//
//        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);
//
//        $updateData = [
//            'city' => 'Berlin',
//            'zipCode' => '10115',
//        ];
//
//        // Act
//        $this->createClient()->request(
//            'PATCH',
//            sprintf('/customers/%s/addresses/%s', $customerTransfer->getCustomerReference(), $addressUuid),
//            [
//                'json' => $updateData,
//                'headers' => ['Content-Type' => 'application/merge-patch+json'],
//            ]
//        );
//
//        // Assert
//        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains(['city' => 'Berlin']);
//        $this->assertJsonContains(['zipCode' => '10115']);
//    }
//
//    public function testGivenNonExistentAddressWhenUpdatingViaPatchThen404IsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $customerResponse = (new CustomerResponseTransfer())
//            ->setCustomerTransfer(
//                (new CustomerTransfer())
//                    ->setCustomerReference($customerTransfer->getCustomerReference())
//                    ->setIdCustomer(1)
//                    ->setAddresses(new AddressesTransfer())
//            )
//            ->setIsSuccess(true)
//            ->setHasCustomer(true);
//
//        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
//            'findCustomerByReference' => $customerResponse,
//        ]);
//
//        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);
//
//        // Act
//        $this->createClient()->request(
//            'PATCH',
//            sprintf('/customers/%s/addresses/NON-EXISTENT-UUID', $customerTransfer->getCustomerReference()),
//            [
//                'json' => ['city' => 'Berlin'],
//                'headers' => ['Content-Type' => 'application/merge-patch+json'],
//            ]
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(404);
//    }
//
//    public function testGivenExistingAddressWhenDeletingViaDeleteThenAddressIsDeletedSuccessfully(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $addressUuid = 'test-address-uuid-delete';
//
//        $existingAddress = (new AddressTransfer())
//            ->setUuid($addressUuid)
//            ->setFirstName('To')
//            ->setLastName('Delete')
//            ->setAddress1('Street')
//            ->setAddress2('Apt')
//            ->setCity('Hamburg')
//            ->setZipCode('20095')
//            ->setIso2Code('DE');
//
//        $customerResponse = (new CustomerResponseTransfer())
//            ->setCustomerTransfer(
//                (new CustomerTransfer())
//                    ->setCustomerReference($customerTransfer->getCustomerReference())
//                    ->setIdCustomer(1)
//                    ->setAddresses((new AddressesTransfer())->addAddress($existingAddress))
//            )
//            ->setIsSuccess(true)
//            ->setHasCustomer(true);
//
//        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
//            'findCustomerByReference' => $customerResponse,
//            'deleteAddress' => $existingAddress,
//        ]);
//
//        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);
//
//        // Act
//        $this->createClient()->request(
//            'DELETE',
//            sprintf('/customers/%s/addresses/%s', $customerTransfer->getCustomerReference(), $addressUuid)
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(204);
//    }
//
//    public function testGivenNonExistentAddressWhenDeletingViaDeleteThen404IsReturned(): void
//    {
//        // Arrange
//        $customerTransfer = $this->tester->haveCustomer([
//            'customerReference' => sprintf('TEST--%s', time()),
//        ]);
//
//        $customerResponse = (new CustomerResponseTransfer())
//            ->setCustomerTransfer(
//                (new CustomerTransfer())
//                    ->setCustomerReference($customerTransfer->getCustomerReference())
//                    ->setIdCustomer(1)
//                    ->setAddresses(new AddressesTransfer())
//            )
//            ->setIsSuccess(true)
//            ->setHasCustomer(true);
//
//        $clientStub = Stub::makeEmpty(CustomerClientInterface::class, [
//            'findCustomerByReference' => $customerResponse,
//        ]);
//
//        $this->getContainer()->set(CustomerClientInterface::class, $clientStub);
//
//        // Act
//        $this->createClient()->request(
//            'DELETE',
//            sprintf('/customers/%s/addresses/NON-EXISTENT-UUID', $customerTransfer->getCustomerReference())
//        );
//
//        // Assert
//        $this->assertResponseStatusCodeSame(404);
//    }
}
