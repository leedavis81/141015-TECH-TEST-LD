<?php
namespace Smart\Service\Adapter;

/**
 * Class PdoAdapter
 * @package Smart\Service\Adapter
 */
class PdoAdapter implements AdapterInterface
{

    /**
     * Connection configuration details. Requires host, user, password and database
     * @var array $config
     */
    protected $config;

    /**
     * An established PDO connection
     * @var \PDO $connection
     */
    private $pdo;

    /**
     * Pass in a configuration array for each adapter
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get everything from a table, with an optional filters array
     * Super simple service adapter, not for production.
     * @param string $table
     * @param array $filters
     * @return array
     */
    public function getAll($table, array $filters = array())
    {
        $params = array();
        $query = "SELECT * FROM $table";
        if (sizeof($filters > 0))
        {
            $query .= " WHERE ";
            foreach ($filters as $key => $value)
            {
                $query .= " $key = :$key AND ";
                $params[':$key'] = $value;
            }
            $query = substr($query, 0, strlen($query)-5);
        }

        $statement = $this->getPdoConnection()->prepare(
            $query,
            array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY)
        );
        $statement->execute($params);
        return $statement->fetchAll();

    }

    /**
     * Start a transaction
     * @throws \Exception
     */
    public function startTransaction()
    {
        // Prevent auto-commiting
        $this->getPdoConnection()->setAttribute(\PDO::ATTR_AUTOCOMMIT, false);
        $this->getPdoConnection()->beginTransaction();
    }

    /**
     * Commit a transaction
     * @throws \Exception
     */
    public function commitTransaction()
    {
        $this->getPdoConnection()->commit();
    }

    /**
     * Rollback a transaction
     * @throws \Exception
     */
    public function rollbackTransaction()
    {
        $this->getPdoConnection()->rollBack();
    }

    /**
     * Insert an entry into a table
     * @param $table
     * @param array $values
     * @return mixed
     */
    public function insert($table, array $values = array())
    {
        if (empty($values))
        {
            return;
        }

        $insertQuery = 'INSERT INTO ' . $table
            . ' (' . implode(', ', array_keys($values)) . ') VALUES (:' . implode(', :', array_keys($values)) . ')';

        $statement = $this->getPdoConnection()->prepare($insertQuery);

        $params = array();
        foreach ($values as $key => $value)
        {
            $params[':' . $key] = $value;
        }

        $statement->execute($params);

    }

    /**
     * Delete an item by ID
     * @param $table
     * @param $id
     */
    public function delete($table, $id)
    {
        $statement = $this->getPdoConnection()->prepare('DELETE FROM ' . $table . ' WHERE id = :id');
        $statement->execute(array(
            ':id' => (int) $id
        ));
    }

    /**
     * Get a PDO connection from our configuration array
     * @return \PDO
     * @throws \Exception
     */
    protected function getPdoConnection()
    {
        if (!$this->pdo instanceof \PDO)
        {
            // Allow an exceptions to bubble up to fail
            if (!isset($this->config['database.host']))
            {
                throw new \Exception('No DB host "database.host" has been provided in configuration');
            }
            if (!isset($this->config['database.dbname']))
            {
                throw new \Exception('No DB name "database.dbname"  has been provided in configuration');
            }
            if (!isset($this->config['database.dbname']))
            {
                throw new \Exception('No DB user "database.user"  has been provided in configuration');
            }
            if (!isset($this->config['database.password']))
            {
                throw new \Exception('No DB password "database.password"  has been provided in configuration');
            }

            if (!isset($this->config['database.port']))
            {
                $this->config['database.port'] = 3306;
            }

            $this->pdo = new \PDO(
                'mysql:host=' . $this->config['database.host'] . ';dbname=' . $this->config['database.dbname'],
                $this->config['database.user'],
                $this->config['database.password'],
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8; SET CHARACTER SET utf8; SET COLLATION_CONNECTION = 'utf8_unicode_ci'")
            );
        }

        return $this->pdo;
    }
}


