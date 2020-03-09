<?php

require_once __DIR__.'/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();


/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades(
    class_alias('Intervention\Image\Facades\Image', 'Image')
);

$app->withEloquent();


/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

$app->routeMiddleware([
    // 'auth' => App\Http\Middleware\Authenticate::class,
    // 'cors'     => App\Http\Middleware\CorsMiddleware::class,
    'jwt.auth' => App\Http\Middleware\JwtMiddleware::class, // JWT AUthenticator
    'log'      => App\Http\Middleware\AccessLog::class // Log Device Activity
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);

$app->register(Urameshibr\Providers\FormRequestServiceProvider::class); // Form-Request To Validator

$app->register(Illuminate\Redis\RedisServiceProvider::class); // Redis Cache
$app->configure('database'); //file to ensure the Redis database configuration is properly loaded.

// $app->register(App\Helpers\LogActivity::class);
// class_alias(App\Helpers\LogActivity::class, 'LogActivity'); // Aliases

$app->register(Irazasyed\Larasupport\Providers\ArtisanServiceProvider::class); // Artisan
$app->register(Intervention\Image\ImageServiceProvider::class); // Intervention Image

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
