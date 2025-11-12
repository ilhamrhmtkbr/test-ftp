<?php

namespace ilhamrhmtkbr\App\Service;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Config\Email;
use ilhamrhmtkbr\App\Models\Candidates;
use ilhamrhmtkbr\App\Models\CompanyEmployeeProjects;
use ilhamrhmtkbr\App\Models\CompanyEmployeeRoles;
use ilhamrhmtkbr\App\Models\CompanyOfficeDepartements;
use ilhamrhmtkbr\App\Models\CompanyOfficeFinancialTransactions;
use ilhamrhmtkbr\App\Models\CompanyOfficeRecruitments;
use ilhamrhmtkbr\App\Models\EmployeeAttendanceRules;
use ilhamrhmtkbr\App\Models\EmployeeContracts;
use ilhamrhmtkbr\App\Models\EmployeeLeaveRequests;
use ilhamrhmtkbr\App\Models\EmployeeOvertime;
use ilhamrhmtkbr\App\Models\EmployeePayrolls;
use ilhamrhmtkbr\App\Models\EmployeeProjectAssigments;
use ilhamrhmtkbr\App\Models\Employees;
use ilhamrhmtkbr\App\Exceptions\ValidationException;
use ilhamrhmtkbr\App\Facades\Request;
use ilhamrhmtkbr\App\Facades\Validation;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Repository\HrRepository;

class HrService
{

    private Validation $validation;
    private HrRepository $hrRepository;

    public function __construct()
    {
        $connection = Database::getConnection();
        $this->validation = new Validation();
        $this->hrRepository = new HrRepository($connection);
    }

