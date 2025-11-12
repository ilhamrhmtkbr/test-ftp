<?php

namespace ilhamrhmtkbr\App\Models;

class CompanyOfficeFinancialTransactions
{
    public ?int $id;
    public string $type;
    public float $amount;
    public string $transaction_date;
    public string $description;
    public string $created_at;
    public string $updated_at;
}
