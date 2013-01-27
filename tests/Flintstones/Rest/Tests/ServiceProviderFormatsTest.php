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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Test format listener determins correct response
 *
 * @author Lyndon Brown <lrebrown@gmail.com>
 */
class ServiceProviderResponseFormatTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    
    public function setUp()
    {
        $this->app = new Application();
    }

    /**
     * Test with a set of priorities and a fallback
     *
     * @dataProvider providerFormatSelection
     */
    public function testFormatSelection($accept, array $priorities, $expected)
    {
        $app = $this->app;
        
        $app->register(new RestServiceProvider(array(
            'rest.priorities' => $priorities,
        )));
        
        $app->get('/foo', function () use ($app) {
            return $app['request']->getRequestFormat();
        });

        $request = Request::create('/foo');
        $request->headers->set('Accept', $accept);
        $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertEquals($expected, $response->getContent());
    }
    
    public function providerFormatSelection()
    {
        $priorities = array('json', 'xml');
        
        return array(
            array('application/json', $priorities, 'json'),
            array('application/json;q=0.1', $priorities, 'json'),
            array('text/xml', $priorities, 'xml'),
            array('text/html', $priorities, 'html'),
            array('application/json;q=0.9,text/xml', $priorities, 'xml'),
            array('application/json;q=0.9,text/xml;q=0.8', $priorities, 'json'),
            array('x/x', $priorities, 'html'),
            array('x/x;q=1,application/json;q=0.1', $priorities, 'json'),
            array('*/*', $priorities, 'json'),
        );
    }

    /**
     * Test with no priorities but with a fallback
     *
     * This fallback should always be picked here (following the logic of
     * the FOS\RestBundle\EventListener\FormatListener() method used here).
     *
     * @dataProvider providerFallbackOnly
     */
    public function testFallbackOnly($fallback)
    {
        $app = $this->app;
        
        $app->register(new RestServiceProvider(array(
            'rest.priorities' => array(),
            'rest.fallback' => $fallback,
        )));
        
        $app->get('/foo', function () use ($app) {
            return $app['request']->getRequestFormat();
        });

        $request = Request::create('/foo');
        $request->headers->set('Accept', 'application/json');
        $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);

        $this->assertEquals($fallback, $response->getContent());
    }
    
    public function providerFallbackOnly()
    {
        return array(
            array('html'),
            array('json'),
            array('xml'),
        );
    }

    /**
     * Test with priorities but no fallback
     *
     * This should result in an exception / 406 error if no format can be negotiated.
     */
    public function testNoFallback()
    {
        $app = $this->app;
        
        $app->register(new RestServiceProvider(array(
            'rest.priorities' => array('html', 'json'),
            'rest.fallback' => null,
        )));
        
        $app->get('/foo', function () use ($app) {
            return $app['request']->getRequestFormat();
        });

        $request = Request::create('/foo');
        $request->headers->set('Accept', 'text/xml');
        $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, true);

        $this->assertEquals(406, $response->getStatusCode());

        try {
            $request = Request::create('/foo');
            $request->headers->set('Accept', 'text/xml');
            $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
    
            $this->fail('An expected HttpException exception has not been raised.');
        }
        catch (HttpException $e) {
            return;
        }
    }

    /**
     * Test with no priorities and no fallback
     *
     * This should never be done, but it is expected to always result in an
     * exception / 406 error, so this tests that expectation.
     */
    public function testNoPrioritiesNoFallback()
    {
        $app = $this->app;
        
        $app->register(new RestServiceProvider(array(
            'rest.priorities' => array(),
            'rest.fallback' => null,
        )));
        
        $app->get('/foo', function () use ($app) {
            return $app['request']->getRequestFormat();
        });

        $request = Request::create('/foo');
        $request->headers->set('Accept', 'application/json');
        $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, true);

        $this->assertEquals(406, $response->getStatusCode());

        try {
            $request = Request::create('/foo');
            $request->headers->set('Accept', 'application/json');
            $response = $app->handle($request, HttpKernelInterface::MASTER_REQUEST, false);
    
            $this->fail('An expected HttpException exception has not been raised.');
        }
        catch (HttpException $e) {
            return;
        }
    }
}
