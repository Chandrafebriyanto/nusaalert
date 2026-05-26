<?php return Rector\Config\RectorConfig::configure()->withPaths([__DIR__ . '/app/Http/Controllers/Api'])->withSets([Rector\Swagger\Set\SwaggerSetList::SWAGGER_TO_ATTRIBUTES]);
