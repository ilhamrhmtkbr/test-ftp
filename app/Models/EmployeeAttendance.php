<?php

namespace ilhamrhmtkbr\App\Models;

class EmployeeAttendance
{
    public ?int $id;
    public string $employee_id;
    public ?string $attendance_date;
    public ?string $check_in_time;
    public ?string $check_out_time;
    public ?string $status;
    public ?string $late_penalty;
    public string $created_at;
    public string $updated_at;
}
