<?php

declare(strict_types=1);

class User {
    public string $firstname;
    public string $lastname;
    public string $email;
    public string $password;

    public function __construct($firstname, $lastname, $email, $password)
    {
        $this->firstname = $firstname;
        $this->lastname  = $lastname;
        $this->email     = $email;
        $this->password  = password_hash($password, PASSWORD_DEFAULT);
    }
}
