<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\Customer\Business\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressResponseTransfer;
use Generated\Shared\Transfer\AddressTransfer;
use Spryker\Zed\Customer\Business\CustomerFacadeInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Customer
 * @group Business
 * @group Facade
 * @group ValidateAddressFieldsTest
 * Add your own group annotations below this line
 */
class ValidateAddressFieldsTest extends Unit
{
    protected const string ISO_2_CODE = 'DE';

    protected const string ERROR_MESSAGE_FIRST_NAME_MISSING = 'customer.address.validation.first_name_missing';

    protected const string ERROR_MESSAGE_LAST_NAME_MISSING = 'customer.address.validation.last_name_missing';

    protected const string ERROR_MESSAGE_NAME_PATTERN = 'customer.address.validation.name_pattern';

    protected const string ERROR_MESSAGE_SALUTATION_INVALID = 'customer.address.validation.salutation_invalid';

    protected const string ERROR_MESSAGE_FIELD_TOO_LONG = 'customer.address.validation.field_too_long';

    /**
     * @var \SprykerTest\Zed\Customer\CustomerBusinessTester
     */
    protected $tester;

    public function testAcceptsAnAddressCarryingOnlyWhatTheTableRequires(): void
    {
        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($this->createValidAddressTransfer());

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
        $this->assertCount(0, $addressResponseTransfer->getErrors());
    }

    public function testRejectsAnAddressWithoutAFirstName(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setFirstName(null);

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertContains(static::ERROR_MESSAGE_FIRST_NAME_MISSING, $this->getErrorMessages($addressResponseTransfer));
    }

    public function testRejectsAnAddressWithoutALastName(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setLastName(null);

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertContains(static::ERROR_MESSAGE_LAST_NAME_MISSING, $this->getErrorMessages($addressResponseTransfer));
    }

    public function testReportsEveryViolationInOneResponse(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()
            ->setFirstName(null)
            ->setLastName(null);

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertCount(2, $addressResponseTransfer->getErrors());
    }

    /**
     * @dataProvider provideNamesCarryingMarkupCharacters
     */
    public function testRejectsANameCarryingMarkupCharacters(string $name): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setFirstName($name);

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertContains(static::ERROR_MESSAGE_NAME_PATTERN, $this->getErrorMessages($addressResponseTransfer));
    }

    /**
     * @return array<string, array<string>>
     */
    public function provideNamesCarryingMarkupCharacters(): array
    {
        return [
            'angle brackets' => ['<script>'],
            'closing angle bracket' => ['Sonia>'],
            'colon' => ['javascript:alert'],
            'slash' => ['Sonia/Wagner'],
        ];
    }

    public function testAcceptsATwoCharacterSurname(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setLastName('Ng');

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
    }

    public function testAcceptsAFreeTextSecondAddressLine(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setAddress2('Apt. 989, second courtyard');

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
    }

    public function testAcceptsAnAddressWithoutASalutation(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setSalutation(null);

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
    }

    public function testRejectsASalutationOutsideTheColumnEnum(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setSalutation('Captain');

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertContains(static::ERROR_MESSAGE_SALUTATION_INVALID, $this->getErrorMessages($addressResponseTransfer));
    }

    public function testRejectsAFirstNameLongerThanTheColumn(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setFirstName(str_repeat('a', 101));

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertContains(static::ERROR_MESSAGE_FIELD_TOO_LONG, $this->getErrorMessages($addressResponseTransfer));
    }

    public function testRejectsAZipCodeLongerThanTheColumn(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setZipCode(str_repeat('1', 16));

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertFalse($addressResponseTransfer->getIsSuccess());
        $this->assertContains(static::ERROR_MESSAGE_FIELD_TOO_LONG, $this->getErrorMessages($addressResponseTransfer));
    }

    public function testAcceptsAFirstNameOfExactlyTheColumnWidth(): void
    {
        // Arrange
        $addressTransfer = $this->createValidAddressTransfer()->setFirstName(str_repeat('a', 100));

        // Act
        $addressResponseTransfer = $this->getFacade()->validateAddress($addressTransfer);

        // Assert
        $this->assertTrue($addressResponseTransfer->getIsSuccess());
    }

    /**
     * @return array<string>
     */
    protected function getErrorMessages(AddressResponseTransfer $addressResponseTransfer): array
    {
        $errorMessages = [];

        foreach ($addressResponseTransfer->getErrors() as $customerErrorTransfer) {
            $errorMessages[] = $customerErrorTransfer->getMessage();
        }

        return $errorMessages;
    }

    protected function createValidAddressTransfer(): AddressTransfer
    {
        return (new AddressTransfer())
            ->setSalutation('Mr')
            ->setFirstName('Sonia')
            ->setLastName('Wagner')
            ->setAddress1('Julie-Wolfthorn-Strasse')
            ->setAddress2('1')
            ->setCity('Berlin')
            ->setZipCode('10115')
            ->setIso2Code(static::ISO_2_CODE);
    }

    protected function getFacade(): CustomerFacadeInterface
    {
        return $this->tester->getFacade();
    }
}
