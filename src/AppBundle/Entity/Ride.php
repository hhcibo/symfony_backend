<?php
/**
 * Created by PhpStorm.
 * User: Jasper
 * Date: 23.09.17
 * Time: 09:17
 */

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Ride
 * @package AppBundle\Entity
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RideRepository")
 */
class Ride
{
    protected static $lineColor = [
        'U1' => '#006ab3',
        'U2' => '#e1211a',
        'U3' => '#fd0',
        'U4' => '#0098a1'
    ];

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     *
     * @var datetime
     */
    private $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var datetime
     */
    private $endTime;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @var string
     */
    private $startStation;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $endStation;

    /**
     * @ORM\Column(type="string", length=5)
     *
     * @var string
     */
    private $line;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     *
     * @var string
     */
    private $uuid;


    /**
     * Ride constructor.
     * @param DateTime $startTime
     * @param string $startStation
     * @param string $uuid
     * @param $line
     */
    public function __construct(DateTime $startTime, $startStation, $uuid, $line)
    {
        $this->startTime = $startTime;
        $this->startStation = $startStation;
        $this->line = $line;
        $this->uuid = $uuid;

        $this->endStation = '';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return string
     */
    public function getStartStation()
    {
        return $this->startStation;
    }

    /**
     * @return DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param DateTime $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return string
     */
    public function getEndStation()
    {
        return $this->endStation;
    }

    /**
     * @param string $endStation
     */
    public function setEndStation($endStation)
    {
        $this->endStation = $endStation;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getLineColor()
    {
        return self::$lineColor[$this->getLine()];
    }

    /**
     * @return float
     */
    public function getCountStations()
    {
        $end = $this->getEndTime() ?: new DateTime();
        $seconds = $end->getTimestamp() - $this->getStartTime()->getTimestamp();

        return round($seconds / 10);
    }


    /**
     * @return float
     */
    public function getPrice()
    {
        return round($this->getCountStations() / 3) * 1.6;
    }
}