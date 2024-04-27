<?php

namespace Omnireceipt\Common\Tests\Unit;

use Omnireceipt\Common\Exceptions\Property\PropertyNotFoundException;
use Omnireceipt\Common\Exceptions\Property\PropertyValidateException;
use Omnireceipt\Common\Supports\PropertiesTrait;
use Omnireceipt\Common\Tests\TestCase;

class PropertiesTraitTest extends TestCase
{
    public function testGetterAndSetter()
    {
        $object = self::makeObject();
        $name = 'Name';
        $qwe_asd = 'QweAsd';

        $object->setName($name);
        $object->setQweAsd($qwe_asd);

        $this->assertEquals($name, $object->getName());
        $this->assertEquals($qwe_asd, $object->getQweAsd());
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testGetterException()
    {

        $object = self::makeObject();
        $this->expectException(PropertyNotFoundException::class);
        $object->getName();
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testValidator()
    {
        $object = self::makeObject();

        $this->assertFalse($object->validate());

        $lastError = $object->getLastError();
        $this->assertIsArray($lastError);
        $this->assertArrayHasKey('properties', $lastError);
        $this->assertIsArray($lastError['properties']);
        $this->assertArrayNotHasKey('nullable', $lastError['properties']);
        $this->assertArrayHasKey('required', $lastError['properties']);
        $this->assertArrayHasKey('string', $lastError['properties']);
        $this->assertArrayHasKey('numeric', $lastError['properties']);
        $this->assertArrayHasKey('in', $lastError['properties']);
        $this->assertArrayNotHasKey('in_nullable', $lastError['properties']);

        $object->setNullable(null);
        $object->setRequired('1');
        $object->setString('asd');
        $object->setNumeric(123.45);
        $object->setIn('zzz');
        $object->setInNullable(null);

        $this->assertTrue($object->validate());

        $properties = $object->getProperties();
        $this->assertIsArray($properties);
        $this->assertArrayHasKey('nullable', $properties);
        $this->assertArrayHasKey('required', $properties);
        $this->assertArrayHasKey('string', $properties);
        $this->assertArrayHasKey('numeric', $properties);
        $this->assertArrayHasKey('in', $properties);
        $this->assertArrayHasKey('in_nullable', $properties);

        $object->setRequired(null);
        $object->setString(123);
        $object->setNumeric('asd');
        $object->setIn('vvv');
        $object->setInNullable('vvv');

        $this->assertFalse($object->validate());
        $lastError = $object->getLastError();
        $this->assertArrayHasKey('required', $lastError['properties']);
        $this->assertArrayHasKey('string', $lastError['properties']);
        $this->assertArrayHasKey('numeric', $lastError['properties']);
        $this->assertArrayHasKey('in', $lastError['properties']);
        $this->assertArrayHasKey('in_nullable', $lastError['properties']);
    }

    /**
     * @depends testValidator
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testValidator')]
    public function testValidatorException()
    {
        $object = self::makeObject();
        $this->expectException(PropertyValidateException::class);
        $object->validateOrFail();
    }

    protected static function makeObject(): object
    {
        return new class {
            use PropertiesTrait;

            const RULES = [
                'nullable' => ['nullable'],
                'required' => ['required'],
                'string' => ['string'],
                'numeric' => ['numeric'],
                'in' => ['in:zzz'],
                'in_nullable' => ['nullable', 'in:zzz'],
            ];

            public function __construct(
                array $properties = []
            ) {
                $this->properties = $properties;
            }
        };
    }
}