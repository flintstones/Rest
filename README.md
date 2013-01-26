# Flintstones RestServiceProvider

Adding some REST capabilities to [Silex][1], so you can
more easily build RESTful APIs. 110% Buzzword-Driven.

You get accept header support and request body decoding.

## Registering

    $app->register(new Flintstones\Rest\ServiceProvider());

## Overriding parameters

The following can be overridden:

* `'rest.priorities'`
  This is an array of formats your code is capable of
  sending back a response as. A negotiation takes place
  to determine which is most suitable based on the
  request's Accept header.
* `'rest.fallback'`
  This is the format to fall back on if no suitable
  format could be negotiated. This can be set to NULL
  if you would like a 406 HTTP status error to be
  returned instead.
* `'rest.decoders'`
  This is an array that refers to decoder services
  which are used to decode request body content.
  Default decoders are included for JSON and XML,
  but you can override this if necessary.
      
Any of these you wish to override must be overridden
prior to the service completing its setup, i.e. before
its `register()` method has executed. Therefore they must
either be set on `$app` prior to registering the service,
or they must be given in an array to it's constructor
(*not* the second optional parameter of `$app->register()`
unlike most Silex service providers).

Example #1:

    $app->register(new Flintstones\Rest\ServiceProvider(array(
        'rest.priorities' => array('html', 'json', 'xml'),
        'rest.fallback' => null,
    )));

Example #2:

    $app['rest.priorities'] = array('html', 'json', 'xml');
    $app['rest.fallback'] = null;
    
    $app->register(new Flintstones\Rest\ServiceProvider());

Example #3:

    $app['rest.decoders.custom'] = function ($app) {
        //code...
    };
    
    $app->register(new Flintstones\Rest\ServiceProvider(array(
        'rest.decoders' => array(
            'json' => 'rest.decoders.json',
            'xml' => 'rest.decoders.xml',
            'custom' => 'rest.decoders.custom',
        ),
    )));
    
Note that if you wish to replace `$app['rest.decoder.json']`
and/or `$app['rest.decoder.xml']`, do so after registering.

## Running the tests

    $ curl -s https://getcomposer.org/installer | php
    $ php composer.phar install
    $ phpunit

## Credits

* [FOSRestBundle][2]
* [Symfony2 Serializer Component][3]

## License

The RestServiceProvider is licensed under the MIT license.

[1]: http://silex-project.org
[2]: https://github.com/FriendsOfSymfony/FOSRestBundle
[3]: https://github.com/symfony/Serializer
