<?php

namespace ilhamrhmtkbr\App\Models;

class EmployeeAttendanceRules
{
    public ?int $id;
    public string $rule_name;
    public string $start_time;
    public string $end_time;
    public string $late_threshold;
    public string $penalty_for_late;
    public string $created_at;
    public string $updated_at;
}
