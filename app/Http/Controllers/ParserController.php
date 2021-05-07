<?php

namespace App\Http\Controllers;

use App\Services\Mosecom\MosecomService;
use ArrayIterator;
use CachingIterator;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    private $mosecomService;

    public function __construct(MosecomService $mosecomService)
    {
        $this->mosecomService = $mosecomService;
    }

    /**
     * @return array
     */
    public function parse()
    {
        return $this->mosecomService->parse();
    }
}
