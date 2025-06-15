<?php
include '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        $id = (int)$_POST['id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM louvores WHERE id = ?");
            $stmt->execute([$id]);

            header("Location: base-louvores.php");
            exit;
        } catch (PDOException $e) {
            die("Erro ao deletar louvor: " . $e->getMessage());
        }
    } else {
        die("ID do louvor não informado.");
    }
} else {
    die("Método inválido.");
}
