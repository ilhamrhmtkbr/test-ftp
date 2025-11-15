<?php

namespace ilhamrhmtkbr\App\Http\Controller;

use Exception;
use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Exceptions\ValidationException;
use ilhamrhmtkbr\App\Facades\Request;
use ilhamrhmtkbr\App\Facades\Session;
use ilhamrhmtkbr\App\Facades\View;
use ilhamrhmtkbr\App\Helper\Components\AlertWithCloseHelper;
use ilhamrhmtkbr\App\Helper\Components\BadgeWithCloseHelper;
use ilhamrhmtkbr\App\Helper\PDFGeneratorHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;
use ilhamrhmtkbr\App\Repository\HrRepository;
use ilhamrhmtkbr\App\Service\HrService;

class HrController
{
    private HrRepository $hrRepository;
    private HrService $hrService;
    private Session $session;

    public function __construct()
    {
        $connection = Database::getConnection();
        $this->hrRepository = new HrRepository($connection);
        $this->hrService = new HrService();
        $this->session = new Session();
    }

    public function downloadPdfCandidates():void
    {
        try {
            $data = $this->hrRepository->findAllCandidates(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/CandidatesPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('candidates-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewCandidates():void
    {
        View::render(
            'HR/Candidates',
            $this->session->current(),
            'Candidates',
            true,
            $this->hrRepository->findAllCandidates(UrlHelper::getParamData())
        );
    }

    public function postCandidateInterview(Request $request): void
    {
        try {
            $candidate = $this->hrService->sendEmailToCandidateForInterview($request);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success send email to : ' . $candidate->user_id, 'success-send-email');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/candidate/details?id=' . $request->email . '#form-send-email');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-send-email');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/candidate/details?id=' . $request->email . '#form-send-email');
        }
    }

    public function updateCandidateStatus(Request $request): void
    {
        try {
            $candidate = $this->hrService->updateCandidateStatus($request);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success update status : ' . $candidate->user_id, 'success-update-status');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/candidate/details?id=' . $request->email . '#form-update-status');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-status');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/candidate/details?id=' . $request->email . '#form-update-status');
        }
    }

    public function viewCandidateDetails():void
    {
        View::render(
            'HR/CandidateDetails',
            $this->session->current(),
            'Candidate Details',
            true,
            $this->hrRepository->findOneUserDetails($_GET['id']),
        );
    }