    public function sendEmailToCandidateForInterview(Request $request): ?Candidates
    {
        $this->validateSendEmailToCandidateForInterview($request);

        try {
            Database::beginTransaction();

            $candidate = new Candidates();
            $candidate->user_id = $request->email;
            $candidate->status = 'interviewed';

            $this->hrRepository->editStatusCandidate($candidate);

            $email = new Email();
            $email->sendEmail($request->email, 'Interview', $request->message);

            Database::commitTransaction();

            return $candidate;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateSendEmailToCandidateForInterview(Request $request): void
    {
        $errors = $this->validation->make([
            'email' => [$request->email, 'required'],
            'message' => [$request->message, 'required|mustString']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function updateCandidateStatus(Request $request): ?Candidates
    {
        $this->validateUpdateCandidateStatus($request);
        try {
            Database::beginTransaction();

            $candidate = new Candidates();
            $candidate->user_id = $request->email;
            $candidate->status = $request->status;
            $this->hrRepository->editStatusCandidate($candidate);

            Database::commitTransaction();

            return $candidate;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUpdateCandidateStatus(Request $request): void
    {
        $errors = $this->validation->make([
            'email' => [$request->email, 'required'],
            'status' => [$request->status, 'required|mustEnum:applied.interviewed.hired.rejected']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function createOrUpdateEmployee(Request $request): Employees
    {
        $this->validateUpdateEmployee($request);

        try {
            Database::beginTransaction();

            $employee = new Employees();
            $employee->user_id = $request->email;
            $employee->role_id = $request->role;
            $employee->department_id = $request->department;
            $employee->salary = $request->salary;
            $employee->hire_date = $request->hire_date;
            $employee->status = $request->status;

            if ($this->hrRepository->isThereAnyEmployee($employee)) {
                $this->hrRepository->editEmployee($employee);
            } else {
                if ($this->hrRepository->isCandidatesHired($employee->user_id)) {
                    $this->hrRepository->saveEmployee($employee);
                } else {
                    throw new ValidationException(['float' => 'Hanya candidate yang berstatus hired yang bisa ditambahkan!']);
                }
            }

            Database::commitTransaction();

            return $employee;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUpdateEmployee(Request $request): void
    {
        $roles = implode('.', array_keys($this->hrRepository->findAllCompanyRoles()));
        $departments = implode('.', array_keys($this->hrRepository->findAllCompanyDepartments()));

        $errors = $this->validation->make([
            'email' => [$request->email, 'mustBeEmail'],
            'role' => [$request->role, "required|mustEnum:$roles"],
            'department' => [$request->department, "required|mustEnum:$departments"],
            'salary' => [$request->salary, 'required|mustNumeric'],
            'hire_date' => [$request->hire_date, 'required|mustString'],
            'status' => [$request->status, 'required|mustEnum:active.inactive'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteEmployee(string $userId): void
    {
        try {
            Database::beginTransaction();

            $this->hrRepository->destroyEmployee($userId);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateEmployeeAttendanceRule(Request $request, bool $isUpdate): void
    {
        $this->validateCreateOrUpdateEmployeeAttendanceRule($request);

        try {
            Database::beginTransaction();

            $employeeAttendanceRule = new EmployeeAttendanceRules();
            $employeeAttendanceRule->id = $request->id;
            $employeeAttendanceRule->rule_name = $request->rule_name;
            $employeeAttendanceRule->start_time = $request->start_time;
            $employeeAttendanceRule->end_time = $request->end_time;
            $employeeAttendanceRule->late_threshold = $request->late_threshold;
            $employeeAttendanceRule->penalty_for_late = $request->penalty_for_late;

            if ($isUpdate) {
                $this->hrRepository->editEmployeeAttendanceRule($employeeAttendanceRule);
            } else {
                if (!$this->hrRepository->isThereAnyEmployeeAttendanceRule($employeeAttendanceRule)) {
                    $this->hrRepository->saveEmployeeAttendanceRule($employeeAttendanceRule);
                } else {
                    throw new ValidationException(['float' => 'Attendance Rule udah ada!']);
                }
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateEmployeeAttendanceRule(Request $request): void
    {
        $errors = $this->validation->make([
            'rule_name' => [$request->rule_name, 'required|mustString'],
            'start_time' => [$request->start_time, 'required'],
            'end_time' => [$request->end_time, 'required'],
            'late_threshold' => [$request->late_threshold, 'required'],
            'penalty_for_late' => [$request->penalty_for_late, 'required']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteEmployeeAttendanceRule(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyEmployeeAttendanceRule($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateEmployeeContract(Request $request, bool $isUpdate): void
    {
        $this->validateCreateOrUpdateEmployeeContract($request);

        try {
            Database::beginTransaction();

            $employeeContract = new EmployeeContracts();
            $employeeContract->id = $request->id;
            $employeeContract->employee_id = $request->employee_id;
            $employeeContract->contract_start_date = $request->contract_start_date;
            $employeeContract->contract_end_date = $request->contract_end_date;
            $employeeContract->salary = $request->salary;
            $employeeContract->contract_terms = $request->contract_terms;

            if ($isUpdate) {
                $this->hrRepository->editEmployeeContract($employeeContract);
            } else {
                if (!$this->hrRepository->isThereAnyEmployeeContract($employeeContract)) {
                    $this->hrRepository->saveEmployeeContract($employeeContract);
                } else {
                    throw new ValidationException(['float' => 'Contract udah ada!']);
                }
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateEmployeeContract(Request $request): void
    {
        $errors = $this->validation->make([
            'employee_id' => [$request->employee_id, 'required'],
            'contract_start_date' => [$request->contract_start_date, 'required|mustString'],
            'contract_end_date' => [$request->contract_end_date, 'required'],
            'salary' => [$request->salary, 'required|mustNumeric'],
            'contract_terms' => [$request->contract_terms, 'required|mustString'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if (TimeHelper::isEndDateGreaterThanStartDate($request->contract_start_date, $request->contract_end_date)) {
            throw new ValidationException(['contract_end_date' => ['Contract End date tidak boleh sebelum start date dong']]);
        }

        $employee = new Employees();
        $employee->user_id = $request->employee_id;

        if (!$this->hrRepository->isThereAnyEmployee($employee)) {
            throw new ValidationException(['employee_id' => ['User tidak ada']]);
        }
    }

    public function deleteEmployeeContract(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyEmployeeContract($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateEmployeeLeaveRequest(Request $request, bool $isUpdate): void
    {
        $this->validateCreateOrUpdateEmployeeLeaveRequest($request);

        try {
            Database::beginTransaction();

            $employeeLeaveRequest = new EmployeeLeaveRequests();
            $employeeLeaveRequest->id = $request->id;
            $employeeLeaveRequest->employee_id = $request->employee_id;
            $employeeLeaveRequest->leave_type = $request->leave_type;
            $employeeLeaveRequest->start_date = $request->start_date;
            $employeeLeaveRequest->end_date = $request->end_date;
            $employeeLeaveRequest->status = $request->status;
            $employeeLeaveRequest->remarks = $request->remarks;

            if ($isUpdate) {
                $this->hrRepository->editEmployeeLeaveRequest($employeeLeaveRequest);
            } else {
                if (!$this->hrRepository->isThereAnyEmployeeLeaveRequest($employeeLeaveRequest)) {
                    $this->hrRepository->saveEmployeeLeaveRequest($employeeLeaveRequest);
                } else {
                    throw new ValidationException(['float' => 'Request udah ada!']);
                }
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateEmployeeLeaveRequest(Request $request): void
    {
        $errors = $this->validation->make([
            'employee_id' => [$request->employee_id, 'required|mustString'],
            'leave_type' => [$request->leave_type, 'required|mustEnum:Sick.Vacation.Personal.Unpaid'],
            'start_date' => [$request->start_date, 'required'],
            'end_date' => [$request->end_date, 'required'],
            'status' => [$request->status, 'required|mustEnum:Pending.Approved.Rejected'],
            'remarks' => [$request->remarks, 'required']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if (TimeHelper::isEndDateGreaterThanStartDate($request->start_date, $request->end_date)) {
            throw new ValidationException(['end_date' => ['End date tidak boleh sebelum start date dong']]);
        }

        $employee = new Employees();
        $employee->user_id = $request->employee_id;

        if (!$this->hrRepository->isThereAnyEmployee($employee)) {
            throw new ValidationException(['employee_id' => ['User tidak ada']]);
        }
    }

    public function deleteEmployeeLeaveRequest(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyEmployeeLeaveRequest($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateEmployeeOvertime(Request $request, bool $isUpdate): void
    {
        $this->validateCreateOrUpdateEmployeeOvertime($request);

        try {
            Database::beginTransaction();

            $employeeOvertime = new EmployeeOvertime();
            $employeeOvertime->id = $request->id;
            $employeeOvertime->employee_id = $request->employee_id;
            $employeeOvertime->overtime_date = $request->overtime_date;
            $employeeOvertime->start_time = $request->start_time;
            $employeeOvertime->end_time = $request->end_time;
            $employeeOvertime->overtime_rate = $request->overtime_rate;
            $employeeOvertime->remarks = $request->remarks;

            if ($isUpdate) {
                $this->hrRepository->editEmployeeOvertime($employeeOvertime);
            } else {
                if (!$this->hrRepository->isThereAnyEmployeeOvertime($employeeOvertime)) {
                    $this->hrRepository->saveEmployeeOvertime($employeeOvertime);
                } else {
                    throw new ValidationException(['float' => 'Overtime udah ada!']);
                }
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateEmployeeOvertime(Request $request): void
    {
        $errors = $this->validation->make([
            'employee_id' => [$request->employee_id, 'required'],
            'overtime_date' => [$request->overtime_date, 'required'],
            'start_time' => [$request->start_time, 'required|mustString'],
            'end_time' => [$request->end_time, 'required|mustString'],
            'overtime_rate' => [$request->overtime_rate, 'required'],
            'remarks' => [$request->remarks, 'required']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if (TimeHelper::isEndDateGreaterThanStartDate($request->start_time, $request->end_time)) {
            throw new ValidationException(['end_time' => ['End time tidak boleh sebelum start date dong']]);
        }

        $employee = new Employees();
        $employee->user_id = $request->employee_id;

        if (!$this->hrRepository->isThereAnyEmployee($employee)) {
            throw new ValidationException(['employee_id' => ['User tidak ada']]);
        }
    }

    public function deleteEmployeeOvertime(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyEmployeeLeaveRequest($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateEmployeePayroll(Request $request, bool $isUpdate): void
    {
        $employee = $this->validateCreateOrUpdateEmployeePayroll($request);

        try {
            Database::beginTransaction();

            $employeePayroll = new EmployeePayrolls();
            $employeePayroll->id = $request->id;
            $employeePayroll->employee_id = $request->email;
            $employeePayroll->payroll_month = $request->payroll_month . '-01';
            $employeePayroll->base_salary = $request->base_salary;

            $totalOvertime = $this->hrRepository->findEmployeeTotalOvertime($employeePayroll);
            $employeePayroll->total_overtime = $totalOvertime;

            $latePenalties = $this->hrRepository->findEmployeeLatePenalties($employeePayroll);
            $employeePayroll->late_penalties = $latePenalties;

            $employeePayroll->status = $request->status;
            $employeePayroll->payment_date = $request->payment_date;
            $employeePayroll->remarks = $request->remarks;

            if ($isUpdate) {
                $this->hrRepository->editEmployeePayroll($employeePayroll);
            } else {
                if ($this->hrRepository->isThereAnyEmployee($employee)) {
                    if (!$this->hrRepository->isThereAnyEmployeePayroll($employeePayroll)) {
                        $this->hrRepository->saveEmployeePayroll($employeePayroll);
                    } else {
                        throw new ValidationException(['float' => 'Payroll sudah dibuat']);
                    }
                } else {
                    throw new ValidationException(['float' => 'Employee ngga ada!']);
                }
            }

            Database::commitTransaction();
        } catch (ValidationException $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateEmployeePayroll(Request $request): ?Employees
    {
        $errors = $this->validation->make([
            'email' => [$request->email, 'mustBeEmail'],
            'payroll_month' => [$request->payroll_month, 'required'],
            'base_salary' => [$request->base_salary, 'required|mustNumeric'],
            'status' => [$request->status, 'required'],
            'payment_date' => [$request->payment_date, 'required'],
            'remarks' => [$request->remarks, 'required'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $employee = new Employees();
        $employee->user_id = $request->email;

        if (!$this->hrRepository->isThereAnyEmployee($employee)) {
            throw new ValidationException(['email' => ['User tidak ada']]);
        }

        return $employee;
    }

    public function deleteEmployeePayroll(string $id): void
    {
        try {
            Database::beginTransaction();

            $this->hrRepository->destroyEmployeePayroll($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateEmployeeProjectAssignment(Request $request, bool $isUpdate): void
    {
        $employee = $this->validateCreateOrUpdateEmployeeProjectAssignment($request);

        try {
            Database::beginTransaction();

            $employeeProjectAssignment = new EmployeeProjectAssigments();
            $employeeProjectAssignment->id = $request->id;
            $employeeProjectAssignment->employee_id = $employee->user_id;
            $employeeProjectAssignment->project_id = $request->project_id;
            $employeeProjectAssignment->role_in_project = $request->role_in_project;
            $employeeProjectAssignment->assigned_date = $request->assigned_date;

            if ($isUpdate) {
                $this->hrRepository->editEmployeePojectAssignment($employeeProjectAssignment);
            } else {
                if (!$this->hrRepository->isThereAnyEmployeeProjectAssignments($employeeProjectAssignment)) {
                    $this->hrRepository->saveEmployeeProjectAssignment($employeeProjectAssignment);
                } else {
                    throw new ValidationException(['float' => 'Project Assignments udah ada!']);
                }
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateEmployeeProjectAssignment(Request $request): Employees
    {
        $errors = $this->validation->make([
            'email' => [$request->email, 'mustBeEmail'],
            'project_id' => [$request->project_id, 'required|mustNumeric'],
            'role_in_project' => [$request->role_in_project, 'required|mustString'],
            'assigned_date' => [$request->assigned_date, 'required'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $employee = new Employees();
        $employee->user_id = $request->email;

        if (!$this->hrRepository->isThereAnyEmployee($employee)) {
            throw new ValidationException(['email' => ['User tidak ada']]);
        }

        return $employee;
    }

    public function deleteEmployeeProjectAssignment(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyEmployeeProjectAssignment($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateCompanyEmployeeProject(Request $request, bool $isUpdate): void
    {
        $this->validateCreateOrUpdateCompanyEmployeeProject($request);

        try {
            Database::beginTransaction();

            $companyEmployeeProject = new CompanyEmployeeProjects();
            $companyEmployeeProject->id = $request->id;
            $companyEmployeeProject->name = $request->name;
            $companyEmployeeProject->description = $request->description;
            $companyEmployeeProject->start_date = $request->start_date;
            $companyEmployeeProject->end_date = $request->end_date;
            $companyEmployeeProject->status = $request->status;

            if ($isUpdate) {
                $this->hrRepository->editCompanyEmployeeProject($companyEmployeeProject);
            } else {
                if (!$this->hrRepository->isThereAnyCompanyEmployeeProject($companyEmployeeProject)) {
                    $this->hrRepository->saveCompanyEmployeeProject($companyEmployeeProject);
                } else {
                    throw new ValidationException(['float' => 'Project udah ada!']);
                }
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateCompanyEmployeeProject(Request $request): void
    {
        $errors = $this->validation->make([
            'name' => [$request->name, 'required|mustString'],
            'description' => [$request->description, 'required|mustString:10,200'],
            'start_date' => [$request->start_date, 'required'],
            'end_date' => [$request->end_date, 'required'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if (TimeHelper::isEndDateGreaterThanStartDate($request->start_date, $request->end_date)) {
            throw new ValidationException(['end_date' => ['End date tidak boleh sebelum start date dong']]);
        }
    }

    public function deleteCompanyEmployeeProject(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyCompanyEmployeeProject($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateCompanyEmployeeRole(Request $request, bool $isUpdate): void
    {
        $this->validateCreateOrUpdateCompanyEmployeeRole($request);
        try {
            Database::beginTransaction();

            $companyEmployeeRole = new CompanyEmployeeRoles();
            $companyEmployeeRole->id = $request->id;
            $companyEmployeeRole->name = $request->name;

            if ($isUpdate) {
                $this->hrRepository->editCompanyEmployeeRole($companyEmployeeRole);
            } else {
                if (!$this->hrRepository->isThereAnyCompanyEmployeeRole($companyEmployeeRole)) {
                    $this->hrRepository->saveCompanyEmployeeRole($companyEmployeeRole);
                } else {
                    throw new ValidationException(['name' => ['Role sudah ada']]);
                }
            }

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateCompanyEmployeeRole(Request $request): void
    {
        $errors = $this->validation->make([
            'name' => [$request->name, 'required|mustString:5,30']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteCompanyEmployeeRole(string|int $id): void
    {
        try {
            Database::beginTransaction();

            $this->hrRepository->destroyCompanyEmployeeRole($id);

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateCompanyOfficeDepartment(Request $request, bool $isUpdate): ?CompanyOfficeDepartements
    {
        $this->validateCreateOrUpdateCompanyOfficeDepartment($request);

        try {
            Database::beginTransaction();

            $companyOfficeDepartment = new CompanyOfficeDepartements();
            $companyOfficeDepartment->id = $request->id;
            $companyOfficeDepartment->name = $request->name;
            $companyOfficeDepartment->description = $request->description;

            if ($isUpdate) {
                $this->hrRepository->editCompanyOfficeDepartment($companyOfficeDepartment);
            } else {
                if (!$this->hrRepository->isThereAnyCompanyOfficeDepartment($companyOfficeDepartment)) {
                    $this->hrRepository->saveCompanyOfficeDepartment($companyOfficeDepartment);
                } else {
                    throw new ValidationException(['float' => 'Department udah ada!']);
                }
            }

            Database::commitTransaction();

            return $companyOfficeDepartment;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateCompanyOfficeDepartment(Request $request): void
    {
        $errors = $this->validation->make([
            'name' => [$request->name, 'required|mustString:2,45'],
            'description' => [$request->description, 'required|mustString:8,100']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteCompanyOfficeDepartment(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyCompanyOfficeDepartment($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateCompanyOfficeFinancialTransaction(Request $request, bool $isUpdate): ?CompanyOfficeFinancialTransactions
    {
        $this->validateCreateOrUpdateCompanyOfficeFinancialTransaction($request);

        try {
            Database::beginTransaction();

            $companyOfficeFinancialTransaction = new CompanyOfficeFinancialTransactions();
            $companyOfficeFinancialTransaction->id = $request->id;
            $companyOfficeFinancialTransaction->type = $request->type;
            $companyOfficeFinancialTransaction->amount = $request->amount;
            $companyOfficeFinancialTransaction->transaction_date = $request->transaction_date;
            $companyOfficeFinancialTransaction->description = $request->description;

            if ($isUpdate) {
                $this->hrRepository->editCompanyOfficeFinancialTransaction($companyOfficeFinancialTransaction);
            } else {
                if (!$this->hrRepository->isThereAnyCompanyOfficeFinancialTransaction($companyOfficeFinancialTransaction)) {
                    $this->hrRepository->saveCompanyOfficeFinancialTransaction($companyOfficeFinancialTransaction);
                } else {
                    throw new ValidationException(['float' => 'Financial Transaction udah ada!']);
                }
            }

            Database::commitTransaction();

            return $companyOfficeFinancialTransaction;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateCompanyOfficeFinancialTransaction(Request $request): void
    {
        $errors = $this->validation->make([
            'type' => [$request->type, 'required|mustEnum:income.expense'],
            'amount' => [$request->amount, 'required|mustNumeric'],
            'transaction_date' => [$request->transaction_date, 'required|mustString'],
            'description' => [$request->description, 'required|mustString']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteCompanyOfficeFinancialTransaction(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyCompanyOfficeFinancialTransaction($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function createOrUpdateCompanyOfficeRecruitment(Request $request, bool $isUpdate): ?CompanyOfficeRecruitments
    {
        $this->validateCreateOrUpdateCompanyOfficeRecruitment($request);

        try {
            Database::beginTransaction();

            $companyOfficeRecruitment = new CompanyOfficeRecruitments();
            $companyOfficeRecruitment->id = $request->id;
            $companyOfficeRecruitment->job_title = $request->job_title;
            $companyOfficeRecruitment->department_id = $request->department;
            $companyOfficeRecruitment->job_description = $request->job_description;
            $companyOfficeRecruitment->status = $request->status;

            if ($isUpdate) {
                $this->hrRepository->editCompanyOfficeRecruitment($companyOfficeRecruitment);
            } else {
                if (!$this->hrRepository->isThereAnyCompanyOfficeRecruitment($companyOfficeRecruitment)) {
                    $this->hrRepository->saveCompanyOfficeRecruitment($companyOfficeRecruitment);
                } else {
                    throw new ValidationException(['float' => 'Recruitment udah ada!']);
                }
            }

            Database::commitTransaction();

            return $companyOfficeRecruitment;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateCreateOrUpdateCompanyOfficeRecruitment(Request $request): void
    {
        $departments = implode('.', array_keys($this->hrRepository->findAllCompanyDepartments()));

        $errors = $this->validation->make([
            'job_title' => [$request->job_title, 'required|mustString:7,50'],
            'department' => [$request->department, "required|mustEnum:$departments"],
            'job_description' => [$request->job_description, 'required|mustString:7,150'],
            'status' => [$request->status, 'required|mustEnum:open.closed']
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteCompanyOfficeRecruitment(string|int $id): void
    {
        try {
            Database::beginTransaction();
            $this->hrRepository->destroyCompanyOfficeRecruitment($id);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }
}
