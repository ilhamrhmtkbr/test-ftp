<?php

namespace ilhamrhmtkbr\App\Models;

class EmployeeLeaveRequests
{
    public ?int $id;
    public string $employee_id;
    public string $leave_type;
    public string $start_date;
    public string $end_date;
    public string $status;
    public string $remarks;
    public string $created_at;
    public string $updated_at;
}
