<?php

namespace App\Services;

class GeographyHelper
{

    private CONST COMPASS_POINTS_ANGLES = [
        'n' => 0,
        'nw' => 315,
        'w' => 270,
        'sw' => 225,
        's' => 180,
        'se' => 135,
        'e' => 90,
        'ne' => 45,
    ];

    private const ERROR = 20;

    public static function getPolar($x, $y)
    {
        $N = ($y>0)?'n':'';
        $S = ($y<0)?'s':'';
        $E = ($x>0)?'e':'';
        $W = ($x<0)?'w':'';
        return $N.$S.$E.$W;
    }

    public static function getDegrees($x, $y)
    {
        return number_format(self::calculateDegrees($x,$y),3);
    }

    public static function getRadians($x, $y)
    {
        return self::calculateRadians($x, $y);
    }

    private static function calculateDegrees($x, $y)
    {
        if($x==0 AND $y==0) {
            return 0;
        }

        return ($x < 0) ? rad2deg(atan2($x,$y))+360 : rad2deg(atan2($x,$y));
    }

    private static function calculateRadians($x, $y)
    {
        if($x==0 AND $y==0) {
            return 0;
        }

        return deg2rad(self::calculateDegrees($x, $y));
    }

    public static function getAngleByCompassPoint(string $compass_point): ?int
    {
        return self::COMPASS_POINTS_ANGLES[$compass_point];
    }

    public static function getCompassError(): float
    {
        return self::ERROR;
    }

    public static function getAngleConditions($windDirection)
    {
        switch ($windDirection) {
            case 'n':
                $min_condition = 0;
                $max_condition = 0;
                break;
            case 'nw':
                $min_condition = 270;
                $max_condition = 360;
                break;
            case 'w':
                $min_condition = 270;
                $max_condition = 270;
                break;
            case 'sw':
                $min_condition = 180;
                $max_condition = 270;
                break;
            case 's':
                $min_condition = 180;
                $max_condition = 180;
                break;
            case 'se':
                $min_condition = 90;
                $max_condition = 180;
                break;
            case 'e':
                $min_condition = 90;
                $max_condition = 90;
                break;
            case 'ne':
                $min_condition = 0;
                $max_condition = 90;
                break;
        }
        return [$min_condition, $max_condition];
    }
}
