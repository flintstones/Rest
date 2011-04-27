<?php

/*
 * This file is part of the Flintstones RestExtension.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
 */

require_once __DIR__.'/../vendor/silex/autoload.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

spl_autoload_register(function ($class) {
    if (0 === strpos($class, 'Flintstones\\Rest\\')) {
        $path = implode('/', array_slice(explode('\\', $class), 2)).'.php';
        require_once __DIR__.'/../'.$path;
        return true;
    }
});
