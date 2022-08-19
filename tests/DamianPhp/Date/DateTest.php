<?php

namespace Tests\DamianPhp\Date;

use Tests\BaseTest;
use DamianPhp\Date\Date;

class DateTest extends BaseTest
{
    public function testGetTimeZone(): void
    {
        $date = new Date();

        $this->assertTrue(is_string($date->getDateTimeFormat()));
    }

    public function testSetTimeZoneForDateTimeZone(): void
    {
        $date1 = new Date();
        $date1->setTimeZoneForDateTimeZone('Europe/Paris');

        $date2 = new Date();
        $date2->setTimeZoneForDateTimeZone('America/Winnipeg');

        $this->assertTrue($date1->getDateTimeFormat() !== $date2->getDateTimeFormat());
    }

    public function testDateFormat(): void
    {
        $date = new Date();

        $this->assertTrue(is_string($date->format('Y-m-d H:i:s')));
    }
}
