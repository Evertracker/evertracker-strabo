<?php

/*
 * This file is part of the Path Segmentation (Strabo) package.
 *
 * (c) Evertracker GmbH <developers@evertracker.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strabo\Model\Segmentation;

require '../../bootstrap.php';

use PHPUnit_Framework_TestCase;
use Strabo\Model\EquatorLocation;

/**
 * Class EquatorLocationTest
 * @package Strabo\Model\Segmentation
 */
class EquatorLocationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Strabo\Model\Location::equals
     */
    public function testEquals()
    {
        // Create the two locations
        $location1 = (new EquatorLocation())->setLatitude(12.123)->setLongitude(43.234);
        $location2 = (new EquatorLocation())->setLatitude(12.123)->setLongitude(43.234);
        $this->assertTrue($location1->equals($location2, 10));
    }

    /**
     * @covers \Strabo\Model\Location::distance
     */
    public function testDistance()
    {
        // Create the two locations
        $location1 = (new EquatorLocation())->setLatitude(12.123)->setLongitude(13.234);
        $location2 = (new EquatorLocation())->setLatitude(12.223)->setLongitude(13.244);
        $this->assertEquals(11172.491, $location1->distance($location2, 3));
    }
}
