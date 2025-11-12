<?php

namespace ilhamrhmtkbr\App\Repository;

use ilhamrhmtkbr\App\Models\Candidates;
use ilhamrhmtkbr\App\Models\CompanyOfficeRecruitments;

class CandidateRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function isThereAnyJobApplied(Candidates $candidates): bool
    {
        $statement = $this->connection->prepare("SELECT user_id, job_id FROM candidates WHERE user_id = ? AND job_id = ?");
        $statement->execute([$candidates->user_id, $candidates->job_id]);

        if ($statement->fetch()) {
            return true;
        } else {
            return false;
        }
    }

    public function saveJobApply(Candidates $candidates): void
    {
        $statement = $this->connection->prepare("INSERT INTO candidates (user_id, job_id) VALUES (?,?)");
        $statement->execute([
            $candidates->user_id,
            $candidates->job_id
        ]);
    }

    public function findOneJob(string|int $id): ?array
    {
        $statement = $this->connection->prepare("SELECT
                cor.id, cor.job_title, cor.job_description, 
                cor.status, cod.name, cod.description, 
                cor.created_at, cor.updated_at 
            FROM company_office_recruitments AS cor
            JOIN company_office_departments AS cod ON cor.department_id = cod.id
            WHERE cor.id = ?");
        $statement->execute([$id]);

        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            return $row;
        } else {
            return null;
        }
    }

    public function findAllJobs(?int $page = 1, ?string $orderBy = 'DESC', ?string $keyword = ''): array
    {
        $itemsPerPage = 5;
        $offset = ($page - 1) * $itemsPerPage;

        $orderBy = strtoupper($orderBy);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'DESC';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total
            FROM company_office_recruitments
            WHERE job_title LIKE :keyword");
        $countStatement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT 
                id, 
                job_title, 
                department_id, 
                IF(LENGTH(job_description) > 35, CONCAT(LEFT(job_description, 35), '...'), job_description) AS job_description, 
                status, 
                created_at,
                updated_at
            FROM company_office_recruitments
            WHERE job_title LIKE :keyword
            ORDER BY id $orderBy
            LIMIT $itemsPerPage OFFSET $offset");
        $statement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);

        $results = [];
        try {
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $profilePortfolio = new CompanyOfficeRecruitments();
                $profilePortfolio->id = $row['id'];
                $profilePortfolio->job_title = $row['job_title'];
                $profilePortfolio->department_id = $row['department_id'];
                $profilePortfolio->job_description = $row['job_description'];
                $profilePortfolio->status = $row['status'];
                $profilePortfolio->created_at = $row['created_at'];
                $profilePortfolio->updated_at = $row['updated_at'];

                $results[] = $profilePortfolio;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function findAllJobsWasApplied(string $userId, ?int $page = 1, ?string $orderBy = 'DESC', ?string $keyword = ''): array
    {
        $itemsPerPage = 5;
        $offset = ($page - 1) * $itemsPerPage;

        $orderBy = strtoupper($orderBy);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'DESC';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total
            FROM candidates AS c
            JOIN company_office_recruitments AS cor ON c.job_id = cor.id    
            WHERE cor.job_title LIKE :keyword AND user_id = :user_id");
        $countStatement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $countStatement->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT 
                c.id, cor.job_title, 
                IF(LENGTH(cor.job_description) > 35, CONCAT(LEFT(cor.job_description, 35), '...'), cor.job_description) AS job_description, 
                cor.status, cor.created_at, cor.updated_at
            FROM candidates AS c
            JOIN company_office_recruitments AS cor ON c.job_id = cor.id 
            JOIN company_office_departments AS cod ON cor.department_id = cod.id   
            WHERE cor.job_title LIKE :keyword AND user_id = :user_id
            ORDER BY id $orderBy
            LIMIT $itemsPerPage OFFSET $offset");
        $statement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_STR);

        $results = [];
        try {
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $profilePortfolio = new CompanyOfficeRecruitments();
                $profilePortfolio->id = $row['id'];
                $profilePortfolio->job_title = $row['job_title'];
                $profilePortfolio->job_description = $row['job_description'];
                $profilePortfolio->status = $row['status'];
                $profilePortfolio->created_at = $row['created_at'];
                $profilePortfolio->updated_at = $row['updated_at'];

                $results[] = $profilePortfolio;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }
}
