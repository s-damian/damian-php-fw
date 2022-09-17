<?php

namespace DamianPhp\Contracts\Date;

/**
 * @author  Stephen Damian <contact@damian-freelance.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    https://github.com/s-damian
 */
interface DateInterface
{
    public function __construct();

    /**
     * Setter pour DateTimeZone.
     */
    public function setTimeZoneForDateTimeZone(string $timeZone): self;

    /**
     * Getter pour DateTime formaté.
     *
     * @param string $format - Pour éventuellement changer format de DateTime.
     */
    public function getDateTimeFormat(string $format = 'Y-m-d H:i:s'): string;
}
