<?php

namespace ilhamrhmtkbr\App\Repository;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Models\UserAdvancePersonal;
use ilhamrhmtkbr\App\Models\UserAdvanceSkills;
use ilhamrhmtkbr\App\Models\UserAdvanceSocial;
use ilhamrhmtkbr\App\Models\UserProfileEducation;
use ilhamrhmtkbr\App\Models\UserProfileEducationDegree;
use ilhamrhmtkbr\App\Models\UserProfileExperience;
use ilhamrhmtkbr\App\Models\UserProfilePortfolio;
use ilhamrhmtkbr\App\Service\SessionService;

class UserRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): User
    {
        $statement = $this->connection->prepare("INSERT INTO user(email, password) VALUES (?, ?)");
        $statement->execute([
            $user->email,
            $user->password
        ]);

        return $user;
    }

    public function updateName(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE user SET 
            name = ? WHERE email = ?");

        $statement->execute([
            $user->name,
            $user->email
        ]);

        return $user;
    }

    public function updateEmail(User $user, string $newEmail): User
    {
        SessionService::update($newEmail);

        $statement = $this->connection->prepare("UPDATE user SET 
            email = ? WHERE email = ?");

        $statement->execute([
            $newEmail,
            $user->email
        ]);

        return $user;
    }

    public function updatePassword(User $user): User
    {
        $statement = $this->connection->prepare("UPDATE user SET 
            password = ? WHERE email = ?");

        $statement->execute([
            $user->password,
            $user->email
        ]);

        return $user;
    }


    public function findOne(User $user): ?User
    {
        $statement = $this->connection->prepare("SELECT email, name, password, created_at, updated_at FROM user WHERE email = ?");
        $statement->execute([$user->email]);

        try {
            if ($row = $statement->fetch()) {
                $user->email = $row['email'];
                $user->name = $row['name'];
                $user->password = $row['password'];
                $user->created_at = $row['created_at'];
                $user->updated_at = $row['updated_at'];

                return $user;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function deleteOne(User $user): string
    {
        $statement = $this->connection->prepare("DELETE FROM user WHERE email = ?");
        $statement->execute([$user->email]);

        return $user->email;
    }

    public function findUserLoginData(?User $user): ?array
    {
        if (!$user) {
            return null;
        }

        $bigResults = [
            'user_advance_personal' => [],
            'user_advance_skills' => [],
            'user_advance_socials' => [],
            'user_profile_education' => [],
            'user_profile_experience' => [],
            'user_profile_portfolio' => [],
        ];

        try {
            // Query 1: user_advance_personal
            $stmt = $this->connection->prepare("
            SELECT user.name, user.email, uap.image AS personal_image, 
                   uap.phone AS personal_phone, 
                   IF(LENGTH(uap.headline) > 31, CONCAT(LEFT(uap.headline, 31), '...'), uap.headline) AS personal_headline, 
                   uap.location AS personal_location
            FROM user
            JOIN user_advance_personal AS uap ON uap.user_id = user.email
            WHERE user.email = ?
        ");
            $stmt->execute([$user->email]);
            $bigResults['user_advance_personal'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Query 2: user_advance_skills
            $stmt = $this->connection->prepare("
            SELECT uas.name AS skill_name, uas.rating AS skill_rating, 
                   IF(LENGTH(uas.description) > 31, CONCAT(LEFT(uas.description, 31), '...'), uas.description) AS skill_description
            FROM user
            JOIN user_advance_skills AS uas ON uas.user_id = user.email
            WHERE user.email = ?
        ");
            $stmt->execute([$user->email]);
            $bigResults['user_advance_skills'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Query 3: user_advance_socials
            $stmt = $this->connection->prepare("
            SELECT uas.app_name, uas.url_link, uas.created_at
            FROM user
            JOIN user_advance_socials AS uas ON uas.user_id = user.email
            WHERE user.email = ?
        ");
            $stmt->execute([$user->email]);
            $bigResults['user_advance_socials'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Query 4: user_profile_education
            $stmt = $this->connection->prepare("
            SELECT upe.institution, upe.field, upe.graduation_year, upe.created_at
            FROM user
            JOIN user_profile_education AS upe ON upe.user_id = user.email
            WHERE user.email = ?
        ");
            $stmt->execute([$user->email]);
            $bigResults['user_profile_education'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Query 5: user_profile_experience
            $stmt = $this->connection->prepare("
            SELECT upe.job_title, 
                   IF(LENGTH(upe.job_description) > 31, CONCAT(LEFT(upe.job_description, 31), '...'), upe.job_description) as job_description, 
                   upe.company_name, upe.work_duration, upe.created_at
            FROM user
            JOIN user_profile_experience AS upe ON upe.user_id = user.email
            WHERE user.email = ?
        ");
            $stmt->execute([$user->email]);
            $bigResults['user_profile_experience'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Query 6: user_profile_portfolio
            $stmt = $this->connection->prepare("
            SELECT upp.title, upp.description, upp.link, upp.picture, upp.created_at
            FROM user
            JOIN user_profile_portfolio AS upp ON upp.user_id = user.email
            WHERE user.email = ?
        ");
            $stmt->execute([$user->email]);
            $bigResults['user_profile_portfolio'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            die("Database error: " . $e->getMessage());
        }

        return $bigResults;
    }


    public function findUserLoginRole(User $user): ?string
    {
        $statement = $this->connection->prepare("SELECT company_employee_roles.name 
            FROM company_employee_roles
            JOIN employees ON company_employee_roles.id = employees.role_id
            JOIN user ON employees.user_id = user.email
            WHERE user.email = ?
        ");
        $statement->execute([$user->email]);

        if ($result = $statement->fetch()) {
            return $result['name'];
        } else {
            return null;
        }
    }

    public function saveAdvancedPersonal(UserAdvancePersonal $advancePersonal): UserAdvancePersonal
    {
        $statement = $this->connection->prepare("INSERT INTO user_advance_personal(user_id, image, phone, headline, location) VALUES (?,?,?,?,?)");
        $statement->execute([
            $advancePersonal->user_id,
            $advancePersonal->image,
            $advancePersonal->phone,
            $advancePersonal->headline,
            $advancePersonal->location
        ]);

        return $advancePersonal;
    }

    public function updateAdvancedPersonal(UserAdvancePersonal $advancePersonal): UserAdvancePersonal
    {
        $statement = $this->connection->prepare("UPDATE user_advance_personal SET image = ?, phone = ?, headline = ?, location = ? WHERE user_id = ? ");
        $statement->execute([
            $advancePersonal->image,
            $advancePersonal->phone,
            $advancePersonal->headline,
            $advancePersonal->location,
            $advancePersonal->user_id
        ]);

        return $advancePersonal;
    }

    public function findOneAdvancedPersonal(UserAdvancePersonal $advancePersonal): ?UserAdvancePersonal
    {
        $statement = $this->connection->prepare("SELECT id, user_id, image, phone, headline, location FROM user_advance_personal WHERE user_id = ?");
        $statement->execute([$advancePersonal->user_id]);

        try {
            if ($row = $statement->fetch()) {
                $advancePersonal->id = $row['id'];
                $advancePersonal->user_id = $row['user_id'];
                $advancePersonal->image = $row['image'];
                $advancePersonal->phone = $row['phone'];
                $advancePersonal->headline = $row['headline'];
                $advancePersonal->location = $row['location'];

                return $advancePersonal;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function destroyOneAdvancePersonal(string $userId): void
    {
        $statement = $this->connection->prepare("DELETE FROM user_advance_personal WHERE user_id = ?");
        $statement->execute([$userId]);
    }

    public function saveAdvanceSkills(UserAdvanceSkills $advanceSkills): UserAdvanceSkills
    {
        $statement = $this->connection->prepare("INSERT INTO 
            user_advance_skills(user_id, name, rating, description) 
            VALUES (?,?,?,?)");
        $statement->execute([
            $advanceSkills->user_id,
            $advanceSkills->name,
            $advanceSkills->rating,
            $advanceSkills->description,
        ]);

        return $advanceSkills;
    }

    public function updateAdvanceSkills(UserAdvanceSkills $advanceSkills): UserAdvanceSkills
    {
        $statement = $this->connection->prepare("UPDATE 
            user_advance_skills 
            SET name = ?, rating = ?, description = ? 
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $advanceSkills->name,
            $advanceSkills->rating,
            $advanceSkills->description,
            $advanceSkills->id,
            $advanceSkills->user_id
        ]);

        return $advanceSkills;
    }

    public function destroyOneAdvanceSkills(UserAdvanceSkills $advanceSkills): UserAdvanceSkills
    {
        $statement = $this->connection->prepare("DELETE FROM
            user_advance_skills
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $advanceSkills->id,
            $advanceSkills->user_id
        ]);

        return $advanceSkills;
    }

    public function isThereAnyAdvanceSkills(UserAdvanceSkills $advanceSkills): bool
    {
        $statement = $this->connection->prepare("SELECT 
            name FROM user_advance_skills 
            WHERE user_id = ? AND name = ?");
        $statement->execute([$advanceSkills->user_id, $advanceSkills->name]);

        try {
            return $statement->fetch() === false;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAllAdvanceSkills(string $userId, array $data): array
    {
        $keyword = $data['keyword'];
        $itemsPerPage = 5;
        $offset = ($data['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($data['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'DESC';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total
            FROM user_advance_skills
            WHERE (name LIKE :keyword OR 
            description LIKE :keyword)
            AND user_id = :user_id");
        $countStatement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $countStatement->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, user_id, name, rating, description
            FROM user_advance_skills
            WHERE (name LIKE :keyword OR 
            description LIKE :keyword)
            AND user_id = :user_id
            ORDER BY id $orderBy
            LIMIT $itemsPerPage OFFSET $offset");
        $statement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_STR);

        $results = [];
        try {
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $advanceSkills = new UserAdvanceSkills();
                $advanceSkills->id = $row['id'];
                $advanceSkills->user_id = $row['user_id'];
                $advanceSkills->name = $row['name'];
                $advanceSkills->rating = $row['rating'];
                $advanceSkills->description = $row['description'];

                $results[] = $advanceSkills;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function saveAdvanceSocial(UserAdvanceSocial $advanceSocial): UserAdvanceSocial
    {
        $statement = $this->connection->prepare("INSERT INTO 
            user_advance_socials (user_id, app_name, url_link) 
            VALUES (?,?,?)");
        $statement->execute([
            $advanceSocial->user_id,
            $advanceSocial->app_name,
            $advanceSocial->url_link
        ]);

        return $advanceSocial;
    }

    public function updateAdvanceSocial(UserAdvanceSocial $advanceSocial): UserAdvanceSocial
    {
        $statement = $this->connection->prepare("UPDATE 
            user_advance_socials
            SET app_name = ?, url_link = ? 
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $advanceSocial->app_name,
            $advanceSocial->url_link,
            $advanceSocial->id,
            $advanceSocial->user_id
        ]);

        return $advanceSocial;
    }

    public function destroyOneAdvanceSocial(UserAdvanceSocial $advanceSocial): UserAdvanceSocial
    {
        $statement = $this->connection->prepare("DELETE FROM
            user_advance_socials
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $advanceSocial->id,
            $advanceSocial->user_id
        ]);

        return $advanceSocial;
    }

    public function isThereAnyAdvanceSocial(UserAdvanceSocial $advanceSocial): bool
    {
        $statement = $this->connection->prepare("SELECT 
            app_name FROM user_advance_socials
            WHERE user_id = ? AND app_name = ?");
        $statement->execute([$advanceSocial->user_id, $advanceSocial->app_name]);

        try {
            return $statement->fetch() === false;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAllAdvanceSocial(string $userId, array $data): array
    {
        $keyword = $data['keyword'];
        $itemsPerPage = 5;
        $offset = ($data['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($data['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'DESC';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total
            FROM user_advance_socials
            WHERE (app_name LIKE :keyword)
            AND user_id = :user_id");
        $countStatement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $countStatement->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, user_id, app_name, url_link, created_at
            FROM user_advance_socials
            WHERE (app_name LIKE :keyword OR 
            url_link LIKE :keyword)
            AND user_id = :user_id
            ORDER BY id $orderBy
            LIMIT $itemsPerPage OFFSET $offset");
        $statement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_STR);

        $results = [];
        try {
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $advanceSocial = new UserAdvanceSocial();
                $advanceSocial->id = $row['id'];
                $advanceSocial->user_id = $row['user_id'];
                $advanceSocial->app_name = $row['app_name'];
                $advanceSocial->url_link = $row['url_link'];
                $advanceSocial->created_at = $row['created_at'];

                $results[] = $advanceSocial;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function saveProfileEducation(UserProfileEducation $profileEducation): UserProfileEducation
    {
        $statement = $this->connection->prepare("INSERT INTO 
            user_profile_education(user_id, degree_id, institution, field, graduation_year) 
            VALUES (?,?,?,?,?)");
        $statement->execute([
            $profileEducation->user_id,
            $profileEducation->degree_id,
            $profileEducation->institution,
            $profileEducation->field,
            $profileEducation->graduation_year
        ]);

        return $profileEducation;
    }

    public function updateProfileEducation(UserProfileEducation $profileEducation): UserProfileEducation
    {
        $statement = $this->connection->prepare("UPDATE 
            user_profile_education 
            SET degree_id = ?, institution = ? , field = ? , graduation_year = ?
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $profileEducation->degree_id,
            $profileEducation->institution,
            $profileEducation->field,
            $profileEducation->graduation_year,
            $profileEducation->id,
            $profileEducation->user_id
        ]);

        return $profileEducation;
    }

    public function destroyOneProfileEducation(UserProfileEducation $profileEducation): UserProfileEducation
    {
        $statement = $this->connection->prepare("DELETE FROM
            user_profile_education
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $profileEducation->id,
            $profileEducation->user_id
        ]);

        return $profileEducation;
    }

    public function isThereAnyProfileEducation(UserProfileEducation $profileEducation): bool
    {
        $statement = $this->connection->prepare("SELECT 
            degree_id FROM user_profile_education 
            WHERE user_id = ? AND 
                  institution = ? AND
                  field = ? AND
                  graduation_year = ?
            ");
        $statement->execute([
            $profileEducation->user_id,
            $profileEducation->institution,
            $profileEducation->field,
            $profileEducation->graduation_year
        ]);

        try {
            return $statement->fetch() === false;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAllProfileEducation(string $userId, array $data): array
    {
        $keyword = $data['keyword'];
        $itemsPerPage = 5;
        $offset = ($data['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($data['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'DESC';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total
            FROM user_profile_education
            WHERE (field LIKE :keyword OR 
            institution LIKE :keyword)
            AND user_id = :user_id");
        $countStatement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $countStatement->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, user_id, degree_id, institution, field, graduation_year, created_at
            FROM user_profile_education
            WHERE (field LIKE :keyword OR 
            institution LIKE :keyword)
            AND user_id = :user_id
            ORDER BY id $orderBy
            LIMIT $itemsPerPage OFFSET $offset");
        $statement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_STR);

        $results = [];
        try {
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $profileEducation = new UserProfileEducation();
                $profileEducation->id = $row['id'];
                $profileEducation->user_id = $row['user_id'];
                $profileEducation->degree_id = $row['degree_id'];
                $profileEducation->institution = $row['institution'];
                $profileEducation->field = $row['field'];
                $profileEducation->graduation_year = $row['graduation_year'];
                $profileEducation->created_at = $row['created_at'];

                $results[] = $profileEducation;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function findAllProfileEducationDegree(): array
    {
        $statement = $this->connection->prepare("SELECT id, degree FROM user_profile_education_degree");

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $profileEducationDegree = new UserProfileEducationDegree();
                $profileEducationDegree->id = $row['id'];
                $profileEducationDegree->degree = $row['degree'];

                $results[] = $profileEducationDegree;
            }
        } finally {
            $statement->closeCursor();
        }

        return $results;
    }

    public function saveProfileExperience(UserProfileExperience $profileExperience): UserProfileExperience
    {
        $statement = $this->connection->prepare("INSERT INTO 
            user_profile_experience(user_id, job_title, job_description, company_name, work_duration) 
            VALUES (?,?,?,?,?)");
        $statement->execute([
            $profileExperience->user_id,
            $profileExperience->job_title,
            $profileExperience->job_description,
            $profileExperience->company_name,
            $profileExperience->work_duration
        ]);

        return $profileExperience;
    }

    public function updateProfileExperience(UserProfileExperience $profileExperience): UserProfileExperience
    {
        $statement = $this->connection->prepare("UPDATE 
            user_profile_experience 
            SET job_title = ?, job_description = ? , company_name = ? , work_duration = ?
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $profileExperience->job_title,
            $profileExperience->job_description,
            $profileExperience->company_name,
            $profileExperience->work_duration,
            $profileExperience->id,
            $profileExperience->user_id
        ]);

        return $profileExperience;
    }

    public function destroyOneProfileExperience(UserProfileExperience $profileExperience): UserProfileExperience
    {
        $statement = $this->connection->prepare("DELETE FROM
            user_profile_experience
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $profileExperience->id,
            $profileExperience->user_id
        ]);

        return $profileExperience;
    }

    public function isThereAnyProfileExperience(UserProfileExperience $profileExperience): bool
    {
        $statement = $this->connection->prepare("SELECT 
            job_title FROM user_profile_experience 
            WHERE user_id = ? AND job_title = ? AND job_description = ?");
        $statement->execute([
            $profileExperience->user_id,
            $profileExperience->job_title,
            $profileExperience->job_description
        ]);

        try {
            return $statement->fetch() === false;
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAllProfileExperience(string $userId, array $data): array
    {
        $keyword = $data['keyword'];
        $itemsPerPage = 5;
        $offset = ($data['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($data['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'DESC';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total
            FROM user_profile_experience
            WHERE (company_name LIKE :keyword OR 
            job_description LIKE :keyword OR
            job_title LIKE :keyword)
            AND user_id = :user_id");
        $countStatement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $countStatement->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, user_id, job_title, job_description, company_name, work_duration, created_at
            FROM user_profile_experience
            WHERE (company_name LIKE :keyword OR 
            job_description LIKE :keyword OR
            job_title LIKE :keyword)
            AND user_id = :user_id
            ORDER BY id $orderBy
            LIMIT $itemsPerPage OFFSET $offset");
        $statement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_STR);

        $results = [];
        try {
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $profileExperience = new UserProfileExperience();
                $profileExperience->id = $row['id'];
                $profileExperience->user_id = $row['user_id'];
                $profileExperience->job_title = $row['job_title'];
                $profileExperience->job_description = $row['job_description'];
                $profileExperience->company_name = $row['company_name'];
                $profileExperience->work_duration = $row['work_duration'];
                $profileExperience->created_at = $row['created_at'];

                $results[] = $profileExperience;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function saveProfilePortfolio(UserProfilePortfolio $profilePortfolio): UserProfilePortfolio
    {
        $statement = $this->connection->prepare("INSERT INTO 
            user_profile_portfolio(user_id, title, description, link, picture) 
            VALUES (?,?,?,?,?)");
        $statement->execute([
            $profilePortfolio->user_id,
            $profilePortfolio->title,
            $profilePortfolio->description,
            $profilePortfolio->link,
            $profilePortfolio->picture
        ]);

        return $profilePortfolio;
    }

    public function updateProfilePortfolio(UserProfilePortfolio $profilePortfolio): UserProfilePortfolio
    {
        $statement = $this->connection->prepare("UPDATE 
            user_profile_portfolio 
            SET title = ?, description = ? , link = ? , picture = ?
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $profilePortfolio->title,
            $profilePortfolio->description,
            $profilePortfolio->link,
            $profilePortfolio->picture,
            $profilePortfolio->id,
            $profilePortfolio->user_id
        ]);

        return $profilePortfolio;
    }

    public function destroyOneProfilePortfolio(UserProfilePortfolio $profilePortfolio): UserProfilePortfolio
    {
        $statement = $this->connection->prepare("DELETE FROM
            user_profile_portfolio
            WHERE id = ? AND user_id = ?");
        $statement->execute([
            $profilePortfolio->id,
            $profilePortfolio->user_id
        ]);

        return $profilePortfolio;
    }

    public function isThereAnyProfilePortfolio(UserProfilePortfolio $profilePortfolio): bool|UserProfilePortfolio
    {
        $statement = $this->connection->prepare("SELECT 
            id, user_id, title, description, link, picture FROM user_profile_portfolio 
            WHERE user_id = ? AND title = ?");
        $statement->execute([
            $profilePortfolio->user_id,
            $profilePortfolio->title
        ]);

        try {
            if ($row = $statement->fetch()) {
                $userPortfolio = new UserProfilePortfolio();
                $userPortfolio->id = $row['id'];
                $userPortfolio->user_id = $row['user_id'];
                $userPortfolio->title = $row['title'];
                $userPortfolio->description = $row['description'];
                $userPortfolio->link = $row['link'];
                $userPortfolio->picture = $row['picture'];

                return $userPortfolio;
            } else {
                return false;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function findAllProfilePortfolio(string $userId, array $data): array
    {
        $keyword = $data['keyword'];
        $itemsPerPage = 5;
        $offset = ($data['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($data['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'DESC';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total
            FROM user_profile_portfolio
            WHERE (link LIKE :keyword OR 
            description LIKE :keyword OR
            title LIKE :keyword)
            AND user_id = :user_id");
        $countStatement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $countStatement->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, user_id, title, description, link, picture, created_at
            FROM user_profile_portfolio
            WHERE (link LIKE :keyword OR 
            description LIKE :keyword OR
            title LIKE :keyword)
            AND user_id = :user_id
            ORDER BY id $orderBy
            LIMIT $itemsPerPage OFFSET $offset");
        $statement->bindValue(':keyword', "%$keyword%", \PDO::PARAM_STR);
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_STR);

        $results = [];
        try {
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $profilePortfolio = new UserProfilePortfolio();
                $profilePortfolio->id = $row['id'];
                $profilePortfolio->user_id = $row['user_id'];
                $profilePortfolio->title = $row['title'];
                $profilePortfolio->description = $row['description'];
                $profilePortfolio->link = $row['link'];
                $profilePortfolio->picture = $row['picture'];
                $profilePortfolio->created_at = $row['created_at'];

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
