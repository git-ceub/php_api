<?php

class Tutor {

    private PDO $conexao;

    public function __construct() {
        $this->conexao = Conexao::getConnection();
    }

    public function listar(){
        $sql = "SELECT * FROM tutor";
        $smt =  $this->conexao->prepare($sql);
        $smt->execute();
        return $smt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function gravar(array $dados): int {
        $sql = "INSERT INTO tutor (nome, email) VALUES (:nome, :email)";
        $smt = $this->conexao->prepare($sql);
        $smt->bindValue(':nome', $dados['nome']);
        $smt->bindValue(':email', $dados['email']);
        $smt->execute();
        return (int) $this->conexao->lastInsertId();
    }

    public function atualizar(int $id, array $dados): bool {
        $sql = "UPDATE tutor SET nome = :nome, email = :email WHERE id = :id";
        $smt = $this->conexao->prepare($sql);
        $smt->bindValue(':nome', $dados['nome']);
        $smt->bindValue(':email', $dados['email']);
        $smt->bindValue(':id', $id, PDO::PARAM_INT);
        $smt->execute();
        return $smt->rowCount() > 0;
    }

    public function excluir(int $id): bool {
        $sql = "DELETE FROM tutor WHERE id = :id";
        $smt = $this->conexao->prepare($sql);
        $smt->bindValue(':id', $id, PDO::PARAM_INT);
        $smt->execute();
        return $smt->rowCount() > 0;
    }

}

?>