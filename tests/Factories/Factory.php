<?php
/**
 * Core components for the Omnireceipt PHP fiscal receipt processing library
 *
 * @link      https://github.com/omnireceipt/common
 * @package   omnireceipt/common
 * @license   MIT
 * @copyright Copyright (c) 2024, Alexander Arhitov, clgsru@gmail.com
 */

namespace Omnireceipt\Common\Tests\Factories;

abstract class Factory
{
    public static function create(string $className)
    {
        return match (static::SOURCE_TYPE) {
            'BUILDER' => static::createBuilder($className),
        };
    }

    abstract public static function definition(): array;

    protected static function createBuilder(string $className)
    {
        $object = new $className;
        foreach(static::definition() as $key => $value) {
            $key = 'set' . implode('', array_map(
                    fn($keyPart) => ucfirst($keyPart),
                    explode('_', $key),
                ));
            $object->$key($value);
        }
        return $object;
    }
}
