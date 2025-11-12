<?php

namespace ilhamrhmtkbr\App\Service;

use DateTime;
use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Models\EmployeeAttendance;
use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Exceptions\ValidationException;
use ilhamrhmtkbr\App\Helper\TimeHelper;
use ilhamrhmtkbr\App\Repository\EmployeeRepository;

class EmployeeService
{

    private EmployeeRepository $employeeRepository;

    public function __construct()
    {
        $connection = Database::getConnection();
        $this->employeeRepository = new EmployeeRepository($connection);
    }

    public function storeAttendanceCheck(User $user): void
    {
        $this->validateStoreAttendanceCheck($user);

        $nowAsDate = date('Y-m-d');
        $nowAsTime = date('H:i:s');

        $newEmployeeAttendance = new EmployeeAttendance();
        $newEmployeeAttendance->employee_id = $user->email;
        $newEmployeeAttendance->attendance_date = $nowAsDate;

        $employeeAttendance = $this->employeeRepository->findEmployeeAttendance($user->email, $nowAsDate);
        $employeeAttendanceRule = $this->employeeRepository->findAttendanceRule(1);

        if ($employeeAttendance != null) {
            if ($employeeAttendance->check_out_time == null) {
                if ($employeeAttendanceRule->end_time <= $nowAsTime) {
                    if ($employeeAttendanceRule->penalty_for_late != null) {
                        $newEmployeeAttendance->status = 'Present';
                    } else {
                        $newEmployeeAttendance->status = 'Late';
                    }

                    $newEmployeeAttendance->late_penalty = 0;
                    $newEmployeeAttendance->check_out_time = $nowAsTime;

                    $this->employeeRepository->editEmployeeAttendance($newEmployeeAttendance);
                } else {
                    throw new ValidationException(['float' => 'masih jam kerja boss']);
                }
            } else {
                throw new ValidationException(['float' => 'udah absen']);
            }
        } else {
            // Kalo si employee Telat
            if ($employeeAttendanceRule->start_time <= $nowAsTime) {
                $ruleStartTime = new DateTime($employeeAttendanceRule->start_time);
                $checkInTime = new DateTime($nowAsTime);

                $lateTime = $ruleStartTime->diff($checkInTime);
                $lateTimeString = $lateTime->format("%H:%I:%S");

                if ($employeeAttendanceRule->late_threshold < $lateTimeString) {
                    $parameter = floor(TimeHelper::getSecond($lateTimeString) / TimeHelper::getSecond($employeeAttendanceRule->late_threshold));
                    $penalty_rate = $parameter * $employeeAttendanceRule->penalty_for_late;

                    $newEmployeeAttendance->status = 'Late';
                    $newEmployeeAttendance->late_penalty = $penalty_rate;
                } else {
                    $newEmployeeAttendance->status = 'Present';
                }
            } else {
                $newEmployeeAttendance->status = 'Present';
            }

            $newEmployeeAttendance->check_in_time = $nowAsTime;

            $this->employeeRepository->saveEmployeeAttendance($newEmployeeAttendance);
        }
    }

    private function validateStoreAttendanceCheck(User $user): void
    {
        if (!$this->employeeRepository->isEmployee($user->email)) {
            throw new ValidationException(['float' => 'bukan employee']);
        }
    }
}
