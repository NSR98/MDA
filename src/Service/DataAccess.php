<?php

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

abstract class DataAccess
{

    private $connection;
    private $security;
    private $requestStack;
    private $lastInsertId;

    public function __construct(Connection $connection, Security $security, RequestStack $requestStack)
    {
        $this->connection = $connection;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    /**
     * @param String $sql
     * @param array $args
     * @param bool $log
     * @return bool|Statement
     */
    public function executeSQL(string $sql = "", array $args = [], $log = true) {
        try {
            $stmt = $this->connection->prepare($sql);
            foreach ($args as $clave => $valor) {
                $stmt->bindValue($clave, $valor);
            }

            if ($stmt->execute()) {
                $this->lastInsertId = $this->connection->lastInsertId();
                return $stmt;
            } else {
                return false;
            }
        } catch (Exception $e) {
            dump($e);
            return false;
        }
    }

    /**
     * @return string
     */
    public function getlastInsertId()
    {
        return $this->lastInsertId;
    }

    /**
     * @param Statement|array $items
     * @return array
     */
    public static function indexRowByCod($items): array
    {
        $res = [];
        if (is_a($items, "Statement")) {
            while ($row = $items->fetch()) {
                $res[$row['id']] = $row;
            }
        } else {
            foreach ($items as $row) {
                $res[$row['id']] = $row;
            }
        }
        return $res;
    }

}
