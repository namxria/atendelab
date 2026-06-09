<?php

require_once __DIR__ . '/../../config/database.php';

require_once __DIR__ . '/../Middleware/auth.php';

class AuthController
{
    private PDO $pdo;

    public function __construct()
    {
        global $pdo;

        $this->pdo = $pdo;
    }

    public function exibirLogin(): void
    {
        if (usuarioAutenticado()) {
            header('Location: ?controller=auth&action=dashboard');
            exit;
        }

        $erro = $_SESSION['erro_login'] ?? null;
        $mensagem = $_SESSION['mensagem'] ?? null;

        unset($_SESSION['erro_login'], $_SESSION['mensagem']);

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function entrar(): void 
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=auth&action=login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === ''){
            $_SESSION['erro_login'] = 'Informe o e-mail e a senha.';

            header('Location: ?controller=auth&action=login');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro_login'] = 'Informe um e-mail valido.';

            header('Location: ?controller=auth&action=login');
            exit;
        }

        $sql = 'SELECT id, nome, email, senha, perfil, status
                FROM usuarios
                WHERE email = :email
                LIMIT 1';

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':email', $email);

        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if {
            !$usuario
            || $usuario['status'] !== 'ativo'
            || !password_verify($senha, $usuario['senha'])
        }
    }
}