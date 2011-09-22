# Flintstones RestServiceProvider

Adding some REST capabilities to [Silex][1], so you can
more easily build RESTful APIs. 110% Buzzword-Driven.

You get accept header support and request body decoding.

## Registering

    $app->register(new Flintstones\Rest\ServiceProvider(), array(
        'rest.fos.class_path'           => __DIR__.'/vendor',
        'rest.serializer.class_path'    => __DIR__.'/vendor',
    ));

## Running the tests

    $ ./vendors.sh
    $ phpunit

## Credits

* [FOSRestBundle][2]
* [Symfony2 Serializer Component][3]

## License

The RestServiceProvider is licensed under the MIT license.

[1]: http://silex-project.org
[2]: https://github.com/FriendsOfSymfony/RestBundle
[3]: https://github.com/symfony/Serializer
