<?php

namespace ilhamrhmtkbr\App\Models;

class EmployeePayrolls
{
    public ?int $id;
    public ?string $employee_id;
    public string $payroll_month;
    public float $base_salary;
    public string $total_overtime;
    public string $late_penalties;
    public string $net_salary;
    public string $status;
    public string $payment_date;
    public string $remarks;
    public string $created_at;
    public string $updated_at;
}
