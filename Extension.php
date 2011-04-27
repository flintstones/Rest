<?php

/*
 * This file is part of the Flintstones RestExtension.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flintstones\Rest;

use FOS\RestBundle\Request\RequestListener;

use Silex\Application;
use Silex\ExtensionInterface;

use Symfony\Component\HttpKernel\Events as HttpKernelEvents;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class Extension implements ExtensionInterface
{
    public function register(Application $app)
    {
        $app['rest.serializer'] = $app->share(function () {
            $serializer = new Serializer();
            $serializer->setEncoder('json', new JsonEncoder());
            $serializer->setEncoder('xml', new XmlEncoder());
            return $serializer;
        });

        if (isset($app['rest.fos.class_path'])) {
            $app['autoloader']->registerNamespace('FOS\RestBundle', $app['rest.fos.class_path']);
        }

        if (isset($app['rest.serializer.class_path'])) {
            $app['autoloader']->registerNamespace('Symfony\Component\Serializer', $app['rest.serializer.class_path']);
        }

        $listener = new RequestListener(array('html' => 1, 'json' => 0.75, 'xml' => 0.5), 'html', true);
        $listener->setSerializer($app['rest.serializer']);
        $app['dispatcher']->addListener(HttpKernelEvents::onCoreRequest, $listener, 10);
    }
}
