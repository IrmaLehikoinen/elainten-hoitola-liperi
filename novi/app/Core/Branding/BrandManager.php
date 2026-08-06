<?php

namespace App\Core\Branding;

use RuntimeException;

final class BrandManager
{
    public function current(): array
    {
        $source = config('branding.source', 'website');

        $brandFile = config("branding.sources.{$source}");

        if (! file_exists($brandFile)) {
            throw new RuntimeException(
                "Bränditiedostoa ei löytynyt: {$brandFile}"
            );
        }

        $brand = require $brandFile;

        if (! is_array($brand)) {
            throw new RuntimeException(
                'Bränditiedoston pitää palauttaa taulukko.'
            );
        }

        return $brand;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return data_get($this->current(), $key, $default);
    }
}