<?php
require '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $cantor = $_POST['cantor'];
    $duracao = $_POST['duracao'];
    $tipo = $_POST['tipo'];
    $data_ultimo_culto = $_POST['data_ultimo_culto'];
    $video_url = $_POST['video_url'];
    $playback_url = $_POST['playback_url'];
    $letra = $_POST['letra'];
    $palavras_chave = $_POST['palavras_chave'];

    $stmt = $pdo->prepare("INSERT INTO louvores 
        (nome, cantor, duracao, tipo, data_ultimo_culto, video_url, playback_url, letra, palavras_chave) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $nome, $cantor, $duracao, $tipo, $data_ultimo_culto,
        $video_url, $playback_url, $letra, $palavras_chave
    ]);

    header("Location: base-louvores.php"); // depois alteramos para exibir os dados via PHP
    exit();
}
?>
