<?php

namespace application;
include('Config.php');
/**
 * Class Adapter
 * @package application
 */
class Adapter
{
    /**
     * @var self
     */
    private static $inst;

    /**
     * @var \PDO
     */
    public $connection;

    /**
     * @return Adapter
     */
    public static function getInstance()
    {
        if (!isset(self::$inst)) {
            self::$inst = new self();
        }

        return self::$inst;
    }

    /**
     */
    public function getConnection()
    {
        $conf = Config::getInstance()->getConfig();
        $this->connection = new \PDO("mysql:host={$conf->db->host};dbname={$conf->db->name}", $conf->db->user, $conf->db->password);
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     */
    public function dropConnection()
    {
        if (isset($this->connection)) {
            $this->connection = null;
        }
    }

    /**
     * @param string $query
     * @param array $args
     */
    public function exec($query, $args = [])
    {
        try {
            $this->getConnection();
            $stmt = $this->connection->prepare($query);
            $stmt->execute($args);
            $this->dropConnection();
        } catch (\PDOException $e) {
            Logger::getInstance()->warn("Error is thrown with message - " . $e->getMessage());
        }
    }

    /**
     * @param string $query
     * @return array
     */
    public function selectAll($query)
    {
        try {
            $this->getConnection();
            $stmt = $this->connection->query($query);
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $this->dropConnection();
            return $result;
        } catch (\PDOException $e) {
            Logger::getInstance()->warn("Error is thrown with message - " . $e->getMessage());
        }
    }
}
