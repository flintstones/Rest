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

    private $decoders;

    public function __construct(\Pimple $container, array $decoders)
    {
        $this->container = $container;
        $this->decoders = $decoders;
    }

    public function supports($format)
    {
    	return isset($this->container[$this->decoders[$format]]);
    }

    public function getDecoder($format)
    {
    	if (!$this->supports($format)) {
    		throw new \InvalidArgumentException(sprintf("Format '%s' is not supported by PimpleDecoderProvider.", $format));
    	}

        return $this->container[$this->decoders[$format]];
    }
}
