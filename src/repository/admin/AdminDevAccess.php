<?php

declare(strict_types = 1);

namespace Demo\Repository;

require_once "../service/MyPDO.php";

use Demo\Service\myPDO;

class AdminDevAccess
{
    private $connection;

    public function __construct()
    {
        $this->connection = myPDO::getConnection();
    }

    public function getPassword(): string
    {
        $sql = "SELECT `password` FROM `admins` WHERE `id` = 1";
        $statement = $this->connection->prepare($sql);
        $statement->execute();
        $realPassword = $statement->fetch(\PDO::FETCH_ASSOC)["password"];
        return $realPassword;
    }
}