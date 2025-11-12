<?php

namespace ilhamrhmtkbr\App\Repository;

use ilhamrhmtkbr\App\Config\Database;
use DateTime;
use ilhamrhmtkbr\App\Models\Candidates;
use ilhamrhmtkbr\App\Models\EmployeePayrolls;
use ilhamrhmtkbr\App\Models\Employees;
use ilhamrhmtkbr\App\Models\CompanyEmployeeProjects;
use ilhamrhmtkbr\App\Models\CompanyEmployeeRoles;
use ilhamrhmtkbr\App\Models\CompanyOfficeDepartements;
use ilhamrhmtkbr\App\Models\CompanyOfficeFinancialTransactions;
use ilhamrhmtkbr\App\Models\CompanyOfficeRecruitments;
use ilhamrhmtkbr\App\Models\EmployeeAttendanceRules;
use ilhamrhmtkbr\App\Models\EmployeeContracts;
use ilhamrhmtkbr\App\Models\EmployeeLeaveRequests;
use ilhamrhmtkbr\App\Models\EmployeeOvertime;
use ilhamrhmtkbr\App\Models\EmployeeProjectAssigments;

class HrRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findAllCandidates(array $parameter): array
    {
        $status = $parameter['status'] ?? '';
        $itemsPerPage = 5;
        $offset = ($parameter['page'] - 1) * $itemsPerPage;
        $orderBy = in_array(strtoupper($parameter['orderBy']), ['ASC', 'DESC']) ? strtoupper($parameter['orderBy']) : 'DESC';

        $baseQuery = "FROM candidates AS c
                  JOIN user AS u ON c.user_id = u.email
                  WHERE u.name LIKE :keyword";

        $queryParams = [':keyword' => "%{$parameter['keyword']}%"];
        if ($status !== '') {
            $baseQuery .= " AND c.status = :status";
            $queryParams[':status'] = $status;
        }

        $countQuery = "SELECT COUNT(*) AS total $baseQuery";
        $countStatement = $this->connection->prepare($countQuery);
        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value, \PDO::PARAM_STR);
        }
        $countStatement->execute();
        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $dataQuery = "SELECT u.name, u.email, u.created_at, c.status 
                  $baseQuery
                  ORDER BY c.id $orderBy
                  LIMIT :limit OFFSET :offset";
        $dataStatement = $this->connection->prepare($dataQuery);

        foreach ($queryParams as $key => $value) {
            $dataStatement->bindValue($key, $value, \PDO::PARAM_STR);
        }
        $dataStatement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $dataStatement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];
        try {
            $dataStatement->execute();
            while ($row = $dataStatement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $dataStatement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results,
        ];
    }

    public function findOneUserDetails(string $userId): ?array
    {
        $connectMysqli = Database::getConnMysqli();

        if ($connectMysqli->connect_error) {
            die("Connection failed: " . $connectMysqli->connect_error);
        }

        // Escape user email to prevent SQL injection
        $email = $connectMysqli->real_escape_string($userId);

        // Multi-query 
        $sql = "SELECT user.name, 
                    user.email, 
                    uap.image, 
                    uap.phone, 
                    uap.headline, 
                    uap.location
                FROM user JOIN user_advance_personal AS uap ON uap.user_id = user.email
                WHERE user.email = '$email';

                SELECT uas.name, 
                    uas.rating, 
                    uas.description
                FROM user JOIN user_advance_skills AS uas ON uas.user_id = user.email
                WHERE user.email = '$email';

                SELECT uas.app_name, 
                    uas.url_link, 
                    uas.created_at
                FROM user JOIN user_advance_socials AS uas ON uas.user_id = user.email
                WHERE user.email = '$email';

                SELECT upe.institution, 
                    upe.field, 
                    upe.graduation_year, 
                    upe.created_at
                FROM user JOIN user_profile_education AS upe ON upe.user_id = user.email
                WHERE user.email = '$email';

                SELECT upe.job_title, 
                    upe.job_description, 
                    upe.company_name, 
                    upe.work_duration, 
                    upe.created_at
                FROM user JOIN user_profile_experience AS upe ON upe.user_id = user.email
                WHERE user.email = '$email';

                SELECT upp.title, 
                    upp.description, 
                    upp.link, 
                    upp.picture, 
                    upp.created_at
                FROM user JOIN user_profile_portfolio AS upp ON upp.user_id = user.email
                WHERE user.email = '$email';
            ";

        $bigResults = [
            'user_personal' => [],
            'user_skills' => [],
            'user_socials' => [],
            'user_education' => [],
            'user_experience' => [],
            'user_portfolio' => [],
        ];

        // Eksekusi multi_query
        if ($connectMysqli->multi_query($sql)) {
            $keys = array_keys($bigResults); // Key array hasil akhir
            $index = 0; // Penunjuk tabel

            do {
                // Ambil hasil query
                if ($result = $connectMysqli->store_result()) {
                    while ($row = $result->fetch_assoc()) {
                        $bigResults[$keys[$index]][] = $row; // Simpan hasil ke array
                    }
                    $result->free(); // Bebaskan memori
                }
                $index++; // Pindah ke tabel berikutnya
            } while ($connectMysqli->next_result() && $index < count($keys));
        } else {
            die("Error in multi_query: " . $connectMysqli->error);
        }

        return $bigResults; // Kembalikan data
    }

    public function editStatusCandidate(Candidates $candidates)
    {
        $statement = $this->connection->prepare("UPDATE candidates SET status = ? WHERE user_id = ?");
        $statement->execute([$candidates->status, $candidates->user_id]);
    }

    public function findAllEmployees(array $parameter): array
    {
        $itemsPerPage = 5;
        $offset = ($parameter['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameter['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $roleName = $parameter['role'] ?? '';
        $departmentName = $parameter['department'] ?? '';
        $salary = $parameter['salary'] ?? '';
        $status = $parameter['status'] ?? '';

        $conditionQuery = '';
        $queryParams = [':keyword' => "%{$parameter['keyword']}%"];

        if (!empty($roleName)) {
            $conditionQuery .= "cer.name = :role_name ";
            $queryParams[':role_name'] = $roleName;
        }

        if (!empty($departmentName)) {
            if ($conditionQuery == '') {
                $conditionQuery .= " cod.name = :department_name ";
            } else {
                $conditionQuery .= "AND cod.name = :department_name ";
            }
            $queryParams[':department_name'] = $departmentName;
        }

        if (!empty($salary)) {
            if ($conditionQuery == '') {
                $conditionQuery .= " e.salary >= :salary ";
            } else {
                $conditionQuery .= "AND e.salary >= :salary ";
            }
            $queryParams[':salary'] = $salary;
        }

        if (!empty($status)) {
            if ($conditionQuery == '') {
                $conditionQuery .= " e.status = :status ";
            } else {
                $conditionQuery .= "AND e.status = :status ";
            }
            $queryParams[':status'] = $status;
        }

        if ($conditionQuery != '') {
            if (strpos($conditionQuery, 'AND')) {
                $conditionQuery = "($conditionQuery) AND";
            } else {
                $conditionQuery = "$conditionQuery AND";
            }
        }

        $query = "FROM employees AS e
            JOIN user AS u ON e.user_id = u.email
            JOIN company_employee_roles AS cer ON e.role_id = cer.id
            JOIN company_office_departments AS cod ON e.department_id = cod.id 
            WHERE $conditionQuery u.name LIKE :keyword";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total 
            $query");

        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value, \PDO::PARAM_STR);
        }

        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $results = [];

        $statement = $this->connection->prepare("SELECT 
                u.name, u.email, cer.name AS role, cod.name AS department, e.salary, e.hire_date, e.status, u.created_at
            $query
            ORDER BY e.hire_date $orderBy
            LIMIT :limit OFFSET :offset
        ");

        foreach ($queryParams as $key => $value) {
            $statement->bindValue($key, $value, \PDO::PARAM_STR);
        }

        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'results' => $results,
            'total-page' => $totalPage
        ];
    }

    public function findAllCompanyRoles(): ?array
    {
        $statement = $this->connection->prepare("SELECT id, name FROM company_employee_roles");
        $statement->execute();

        $results = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $results[$row['id']] = $row['name'];
        }

        return $results;
    }

    public function findAllCompanyDepartments(): ?array
    {
        $statement = $this->connection->prepare("SELECT id, name FROM company_office_departments");
        $statement->execute();

        $results = [];

        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $results[$row['id']] = $row['name'];
        }

        return $results;
    }

    public function isThereAnyEmployee(Employees $employee): bool
    {
        $statement = $this->connection->prepare("SELECT user_id FROM employees WHERE user_id = ?");
        $statement->execute([$employee->user_id]);

        return $statement->fetch() !== false;
    }

    public function saveEmployee(Employees $employee): void
    {
        $statement = $this->connection->prepare("INSERT INTO employees (user_id, role_id, department_id, salary, hire_date, status)
            VALUES (?,?,?,?,?,?)");
        $statement->execute([
            $employee->user_id,
            $employee->role_id,
            $employee->department_id,
            $employee->salary,
            $employee->hire_date,
            $employee->status
        ]);
    }

    public function editEmployee(Employees $employee): void
    {
        $statement = $this->connection->prepare("UPDATE employees SET
            role_id = ?,
            department_id = ?,
            salary = ?,
            hire_date = ?,
            status = ?
            WHERE user_id = ?");
        $statement->execute([
            $employee->role_id,
            $employee->department_id,
            $employee->salary,
            $employee->hire_date,
            $employee->status,
            $employee->user_id
        ]);
    }

    public function isCandidatesHired(string $userId): bool
    {
        $statement = $this->connection->prepare("SELECT user_id FROM candidates WHERE user_id = ? AND status = 'hired'");
        $statement->execute([$userId]);

        return $statement->fetch() !== false;
    }

    public function destroyEmployee(string $userId): void
    {
        $statement = $this->connection->prepare("DELETE FROM employees WHERE user_id = ?");
        $statement->execute([$userId]);

        $checkCondition = $this->connection->prepare("SELECT user_id FROM candidates WHERE user_id = ?");
        $checkCondition->execute([$userId]);

        if ($checkCondition->fetch() !== false) {
            $nextStatement = $this->connection->prepare("DELETE FROM candidates WHERE user_id = ?");
            $nextStatement->execute([$userId]);
        };
    }

    public function findEmployeeDetails(string $userId): array
    {
        $connectMysqli = Database::getConnMysqli();

        if ($connectMysqli->connect_error) {
            die("Connection failed: " . $connectMysqli->connect_error);
        }

        $email = $connectMysqli->real_escape_string($userId);

        $sql = "SELECT u.name, u.email, cer.name AS role, cod.name AS department, e.salary, e.hire_date, e.status, e.created_at
            FROM employees AS e
            JOIN user AS u ON e.user_id = u.email
            JOIN company_office_departments AS cod ON e.department_id = cod.id
            JOIN company_employee_roles AS cer ON e.role_id = cer.id
            WHERE u.email = '$email';

            SELECT ea.attendance_date, ea.check_in_time, ea.check_out_time, ea.status, ea.late_penalty, ea.created_at, ea.updated_at
            FROM employees AS e
            JOIN employee_attendance AS ea ON e.user_id = ea.employee_id
            WHERE e.user_id = '$email';

            SELECT ec.contract_start_date, ec.contract_end_date, ec.salary, ec.contract_terms, ec.created_at, ec.updated_at
            FROM employees AS e
            JOIN employee_contracts AS ec ON e.user_id = ec.employee_id
            WHERE e.user_id = '$email';

            SELECT elr.leave_type, elr.start_date, elr.end_date, elr.status, elr.remarks, elr.created_at, elr.updated_at
            FROM employees AS e
            JOIN employee_leave_requests AS elr ON e.user_id = elr.employee_id
            WHERE e.user_id = '$email';

            SELECT eo.overtime_date, eo.start_time, eo.end_time, eo.total_hours, eo.overtime_rate, eo.total_payment, eo.remarks, eo.created_at, eo.updated_at 
            FROM employees AS e
            JOIN employee_overtime AS eo ON e.user_id = eo.employee_id
            WHERE e.user_id = '$email';
            
            SELECT ep.payroll_month, ep.base_salary, ep.total_overtime, ep.late_penalties, ep.net_salary, ep.status, ep.payment_date, ep.remarks, ep.created_at, ep.updated_at
            FROM employees AS e
            JOIN employee_payrolls AS ep ON ep.employee_id = e.user_id
            WHERE e.user_id = '$email';

            SELECT cep.name, cep.description, cep.start_date, cep.end_date, cep.status, cep.created_at, cep.updated_at, epa.role_in_project, epa.assigned_date
            FROM employees AS e
            JOIN employee_project_assignments AS epa ON epa.employee_id = e.user_id
            JOIN company_employee_projects AS cep ON epa.project_id = cep.id
            WHERE e.user_id = '$email';
            ";

        $bigResults = [
            'employee_data' => [],
            'employee_attendance' => [],
            'employee_contracts' => [],
            'employee_leave_request' => [],
            'employee_overtime' => [],
            'employee_payrolls' => [],
            'employee_project_assignments' => [],
        ];

        if ($connectMysqli->multi_query($sql)) {
            $keys = array_keys($bigResults);
            $index = 0;

            do {
                if ($result = $connectMysqli->store_result()) {
                    while ($row = $result->fetch_assoc()) {
                        $bigResults[$keys[$index]][] = $row;
                    }
                    $result->free();
                }
                $index++;
            } while ($connectMysqli->next_result() && $index < count($keys));
        } else {
            die("Error : " . $connectMysqli->error);
        }

        return $bigResults;
    }

    public function findAllEmployeeAttendance(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $queryParams = [':keyword' => ["%{$parameters['keyword']}%", \PDO::PARAM_STR]];
        $queryConditions = ['u.name LIKE :keyword'];

        if ($parameters['status'] != '') {
            $queryParams[':status'] = [$parameters['status'], \PDO::PARAM_STR];
            $queryConditions[] = 'ea.status = :status';
        }

        $attendanceDateFrom = $parameters['attendance_date_from'] ?? '';
        $attendanceDateUntil = $parameters['attendance_date_until'] ?? '';

        if (!empty($attendanceDateFrom) && !empty($attendanceDateUntil)) {
            $queryConditions[] = 'ea.attendance_date BETWEEN ' . $attendanceDateFrom . ' AND ' . $attendanceDateUntil;
        }

        if (count($queryConditions) > 1) {
            $conditions = implode(' AND ', $queryConditions);
        } else {
            $conditions = $queryConditions[0];
        }

        $querySql = "FROM employee_attendance AS ea 
            JOIN user AS u ON ea.employee_id = u.email
            WHERE $conditions";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $querySql");
        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value[0], $value[1]);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT u.name, ea.attendance_date, ea.check_in_time, ea.check_out_time, ea.status, ea.late_penalty, ea.created_at, ea.updated_at
            $querySql
            ORDER BY ea.id $orderBy
            LIMIT :limit OFFSET :offset
        ");
        foreach ($queryParams as $key => $value) {
            $statement->bindValue($key, $value[0], $value[1]);
        }
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'results' => $results,
            'total-page' => $totalPage
        ];
    }

    public function findAllEmployeeAttendanceRules(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $querySql = "FROM employee_attendance_rules WHERE rule_name LIKE :keyword";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $querySql");
        $countStatement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, rule_name, start_time, end_time, late_threshold, penalty_for_late, created_at, updated_at
            $querySql
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset
        ");
        $statement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'results' => $results,
            'total-page' => $totalPage
        ];
    }

    public function isThereAnyEmployeeAttendanceRule(EmployeeAttendanceRules $employeeAttendanceRule): bool
    {
        $statement = $this->connection->prepare("SELECT rule_name FROM employee_attendance_rules WHERE rule_name = ?");
        $statement->execute([$employeeAttendanceRule->rule_name]);

        return $statement->fetch() !== false;
    }

    public function saveEmployeeAttendanceRule(EmployeeAttendanceRules $employeeAttendanceRule): ?EmployeeAttendanceRules
    {
        $statement = $this->connection->prepare("INSERT INTO employee_attendance_rules (rule_name, start_time, end_time, late_threshold, penalty_for_late) VALUES(?,?,?,?,?)");
        $statement->execute([
            $employeeAttendanceRule->rule_name,
            $employeeAttendanceRule->start_time,
            $employeeAttendanceRule->end_time,
            $employeeAttendanceRule->late_threshold,
            $employeeAttendanceRule->penalty_for_late
        ]);

        return $employeeAttendanceRule;
    }

    public function editEmployeeAttendanceRule(EmployeeAttendanceRules $employeeAttendanceRule): ?EmployeeAttendanceRules
    {
        $statement = $this->connection->prepare("UPDATE employee_attendance_rules SET rule_name = ?, start_time = ?, end_time = ?, late_threshold = ?, penalty_for_late = ? WHERE id = ?");
        $statement->execute([
            $employeeAttendanceRule->rule_name,
            $employeeAttendanceRule->start_time,
            $employeeAttendanceRule->end_time,
            $employeeAttendanceRule->late_threshold,
            $employeeAttendanceRule->penalty_for_late,
            $employeeAttendanceRule->id
        ]);

        return $employeeAttendanceRule;
    }

    public function destroyEmployeeAttendanceRule(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_attendance_rules WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestEmployeeAttendanceRuleGetId(string $ruleName): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM employee_attendance_rules WHERE rule_name = ?");
        $statement->execute([$ruleName]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function forTestEmployeeAttendanceRuleDestroy(string $ruleName): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_attendance_rules WHERE rule_name = ?");
        $statement->execute([$ruleName]);
    }

    public function findAllEmployeeContracts(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $queryParams = [':keyword' => ["%{$parameters['keyword']}%", \PDO::PARAM_STR]];
        $queryConditions = ['u.name LIKE :keyword'];

        $contractStartDateFrom = $parameters['contract_start_date_from'] ?? '';
        $contractStartDateUntil = $parameters['contract_start_date_until'] ?? '';

        if (!empty($contractStartDateFrom) && !empty($contractStartDateUntil)) {
            $queryConditions[] = 'ec.contract_start_date BETWEEN ' . $contractStartDateFrom . ' AND ' . $contractStartDateUntil;
        }

        if (count($queryConditions) > 1) {
            $conditions = implode(' AND ', $queryConditions);
        } else {
            $conditions = $queryConditions[0];
        }

        $querySql = "FROM employee_contracts AS ec 
            JOIN user AS u ON ec.employee_id = u.email
            WHERE $conditions";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $querySql");
        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value[0], $value[1]);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT ec.id, u.name, u.email, ec.contract_start_date, ec.contract_end_date, ec.salary, ec.contract_terms, ec.created_at, ec.updated_at
            $querySql
            ORDER BY ec.id $orderBy
            LIMIT :limit OFFSET :offset
        ");
        foreach ($queryParams as $key => $value) {
            $statement->bindValue($key, $value[0], $value[1]);
        }
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'results' => $results,
            'total-page' => $totalPage
        ];
    }

    public function isThereAnyEmployeeContract(EmployeeContracts $employeeContract): bool
    {
        $statement = $this->connection->prepare("SELECT employee_id FROM employee_contracts WHERE employee_id = ?");
        $statement->execute([$employeeContract->employee_id]);

        return $statement->fetch() !== false;
    }

    public function saveEmployeeContract(EmployeeContracts $employeeContract): ?EmployeeContracts
    {
        $statement = $this->connection->prepare("INSERT INTO employee_contracts (employee_id, contract_start_date, contract_end_date, salary, contract_terms) VALUES (?,?,?,?,?)");
        $statement->execute([
            $employeeContract->employee_id,
            $employeeContract->contract_start_date,
            $employeeContract->contract_end_date,
            $employeeContract->salary,
            $employeeContract->contract_terms
        ]);

        return $employeeContract;
    }

    public function editEmployeeContract(EmployeeContracts $employeeContract): ?EmployeeContracts
    {
        $statement = $this->connection->prepare("UPDATE employee_contracts SET contract_start_date = ?, contract_end_date = ?, salary = ?, contract_terms = ? WHERE id = ?");
        $statement->execute([
            $employeeContract->contract_start_date,
            $employeeContract->contract_end_date,
            $employeeContract->salary,
            $employeeContract->contract_terms,
            $employeeContract->id
        ]);

        return $employeeContract;
    }

    public function destroyEmployeeContract(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_contracts WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestEmployeeContractDestroy(string $employeeId): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_contracts WHERE employee_id = ?");
        $statement->execute([$employeeId]);
    }

    public function forTestEmployeeContractGetId(EmployeeContracts $employeeContract): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM employee_contracts WHERE employee_id = ? AND contract_start_date = ? AND contract_end_date = ? AND salary = ? AND contract_terms = ?");
        $statement->execute([
            $employeeContract->employee_id,
            $employeeContract->contract_start_date,
            $employeeContract->contract_end_date,
            $employeeContract->salary,
            $employeeContract->contract_terms
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllEmployeeLeaveRequests(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $queryParams = [':keyword' => ["%{$parameters['keyword']}%", \PDO::PARAM_STR]];
        $queryConditions = ['u.name LIKE :keyword'];

        if ($parameters['status'] != '') {
            $queryParams[':status'] = [$parameters['status'], \PDO::PARAM_STR];
            $queryConditions[] = 'elr.status = :status';
        }

        if (isset($parameters['leave_type']) && $parameters['leave_type'] != '') {
            $queryParams[':leave_type'] = [$parameters['leave_type'], \PDO::PARAM_STR];
            $queryConditions[] = 'elr.leave_type = :leave_type';
        }

        $startDateFrom = $parameters['start_date_from'] ?? '';
        $startDateUntil = $parameters['start_date_until'] ?? '';

        if (!empty($startDateFrom) && !empty($startDateUntil)) {
            $queryConditions[] = 'elr.start_date BETWEEN ' . $startDateFrom . ' AND ' . $startDateUntil;
        }

        if (count($queryConditions) > 1) {
            $conditions = implode(' AND ', $queryConditions);
        } else {
            $conditions = $queryConditions[0];
        }

        $querySql = "FROM employee_leave_requests AS elr 
            JOIN user AS u ON elr.employee_id = u.email
            WHERE $conditions";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $querySql");
        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value[0], $value[1]);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT 
            elr.id, u.name, u.email, elr.leave_type, elr.start_date, elr.end_date, elr.status, elr.remarks, elr.created_at, elr.updated_at
            $querySql
            ORDER BY elr.id $orderBy
            LIMIT :limit OFFSET :offset
        ");
        foreach ($queryParams as $key => $value) {
            $statement->bindValue($key, $value[0], $value[1]);
        }
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'results' => $results,
            'total-page' => $totalPage
        ];
    }

    public function isThereAnyEmployeeLeaveRequest(EmployeeLeaveRequests $employeeLeaveRequest): bool
    {
        $statement = $this->connection->prepare("SELECT leave_type, remarks FROM employee_leave_requests WHERE leave_type = ? AND remarks = ?");
        $statement->execute([
            $employeeLeaveRequest->leave_type,
            $employeeLeaveRequest->remarks
        ]);

        return $statement->fetch() !== false;
    }

    public function saveEmployeeLeaveRequest(EmployeeLeaveRequests $employeeLeaveRequest): ?EmployeeLeaveRequests
    {
        $statement = $this->connection->prepare("INSERT INTO employee_leave_requests(employee_id, leave_type, start_date, end_date, status, remarks) VALUES (?,?,?,?,?,?)");
        $statement->execute([
            $employeeLeaveRequest->employee_id,
            $employeeLeaveRequest->leave_type,
            $employeeLeaveRequest->start_date,
            $employeeLeaveRequest->end_date,
            $employeeLeaveRequest->status,
            $employeeLeaveRequest->remarks
        ]);

        return $employeeLeaveRequest;
    }

    public function editEmployeeLeaveRequest(EmployeeLeaveRequests $employeeLeaveRequest): ?EmployeeLeaveRequests
    {
        $statement = $this->connection->prepare("UPDATE employee_leave_requests SET leave_type = ?, start_date = ?, end_date = ?, status = ?, remarks = ? WHERE id = ?");
        $statement->execute([
            $employeeLeaveRequest->leave_type,
            $employeeLeaveRequest->start_date,
            $employeeLeaveRequest->end_date,
            $employeeLeaveRequest->status,
            $employeeLeaveRequest->remarks,
            $employeeLeaveRequest->id
        ]);

        return $employeeLeaveRequest;
    }

    public function destroyEmployeeLeaveRequest(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_leave_requests WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestEmployeeLeaveRequestDestroy(string $employeeId): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_leave_requests WHERE employee_id = ?");
        $statement->execute([$employeeId]);
    }

    public function forTestEmployeeLeaveRequestGetId(EmployeeLeaveRequests $employeeLeaveRequest): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM employee_leave_requests WHERE employee_id = ? AND leave_type = ? AND start_date = ? AND end_date = ? AND status = ? AND remarks = ?");
        $statement->execute([
            $employeeLeaveRequest->employee_id,
            $employeeLeaveRequest->leave_type,
            $employeeLeaveRequest->start_date,
            $employeeLeaveRequest->end_date,
            $employeeLeaveRequest->status,
            $employeeLeaveRequest->remarks
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllEmployeeOvertime(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $queryParams = [':keyword' => ["%{$parameters['keyword']}%", \PDO::PARAM_STR]];
        $queryConditions = ['u.name LIKE :keyword'];

        $overtimeDateFrom = $parameters['overtime_date_from'] ?? '';
        $overtimeDateUntil = $parameters['overtime_date_until'] ?? '';

        if (!empty($overtimeDateFrom) && !empty($overtimeDateUntil)) {
            $queryConditions[] = 'elr.overtime_date BETWEEN ' . $overtimeDateFrom . ' AND ' . $overtimeDateUntil;
        }

        if (count($queryConditions) > 1) {
            $conditions = implode(' AND ', $queryConditions);
        } else {
            $conditions = $queryConditions[0];
        }

        $querySql = "FROM employee_overtime AS eo 
            JOIN user AS u ON eo.employee_id = u.email
            WHERE $conditions";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $querySql");
        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value[0], $value[1]);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT 
                eo.id, u.email, u.name, 
                eo.overtime_date, eo.start_time, eo.end_time, 
                eo.total_hours, eo.overtime_rate, eo.total_payment, 
                eo.remarks, eo.created_at, eo.updated_at
            $querySql
            ORDER BY eo.id $orderBy
            LIMIT :limit OFFSET :offset
        ");
        foreach ($queryParams as $key => $value) {
            $statement->bindValue($key, $value[0], $value[1]);
        }
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'results' => $results,
            'total-page' => $totalPage
        ];
    }

    public function isThereAnyEmployeeOvertime(EmployeeOvertime $employeeOvertime): bool
    {
        $statement = $this->connection->prepare("SELECT employee_id, overtime_date
            FROM employee_overtime WHERE employee_id = ? AND overtime_date = ? ");
        $statement->execute([
            $employeeOvertime->employee_id,
            $employeeOvertime->overtime_date
        ]);

        return $statement->fetch() !== false;
    }

    public function saveEmployeeOvertime(EmployeeOvertime $employeeOvertime): ?EmployeeOvertime
    {
        $statement = $this->connection->prepare("INSERT INTO employee_overtime (employee_id, overtime_date, start_time, end_time, overtime_rate, remarks) VALUES (?,?,?,?,?,?)");
        $statement->execute([
            $employeeOvertime->employee_id,
            $employeeOvertime->overtime_date,
            $employeeOvertime->start_time,
            $employeeOvertime->end_time,
            $employeeOvertime->overtime_rate,
            $employeeOvertime->remarks
        ]);

        return $employeeOvertime;
    }

    public function editEmployeeOvertime(EmployeeOvertime $employeeOvertime): ?EmployeeOvertime
    {
        $statement = $this->connection->prepare("UPDATE employee_overtime SET overtime_date = ?, start_time = ?, end_time = ?, overtime_rate = ?, remarks = ? WHERE id = ?");
        $statement->execute([
            $employeeOvertime->overtime_date,
            $employeeOvertime->start_time,
            $employeeOvertime->end_time,
            $employeeOvertime->overtime_rate,
            $employeeOvertime->remarks,
            $employeeOvertime->id
        ]);

        return $employeeOvertime;
    }

    public function destroyEmployeeOvertime(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_overtime WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestEmployeeOvertimeDestroy(string $employeeId): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_overtime WHERE employee_id = ?");
        $statement->execute([$employeeId]);
    }

    public function forTestEmployeeOvertimeGetId(EmployeeOvertime $employeeOvertime): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM employee_overtime WHERE employee_id = ? AND overtime_date = ? AND start_time = ? AND end_time = ? AND overtime_rate = ? AND remarks = ?");
        $statement->execute([
            $employeeOvertime->employee_id,
            $employeeOvertime->overtime_date,
            $employeeOvertime->start_time,
            $employeeOvertime->end_time,
            $employeeOvertime->overtime_rate,
            $employeeOvertime->remarks
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllEmployeePayrolls(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = $parameters['orderBy'];
        if ($parameters['orderBy'] != '') {
            strtoupper($parameters['orderBy']);
            if (!in_array($parameters['orderBy'], ['ASC', 'DESC'])) {
                $orderBy = 'ASD';
            }
        }

        $structure = "FROM employee_payrolls AS ep
            JOIN employees AS e ON ep.employee_id = e.user_id
            JOIN user AS u ON e.user_id = u.email
            JOIN company_office_departments AS cod ON e.department_id = cod.id
            JOIN company_employee_roles AS cer ON e.role_id = cer.id
            WHERE u.name LIKE :keyword ";
        $queryParams = [':keyword' => ["%{$parameters['keyword']}%", \PDO::PARAM_STR]];

        $queryConditions = [];

        if ($parameters['status'] != '') {
            $queryConditions[] = 'ep.status = :status';
            $queryParams[':status'] = [$parameters['status'], \PDO::PARAM_STR];
        }

        $total_overtime = $parameters['total_overtime'] ?? '';
        if (!empty($total_overtime)) {
            $queryConditions[] = 'ep.total_overtime = :total_overtime';
            $queryParams[':total_overtime'] = [$total_overtime, \PDO::PARAM_INT];
        }

        $base_salary = $parameters['base_salary'] ?? '';
        if (!empty($base_salary)) {
            $queryConditions[] = 'ep.base_salary >= :base_salary';
            $queryParams[':base_salary'] = [$base_salary, \PDO::PARAM_INT];
        }

        $late_penalties = $parameters['late_penalties'] ?? '';
        if (!empty($late_penalties)) {
            $queryConditions[] = 'ep.late_penalties >= :late_penalties';
            $queryParams[':late_penalties'] = [$late_penalties, \PDO::PARAM_INT];
        }

        $net_salary = $parameters['net_salary'] ?? '';
        if (!empty($net_salary)) {
            $queryConditions[] = 'ep.net_salary >= :net_salary';
            $queryParams[':net_salary'] = [$net_salary, \PDO::PARAM_INT];
        }

        $paymentDateFrom = $parameters['payment_date_from'] ?? '';
        $paymentDateUntil = $parameters['payment_date_until'] ?? '';

        if (!empty($paymentDateFrom) && !empty($paymentDateUntil)) {
            $queryConditions[] = 'ep.payment_date BETWEEN ' . $paymentDateFrom . ' AND ' . $paymentDateUntil;
        }

        if (empty($queryConditions)) {
            $querySql = $structure;
        } else {
            $querySql = $structure . 'AND ' . $queryConditions[0];
            if (count($queryConditions) > 1) {
                $querySql = $structure . 'AND ' . implode(' AND ', $queryConditions);
            }
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $querySql");
        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value[0], is_numeric($value[1]) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT 
            ep.id,
            u.name, u.email, 
            cod.name AS department,
            cer.name AS role,
            ep.payroll_month, ep.base_salary, ep.total_overtime, 
            ep.late_penalties, ep.net_salary, ep.status, ep.payment_date,
            ep.remarks, ep.created_at, ep.updated_at 
            $querySql
            ORDER BY ep.created_at $orderBy
            LIMIT :limit OFFSET :offset
        ");

        foreach ($queryParams as $key => $value) {
            $statement->bindValue($key, $value[0], $value[1]);
        }
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];
        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function isThereAnyEmployeePayroll(EmployeePayrolls $employeePayrolls): bool
    {
        $statement = $this->connection->prepare("SELECT employee_id FROM employee_payrolls WHERE employee_id = ? AND payroll_month = ?");
        $statement->execute([$employeePayrolls->employee_id, $employeePayrolls->payroll_month]);

        return $statement->fetch() !== false;
    }

    public function findEmployeeTotalOvertime(EmployeePayrolls $employeePayroll): int
    {
        $statement = $this->connection->prepare("SELECT SUM(total_payment) AS total FROM employee_overtime WHERE employee_id = ? AND overtime_date BETWEEN ? AND ?");

        $nextMonth = new DateTime($employeePayroll->payroll_month);
        $nextMonth->modify('+1 month');

        $statement->execute([$employeePayroll->employee_id, $employeePayroll->payroll_month, $nextMonth->format('Y-m-d')]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function findEmployeeLatePenalties(EmployeePayrolls $employeePayroll): int
    {
        $statement = $this->connection->prepare("SELECT SUM(late_penalty) AS total FROM employee_attendance WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?");

        $nextMonth = new DateTime($employeePayroll->payroll_month);
        $nextMonth->modify('+1 month');

        $statement->execute([$employeePayroll->employee_id, $employeePayroll->payroll_month, $nextMonth->format('Y-m-d')]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function saveEmployeePayroll(EmployeePayrolls $employeePayroll): EmployeePayrolls
    {
        $statement = $this->connection->prepare("INSERT INTO employee_payrolls 
                (employee_id, payroll_month, base_salary, total_overtime, late_penalties, status, payment_date, remarks) 
            VALUES(?,?,?,?,?,?,?,?)");
        $statement->execute([
            $employeePayroll->employee_id,
            $employeePayroll->payroll_month,
            $employeePayroll->base_salary,
            $employeePayroll->total_overtime,
            $employeePayroll->late_penalties,
            $employeePayroll->status,
            $employeePayroll->payment_date,
            $employeePayroll->remarks
        ]);

        return $employeePayroll;
    }

    public function editEmployeePayroll(EmployeePayrolls $employeePayroll): EmployeePayrolls
    {
        $statement = $this->connection->prepare("UPDATE employee_payrolls SET payroll_month = ?, base_salary = ?, total_overtime = ?, late_penalties = ?, status = ?, payment_date = ?, remarks = ? WHERE id = ?");
        $statement->execute([
            $employeePayroll->payroll_month,
            $employeePayroll->base_salary,
            $employeePayroll->total_overtime,
            $employeePayroll->late_penalties,
            $employeePayroll->status,
            $employeePayroll->payment_date,
            $employeePayroll->remarks,
            $employeePayroll->id
        ]);

        return $employeePayroll;
    }

    public function destroyEmployeePayroll(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_payrolls WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestEmployeePayrollDestroy(string $employeeId): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_payrolls WHERE employee_id = ?");
        $statement->execute([$employeeId]);
    }

    public function forTestEmployeePayrollGetId(EmployeePayrolls $employeePayroll): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM employee_payrolls WHERE employee_id = ? AND payroll_month = ? AND base_salary = ? AND status = ? AND payment_date = ? AND remarks = ?");
        $statement->execute([
            $employeePayroll->employee_id,
            $employeePayroll->payroll_month,
            $employeePayroll->base_salary,
            $employeePayroll->status,
            $employeePayroll->payment_date,
            $employeePayroll->remarks
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllEmployeeProjectAssignments(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $conditionQuery = "FROM employee_project_assignments AS epa
            JOIN company_employee_projects AS cep ON epa.project_id = cep.id
            JOIN user AS u ON epa.employee_id = u.email
            WHERE (cep.name LIKE :keyword OR
                epa.employee_id LIKE :keyword OR       
                u.name LIKE :keyword OR     
                cep.description LIKE :keyword) ";

        if (isset($parameters['role_in_project']) && $parameters['role_in_project'] != null) {
            $conditionQuery .= "AND role_in_project = :role_in_project";
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total
            $conditionQuery
        ");
        $countStatement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);

        if (isset($parameters['role_in_project']) && $parameters['role_in_project'] != null) {
            $countStatement->bindValue(':role_in_project', $parameters['role_in_project'], \PDO::PARAM_STR);
        }

        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT 
                epa.id, u.name, u.email, cep.name as project_name, 
            IF(LENGTH(cep.description > 35), CONCAT(LEFT(cep.description, 35), '...'), cep.description) AS description, 
                epa.role_in_project,
                epa.assigned_date
            $conditionQuery
            ORDER BY epa.id $orderBy
            LIMIT :limit OFFSET :offset
        ");

        $statement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function isThereAnyEmployeeProjectAssignments(EmployeeProjectAssigments $employeeProjectAssigments): ?bool
    {
        $statement = $this->connection->prepare("SELECT employee_id FROM employee_project_assignments WHERE (employee_id = ? AND project_id = ? AND role_in_project = ?)");
        $statement->execute([
            $employeeProjectAssigments->employee_id,
            $employeeProjectAssigments->project_id,
            $employeeProjectAssigments->role_in_project
        ]);

        return $statement->fetch() !== false;
    }

    public function saveEmployeeProjectAssignment(EmployeeProjectAssigments $employeeProjectAssigments): void
    {
        $statement = $this->connection->prepare("INSERT INTO employee_project_assignments (employee_id, project_id, role_in_project, assigned_date) VALUES (?,?,?,?)");
        $statement->execute([
            $employeeProjectAssigments->employee_id,
            $employeeProjectAssigments->project_id,
            $employeeProjectAssigments->role_in_project,
            $employeeProjectAssigments->assigned_date
        ]);
    }

    public function editEmployeePojectAssignment(EmployeeProjectAssigments $employeeProjectAssigments): void
    {
        $statement = $this->connection->prepare("UPDATE employee_project_assignments SET project_id = ?, role_in_project = ?, assigned_date = ? WHERE id = ?");
        $statement->execute([
            $employeeProjectAssigments->project_id,
            $employeeProjectAssigments->role_in_project,
            $employeeProjectAssigments->assigned_date,
            $employeeProjectAssigments->id
        ]);
    }

    public function destroyEmployeeProjectAssignment(string|int $id)
    {
        $statement = $this->connection->prepare("DELETE FROM employee_project_assignments WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestEmployeeProjectAssignmentDestroy(string $employeeId): void
    {
        $statement = $this->connection->prepare("DELETE FROM employee_project_assignments WHERE employee_id = ?");
        $statement->execute([$employeeId]);
    }

    public function forTestEmployeeProjectAssignmentGetId(EmployeeProjectAssigments $employeeProjectAssigment): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM employee_project_assignments WHERE employee_id = ? AND project_id = ? AND role_in_project = ? AND assigned_date = ?");
        $statement->execute([
            $employeeProjectAssigment->employee_id,
            $employeeProjectAssigment->project_id,
            $employeeProjectAssigment->role_in_project,
            $employeeProjectAssigment->assigned_date
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllCompanyEmployeeProjectsNoFilter(): array
    {
        $statement = $this->connection->prepare("SELECT id, name FROM company_employee_projects");
        $statement->execute();

        $results = [];

        try {
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[$row['id']] = $row['name'];
            }
        } finally {
            $statement->closeCursor();
        }

        return $results;
    }

    public function findAllCompanyEmployeeProjects(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $queryConditions = "FROM company_employee_projects 
            WHERE name LIKE :keyword OR 
                description LIKE :keyword";
        if ($parameters['status'] != '') {
            $queryConditions = "AND status = :status";
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $queryConditions");
        $countStatement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        if ($parameters['status'] != '') {
            $countStatement->bindValue(':status', $parameters['status'], \PDO::PARAM_STR);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, name, description, start_date, end_date, status, created_at, updated_at
            $queryConditions
            ORDER BY created_at $orderBy
            LIMIT :limit OFFSET :offset
        ");
        $statement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        if ($parameters['status'] != '') {
            $statement->bindValue(':status', $parameters['status'], \PDO::PARAM_STR);
        }
        $statement->bindValue('limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();

            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function isThereAnyCompanyEmployeeProject(CompanyEmployeeProjects $companyEmployeeProjects): bool
    {
        $statement = $this->connection->prepare("SELECT name FROM company_employee_projects WHERE name = ? AND description = ?");
        $statement->execute([$companyEmployeeProjects->name, $companyEmployeeProjects->description]);

        return $statement->fetch() !== false;
    }

    public function saveCompanyEmployeeProject(CompanyEmployeeProjects $companyEmployeeProjects): void
    {
        $statement = $this->connection->prepare("INSERT INTO company_employee_projects (name, description, start_date, end_date, status) VALUES (?,?,?,?,?)");
        $statement->execute([
            $companyEmployeeProjects->name,
            $companyEmployeeProjects->description,
            $companyEmployeeProjects->start_date,
            $companyEmployeeProjects->end_date,
            $companyEmployeeProjects->status
        ]);
    }

    public function editCompanyEmployeeProject(CompanyEmployeeProjects $companyEmployeeProjects): void
    {
        $statement = $this->connection->prepare("UPDATE company_employee_projects SET name = ?, description = ?, start_date = ?, end_date = ?, status = ? WHERE id = ?");
        $statement->execute([
            $companyEmployeeProjects->name,
            $companyEmployeeProjects->description,
            $companyEmployeeProjects->start_date,
            $companyEmployeeProjects->end_date,
            $companyEmployeeProjects->status,
            $companyEmployeeProjects->id
        ]);
    }

    public function destroyCompanyEmployeeProject(string $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_employee_projects WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestCompanyEmployeeProjectDestroy(string $name): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_employee_projects WHERE name = ?");
        $statement->execute([$name]);
    }

    public function forTestCompanyEmployeeProjectGetId(CompanyEmployeeProjects $companyEmployeeProject): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM company_employee_projects WHERE name = ? AND description = ? AND start_date = ? AND end_date = ?");
        $statement->execute([
            $companyEmployeeProject->name,
            $companyEmployeeProject->description,
            $companyEmployeeProject->start_date,
            $companyEmployeeProject->end_date
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllCompanyEmployeeRoles(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $queryConditions = "FROM company_employee_roles WHERE name LIKE :keyword";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $queryConditions");
        $countStatement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, name, created_at, updated_at
            $queryConditions
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset
        ");
        $statement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function isThereAnyCompanyEmployeeRole(CompanyEmployeeRoles $companyEmployeeRole): bool
    {
        $statement = $this->connection->prepare("SELECT name FROM company_employee_roles WHERE name = ?");
        $statement->execute([$companyEmployeeRole->name]);

        return $statement->fetch() !== false;
    }

    public function saveCompanyEmployeeRole(CompanyEmployeeRoles $companyEmployeeRole): CompanyEmployeeRoles
    {
        $statement = $this->connection->prepare("INSERT INTO company_employee_roles( name) VALUES (?)");
        $statement->execute([$companyEmployeeRole->name]);

        return $companyEmployeeRole;
    }

    public function editCompanyEmployeeRole(CompanyEmployeeRoles $companyEmployeeRole): CompanyEmployeeRoles
    {
        $statement = $this->connection->prepare("UPDATE company_employee_roles SET name = ? WHERE id = ?");
        $statement->execute([$companyEmployeeRole->name, $companyEmployeeRole->id]);

        return $companyEmployeeRole;
    }

    public function destroyCompanyEmployeeRole(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_employee_roles WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestCompanyEmployeeRoleDestroy(string $name): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_employee_roles WHERE name = ?");
        $statement->execute([$name]);
    }

    public function forTestCompanyEmployeeRoleGetId(CompanyEmployeeRoles $companyEmployeeRole): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM company_employee_roles WHERE name = ?");
        $statement->execute([
            $companyEmployeeRole->name
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllCompanyOfficeDepartments(array $parameters): array
    {

        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $queryConditions = "FROM company_office_departments WHERE (name LIKE :keyword OR description LIKE :keyword)";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $queryConditions");
        $countStatement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT id, name, description, created_at, updated_at 
            $queryConditions
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset
        ");
        $statement->bindValue(':keyword', "%{$parameters['keyword']}%", \PDO::PARAM_STR);
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'results' => $results,
            'total-page' => $totalPage
        ];
    }

    public function isThereAnyCompanyOfficeDepartment(CompanyOfficeDepartements $companyOfficeDepartement): bool
    {
        $statement = $this->connection->prepare("SELECT name FROM company_office_departments WHERE name = ?");
        $statement->execute([$companyOfficeDepartement->name]);

        return $statement->fetch() !== false;
    }

    public function saveCompanyOfficeDepartment(CompanyOfficeDepartements $companyOfficeDepartement): ?CompanyOfficeDepartements
    {
        $statement = $this->connection->prepare("INSERT INTO company_office_departments (name, description) VALUES (?,?)");
        $statement->execute([$companyOfficeDepartement->name, $companyOfficeDepartement->description]);

        return $companyOfficeDepartement;
    }

    public function editCompanyOfficeDepartment(CompanyOfficeDepartements $companyOfficeDepartement): ?CompanyOfficeDepartements
    {
        $statement = $this->connection->prepare("UPDATE company_office_departments SET name = ?, description = ? WHERE id = ?");
        $statement->execute([$companyOfficeDepartement->name, $companyOfficeDepartement->description, $companyOfficeDepartement->id]);

        return $companyOfficeDepartement;
    }

    public function destroyCompanyOfficeDepartment(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_office_departments WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestCompanyOfficeDepartmentDestroy(string $name): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_office_departments WHERE name = ?");
        $statement->execute([$name]);
    }

    public function forTestCompanyOfficeDepartmentGetId(CompanyOfficeDepartements $companyOfficeDepartement): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM company_office_departments WHERE name = ?");
        $statement->execute([
            $companyOfficeDepartement->name
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllCompanyOfficeFinancialTransactions(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $conditionQuery = ['description LIKE :keyword'];

        $queryParams = [':keyword' => ["%{$parameters['keyword']}%", \PDO::PARAM_STR]];

        $type = isset($parameters['type']) ? $parameters['type'] : '';
        if ($type != '') {
            $queryParams[':type'] = [$type, \PDO::PARAM_STR];
            $conditionQuery[] = 'type = :type';
        }

        $amount = $parameters['amount'] ?? '';
        if ($amount != '') {
            $queryParams[':amount'] = [$amount, \PDO::PARAM_INT];
            $conditionQuery[] = 'amount >= :amount';
        }

        $transactionDate = isset($parameters['transaction_date_from']) ? $parameters['transaction_date_from'] : '';
        if ($transactionDate != '') {
            $queryParams[':transaction_date_from'] = [$transactionDate, \PDO::PARAM_STR];
            $queryParams[':transaction_date_until'] = [$parameters['transaction_date_until'], \PDO::PARAM_STR];
            $conditionQuery[] = "transaction_date BETWEEN :transaction_date_from ON :transaction_date_until";
        }

        if (count($conditionQuery) > 1) {
            $queryConditions = implode(' AND ', $conditionQuery);
        } else {
            $queryConditions = $conditionQuery[0];
        }

        $sql = "FROM company_office_financial_transactions WHERE $queryConditions";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $sql");
        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value[0], $value[1]);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT 
                id, type, amount, transaction_date, 
                IF(LENGTH(description) > 35, CONCAT(LEFT(description, 35), '...'), description) AS description, 
                created_at, updated_at
            $sql
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset");
        foreach ($queryParams as $key => $value) {
            $statement->bindValue($key, $value[0], $value[1]);
        }
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'total-page' => $totalPage,
            'results' => $results
        ];
    }

    public function isThereAnyCompanyOfficeFinancialTransaction(CompanyOfficeFinancialTransactions $companyOfficeFinancialTransaction): bool
    {
        $statement = $this->connection->prepare("SELECT type, amount, transaction_date, description 
            FROM company_office_financial_transactions 
            WHERE type = ? AND amount = ? AND transaction_date = ? AND description = ?");
        $statement->execute([
            $companyOfficeFinancialTransaction->type,
            $companyOfficeFinancialTransaction->amount,
            $companyOfficeFinancialTransaction->transaction_date,
            $companyOfficeFinancialTransaction->description
        ]);

        return $statement->fetch() !== false;
    }

    public function saveCompanyOfficeFinancialTransaction(CompanyOfficeFinancialTransactions $companyOfficeFinancialTransaction): ?CompanyOfficeFinancialTransactions
    {
        $statement = $this->connection->prepare("INSERT INTO company_office_financial_transactions(type, amount, transaction_date, description) VALUES (?,?,?,?)");
        $statement->execute([
            $companyOfficeFinancialTransaction->type,
            $companyOfficeFinancialTransaction->amount,
            $companyOfficeFinancialTransaction->transaction_date,
            $companyOfficeFinancialTransaction->description
        ]);

        return $companyOfficeFinancialTransaction;
    }

    public function editCompanyOfficeFinancialTransaction(CompanyOfficeFinancialTransactions $companyOfficeFinancialTransaction): ?CompanyOfficeFinancialTransactions
    {
        $statement = $this->connection->prepare("UPDATE company_office_financial_transactions SET type = ?, amount = ?, transaction_date = ?, description = ? WHERE id = ?");
        $statement->execute([
            $companyOfficeFinancialTransaction->type,
            $companyOfficeFinancialTransaction->amount,
            $companyOfficeFinancialTransaction->transaction_date,
            $companyOfficeFinancialTransaction->description,
            $companyOfficeFinancialTransaction->id
        ]);

        return $companyOfficeFinancialTransaction;
    }

    public function destroyCompanyOfficeFinancialTransaction(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_office_financial_transactions WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestCompanyOfficeFinancialTransactionDestroy(CompanyOfficeFinancialTransactions $companyOfficeFinancialTransaction): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_office_financial_transactions WHERE type = ? AND amount = ? AND transaction_date = ? AND description = ?");
        $statement->execute([
            $companyOfficeFinancialTransaction->type,
            $companyOfficeFinancialTransaction->amount,
            $companyOfficeFinancialTransaction->transaction_date,
            $companyOfficeFinancialTransaction->description
        ]);
    }

    public function forTestCompanyOfficeFinancialTransactionGetId(CompanyOfficeFinancialTransactions $companyOfficeFinancialTransaction): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM company_office_financial_transactions WHERE type = ? AND amount = ? AND transaction_date = ? AND description = ?");
        $statement->execute([
            $companyOfficeFinancialTransaction->type,
            $companyOfficeFinancialTransaction->amount,
            $companyOfficeFinancialTransaction->transaction_date,
            $companyOfficeFinancialTransaction->description
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }

    public function findAllCompanyOfficeRecruitments(array $parameters): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $queryParams = [':keyword' => ["%{$parameters['keyword']}%", \PDO::PARAM_STR]];
        $querySql = ['(cor.job_title LIKE :keyword OR cod.name LIKE :keyword OR cor.job_description LIKE :keyword)'];

        $departmentName = $parameters['department'] ?? '';
        if ($departmentName != '') {
            $queryParams[':department'] = [$departmentName, \PDO::PARAM_STR];
            $querySql[] = 'cod.name = :department';
        }

        $status = $parameters['status'] ?? '';
        if ($status != '') {
            $queryParams[':status'] = [$status, \PDO::PARAM_STR];
            $querySql[] = 'cor.status = :status';
        }

        if (count($querySql) > 1) {
            $queryConditions = implode(' AND ', $querySql);
        } else {
            $queryConditions = $querySql[0];
        }

        $sql = "FROM company_office_recruitments AS cor
            JOIN company_office_departments AS cod ON cor.department_id = cod.id
            WHERE $queryConditions";

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $sql");
        foreach ($queryParams as $key => $value) {
            $countStatement->bindValue($key, $value[0], $value[1]);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT 
            cor.id, cor.job_title, cod.name AS department_name, 
            cor.job_description, cor.status, cor.created_at, cor.updated_at
            $sql
            ORDER BY cor.id $orderBy
            LIMIT :limit OFFSET :offset");
        foreach ($queryParams as $key => $value) {
            $statement->bindValue($key, $value[0], $value[1]);
        }
        $statement->bindValue(':limit', $itemsPerPage, \PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, \PDO::PARAM_INT);

        $results = [];

        try {
            $statement->execute();
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
        } finally {
            $statement->closeCursor();
        }

        return [
            'results' => $results,
            'total-page' => $totalPage
        ];
    }

    public function isThereAnyCompanyOfficeRecruitment(CompanyOfficeRecruitments $companyOfficeRecruitment): bool
    {
        $statement = $this->connection->prepare("SELECT department_id, job_description FROM company_office_recruitments WHERE department_id = ? AND job_description = ?");
        $statement->execute([
            $companyOfficeRecruitment->department_id,
            $companyOfficeRecruitment->job_description
        ]);

        return $statement->fetch() !== false;
    }

    public function saveCompanyOfficeRecruitment(CompanyOfficeRecruitments $companyOfficeRecruitment): ?CompanyOfficeRecruitments
    {
        $statement = $this->connection->prepare("INSERT INTO company_office_recruitments(job_title, department_id, job_description, status) VALUES (?,?,?,?)");
        $statement->execute([
            $companyOfficeRecruitment->job_title,
            $companyOfficeRecruitment->department_id,
            $companyOfficeRecruitment->job_description,
            $companyOfficeRecruitment->status
        ]);

        return $companyOfficeRecruitment;
    }

    public function editCompanyOfficeRecruitment(CompanyOfficeRecruitments $companyOfficeRecruitment): ?CompanyOfficeRecruitments
    {
        $statement = $this->connection->prepare("UPDATE company_office_recruitments SET job_title = ?, department_id = ?, job_description = ?, status = ? WHERE id = ?");
        $statement->execute([
            $companyOfficeRecruitment->job_title,
            $companyOfficeRecruitment->department_id,
            $companyOfficeRecruitment->job_description,
            $companyOfficeRecruitment->status,
            $companyOfficeRecruitment->id
        ]);

        return $companyOfficeRecruitment;
    }

    public function destroyCompanyOfficeRecruitment(string|int $id): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_office_recruitments WHERE id = ?");
        $statement->execute([$id]);
    }

    public function forTestCompanyOfficeRecruitmentDestroy(string $jobTitle): void
    {
        $statement = $this->connection->prepare("DELETE FROM company_office_recruitments WHERE job_title = ?");
        $statement->execute([$jobTitle]);
    }

    public function forTestCompanyOfficeRecruitmentGetId(CompanyOfficeRecruitments $companyOfficeRecruitment): string|int
    {
        $statement = $this->connection->prepare("SELECT id FROM company_office_recruitments WHERE job_title = ? AND department_id = ? AND job_description = ? AND status = ?");
        $statement->execute([
            $companyOfficeRecruitment->job_title,
            $companyOfficeRecruitment->department_id,
            $companyOfficeRecruitment->job_description,
            $companyOfficeRecruitment->status
        ]);

        return $statement->fetch(\PDO::FETCH_ASSOC)['id'];
    }
}
