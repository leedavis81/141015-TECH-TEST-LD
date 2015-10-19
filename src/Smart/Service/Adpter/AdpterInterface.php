<?php

namespace Smart\Service\Adapter;

interface AdapterInterface
{
    /**
     * Pass in a configuration array for each adapter
     * @param array $config
     */
    public function __construct($config);

    /**
     * Get everything from a table, with an optional filters array
     * Super simple service adapter, not for production.
     * @param string $table
     * @param array $filters
     */
    public function getAll($table, array $filters = array());

    /**
     * Insert an entry into a table
     * @param $table
     * @param array $values
     * @return mixed
     */
    public function insert($table, array $values = array());
}


