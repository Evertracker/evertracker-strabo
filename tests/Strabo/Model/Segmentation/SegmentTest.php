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

use PHPUnit_Framework_TestCase;

require '../../../bootstrap.php';

class SegmentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Segment
     */
    protected $segment;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->segment = new Segment();
    }

    /**
     * @covers \Strabo\Model\Segmentation\Segment::analyse
     */
    public function testAnalyse()
    {
        // Clear segment locations
        $this->getSegment()->setLocations([]);
    }

    /**
     * @return Segment
     */
    public function getSegment(): Segment
    {
        return $this->segment;
    }
}
