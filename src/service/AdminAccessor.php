<?php

declare(strict_types = 1);

namespace Demo\Service;

require_once "../repository/admin/AdminDevAccess.php";

use Demo\Repository\AdminDevAccess;

class AdminAccessor
{
    private $repository;

    public function __construct()
    {
        $this->repository = new AdminDevAccess();
    }

    public function verifyPassword(?string $password): bool
    {
        if (!is_string($password)) {
            return false;
        }
        $realPassword = $this->repository->getPassword();
        return crypt($password, $realPassword) == $realPassword;
    }
}