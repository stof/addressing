<?php

namespace CommerceGuys\Addressing\Tests\AddressFormat;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressFormat\FieldHelper;
use CommerceGuys\Addressing\AddressFormat\FieldOverride;
use CommerceGuys\Addressing\AddressFormat\FieldOverrides;

/**
 * @coversDefaultClass \CommerceGuys\Addressing\AddressFormat\FieldHelper
 */
class FieldHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getGroupedFields
     */
    public function testGetGroupedFields()
    {
        $format = "%givenName %familyName\n%organization\n%addressLine1\n%addressLine2\n%locality, %postalCode";
        $fieldOverrides = new FieldOverrides([]);
        $expectedGroupedFields = [
            [AddressField::GIVEN_NAME, AddressField::FAMILY_NAME],
            [AddressField::ORGANIZATION],
            [AddressField::ADDRESS_LINE1],
            [AddressField::ADDRESS_LINE2],
            [AddressField::LOCALITY, AddressField::POSTAL_CODE],
        ];
        $this->assertEquals($expectedGroupedFields, FieldHelper::getGroupedFields($format, $fieldOverrides));

        $fieldOverrides = new FieldOverrides([
            AddressField::ORGANIZATION => FieldOverride::HIDDEN,
            AddressField::LOCALITY => FieldOverride::HIDDEN,
        ]);
        $expectedGroupedFields = [
            [AddressField::GIVEN_NAME, AddressField::FAMILY_NAME],
            [AddressField::ADDRESS_LINE1],
            [AddressField::ADDRESS_LINE2],
            [AddressField::POSTAL_CODE],
        ];
        $this->assertEquals($expectedGroupedFields, FieldHelper::getGroupedFields($format, $fieldOverrides));
    }

    /**
     * @covers ::getRequiredFields
     */
    public function testGetRequiredFields()
    {
        $addressFormat = new AddressFormat([
            'country_code' => 'US',
            'format' => "%givenName %familyName\n%organization\n%addressLine1\n%addressLine2\n%locality, %administrativeArea %postalCode",
            'required_fields' => [
                AddressField::ADMINISTRATIVE_AREA,
                AddressField::LOCALITY,
                AddressField::POSTAL_CODE,
            ],
        ]);
        $fieldOverrides = new FieldOverrides([]);
        $expectedRequiredFields = [
            AddressField::ADMINISTRATIVE_AREA,
            AddressField::LOCALITY,
            AddressField::POSTAL_CODE,
        ];
        $this->assertEquals($expectedRequiredFields, FieldHelper::getRequiredFields($addressFormat, $fieldOverrides));

        $fieldOverrides = new FieldOverrides([
            AddressField::ADMINISTRATIVE_AREA => FieldOverride::HIDDEN,
            AddressField::POSTAL_CODE => FieldOverride::OPTIONAL,
            AddressField::ADDRESS_LINE1 => FieldOverride::REQUIRED,
        ]);
        $expectedRequiredFields = [
            AddressField::LOCALITY,
            AddressField::ADDRESS_LINE1,
        ];
        $this->assertEquals($expectedRequiredFields, FieldHelper::getRequiredFields($addressFormat, $fieldOverrides));
    }
}
