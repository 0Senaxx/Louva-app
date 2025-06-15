<?php
include '../../conexao.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID do louvor inválido.");
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM louvores WHERE id = ?");
    $stmt->execute([$id]);
    $louvor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$louvor) {
        die("Louvor não encontrado.");
    }
} catch (PDOException $e) {
    die("Erro ao consultar banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Louvor - <?= htmlspecialchars($louvor['nome']) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<header class="bg-indigo-600 text-white py-4 shadow-md sticky top-0 z-50">
  <div class="px-4 flex justify-between items-center">
    <h1 class="text-lg font-semibold">✏️ Editar Louvor</h1>
    <a href="../base-louvores.php" class="text-sm underline">Voltar</a>
  </div>
</header>

<main class="px-4 py-6 max-w-3xl mx-auto">
  <form action="salvarEdicaoLouvor.php" method="POST" class="space-y-4">
    <input type="hidden" name="id" value="<?= $louvor['id'] ?>">

    <div>
      <label class="block font-medium mb-1">Nome do Louvor</label>
      <input type="text" name="nome" required
        value="<?= htmlspecialchars($louvor['nome']) ?>"
        class="w-full px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-indigo-400" />
    </div>

    <div>
      <label class="block font-medium mb-1">Cantor/Banda</label>
      <input type="text" name="cantor" required
        value="<?= htmlspecialchars($louvor['cantor']) ?>"
        class="w-full px-4 py-2 rounded-lg border" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div>
        <label class="block font-medium mb-1">Duração (min)</label>
        <input type="text" name="duracao" placeholder="Ex: 4:35"
          value="<?= htmlspecialchars($louvor['duracao']) ?>"
          class="w-full px-4 py-2 rounded-lg border" />
      </div>

      <div>
        <label class="block font-medium mb-1">Tipo de Louvor</label>
        <select name="tipo" class="w-full px-4 py-2 rounded-lg border">
          <option value="">Selecione</option>
          <?php
          $tipos = ["Celebração", "Reflexão", "Adoração", "Missões"];
          foreach ($tipos as $tipo) {
              $selected = ($louvor['tipo'] === $tipo) ? "selected" : "";
              echo "<option value=\"$tipo\" $selected>$tipo</option>";
          }
          ?>
        </select>
      </div>
    </div>

    <div>
      <label class="block font-medium mb-1">Link do vídeo cantado (YouTube)</label>
      <input type="url" name="video_url" placeholder="https://..."
        value="<?= htmlspecialchars($louvor['video_url']) ?>"
        class="w-full px-4 py-2 rounded-lg border" />
    </div>

    <div>
      <label class="block font-medium mb-1">Link do playback</label>
      <input type="url" name="playback_url" placeholder="https://..."
        value="<?= htmlspecialchars($louvor['playback_url']) ?>"
        class="w-full px-4 py-2 rounded-lg border" />
    </div>

    <div>
      <label class="block font-medium mb-1">Letra do Louvor</label>
      <textarea name="letra" rows="5" class="w-full px-4 py-2 rounded-lg border resize-none" placeholder="Digite a letra completa..."><?= htmlspecialchars($louvor['letra']) ?></textarea>
    </div>

    <div>
      <label class="block font-medium mb-1">Palavras-chave</label>
      <input type="text" name="palavras_chave" placeholder="Ex: animado, fogo, batalha, ceia"
        value="<?= htmlspecialchars($louvor['palavras_chave']) ?>"
        class="w-full px-4 py-2 rounded-lg border" />
      <p class="text-xs text-gray-500 mt-1">Use vírgulas para separar as palavras.</p>
    </div>

    <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold shadow hover:bg-indigo-700 transition">
      Salvar Alterações
    </button>
  </form>
</main>

<footer class="text-center text-xs py-4 text-gray-500">
  &copy; 2025 Grupo de Louvor - Igreja Local
</footer>

</body>
</html>
