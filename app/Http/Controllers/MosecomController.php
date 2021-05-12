<?php

namespace App\Http\Controllers;

use App\Services\Mosecom\MosecomService;

class MosecomController extends Controller
{
    /** @var MosecomService $mosecomService */
    private $mosecomService;

    public function __construct(MosecomService $mosecomService)
    {
        $this->mosecomService = $mosecomService;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function parse(string $name = null)
    {
        $stations = $this->mosecomService->parse($name);
        $this->mosecomService->save($stations);

        //return $stations;
    }
}
