<?php

include 'Conexao.php';
include 'Tutor.php';

const OPCOES_JSON = JSON_PRETTY_PRINT
    | JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_PRESERVE_ZERO_FRACTION;


$metodo = $_SERVER['REQUEST_METHOD'];
$caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($base !== '' && str_starts_with($caminho, $base)) {
    $caminho = substr($caminho, strlen($base)) ?: '/';
}

$alunos = [
    ['id' => 1, 'nome' => 'Ana', 'nota' => 8.5],
    ['id' => 2, 'nome' => 'Bruno', 'nota' => 6.0],
    ['id' => 3, 'nome' => 'Carla', 'nota' => 9.2],
    ['id' => 4, 'nome' => 'Diego', 'nota' => 7.0],
];

$tutor = new Tutor();
$tutores = $tutor->listar();

if ($metodo === 'GET' && $caminho === '/tutores') {
    responder([
        'total' => count($tutores),
        'tutores' => $tutores,
    ]);
}

if ($metodo === 'GET' && preg_match('#^/alunos/(\d+)$#', $caminho, $partes)) {
    $alunoId = (int) $partes[1];
    foreach ($alunos as $aluno) {
        if ($aluno['id'] === $alunoId) {
            responder($aluno);
        }
    }
    responder(['detail' => 'Aluno não encontrado'], 404);
}


if ($metodo === 'GET' && $caminho === '/alunos') {
    responder([
        'total' => count($alunos),
        'alunos' => $alunos,
    ]);
}

if ($metodo === 'GET' && $caminho === '/') {
    responder([
        'mensagem' => 'Bem-vindos à API de Alunos!',
        'rotas' => ['/sobre', '/alunos', '/indicadores', '/aprovados'],
    ]);
}

if ($metodo === 'GET' && $caminho === '/sobre') {
    responder([
        'disciplina' => 'Programação Web com PHP',
        'aula' => 1,
        'versao_api' => '1.0.0',
    ]);
}

responder(['detail' => 'Rota não encontrada'], 404);


function responder($dados, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, OPCOES_JSON);
    exit;
}
?>
