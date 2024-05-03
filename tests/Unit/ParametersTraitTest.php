<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Unit;

use Omnireceipt\Common\Exceptions\Parameters\ParameterNotFoundException;
use Omnireceipt\Common\Exceptions\Parameters\ParameterValidateException;
use Omnireceipt\Common\Supports\ParametersTrait;
use Omnireceipt\Common\Tests\TestCase;

class ParametersTraitTest extends TestCase
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
        $this->assertEmpty($object->getParameters());
        $this->expectException(ParameterNotFoundException::class);
        $object->getName();
    }

    /**
     * @depends testGetterAndSetter
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testGetterAndSetter')]
    public function testGetterOrNull()
    {

        $object = self::makeObject();
        $this->assertEmpty($object->getParameters());
        $this->assertNull($object->getNameOrNull());
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

        $lastError = $object->validateLastError();
        $this->assertIsArray($lastError);
        $this->assertArrayHasKey('parameters', $lastError);
        $this->assertIsArray($lastError['parameters']);
        $this->assertArrayNotHasKey('nullable', $lastError['parameters']);
        $this->assertArrayHasKey('required', $lastError['parameters']);
        $this->assertArrayHasKey('string', $lastError['parameters']);
        $this->assertArrayHasKey('numeric', $lastError['parameters']);
        $this->assertArrayHasKey('in', $lastError['parameters']);
        $this->assertArrayNotHasKey('in_nullable', $lastError['parameters']);

        $object->setNullable(null);
        $object->setRequired('1');
        $object->setString('asd');
        $object->setNumeric(123.45);
        $object->setIn('zzz');
        $object->setInNullable(null);

        $this->assertTrue($object->validate());

        $parameters = $object->getParameters();
        $this->assertIsArray($parameters);
        $this->assertArrayHasKey('nullable', $parameters);
        $this->assertArrayHasKey('required', $parameters);
        $this->assertArrayHasKey('string', $parameters);
        $this->assertArrayHasKey('numeric', $parameters);
        $this->assertArrayHasKey('in', $parameters);
        $this->assertArrayHasKey('in_nullable', $parameters);

        $object->setRequired(null);
        $object->setString(123);
        $object->setNumeric('asd');
        $object->setIn('vvv');
        $object->setInNullable('vvv');

        $this->assertFalse($object->validate());
        $lastError = $object->validateLastError();
        $this->assertArrayHasKey('required', $lastError['parameters']);
        $this->assertArrayHasKey('string', $lastError['parameters']);
        $this->assertArrayHasKey('numeric', $lastError['parameters']);
        $this->assertArrayHasKey('in', $lastError['parameters']);
        $this->assertArrayHasKey('in_nullable', $lastError['parameters']);
    }

    /**
     * @depends testValidator
     * @return void
     */
    #[\PHPUnit\Framework\Attributes\Depends('testValidator')]
    public function testValidatorException()
    {
        $object = self::makeObject();
        $this->expectException(ParameterValidateException::class);
        $object->validateOrFail();
    }

    protected static function makeObject(): object
    {
        return new class {
            use ParametersTrait;

            public static function rules(): array
            {
                return [
                    'nullable'    => ['nullable'],
                    'required'    => ['required'],
                    'string'      => ['string'],
                    'numeric'     => ['numeric'],
                    'in'          => ['in:zzz'],
                    'in_nullable' => ['nullable', 'in:zzz'],
                ];
            }

            public function __construct(
                array $parameters = []
            ) {
                $this->initialize($parameters);
            }
        };
    }
}