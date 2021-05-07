<?php

namespace App\Http\Controllers;

use App\Services\Mosecom\MosecomParserService;
use Illuminate\Http\Request;

class ParserController extends Controller
{
    private $mosecomParser;

    public function __construct(MosecomParserService $mosecomParser)
    {
        $this->mosecomParser = $mosecomParser;
    }

    public function parse()
    {
        $stations = $this->mosecomParser->getStations();

        return $this->mosecomParser->getStationInfoByName($stations[0]);
    }
}
