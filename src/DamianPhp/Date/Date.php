<?php

declare(strict_types=1);

namespace DamianPhp\Date;

use DateTime;
use DateTimeZone;
use DamianPhp\Support\Helper;
use DamianPhp\Contracts\Date\DateInterface;

/**
 * Classe client.
 * Gestion de dates.
 * Peut fonctionner avec une Facade.
 *
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
class Date extends DateTime implements DateInterface
{
    private string $timeZone;

    private DateTimeZone $dateTimeZone;

    private DateTime $dateTime;

    public function __construct(string $datetime = "now", ?DateTimeZone $timezone = null)
    {
        parent::__construct($datetime, $timezone);

        $this->timeZone = Helper::config('app')['timezone'];
    }

    /**
     * Setter pour DateTimeZone.
     */
    public function setTimeZoneForDateTimeZone(string $timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    /**
     * Getter pour DateTime formaté.
     *
     * @param string $format - Pour éventuellement changer le format de DateTime.
     */
    public function getDateTimeFormat(string $format = 'Y-m-d H:i:s'): string
    {
        $this->setDateTimeZone()->setDateTime();

        return $this->dateTime->format($format);
    }

    private function setDateTimeZone(): self
    {
        $this->dateTimeZone = new DateTimeZone($this->timeZone);

        return $this;
    }

    private function setDateTime(): self
    {
        $this->dateTime = self::createFromFormat('d/m/Y', date('d/m/Y'), $this->dateTimeZone);

        return $this;
    }
}
