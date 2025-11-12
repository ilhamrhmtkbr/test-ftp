<?php

namespace ilhamrhmtkbr\App\Http\Controller;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Exceptions\ValidationException;
use ilhamrhmtkbr\App\Facades\View;
use ilhamrhmtkbr\App\Helper\Components\AlertWithCloseHelper;
use ilhamrhmtkbr\App\Helper\PDFGeneratorHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;
use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Redis\Session;
use ilhamrhmtkbr\App\Repository\EmployeeRepository;
use ilhamrhmtkbr\App\Service\EmployeeService;

class EmployeeController
{
    private EmployeeRepository $employeeRepository;
    private EmployeeService $employeeService;
    private User $user;

    public function __construct()
    {
        $connection = Database::getConnection();
        $this->employeeRepository = new EmployeeRepository($connection);
        $session = new Session();
        $this->user = $session->current();
        $this->employeeService = new EmployeeService();
    }

    public function attendanceCheck() : void
    {
        try {
            $this->employeeService->storeAttendanceCheck($this->user);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success Update Attendance', 'success-attendance-check');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/employee/attendance');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-attendance-check');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/employee/attendance');
        }
    }

    public function viewEmployee() : void
    {
        View::render(
            'Employee/Employee',
            $this->user,
            'Employee',
            data: $this->employeeRepository->findEmployee($this->user->email),
        );
    }

    public function downloadPdfAttendance() : void
    {
        try {
            $data = $this->employeeRepository->findAttendance(UrlHelper::getParamData(), $this->user->email);

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('Employee/AttendancePdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('attendance-filter-by-' . implode('-', $names) . '.pdf');
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewAttendance() : void
    {
        View::render(
            'Employee/Attendance',
            $this->user,
            'Attendance',
            true,
            $this->employeeRepository->findAttendance(UrlHelper::getParamData(), $this->user->email)
        );
    }

    public function DownloadPdfContracts() : void
    {
        try {
            $data = $this->employeeRepository->findContracts(UrlHelper::getParamData(), $this->user->email);

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('Employee/ContractsPdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('contracts-filter-by-' . implode('-', $names) . '.pdf');
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewContracts() : void
    {
        View::render(
            'Employee/Contracts',
            $this->user,
            'Contract',
            data: $this->employeeRepository->findContracts(UrlHelper::getParamData(), $this->user->email)
        );
    }

    public function DownloadPdfLeaveRequests() : void
    {
        try {
            $data = $this->employeeRepository->findLeaveRequests(UrlHelper::getParamData(), $this->user->email);

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('Employee/LeaveRequestsPdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('leave-requests-filter-by-' . implode('-', $names) . '.pdf');
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewLeaveRequests() : void
    {
        View::render(
            'Employee/LeaveRequests',
            $this->user,
            'Leave Request',
            data: $this->employeeRepository->findLeaveRequests(UrlHelper::getParamData(), $this->user->email)
        );
    }

    public function DownloadPdfOvertime() : void
    {
        try {
            $data = $this->employeeRepository->findOvertime(UrlHelper::getParamData(), $this->user->email);

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('Employee/OvertimePdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('overtime-filter-by-' . implode('-', $names) . '.pdf');
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewOvertime() : void
    {
        View::render(
            'Employee/Overtime',
            $this->user,
            'Overtime',
            data: $this->employeeRepository->findOvertime(UrlHelper::getParamData(), $this->user->email)
        );
    }

    public function DownloadPdfPayrolls() : void
    {
        try {
            $data = $this->employeeRepository->findPayrolls(UrlHelper::getParamData(), $this->user->email);

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('Employee/PayrollsPdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('payrolls-filter-by-' . implode('-', $names) . '.pdf');
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewPayrolls() : void
    {
        View::render(
            'Employee/Payrolls',
            $this->user,
            'Payrolls',
            data: $this->employeeRepository->findPayrolls(UrlHelper::getParamData(), $this->user->email)
        );
    }

    public function DownloadPdfProjectAssignments() : void
    {
        try {
            $data = $this->employeeRepository->findProjectAssignments(UrlHelper::getParamData(), $this->user->email);

            $pdf = new PDFGeneratorHelper();
            $pdf->loadHtmlFromPhpFile('Employee/ProjectAssignmentsPdf', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $names = [];
            foreach (UrlHelper::getParamData() as $key => $value) {
                if ($value != '') {
                    $names[] = $key;
                }
            }

            $pdf->stream('project-assignments-filter-by-' . implode('-', $names) . '.pdf');
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function viewProjectAssignments() : void
    {
        View::render(
            'Employee/ProjectAssignments',
            $this->user,
            'Project Assignments',
            data: $this->employeeRepository->findProjectAssignments(UrlHelper::getParamData(), $this->user->email)
        );
    }
}
