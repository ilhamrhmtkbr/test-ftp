<?php

namespace ilhamrhmtkbr\App\Models;

class UserProfileEducation
{
    public ?int $id;
    public ?string $user_id;
    public string $degree_id;
    public string $institution;
    public string $field;
    public string $graduation_year;
    public string $created_at;
}
