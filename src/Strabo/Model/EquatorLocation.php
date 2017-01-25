<?php

/*
 * This file is part of the Path Segmentation (Strabo) package.
 *
 * (c) Evertracker GmbH <developers@evertracker.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strabo\Model;

/**
 * Class EquatorLocation
 * @package Strabo\Model
 */
class EquatorLocation extends Location
{
    const __EARTH_RADIUS_METERS = 6371000;

    /**
     * @return float
     */
    function getEarthRadius()
    {
        return self::__EARTH_RADIUS_METERS;
    }
}
