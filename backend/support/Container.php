<?php

declare(strict_types=1);
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 *
 * @see      http://www.workerman.net/
 *
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace support;

use Psr\Container\ContainerInterface;
use Webman\Config;

/**
 * Class Container.
 *
 * @method static mixed get($name)
 * @method static mixed make($name, array $parameters)
 * @method static bool has($name)
 */
class Container
{
    public static function instance(string $plugin = ''): ContainerInterface
    {
        /* @phpstan-ignore-next-line */
        return Config::get($plugin ? "plugin.{$plugin}.container" : 'container');
    }

    /** @phpstan-ignore-next-line */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $plugin = \Webman\App::getPluginByClass($name);
        /* @phpstan-ignore-next-line */
        return static::instance($plugin)->{$name}(...$arguments);
    }
}
