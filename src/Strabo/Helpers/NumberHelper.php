<?php

/*
 * This file is part of the Path Segmentation (Strabo) package.
 *
 * (c) Evertracker GmbH <developers@evertracker.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strabo\Helpers;

/**
 * Class NumberHelper
 * @package Strabo\Helpers
 */
class NumberHelper
{
    /**
     * Calculate the floor of a number including decimals.
     * It has the same functionality as floor() but it can also include decimals.
     *
     * @param float $number    The float number to round.
     * @param int   $precision The number of decimals to include.
     *
     * @return float
     */
    public static function floor($number, $precision = 0)
    {
        $decimals = pow(10, $precision);

        return floor($number * $decimals) / $decimals;
    }
}
