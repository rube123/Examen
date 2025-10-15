protected $routeMiddleware = [
// ...
'role' => \App\Http\Middleware\EnsureRole::class,
'admin' => \App\Http\Middleware\EnsureAdmin::class,
];