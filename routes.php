<?php
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Middleware/auth.php';

$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

switch ($controller) {
    case 'auth':
        $authController = new AuthController();

        switch ($action) {
            case "login":
                $authController->exibirLogin();
                break;
            case "logout":
                $authController->logout();
                break;
            case "entrar":
                $authController->entrar();
                break;
            case "dashboard":
                $authController->dashboard();
                break;
            default:
                http_response_code(404);
                echo "<h1>Rota não teste</h1>";
                break;
        }
        break;
    case 'usuarios':
        $UsuariosController = new UsuariosController();
        switch ($action) {
            case "listar":
                $UsuariosController->listar();
                break;
            case "buscar":
                $UsuariosController->findById();
                break;
            case "criar":
                $UsuariosController->criar();
                break;
            case "atualizar":
                $UsuariosController->atualizar();
                break;
            case "excluir":
                $UsuariosController->delete();
                break;
            default:
                http_response_code(404);
                echo "<h1>Rota não teste</h1>";
                break;
        }
        break;
    default:
        http_response_code(404);
        echo "<h1>Rota não test</h1>";
        break;
}