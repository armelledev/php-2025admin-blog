<?php

declare (strict_types=1);
namespace RectorPrefix202507\Doctrine\Inflector;

class NoopWordInflector implements WordInflector
{
    public function inflect(string $word) : string
    {
        return $word;
    }
}
