<?php

class Auth
{
    public function __construct(private User $users)
    {
    }

    public function register(string $name, string $email, string $password): bool
    {
        if ($this->users->findByEmail($email)) {
            return false;
        }

        return $this->users->create($name, $email, $password, 'user');
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);

        if (!$user || !$this->passwordMatches($password, $user['password'])) {
            return false;
        }

        Session::set('user', [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);

        return true;
    }

    private function passwordMatches(string $password, string $storedHash): bool
    {
        if (password_verify($password, $storedHash)) {
            return true;
        }

        // Used only for the demo accounts inserted by database/schema.sql.
        return strlen($storedHash) === 64 && hash_equals(hash('sha256', $password), $storedHash);
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? null) === 'admin';
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect('/login.php');
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();

        if (!self::isAdmin()) {
            redirect('/dashboard.php');
        }
    }

    public static function logout(): void
    {
        Session::destroy();
    }
}
