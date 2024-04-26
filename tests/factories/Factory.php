<?php

namespace Omnireceipt\Common\Tests\factories;

abstract class Factory
{
    public static function create()
    {
        return match (static::SOURCE_TYPE) {
            'BUILDER' => static::createBuilder(),
        };
    }

    abstract protected static function definition(): array;

    protected static function createBuilder()
    {
        $className = static::SOURCE;
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
