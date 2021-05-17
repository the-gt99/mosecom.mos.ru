<?php


namespace App\Services;

interface GrabAdapterInterface
{
    public static function getAdapterName();

    /**
     * collection|Station[]
     */
    public function grabData(): void;
}
