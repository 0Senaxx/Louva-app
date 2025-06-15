<?php
include '../../conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Método inválido.");
}

$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? '';
$cantor = $_POST['cantor'] ?? '';
$duracao = $_POST['duracao'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$video_url = $_POST['video_url'] ?? '';
$playback_url = $_POST['playback_url'] ?? '';
$letra = $_POST['letra'] ?? '';
$palavras_chave = $_POST['palavras_chave'] ?? '';

if (!$id || !is_numeric($id) || empty($nome) || empty($cantor)) {
    die("Dados obrigatórios faltando ou inválidos.");
}

try {
    $stmt = $pdo->prepare("
        UPDATE louvores SET
        nome = ?, cantor = ?, duracao = ?, tipo = ?, video_url = ?, playback_url = ?, letra = ?, palavras_chave = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $nome, $cantor, $duracao, $tipo, $video_url, $playback_url, $letra, $palavras_chave, $id
    ]);

    header("Location: editar-louvor.php?id=" . $id . "&sucesso=1");
    exit;

} catch (PDOException $e) {
    die("Erro ao atualizar louvor: " . $e->getMessage());
}
?>
