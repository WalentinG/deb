<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

// 如果你需要自动依赖注入(包括注解注入)。
// 请先运行 composer require php-di/php-di && composer require doctrine/annotations
// 并将下面的代码注释解除，并注释掉最后一行 return new Webman\Container;
use DI\ContainerBuilder;

$builder = new ContainerBuilder();
$builder->addDefinitions(config('dependence', [])); // @phpstan-ignore-line
// to override dependencies for testing
if (env('DEPENDENCE_PATH')) {
    $builder->addDefinitions(base_path() . '/' . env('DEPENDENCE_PATH'));
}

$builder->useAutowiring(true);
$builder->useAnnotations(true);

return $builder->build();
