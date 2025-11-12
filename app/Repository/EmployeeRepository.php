<?php

namespace ilhamrhmtkbr\App\Repository;

use ilhamrhmtkbr\App\Models\EmployeeAttendance;
use ilhamrhmtkbr\App\Models\EmployeeAttendanceRules;

class EmployeeRepository
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function findEmployee(string $userId): ?array
    {
        $statement = $this->connection->prepare("SELECT u.name, u.email, cor.name AS role, cod.name AS department, e.salary, e.hire_date, e.status, e.created_at, e.updated_at 
            FROM employees AS e
            JOIN user AS u ON e.user_id = u.email
            JOIN company_employee_roles AS cor ON e.role_id = cor.id
            JOIN company_office_departments AS cod ON e.department_id = cod.id
            WHERE e.user_id = :user_id
        ");
        $statement->bindValue(':user_id', $userId, \PDO::PARAM_STR);
        try {
            $statement->execute();

            if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                return $row;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function isEmployee(string $userId): bool
    {
        $statement = $this->connection->prepare("SELECT user_id FROM employees WHERE user_id = ?");
        $statement->execute([$userId]);

        return $statement->fetch() !== false;
    }

    public function findEmployeeAttendance(string $userId, string $attendanceDate): ?EmployeeAttendance
    {
        $statement = $this->connection->prepare("SELECT attendance_date, check_in_time, check_out_time, status FROM employee_attendance WHERE employee_id = ? AND attendance_date = ?");
        $statement->execute([$userId, $attendanceDate]);

        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $employeeAttendance = new EmployeeAttendance();
            $employeeAttendance->attendance_date = $row['attendance_date'];
            $employeeAttendance->check_in_time = $row['check_in_time'];
            $employeeAttendance->check_out_time = $row['check_out_time'];
            $employeeAttendance->status = $row['status'];

            return $employeeAttendance;
        } else {
            return null;
        }
    }

    public function saveEmployeeAttendance(EmployeeAttendance $employeeAttendance): void
    {
        $statement = $this->connection->prepare("INSERT INTO employee_attendance (employee_id, attendance_date, check_in_time, status, late_penalty) VALUES (?,?,?,?,?)");
        $statement->execute([
            $employeeAttendance->employee_id,
            $employeeAttendance->attendance_date,
            $employeeAttendance->check_in_time,
            $employeeAttendance->status,
            $employeeAttendance->late_penalty
        ]);
    }

    public function editEmployeeAttendance(EmployeeAttendance $employeeAttendance): void
    {
        $statement = $this->connection->prepare("UPDATE employee_attendance SET check_out_time = ?, status = ?, late_penalty = ? WHERE employee_id = ? AND attendance_date = ?");
        $statement->execute([
            $employeeAttendance->check_out_time,
            $employeeAttendance->status,
            $employeeAttendance->late_penalty,
            $employeeAttendance->employee_id,
            $employeeAttendance->attendance_date
        ]);
    }

    public function findAttendanceRule(string|int $id): ?EmployeeAttendanceRules
    {
        $statement = $this->connection->prepare("SELECT start_time, end_time, late_threshold, penalty_for_late FROM employee_attendance_rules WHERE id = ?");
        $statement->execute([$id]);

        if ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $employeeAttendanceRule = new EmployeeAttendanceRules();
            $employeeAttendanceRule->start_time = $row['start_time'];
            $employeeAttendanceRule->end_time = $row['end_time'];
            $employeeAttendanceRule->late_threshold = $row['late_threshold'];
            $employeeAttendanceRule->penalty_for_late = $row['penalty_for_late'];

            return $employeeAttendanceRule;
        } else {
            return null;
        }
    }

    public function findAttendance(array $parameters, string $employeeId): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $conditions = "FROM employee_attendance WHERE employee_id = '$employeeId'";
        $queryParams = [];
        if (isset($parameters['status']) && $parameters['status'] != '') {
            $queryParams[] = [':status', $parameters['status'], \PDO::PARAM_STR];
            $conditions .= ' AND status = :status';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $conditions");
        if (isset($parameters['status']) && $parameters['status'] != '') {
            $countStatement->bindValue($queryParams[0][0], $queryParams[0][1], $queryParams[0][2]);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT attendance_date, check_in_time, check_out_time, status, late_penalty, created_at, updated_at 
            $conditions
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset");
        if (isset($parameters['status']) && $parameters['status'] != '') {
            $statement->bindValue($queryParams[0][0], $queryParams[0][1], $queryParams[0][2]);
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

    public function findContracts(array $parameters, string $employeeId): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $conditions = "FROM employee_contracts WHERE employee_id = '$employeeId'";
        $queryParams = [];

        if (isset($parameters['contract_start_date_from']) && isset($parameters['contract_start_date_until'])) {
            $queryParams[':contract_start_date_from'] = [$parameters['contract_start_date_from'], \PDO::PARAM_STR];
            $queryParams[':contract_start_date_until'] = [$parameters['contract_start_date_until'], \PDO::PARAM_STR];
            $conditions .= ' AND contract_start_date BETWEEN :contract_start_date_from AND contract_start_date_until';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $conditions");
        if (isset($parameters['contract_start_date_from']) && isset($parameters['contract_start_date_until'])) {
            foreach ($queryParams as $key => $value) {
                $countStatement->bindValue($key, $value[0], $value[1]);
            }
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT contract_start_date, contract_end_date, salary, contract_terms, created_at, updated_at 
            $conditions
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset");
        if (isset($parameters['contract_start_date_from']) && isset($parameters['contract_start_date_until'])) {
            foreach ($queryParams as $key => $value) {
                $countStatement->bindValue($key, $value[0], $value[1]);
            }
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

    public function findLeaveRequests(array $parameters, string $employeeId): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $conditions = "FROM employee_leave_requests WHERE employee_id = '$employeeId'";
        $queryParams = [];

        if ($parameters['status'] != '') {
            $queryParams[':status'] = [$parameters['status'], \PDO::PARAM_STR];
            $conditions .= ' AND status = :status';
        }

        if (isset($parameters['leave_type'])) {
            $queryParams[':leave_type'] = [$parameters['leave_type'], \PDO::PARAM_STR];
            $conditions .= ' AND leave_type = :leave_type';
        }

        if (isset($parameters['start_date_from']) && isset($parameters['start_date_until'])) {
            $queryParams[':start_date_from'] = [$parameters['start_date_from'], \PDO::PARAM_STR];
            $queryParams[':start_date_until'] = [$parameters['start_date_until'], \PDO::PARAM_STR];
            $conditions .= ' AND start_date BETWEEN :start_date_from AND start_date_until';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $conditions");
        if (count($queryParams) > 1) {
            foreach ($queryParams as $key => $value) {
                $countStatement->bindValue($key, $value[0], $value[1]);
            }
        } elseif (count($queryParams) == 1) {
            $key = array_key_first($queryParams);
            $countStatement->bindValue($key, $queryParams[$key][0], $queryParams[$key][1]);
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT leave_type, start_date, end_date, status, remarks, created_at, updated_at 
            $conditions
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset");
        if (count($queryParams) > 1) {
            foreach ($queryParams as $key => $value) {
                $statement->bindValue($key, $value[0], $value[1]);
            }
        } elseif (count($queryParams) == 1) {
            $key = array_key_first($queryParams);
            $statement->bindValue($key, $queryParams[$key][0], $queryParams[$key][1]);
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

    public function findOvertime(array $parameters, string $employeeId): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $conditions = "FROM employee_overtime WHERE employee_id = '$employeeId'";
        $queryParams = [];

        if (isset($parameters['total_hours'])) {
            $queryParams[':total_hours'] = [$parameters['total_hours'], \PDO::PARAM_STR];
            $conditions .= ' AND total_hours >= :total_hours';
        }

        if (isset($parameters['overtime_rate'])) {
            $queryParams[':overtime_rate'] = [$parameters['overtime_rate'], \PDO::PARAM_STR];
            $conditions .= ' AND overtime_rate >= :overtime_rate';
        }

        if (isset($parameters['total_payments'])) {
            $queryParams[':total_payments'] = [$parameters['total_payments'], \PDO::PARAM_STR];
            $conditions .= ' AND total_payments >= :total_payments';
        }

        if (isset($parameters['overtime_date_from']) && isset($parameters['overtime_date_until'])) {
            $queryParams[':overtime_date_from'] = [$parameters['overtime_date_from'], \PDO::PARAM_STR];
            $queryParams[':overtime_date_until'] = [$parameters['overtime_date_until'], \PDO::PARAM_STR];
            $conditions .= ' AND overtime_date BETWEEN :overtime_date_from AND overtime_date_until';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $conditions");
        if (count($queryParams) >= 1) {
            foreach ($queryParams as $key => $value) {
                $countStatement->bindValue($key, $value[0], $value[1]);
            }
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT overtime_date, start_time, end_time, total_hours, overtime_rate, total_payment, remarks, created_at, updated_at 
            $conditions
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset");
        if (count($queryParams) >= 1) {
            foreach ($queryParams as $key => $value) {
                $statement->bindValue($key, $value[0], $value[1]);
            }
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

    public function findPayrolls(array $parameters, string $employeeId): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $conditions = "FROM employee_payrolls WHERE employee_id = '$employeeId'";
        $queryParams = [];

        if (isset($parameters['base_salary'])) {
            $queryParams[':base_salary'] = [$parameters['base_salary'], \PDO::PARAM_STR];
            $conditions .= ' AND base_salary >= :base_salary';
        }

        if (isset($parameters['total_overtime'])) {
            $queryParams[':total_overtime'] = [$parameters['total_overtime'], \PDO::PARAM_STR];
            $conditions .= ' AND total_overtime >= :total_overtime';
        }

        if (isset($parameters['net_salary'])) {
            $queryParams[':net_salary'] = [$parameters['net_salary'], \PDO::PARAM_STR];
            $conditions .= ' AND net_salary >= :net_salary';
        }

        if (isset($parameters['status'])) {
            $queryParams[':status'] = [$parameters['status'], \PDO::PARAM_STR];
            $conditions .= ' AND status >= :status';
        }

        if (isset($parameters['payroll_month_from']) && isset($parameters['payroll_month_until'])) {
            $queryParams[':payroll_month_from'] = [$parameters['payroll_month_from'], \PDO::PARAM_STR];
            $queryParams[':payroll_month_until'] = [$parameters['payroll_month_until'], \PDO::PARAM_STR];
            $conditions .= ' AND payroll_month BETWEEN :payroll_month_from AND payroll_month_until';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $conditions");
        if (count($queryParams) >= 1) {
            foreach ($queryParams as $key => $value) {
                $countStatement->bindValue($key, $value[0], $value[1]);
            }
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT payroll_month, base_salary, total_overtime, late_penalties, status, payment_date, remarks, created_at, updated_at
            $conditions
            ORDER BY id $orderBy
            LIMIT :limit OFFSET :offset");
        if (count($queryParams) >= 1) {
            foreach ($queryParams as $key => $value) {
                $statement->bindValue($key, $value[0], $value[1]);
            }
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

    public function findProjectAssignments(array $parameters, string $employeeId): array
    {
        $itemsPerPage = 5;
        $offset = ($parameters['page'] - 1) * $itemsPerPage;

        $orderBy = strtoupper($parameters['orderBy']);
        if (!in_array($orderBy, ['ASC', 'DESC'])) {
            $orderBy = 'ASC';
        }

        $conditions = "FROM employee_project_assignments AS epa
            JOIN company_employee_projects AS cep ON epa.project_id = cep.id WHERE employee_id = '$employeeId'";
        $queryParams = [];

        if ($parameters['status'] != '') {
            $queryParams[':status'] = [$parameters['status'], \PDO::PARAM_STR];
            $conditions .= ' AND cep.status = :status';
        }

        if (isset($parameters['assigned_date_from']) && isset($parameters['assigned_date_until'])) {
            $queryParams[':assigned_date_from'] = [$parameters['assigned_date_from'], \PDO::PARAM_STR];
            $queryParams[':assigned_date_until'] = [$parameters['assigned_date_until'], \PDO::PARAM_STR];
            $conditions .= ' AND assigned_date BETWEEN :assigned_date_from AND assigned_date_until';
        }

        $countStatement = $this->connection->prepare("SELECT COUNT(*) AS total $conditions");
        if (count($queryParams) >= 1) {
            foreach ($queryParams as $key => $value) {
                $countStatement->bindValue($key, $value[0], $value[1]);
            }
        }
        $countStatement->execute();

        $totalRows = $countStatement->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        $totalPage = ceil($totalRows / $itemsPerPage);

        $statement = $this->connection->prepare("SELECT cep.name, cep.description, cep.start_date, cep.end_date, cep.status, epa.role_in_project, epa.assigned_date 
            $conditions
            ORDER BY epa.id $orderBy
            LIMIT :limit OFFSET :offset");
        if (count($queryParams) >= 1) {
            foreach ($queryParams as $key => $value) {
                $statement->bindValue($key, $value[0], $value[1]);
            }
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
}
