<?php

namespace ilhamrhmtkbr\App\Models;

class EmployeeProjectAssigments
{
    public ?int $id;
    public ?string $employee_id;
    public int $project_id;
    public string $role_in_project;
    public string $assigned_date;
}
