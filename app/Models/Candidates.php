<?php

namespace ilhamrhmtkbr\App\Models;

class Candidates
{
    public ?int $id;
    public ?string $user_id;
    public int $job_id;
    public string $status;
    public string $created_at;
}
