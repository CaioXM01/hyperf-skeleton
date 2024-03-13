<?php

namespace App\Domain\DTO\User;

class CreateUserDto
{
    public string $name;
    public string $email;
    public string $document;
    public string $password;
    public int $balance;
    public string $type;

    public function __construct(
        string $name,
        string $email,
        string $document,
        string $password,
        int $balance,
        string $type
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->document = $document;
        $this->password = $password;
        $this->balance = $balance;
        $this->type = $type;
    }
}
