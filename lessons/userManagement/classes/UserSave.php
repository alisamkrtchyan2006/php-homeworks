<?php

declare(strict_types=1);

class UserSave {
    private string $file;

    public function __construct($filePath) 
    {
        $this->file = $filePath;

        if (!file_exists($filePath)) {
            file_put_contents($filePath, "firstname,lastname,email,password\n");
        }
    }

    public function getAll(): array 
    {
        $lines = file($this->file, FILE_SKIP_EMPTY_LINES);

        $rows = [];
        foreach ($lines as $i => $line) {
            if ($i === 0) continue;

            $rows[] = str_getcsv($line, ',', '"', '\\');
        }
        return $rows;
    }   

    public function emailExists($email): bool 
    {
        $lines = file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $i => $line) 
        {
            if ($i === 0) continue; 

            $row = str_getcsv($line, ',', '"', '\\');
            if (count($row) < 3) continue; 

            $existingEmail = trim($row[2]);

            if (strtolower($existingEmail) === strtolower(trim($email))) {
                return true;
            }
        }
        return false;
    }

    public function saveUser(User $user): void 
    {
        $line = "{$user->firstname},{$user->lastname},{$user->email},{$user->password}\n";
        
        file_put_contents($this->file, $line, FILE_APPEND);
    }

    public function checkLogin($email, $password): bool 
    {
        $users = $this->getAll();

        foreach ($users as $user) 
        {
            [$firstName, $lastName, $userEmail, $userPassword] = $user;

            if ($userEmail === $email && password_verify($password, $userPassword)) {
                return true;
            }
        }
        return false;
    }
}
