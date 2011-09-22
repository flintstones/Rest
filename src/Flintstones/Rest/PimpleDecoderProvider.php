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

use FOS\RestBundle\DecoderProvider\DecoderProviderInterface;

class PimpleDecoderProvider implements DecoderProviderInterface
{
    private $container;

    public function __construct(\Pimple $container)
    {
        $this->container = $container;
    }

    public function getDecoder($id)
    {
        return $this->container[$id];
    }
}
