<?php

declare (strict_types=1);
namespace RectorPrefix202507;

use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;
return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->ruleWithConfiguration(RenameClassRector::class, [
        // @see https://github.com/symfony/symfony/pull/42050
        'Symfony\\Component\\Security\\Http\\Event\\DeauthenticatedEvent' => 'Symfony\\Component\\Security\\Http\\Event\\TokenDeauthenticatedEvent',
    ]);
};
