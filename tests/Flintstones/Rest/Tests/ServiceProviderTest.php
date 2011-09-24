<?php

/*
 * This file is part of the Flintstones RestServiceProvider.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
 */

namespace Flintstones\Rest\Tests;

use Flintstones\Rest\ServiceProvider as RestServiceProvider;

use Silex\Application;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * RestExtension test cases.
 *
 * @author Igor Wiedler <igor@wiedler.ch>
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!is_file(__DIR__.'/../../../../vendor/FOS/RestBundle/FOSRestBundle.php')) {
            $this->markTestSkipped('FOS\RestBundle submodule was not installed.');
        }
    }

    public function testRegister()
    {
        $app = new Application();

        $app->register(new RestServiceProvider(), array(
            'rest.fos.class_path'           => __DIR__.'/../../../../vendor',
            'rest.serializer.class_path'    => __DIR__.'/../../../../vendor',
        ));

        $this->assertInstanceOf('Symfony\Component\Serializer\Serializer', $app['rest.serializer']);

        return $app;
    }

    /**
     * @depends testRegister
     */
    public function testDecodingOfRequestBody(Application $app)
    {
        $app->put('/api/user/{id}', function ($id) use ($app) {
            return $app['request']->get('name');
        });

        $request = Request::create('/api/user/1', 'put', array(), array(), array(), array(), '{"name":"igor"}');
        $request->headers->set('Content-Type', 'application/json');
        $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertEquals('igor', $response->getContent());
    }

    /**
     * @depends testRegister
     */
    public function testFormatDetection(Application $app)
    {
        $app->get('/api/user/{id}', function ($id) use ($app) {
            return $app['request']->getRequestFormat();
        });

        $request = Request::create('/api/user/1');
        $request->headers->set('Accept', 'application/json');
        $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertEquals('json', $response->getContent());
    }
}
