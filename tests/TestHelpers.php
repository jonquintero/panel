<?php
/**
 * Created by PhpStorm.
 * User: usuario
 * Date: 24/8/2018
 * Time: 1:19 PM
 */

namespace Tests;


trait TestHelpers
{
    protected function assertDatabaseEmpty($table, $connection = null)
    {
        $total  = $this->getConnection($connection)->table($table)->count();
        $this->assertSame(0,$total, sprintf(
            "Failed asserting the table [%s] is empty. %s %s found.", $table, $total, str_plural('row', $total)
        ));
    }

    /**
     * @return array
     */
    protected function withData(array $custom = [])
    {
        return array_merge($this->defaultData(), $custom);
    }

    protected function defaultData()
    {
        return $this->defaultData;
    }
}