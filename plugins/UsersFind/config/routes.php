<?php
use Cake\Routing\Router;

Router::plugin(
    'UsersFind',
    ['path' => '/users-find'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
