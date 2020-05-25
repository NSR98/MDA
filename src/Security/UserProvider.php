<?php

namespace App\Security;

use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\DBAL\Connection;

class UserProvider implements UserProviderInterface
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function loadUserByUsername($username)
    {
        return $this->getUser($username);
    }

    private function getUser($username)
    {
        $sql = "SELECT * FROM usuarios WHERE user = :name";
        try {
            $stmt = $this->connection->prepare($sql);
        } catch (DBALException $e) {
        }
        $stmt->bindValue("name", $username);
        $stmt->execute();
        $row = $stmt->fetch();

        if (!$row || !$row['user'])
        {
            $exception = new UsernameNotFoundException(sprintf('Username "%s" not found in the database.', $username));
            $exception->setUsername($username);
            throw $exception;
        }
        else
        {
            return new User($row["id"], $row["user"], $row["password"], $row["surname"], $row["name"], $row["dni"], $row["email"], $row["rol"]);
        }
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->getUser($user->getUsername());
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
