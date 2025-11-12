<?php

namespace ilhamrhmtkbr\App\Repository;

use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Models\UserSession;

class SessionRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function create(UserSession $session): UserSession
    {
        $user = new User();
        $user->email = $session->user_id;
        if (self::findOne($user) == null) {
            $statement = $this->connection->prepare("INSERT INTO user_sessions(user_id) VALUES (?)");
            $statement->execute([$session->user_id]);
        }
        return $session;
    }

    public function findOne(User $user): ?UserSession
    {
        $statement = $this->connection->prepare("SELECT user_id FROM user_sessions WHERE user_id = ?");
        $statement->execute([$user->email]);

        try {
            if ($row = $statement->fetch()) {
                $session = new UserSession();
                $session->user_id = $row['user_id'];

                return $session;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteById(string $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM user_sessions WHERE user_id = ?");
        $statement->execute([$id]);
    }

    public function deleteAll(): void
    {
        $this->connection->exec("DELETE FROM sessions");
    }
}
