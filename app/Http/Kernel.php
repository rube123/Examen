protected $routeMiddleware = [
// ...
'role' => \App\Http\Middleware\EnsureRole::class,
'admin' => \App\Http\Middleware\AdminMiddleware::class,
];