<?php

use App\Di;
use App\App;
use App\Adapters;
use App\Console;

require __DIR__ . '/../vendor/autoload.php';

Di::config('console', new Console());
Di::config('adapter.disk', Adapters\Disk::class);
Di::config('adapter.web', Adapters\Web::class);

$app = Di::get(App::class)(require('config/app.php'));

Di::config('app', $app);

$app->run();
