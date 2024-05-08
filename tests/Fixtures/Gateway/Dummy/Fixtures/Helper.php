<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy\Fixtures;

use Omnireceipt\Common\Exceptions\RuntimeException;

class Helper
{
    /**
     * @param string $name
     * @return string
     */
    public static function getFixture(string $name): string
    {
        if (str_contains($name, '..')) {
            throw new RuntimeException("Bad name fixture");
        }

        $fileName = __DIR__ . "/fixtures/{$name}.json";
        if (! file_exists($fileName)) {
            throw new RuntimeException("File fixture \"$name\" not found");
        }

        return file_get_contents($fileName);
    }

    public static function getFixtureAsArray(string $name): array
    {
        return json_decode(self::getFixture($name), true);
    }
}
