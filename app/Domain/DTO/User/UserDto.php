<?php

namespace App\Domain\DTO\User;
use Carbon\Carbon;

class UserDto
{
    public int $id;
    public string $name;
    public string $email;
    public string $document;
    public int $balance;
    public string $type;
    public Carbon $created_at;
    public Carbon $updated_at;

    public function __construct(
        int $id,
        string $name,
        string $email,
        string $document,
        int $balance,
        string $type,
        Carbon $created_at,
        Carbon $updated_at
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->document = $document;
        $this->balance = $balance;
        $this->type = $type;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }
}
