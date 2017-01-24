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

use DateTime;
use InvalidArgumentException;
use Strabo\Helpers\NumberHelper;

/**
 * Class Location
 * @package Strabo\Model
 */
abstract class Location
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * @var float
     */
    protected $altitude;

    /**
     * @var DateTime
     */
    protected $timestamp;

    /**
     * @return float
     */
    abstract function getRadianInMeters();

    /**
     * Check if the given location is the same as current location.
     *
     * @param Location $location  The location to check for equal.
     * @param int      $precision The precision in decimals to check. 10 by default.
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public function equals($location, $precision = 10)
    {
        // Check if location is valid
        if (empty($location) || !($location instanceof self)) {
            throw new InvalidArgumentException('The given location is not a valid location object.');
        }

        // Round values
        $lat1Rounded = NumberHelper::floor($this->getLatitude(), $precision);
        $long1Rounded = NumberHelper::floor($this->getLongitude(), $precision);
        $lat2Rounded = NumberHelper::floor($location->getLatitude(), $precision);
        $long2Rounded = NumberHelper::floor($location->getLongitude(), $precision);

        // Check equality
        return ($lat1Rounded === $lat2Rounded) && ($long1Rounded === $long2Rounded);
    }

    /**
     * Get the distance between two locations.
     *
     * @param Location $location
     *
     * @return float                    The distance in meters.
     * @throws InvalidArgumentException
     */
    public function getDistance(Location $location)
    {
        // Check objects.
        if (empty($location)) {
            throw new InvalidArgumentException('The given location is not a valid location object.');
        }

        // Check equals
        if ($this->equals($location, $precision = 10)) {
            return 0;
        }

        // Get radians measurements
        $radiusLatitude1 = $this->gradToRadian($this->getLatitude());
        $radiusLatitude2 = $this->gradToRadian($location->getLatitude());
        $deltaLatitude = $this->gradToRadian($location->getLatitude() - $this->getLatitude());
        $deltaLongitude = $this->gradToRadian($location->getLongitude() - $this->getLongitude());

        // Calculate the distance on the surface of the earth
        $deltaLatitude_sin = sin($deltaLatitude / 2);
        $deltaLongitude_sin = sin($deltaLongitude / 2);
        $a = (pow($deltaLatitude_sin, 2)) + (cos($radiusLatitude1) * cos($radiusLatitude2) * pow($deltaLongitude_sin, 2));
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $this->getRadianInMeters() * $c;
    }

    /**
     * @param float $grad
     *
     * @return float
     */
    public static function gradToRadian($grad)
    {
        return pi() * $grad / 180;
    }

    /**
     * Get the speed between two locations.
     *
     * @param Location $location
     *
     * @return float The speed in meters per second from one location to the other
     */
    public function getSpeed(Location $location)
    {
        // Get distance
        $distance = $this->getDistance($location);
        $duration = abs($this->getTimestamp() - $location->getTimestamp());
        if (empty($duration)) {
            throw new InvalidArgumentException('The duration between the two locations is 0 and thus speed cannot be calculated');
        }

        return $distance / $duration;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     *
     * @return $this
     */
    public function setLatitude(float $latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     *
     * @return $this
     */
    public function setLongitude(float $longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return float
     */
    public function getAltitude()
    {
        return $this->altitude;
    }

    /**
     * @param float $altitude
     *
     * @return $this
     */
    public function setAltitude(float $altitude)
    {
        $this->altitude = $altitude;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime $timestamp
     *
     * @return $this
     */
    public function setTimestamp(DateTime $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
