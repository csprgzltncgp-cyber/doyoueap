<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Rector\Class_\AnonymousMigrationsRector;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/config',
        __DIR__.'/lang',
        __DIR__.'/routes',
    ]);

    $rectorConfig->skip([
        __DIR__.'/routes/web.php',
    ]);

    $rectorConfig->importNames();

    $rectorConfig->rules([
        InlineConstructorDefaultToPropertyRector::class,
        AnonymousMigrationsRector::class,
    ]);

    $rectorConfig->skip([
        FlipTypeControlToUseExclusiveTypeRector::class,
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_81,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
        LaravelSetList::LARAVEL_100,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
    ]);
};
