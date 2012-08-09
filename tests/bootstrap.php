<?php

/*
 * This file is part of the Flintstones RestServiceProvider.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
 */

$file = __DIR__.'/../vendor/autoload.php';
if (!file_exists($file)) {
  throw new RuntimeException('Install dependencies to run test suite.');
}

$autoload = require_once $file;

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespace('Flintstones\Rest', __DIR__.'/../src');
$loader->registerNamespace('Flintstones\Tests\Rest', __DIR__);
$loader->register();
