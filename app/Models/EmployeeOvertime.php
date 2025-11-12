<?php

namespace ilhamrhmtkbr\App\Models;

class EmployeeOvertime
{
    public ?int $id;
    public string $employee_id;
    public string $overtime_date;
    public string $start_time;
    public string $end_time;
    public string $total_hours;
    public string $overtime_rate;
    public string $total_payment;
    public string $remarks;
    public string $created_at;
    public string $updated_at;
}
