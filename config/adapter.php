<?php

use App\Services\AirCms\AirCmsAdapter;
use App\Services\Mosecom\MosecomAdapter;

return [
    'adapters' => [
        AirCmsAdapter::class,
        MosecomAdapter::class
    ],
];

