<?php

namespace SSF\ORM\Cache\Old;

use DateInterval;
use DateTime;

class TTL
{
    /**
     * @var DateInterval|null
     */
    private ?DateInterval $dateInterval = null;

    /**
     * @var int|null
     */
    private ?int $secondsLeft = null;

    /**
     * @param DateInterval|int|null $ttl
     */
    public function __construct(
        DateInterval|int|null $ttl = null
    ){
        if ($ttl !== null) {
            $this->secondsLeft = $this->convertToSecondsLeft($ttl);
            $this->dateInterval = $this->convertToDateInterval($ttl);
        }
    }

    public function getDateInterval(): ?DateInterval
    {
        return $this->dateInterval;
    }

    public function getSecondsLeft(): ?int
    {
        return $this->secondsLeft;
    }

    public function getTimestamp(): ?int
    {
        return time() + $this->secondsLeft;
    }

    public function setDateInterval(DateInterval $dateInterval): static
    {
        $this->dateInterval = $dateInterval;
        $this->secondsLeft = $this->convertToSecondsLeft($dateInterval);
        return $this;
    }

    public function setSecondsLeft(int $secondsLeft): static
    {
        $this->secondsLeft = $secondsLeft;
        $this->dateInterval = $this->convertToDateInterval($secondsLeft);
        return $this;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->secondsLeft = $timestamp - time();
        $this->dateInterval = $this->convertToDateInterval($this->secondsLeft);
        return $this;
    }

    public static function dateInterval(DateInterval|int|null $ttl): ?DateInterval
    {
        return (new static($ttl))->getDateInterval();
    }

    public static function secondsLeft(DateInterval|int|null $ttl): ?int
    {
        return (new static($ttl))->getSecondsLeft();
    }

    public static function timestamp(DateInterval|int|null $ttl): ?int
    {
        return (new static($ttl))->getTimestamp();
    }

    /**
     * @param DateInterval|int|null $ttl
     * @return DateInterval|null
     */
    private function convertToDateInterval(DateInterval|int|null $ttl): ?DateInterval
    {
        if (null === $ttl || $ttl instanceof DateInterval) {
            return $ttl;
        }

        return DateInterval::createFromDateString("$ttl seconds");
    }

    /**
     * @param DateInterval|int|null $ttl
     * @return int|null
     */
    private function convertToSecondsLeft(DateInterval|int|null $ttl): ?int
    {
        if (null === $ttl) {
            return null;
        }

        if ($ttl instanceof DateInterval) {
            return (new DateTime())->add($ttl)->getTimestamp();
        }

        return $ttl;
    }

    /**
     * @param DateInterval|int|null $ttl
     * @return int|null
     */
    private function convertToTimestamp(DateInterval|int|null $ttl): ?int
    {
        return time() + $this->convertToSecondsLeft($ttl);
    }
}