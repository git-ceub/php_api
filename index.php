<?php

// Importa a classe de conexão com o banco e a classe Tutor (CRUD de tutores)
include 'Conexao.php';
include 'Tutor.php';

// Opções usadas no json_encode: formatado, com acentos e barras legíveis,
// e mantendo decimais como 1.0 em vez de 1
const OPCOES_JSON = JSON_PRETTY_PRINT
    | JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_PRESERVE_ZERO_FRACTION;


// Método HTTP da requisição (GET, POST, PUT, DELETE...)
$metodo = $_SERVER['REQUEST_METHOD'];
// Caminho da URL, sem a query string (ex.: /tutores)
$caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove da URL a pasta onde o script está (ex.: /php_api), para que as
// rotas sejam comparadas apenas pelo trecho final (ex.: /tutores)
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
if ($base !== '' && str_starts_with($caminho, $base)) {
    $caminho = substr($caminho, strlen($base)) ?: '/';
}

// Cria o objeto Tutor e já carrega a lista de tutores do banco
$tutor = new Tutor();
$tutores = $tutor->listar();

// GET /tutores -> retorna o total e a lista de tutores
if ($metodo === 'GET' && $caminho === '/tutores') {
    responder([
        'total' => count($tutores),
        'tutores' => $tutores,
    ]);
}

// POST /tutores -> cria um novo tutor a partir do JSON enviado (retorna 201 Created)
if ($metodo === 'POST' && $caminho === '/tutores') {
    $dados = lerJson();       // lê o corpo da requisição
    validarTutor($dados);     // garante nome e e-mail válidos
    $id = $tutor->gravar($dados);
    responder(['id' => $id, 'nome' => $dados['nome'], 'email' => $dados['email']], 201);
}

// PUT /tutores/{id} -> atualiza o tutor; $partes[1] contém o id capturado pela regex
if ($metodo === 'PUT' && preg_match('#^/tutores/(\d+)$#', $caminho, $partes)) {
    $dados = lerJson();
    validarTutor($dados);
    $id = (int) $partes[1];
    if ($tutor->atualizar($id, $dados)) {
        responder(['id' => $id, 'nome' => $dados['nome'], 'email' => $dados['email']]);
    }
    responder(['detail' => 'Tutor não encontrado ou sem alterações'], 404);
}

// DELETE /tutores/{id} -> exclui o tutor; retorna 204 (sem conteúdo) se der certo
if ($metodo === 'DELETE' && preg_match('#^/tutores/(\d+)$#', $caminho, $partes)) {
    if ($tutor->excluir((int) $partes[1])) {
        http_response_code(204);
        exit;
    }
    responder(['detail' => 'Tutor não encontrado'], 404);
}

// GET / -> mensagem de boas-vindas e lista de rotas disponíveis
if ($metodo === 'GET' && $caminho === '/') {
    responder([
        'mensagem' => 'Bem-vindos à API de Alunos!',
        'rotas' => ['/sobre', '/tutores','/indicadores', '/aprovados'],
    ]);
}

// GET /sobre -> informações gerais da API
if ($metodo === 'GET' && $caminho === '/sobre') {
    responder([
        'disciplina' => 'Programação Web com PHP',
        'aula' => 1,
        'versao_api' => '1.0.0',
    ]);
}

// Nenhuma rota anterior respondeu: retorna 404
responder(['detail' => 'Rota não encontrada'], 404);


// Lê o corpo da requisição, converte de JSON para array e
// devolve erro 400 se o JSON for inválido
function lerJson(): array
{
    $dados = json_decode(file_get_contents('php://input'), true);
    if (!is_array($dados)) {
        responder(['detail' => 'Corpo da requisição deve ser um JSON válido'], 400);
    }
    return $dados;
}

// Valida os dados do tutor: 'nome' e 'email' obrigatórios (texto)
// e e-mail em formato válido; em caso de erro responde 422
function validarTutor(array $dados): void
{
    foreach (['nome', 'email'] as $campo) {
        if (empty($dados[$campo]) || !is_string($dados[$campo])) {
            responder(['detail' => "Campo '{$campo}' é obrigatório"], 422);
        }
    }
    if (!filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
        responder(['detail' => "Campo 'email' inválido"], 422);
    }
}

// Envia a resposta em JSON com o status HTTP informado e encerra o script
function responder($dados, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, OPCOES_JSON);
    exit;
}
?>
