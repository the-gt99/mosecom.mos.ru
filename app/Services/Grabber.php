<?php


namespace App\Services;


class Grabber
{
    private $adapters = [];

    private $conf;

    public function __construct()
    {
        $this->conf = config('adapter');
        $this->initAll();
    }

    private function initAll()
    {
        foreach ($this->conf['adapters'] as $adapter) {
            $this->adapters[$adapter] = new $adapter();
        }
    }

    public function getAdapters()
    {
        return $this->adapters;
    }

    public function getAdapter($abstract): ?GrabAdapterInterface
    {
        return $this->adapters[$abstract] ?? null;
    }

}
