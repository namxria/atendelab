<?php

class UsuariosController
{

    private PDO $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header('content-type: application/json; charset=utf-8');

        $sql = 'SELECT u.id, u.nome, u.email, u.perfil FROM usuarios u ORDER BY u.id DESC';
        $stmt = $this->pdo->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($usuarios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('content-type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID do usuário inválido']);
            return;
        }

        $sql = 'SELECT u.id, u.nome, u.email, u.perfil FROM usuarios u WHERE u.id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuário não encontrado']);
            return;
        }

        echo json_encode($usuario, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('content-type: application/json; charset=utf-8');

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if ($nome == '' || $email == '' || $senha == '') {
            http_response_code(400);
            echo json_encode(['error' => 'Nome, Email e Senha são obrigatórios']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email inválido']);
            return;
        }

        if (!in_array($perfil, ['admin', 'atendente', 'aluno'], true)) {
            http_response_code(400);
            echo json_encode(['error' => 'Perfil inválido']);
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Status inválido']);
            return;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {

            $sql = 'INSERT INTO usuarios (nome, email, senha, perfil, status) VALUES (:nome, :email, :senha, :perfil, :status)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senhaHash);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->execute();

            http_response_code(201);
            echo json_encode(['messagem' => 'Usuário criado com sucesso', 'id' => $this->pdo->lastInsertId()], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar usuário: ' . $e->getMessage()]);
        }


    }
    public function atualizar(): void
    {
        header('content-type: application/json; charset=utf-8');

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $perfil = $_POST['perfil'] ?? 'atendente';
        $status = $_POST['status'] ?? 'ativo';

        if ($nome == '' || $email == '' || $senha == '') {
            http_response_code(400);
            echo json_encode(['error' => 'Nome, Email e Senha são obrigatórios']);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email inválido']);
            return;
        }

        if (!in_array($perfil, ['admin', 'aluno', 'atendente'], true)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Perfil inválido.']);
            return;
           }

        if (!in_array($status, ['ativo', 'inativo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Status inválido']);
            return;
        }

        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        try {

            $sql = 'UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, perfil = :perfil, status = :status WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':senha', $senhaHash);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(201);
            echo json_encode(['messagem' => 'Usuário atualizado com sucesso', 'id' => $id], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar usuário: ' . $e->getMessage()]);
        }
    }

    public function excluir(): void
    {
        header('Content-type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID do usuário inválido']);
            return;
        }

        try {
            $sql = 'DELETE FROM usuarios WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            http_response_code(200);
            echo json_encode(['messagem' => 'Usuário deletado com sucesso', 'id' => $id], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao deletar usuário: ' . $e->getMessage()]);
        }
    }
}