    public function downloadPdfEmployees():void
    {
        try {
            $data = $this->hrRepository->findAllEmployees(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/EmployeesPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('employees-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewEmployees():void
    {
        View::render(
            'HR/Employees',
            $this->session->current(),
            'Employees',
            true,
            [
                'employees' => $this->hrRepository->findAllEmployees(UrlHelper::getParamData()),
                'employee_roles' => $this->hrRepository->findAllCompanyRoles(),
                'company_departments' => $this->hrRepository->findAllCompanyDepartments()
            ]
        );
    }

    public function postEmployee(Request $request): void
    {
        try {
            $employees = $this->hrService->createOrUpdateEmployee($request);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success update : ' . $employees->user_id, 'success-update-employee');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employees#form-update-employee');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-employee');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employees#form-update-employee');
        }
    }

    public function deleteEmployee(Request $request): void
    {
        try {
            $this->hrService->deleteEmployee($request->user_id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete', 'success-delete-employee-project-assignment');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/project-assignments');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-employee-project-assignment');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/project-assignments');
        }
    }

    public function viewEmployeeDetails():void
    {
        View::render(
            'HR/EmployeeDetails',
            $this->session->current(),
            'Employee Details',
            data: $this->hrRepository->findEmployeeDetails($_GET['id']),
        );
    }

    public function downloadPdfEmployeeAttendance():void
    {
        try {
            $data = $this->hrRepository->findAllEmployeeAttendance(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/EmployeeAttendancePdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('employee-attendance-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewEmployeeAttendance():void
    {
        View::render(
            'HR/EmployeeAttendance',
            $this->session->current(),
            'Employee Attendance',
            true,
            $this->hrRepository->findAllEmployeeAttendance(UrlHelper::getParamData())
        );
    }

    public function downloadPdfEmployeeAttendanceRules():void
    {
        try {
            $data = $this->hrRepository->findAllEmployeeAttendanceRules(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/EmployeeAttendanceRulesPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('employee-attendance-rules-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewEmployeeAttendanceRules():void
    {
        View::render(
            'HR/EmployeeAttendanceRules',
            $this->session->current(),
            'Employee Attendance Rules',
            true,
            $this->hrRepository->findAllEmployeeAttendanceRules(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateEmployeeAttendanceRule(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateEmployeeAttendanceRule($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' attendance-rule : ' . $request->rule_name, 'success-update-employee-attendance-rule');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/attendance-rules#form-update-employee-attendance-rule');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-employee-attendance-rule');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/attendance-rules#form-update-employee-attendance-rule');
        }
    }

    public function deleteEmployeeAttendanceRule(Request $request): void
    {
        try {
            $this->hrService->deleteEmployeeAttendanceRule($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete : ' . $request->id, 'success-delete-employee-attendance-rule');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/attendance-rules');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-employee-attendance-rule');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/attendance-rules');
        }
    }

    public function downloadPdfEmployeeContracts():void
    {
        try {
            $data = $this->hrRepository->findAllEmployeeContracts(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/EmployeeContractsPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('employee-contracts-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewEmployeeContracts():void
    {
        View::render(
            'HR/EmployeeContracts',
            $this->session->current(),
            'Employee Contracts',
            true,
            $this->hrRepository->findAllEmployeeContracts(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateEmployeeContract(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateEmployeeContract($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' contract : ' . $request->employee_id, 'success-update-employee-contract');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/contracts#form-update-employee-contract');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-employee-contract');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/contracts#form-update-employee-contract');
        }
    }

    public function deleteEmployeeContract(Request $request): void
    {
        try {
            $this->hrService->deleteEmployeeContract($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete : ' . $request->id, 'success-delete-employee-contract');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/contracts');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-employee-contract');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/contracts');
        }
    }

    public function downloadPdfEmployeeLeaveRequests():void
    {
        try {
            $data = $this->hrRepository->findAllEmployeeLeaveRequests(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/EmployeeLeaveRequestsPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('employee-leave-requests-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewEmployeeLeaveRequests():void
    {
        View::render(
            'HR/EmployeeLeaveRequests',
            $this->session->current(),
            'Employee Leave Request',
            true,
            $this->hrRepository->findAllEmployeeLeaveRequests(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateEmployeeLeaveRequest(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateEmployeeLeaveRequest($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' leave request : ' . $request->employee_id, 'success-update-employee-leave-request');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/leave-requests#form-update-employee-leave-request');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-employee-leave-request');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/leave-requests#form-update-employee-leave-request');
        }
    }

    public function deleteEmployeeLeaveRequest(Request $request): void
    {
        try {
            $this->hrService->deleteEmployeeLeaveRequest($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete : ' . $request->id, 'success-delete-employee-leave-request');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/leave-requests');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-employee-leave-request');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/leave-requests');
        }
    }

    public function downloadPdfEmployeeOvertime():void
    {
        try {
            $data = $this->hrRepository->findAllEmployeeOvertime(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/EmployeeOvertimePdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('employee-overtime-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewEmployeeOvertime():void
    {
        View::render(
            'HR/EmployeeOvertime',
            $this->session->current(),
            'Employee Overtime',
            true,
            $this->hrRepository->findAllEmployeeOvertime(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateEmployeeOvertime(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateEmployeeOvertime($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' overtime : ' . $request->employee_id, 'success-update-employee-overtime');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/overtime#form-update-employee-overtime');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-employee-overtime');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/overtime#form-update-employee-overtime');
        }
    }

    public function deleteEmployeeOvertime(Request $request): void
    {
        try {
            $this->hrService->deleteEmployeeOvertime($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete : ' . $request->id, 'success-delete-employee-overtime');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/overtime');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-employee-overtime');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/overtime');
        }
    }

    public function downloadPdfEmployeePayrolls():void
    {
        try {
            $data = $this->hrRepository->findAllEmployeePayrolls(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/EmployeePayrollsPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('employee-payrolls-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewEmployeePayrolls():void
    {
        View::render(
            'HR/EmployeePayrolls',
            $this->session->current(),
            'Employee Payrolls',
            true,
            $this->hrRepository->findAllEmployeePayrolls(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateEmployeePayroll(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateEmployeePayroll($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' payroll : ' . $request->email, 'success-update-employee-payroll');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/payrolls#form-update-employee-payroll');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-employee-payroll');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/payrolls#form-update-employee-payroll');
        }
    }

    public function deleteEmployeePayroll(Request $request): void
    {
        try {
            $this->hrService->deleteEmployeePayroll($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete : ' . $request->id, 'success-delete-employee-payroll');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/payrolls');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-employee-payroll');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/payrolls');
        }
    }

    public function downloadPdfEmployeeProjectAssignment():void
    {
        try {
            $data = $this->hrRepository->findAllEmployeeProjectAssignments(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/EmployeeProjectAssignmentsPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('employee-project-assignments-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewEmployeeProjectAssignments():void
    {
        View::render(
            'HR/EmployeeProjectsAssignments',
            $this->session->current(),
            'Employee Project Assignments',
            true,
            [
                'employee_project_assignments' => $this->hrRepository->findAllEmployeeProjectAssignments(UrlHelper::getParamData()),
                'company_employee_projects' => $this->hrRepository->findAllCompanyEmployeeProjectsNoFilter()
            ]
        );
    }

    public function createOrUpdateEmployeeProjectAssignment(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateEmployeeProjectAssignment($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' project assignment : ' . $request->email, 'success-update-employee-project-assignment');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/project-assignments#form-update-employee-project-assignment');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-employee-project-assignment');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/project-assignments#form-update-employee-project-assignment');
        }
    }

    public function deleteEmployeeProjectAssignment(Request $request): void
    {
        try {
            $this->hrService->deleteEmployeeProjectAssignment($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete : ' . $request->id, 'success-delete-employee-project-assignment');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/project-assignments');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-employee-project-assignment');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/employee/project-assignments');
        }
    }

    public function viewCompany():void
    {
        View::render(
            'HR/CompanyDashboard',
            $this->session->current(),
            'Company'
        );
    }

    public function downloadPdfCompanyEmployeeProjects():void
    {
        try {
            $data = $this->hrRepository->findAllCompanyEmployeeProjects(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/CompanyEmployeeProjectsPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('company-employee-projects-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewCompanyEmployeeProjects():void
    {
        View::render(
            'HR/CompanyEmployeeProjects',
            $this->session->current(),
            'Company Employee Projects',
            true,
            $this->hrRepository->findAllCompanyEmployeeProjects(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateCompanyEmployeeProject(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateCompanyEmployeeProject($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' company employee project : ' . $request->name, 'success-update-company-employee-project');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/employee/projects#form-update-company-employee-project');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-company-employee-project');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/employee/projects#form-update-company-employee-project');
        }
    }

    public function deleteCompanyEmployeeProject(Request $request): void
    {
        try {
            $this->hrService->deleteCompanyEmployeeProject($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete', 'success-delete-company-employee-project');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/employee/projects');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-company-employee-project');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/employee/projects');
        }
    }

    public function downloadPdfCompanyEmployeeRoles():void
    {
        try {
            $data = $this->hrRepository->findAllCompanyEmployeeRoles(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/CompanyEmployeeRolesPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('company-employee-roles-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewCompanyEmployeeRoles():void
    {
        View::render(
            'HR/CompanyEmployeeRoles',
            $this->session->current(),
            'Company Employee Roles',
            true,
            $this->hrRepository->findAllCompanyEmployeeRoles(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateCompanyEmployeeRole(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateCompanyEmployeeRole($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' company employee role : ' . $request->name, 'success-update-company-employee-role');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/employee/roles#form-update-company-employee-role');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-company-employee-role');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/employee/roles#form-update-company-employee-role');
        }
    }

    public function deleteCompanyEmployeeRole(Request $request): void
    {
        try {
            $this->hrService->deleteCompanyEmployeeRole($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete', 'success-delete-company-employee-role');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/employee/roles');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-company-employee-role');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/employee/roles');
        }
    }

    public function downloadPdfCompanyOfficeDepartments():void
    {
        try {
            $data = $this->hrRepository->findAllCompanyOfficeDepartments(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/CompanyOfficeDepartmentsPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('company-office-departments-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewCompanyOfficeDepartments():void
    {
        View::render(
            'HR/CompanyOfficeDepartments',
            $this->session->current(),
            'Company Office Roles',
            true,
            $this->hrRepository->findAllCompanyOfficeDepartments(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateCompanyOfficeDepartment(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateCompanyOfficeDepartment($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' company office department : ' . $request->name, 'success-update-company-office-department');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/departments#form-update-company-office-department');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-company-office-department');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/departments#form-update-company-office-department');
        }
    }

    public function deleteCompanyOfficeDepartment(Request $request): void
    {
        try {
            $this->hrService->deleteCompanyOfficeDepartment($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete', 'success-delete-company-office-department');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/departments');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-company-office-department');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/departments');
        }
    }

    public function downloadPdfCompanyOfficeFinancialTransactions():void
    {
        try {
            $data = $this->hrRepository->findAllCompanyOfficeFinancialTransactions(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/CompanyOfficeFinancialTransactionsPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('company-office-financial-transactions-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewCompanyOfficeFinancialTransactions():void
    {
        View::render(
            'HR/CompanyOfficeFinancialTransactions',
            $this->session->current(),
            'Company Office Financial Transactions',
            true,
            $this->hrRepository->findAllCompanyOfficeFinancialTransactions(UrlHelper::getParamData())
        );
    }

    public function createOrUpdateCompanyOfficeFinancialTransaction(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateCompanyOfficeFinancialTransaction($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' company employee office financial transaction : ' . $request->type, 'success-update-company-office-financial-transaction');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/financial-transactions#form-update-company-office-financial-transaction');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-company-office-financial-transaction');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/financial-transactions#form-update-company-office-financial-transaction');
        }
    }

    public function deleteCompanyOfficeFinancialTransaction(Request $request): void
    {
        try {
            $this->hrService->deleteCompanyOfficeFinancialTransaction($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete', 'success-delete-company-office-financial-transaction');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/financial-transactions');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-financial-company-office-transaction');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/financial-transactions');
        }
    }

    public function downloadPdfCompanyOfficeRecruitments():void
    {
        try {
            $data = $this->hrRepository->findAllCompanyOfficeRecruitments(UrlHelper::getParamData());

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('HR/CompanyOfficeRecruitmentsPdf', $data);
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('company-office-recruitment-filter-by-' . implode('-', $names) . '.pdf');
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewCompanyOfficeRecruitments():void
    {
        View::render(
            'HR/CompanyOfficeRecruitments',
            $this->session->current(),
            'Company Office Recruitments',
            true,
            [
                'company_office_departments' => $this->hrRepository->findAllCompanyDepartments(),
                'company_office_recruitments' => $this->hrRepository->findAllCompanyOfficeRecruitments(UrlHelper::getParamData())
            ]
        );
    }

    public function createOrUpdateCompanyOfficeRecruitment(Request $request): void
    {
        try {
            $this->hrService->createOrUpdateCompanyOfficeRecruitment($request, $_POST['id'] != null);
            if ($_POST['id'] != null) {
                $message = 'update';
            } else {
                $message = 'save';
            }
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success ' . $message . ' company employee office recruitment : ' . $request->job_title, 'success-update-company-office-recruitment');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/recruitments#form-update-company-office-recruitment');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-update-company-office-recruitment');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/recruitments#form-update-company-office-recruitment');
        }
    }

    public function deleteCompanyOfficeRecruitment(Request $request): void
    {
        try {
            $this->hrService->deleteCompanyOfficeRecruitment($request->id);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success delete', 'success-delete-company-office-recruitment');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/recruitments');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-delete-company-office-recruitment');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/hr/company/office/recruitments');
        }
    }
}
