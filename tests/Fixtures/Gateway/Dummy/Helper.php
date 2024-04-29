<?php

namespace Omnireceipt\Common\Tests\Fixtures\Gateway\Dummy;

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

        $fileName = implode(DIRECTORY_SEPARATOR, [
                __DIR__,
                'Fixtures',
                $name,
            ]) . '.json';
        if (! file_exists($fileName)) {
            throw new RuntimeException("File fixture \"{$name}\" not found");
        }

        return file_get_contents($fileName);
    }

    public static function getFixtureAsArray(string $name): array
    {
        return json_decode(self::getFixture($name), true);
    }
}
