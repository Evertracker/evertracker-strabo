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

use LogicException;
use Strabo\Model\Location;

/**
 * Class Segment
 * @package Strabo\Model\Segmentation
 */
class Segment
{
    /**
     * @var int The segment type based on SegmentType
     */
    private $type;

    /**
     * @var Location[] All the locations that are included in this segment
     */
    private $locations = [];

    /**
     * @var float The average speed of the segment
     */
    private $averageSpeed = 0;

    /**
     * @var float The min speed of the segment
     */
    private $minSpeed = 0;

    /**
     * @var float The max speed of the segment
     */
    private $maxSpeed = 0;

    /**
     * @var float The total distance of the segment, as a sum of all the location distances
     */
    private $distance = 0;

    /**
     * @var float The transposition of the segment, as a distance from first to last location
     */
    private $transposition = 0;

    /**
     * @var bool
     */
    private $sorted = false;

    /**
     * Segment constructor.
     *
     * @param Location[] $locations
     * @param int        $type
     */
    public function __construct(array $locations = [], $type = null)
    {
        $this->setLocations($locations, false);
        $this->setType($type);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType(int $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Location[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @param Location[] $locations All the new locations of the segment
     * @param bool       $analyse   Set to true to analyse the segment, or call analyse()
     *
     * @return $this
     */
    public function setLocations(array $locations, $analyse = false)
    {
        $this->locations = $locations;
        $this->sorted = false;

        return $this->sortLocations($analyse);
    }

    /**
     * @param Location $location The location to push into the segment
     * @param bool     $analyse  Set to true to analyse the segment, or call analyse()
     *
     * @return $this
     */
    public function pushLocation(Location $location, $analyse = false)
    {
        array_push($this->locations, $location);
        $this->sorted = false;

        return $this->sortLocations($analyse);
    }

    /**
     * @param bool $analyse Set to true to analyse the segment, or call analyse()
     *
     * @return $this
     */
    public function popLocation($analyse = false)
    {
        if (!$this->locations) {
            throw new LogicException('You tried to pop from an empty location stack.');
        }

        array_shift($this->locations);

        if ($analyse) {
            $this->analyse();
        }

        return $this;
    }

    /**
     * Sort locations based on timestamp
     *
     * @param bool $analyse Set to true to analyse the segment, or call analyse()
     *
     * @return $this
     */
    protected function sortLocations($analyse = false)
    {
        // Check if the locations are already sorted
        if ($this->sorted) {
            return $this;
        }

        // Sort locations in ASCENDING order based on timestamp
        usort($this->locations, function (Location $location1, Location $location2) {
            if ($location1->getTimestamp() === $location2->getTimestamp()) {
                return 0;
            }

            return ($location1->getTimestamp() < $location2->getTimestamp()) ? -1 : 1;
        });

        // Set flag for sorted
        $this->sorted = true;

        // Analyse if necessary
        if ($analyse) {
            $this->analyse();
        }

        return $this;
    }

    /**
     * Get the first location of the segment in chronological order.
     * If no locations found, it will return null.
     *
     * @return null|Location
     */
    public function getFirstLocation()
    {
        return count($this->getLocations()) > 0 ? $this->getLocations()[0] : null;
    }

    /**
     * Get the last location of the segment in chronological order.
     * If no locations found, it will return null.
     *
     * @return null|Location
     */
    public function getLastLocation()
    {
        $count = count($this->getLocations());

        return $count > 0 ? $this->getLocations()[$count - 1] : null;
    }

    /**
     * Analyse the segment and calculate the following:
     * - Distance of the segment as in sum of location transpositions
     * - Length of the segment as distance from first to last location
     * - Speeds: Average, Min and Max
     */
    protected function analyse()
    {
        // Check if there are locations to analyse
        if (count($this->getLocations()) < 2) {
            return;
        }

        // Analyse and extract distance and speed
        $distance = 0;
        $minSpeed = PHP_INT_MAX;
        $maxSpeed = 0;
        $lastLocation = null;
        foreach ($this->locations as $location) {
            if (!empty($lastLocation)) {
                $distance += $location->getDistance($lastLocation);
            }

            $lastLocation = $location;

            // Set min and max speed
            $speed = $location->getSpeed($lastLocation);
            $minSpeed = $speed < $minSpeed ? $speed : $minSpeed;
            $maxSpeed = $speed > $maxSpeed ? $speed : $maxSpeed;
        }

        // Set total distance, min and max speeds
        $this->setDistance($distance);
        $this->setMinSpeed($minSpeed);
        $this->setMaxSpeed($maxSpeed);

        // Calculate average speed
        $time = $this->getLastLocation()->getTimestamp() - $this->getFirstLocation()->getTimestamp();
        if ($time > 0) {
            $this->setAverageSpeed($this->getDistance() / $time);
        }

        // Calculate segment transposition length
        $this->setTransposition($this->getFirstLocation()->getDistance($this->getLastLocation()));
    }

    /**
     * @return float
     */
    public function getAverageSpeed()
    {
        return $this->averageSpeed;
    }

    /**
     * @param float $averageSpeed
     *
     * @return $this
     */
    public function setAverageSpeed(float $averageSpeed)
    {
        $this->averageSpeed = $averageSpeed;

        return $this;
    }

    /**
     * @return float
     */
    public function getMinSpeed()
    {
        return $this->minSpeed;
    }

    /**
     * @param float $minSpeed
     *
     * @return $this
     */
    public function setMinSpeed(float $minSpeed)
    {
        $this->minSpeed = $minSpeed;

        return $this;
    }

    /**
     * @return float
     */
    public function getMaxSpeed()
    {
        return $this->maxSpeed;
    }

    /**
     * @param float $maxSpeed
     *
     * @return $this
     */
    public function setMaxSpeed(float $maxSpeed)
    {
        $this->maxSpeed = $maxSpeed;

        return $this;
    }

    /**
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     *
     * @return $this
     */
    public function setDistance(float $distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * @return float
     */
    public function getTransposition()
    {
        return $this->transposition;
    }

    /**
     * @param float $transposition
     *
     * @return $this
     */
    public function setTransposition(float $transposition)
    {
        $this->transposition = $transposition;

        return $this;
    }
}
