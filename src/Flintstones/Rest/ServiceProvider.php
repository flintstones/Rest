<?php

/*
 * This file is part of the Flintstones RestServiceProvider.
 *
 * (c) Igor Wiedler <igor@wiedler.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flintstones\Rest;

use FOS\RestBundle\EventListener\BodyListener;
use FOS\RestBundle\EventListener\FormatListener;
use FOS\Rest\Util\FormatNegotiator;
use FOS\Rest\Decoder\JsonDecoder;
use FOS\Rest\Decoder\XmlDecoder;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Symfony\Component\HttpKernel\KernelEvents as HttpKernelEvents;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['rest.serializer'] = $app->share(function () {
            $encoders = array (
                'json' => new JsonEncoder(),
                'xml'  => new XmlEncoder()
            );
            $serializer = new Serializer(array(), $encoders);
            return $serializer;
        });

        if (!isset($app['rest.priorities'])) {
            $app['rest.priorities'] = array('json', 'xml');
        }

        $app['rest.format_negotiator'] = function ($app) {
            return new FormatNegotiator();
        };

        $app['rest.decoder.json'] = function ($app) {
            return new JsonDecoder();
        };

        $app['rest.decoder.xml'] = function ($app) {
            return new XmlDecoder();
        };

        $app['rest.decoders'] = isset($app['rest.decoders']) ? $app['rest.decoders'] : array(
            'json'  => 'rest.decoder.json',
            'xml'   => 'rest.decoder.xml',
        );

        $listener = new BodyListener(new PimpleDecoderProvider($app, $app['rest.decoders']));
        $app['dispatcher']->addListener(HttpKernelEvents::REQUEST, array($listener, 'onKernelRequest'));

        $listener = new FormatListener($app['rest.format_negotiator'], 'html', $app['rest.priorities']);
        $app['dispatcher']->addListener(HttpKernelEvents::CONTROLLER, array($listener, 'onKernelController'), 10);
    }

  /**
   * Bootstraps the application.
   *
   * This method is called after all services are registers
   * and should be used for "dynamic" configuration (whenever
   * a service must be requested).
   */
  public function boot(Application $app) {
    // TODO: Implement boot() method.
  }
}
