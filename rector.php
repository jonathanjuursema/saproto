<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\Config\RectorConfig;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\Renaming\Rector\PropertyFetch\RenamePropertyRector;
use Rector\Strict\Rector\Empty_\DisallowedEmptyRuleFixerRector;
use Rector\Transform\Rector\String_\StringToClassConstantRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

try {
    return RectorConfig::configure()
        ->withCache(
            // ensure file system caching is used instead of in-memory
            cacheDirectory: '.tmp/rector',

            // specify a path that works locally as well as on CI job runners
            cacheClass: FileCacheStorage::class
        )
        ->withPaths([
            __DIR__.'/app',
            __DIR__.'/config',
            __DIR__.'/routes',
            __DIR__.'/resources/views',
            __DIR__.'/tests',
            __DIR__.'/database',
        ])->withPhpSets(php82: true)
        ->withsets([
            LaravelLevelSetList::UP_TO_LARAVEL_110,
            LaravelSetList::LARAVEL_CODE_QUALITY,
            LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
            LaravelSetList::LARAVEL_ELOQUENT_MAGIC_METHOD_TO_QUERY_BUILDER,
            LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
            LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
            LaravelSetList::LARAVEL_COLLECTION,
            LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        ])
        ->withAttributesSets(symfony: true, doctrine: true, phpunit: true)
        ->withImportNames(removeUnusedImports: true)
        ->withRules(
            [
                ClassPropertyAssignToConstructorPromotionRector::class,
                StringClassNameToClassConstantRector::class,
            ]
        )
        ->withPreparedSets(deadCode: true,
            codeQuality: true,
            codingStyle: true,
            typeDeclarations: true,
            privatization: true,
            naming: false,
            instanceOf: true,
            earlyReturn: true,
            strictBooleans: true,
        )
        ->withSkip(
            [
                DisallowedEmptyRuleFixerRector::class,
                StringToClassConstantRector::class,
                EncapsedStringsToSprintfRector::class,
                NullToStrictStringFuncCallArgRector::class,
                AddClosureVoidReturnTypeWhereNoReturnRector::class,
                RenamePropertyRector::class,
                __DIR__.'/app/Libraries',
            ]);
} catch (InvalidConfigurationException $e) {
}
