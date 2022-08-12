<?php

namespace Tests\DamianPhp\Support\String;

use Tests\BaseTest;
use DamianPhp\Support\String\OrderBy;

class OrderByTest extends BaseTest
{
    public function testOrderBy(): void
    {
        $orderBy = new OrderBy();
        $orderBy->setColumns(['id', 'name']);
        $resultOrderBy = $orderBy->get();

        $this->assertTrue(is_array($resultOrderBy));

        $this->assertTrue( isset($resultOrderBy['orderBy']) && is_string($resultOrderBy['orderBy']) );
        $this->assertTrue( isset($resultOrderBy['order']) && is_string($resultOrderBy['order']) );
        $this->assertTrue( isset($resultOrderBy['attrs']) && is_array($resultOrderBy['attrs']) );

        $this->assertTrue( isset($resultOrderBy['attrs']['id']['href']) && is_string($resultOrderBy['attrs']['id']['href']) );
        $this->assertTrue( isset($resultOrderBy['attrs']['id']['class']) && is_string($resultOrderBy['attrs']['id']['class']) );

        $this->assertTrue( isset($resultOrderBy['attrs']['name']['href']) && is_string($resultOrderBy['attrs']['name']['href']) );
        $this->assertTrue( isset($resultOrderBy['attrs']['name']['href']) && is_string($resultOrderBy['attrs']['name']['href']) );
    }
}
