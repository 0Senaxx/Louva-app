<?php
include '../conexao.php'; // Ajuste o caminho se necessário

try {
    // Aqui você já tem a conexão $pdo do conexao.php
    $stmt = $pdo->query("SELECT * FROM louvores ORDER BY nome ASC");
    $louvores = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao consultar banco: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Base de Louvores</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <header class="bg-indigo-600 text-white py-4 shadow-md sticky top-0 z-50">
    <div class="px-4 flex justify-between items-center">
      <h1 class="text-lg font-semibold">🎵 Base de Louvores</h1>
      <a href="../01-Inicio/Painel.php" class="text-sm underline">Voltar</a>
    </div>
  </header>

  <main class="px-4 py-6">
    <a href="Adicionar/adicionar-louvor.php" class="block text-center w-full bg-indigo-500 text-white py-3 rounded-lg font-semibold mb-6 shadow-md hover:bg-indigo-600 transition">
      ➕ Adicionar Novo Louvor
    </a>

    <!-- Lista de louvores -->
    <div class="space-y-4">
      <?php if (count($louvores) === 0): ?>
        <p class="text-center text-gray-500">Nenhum louvor cadastrado.</p>
      <?php else: ?>
        <?php foreach ($louvores as $louvor): ?>
          <div class="bg-white p-4 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-1"><?= htmlspecialchars($louvor['nome']) ?></h2>
            <p class="text-sm text-gray-500 mb-2">
              <?= htmlspecialchars($louvor['cantor']) ?> • <?= htmlspecialchars($louvor['duracao']) ?: '-' ?>
            </p>

            <div class="text-sm space-y-1">
              <p><strong>Tipo:</strong> <?= htmlspecialchars($louvor['tipo']) ?: '-' ?></p>
              <p><strong>Tags:</strong> <?= htmlspecialchars($louvor['palavras_chave']) ?: '-' ?></p>
            </div>

            <div class="flex justify-between items-center mt-3 text-sm">
  <!-- Grupo de botões à esquerda -->
  <div class="flex gap-2">
    <a href="exibir-louvor.php?id=<?= $louvor['id'] ?>" 
      class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition text-center">
      Ver Letra e Vídeo
    </a>

    <a href="Editar/editar-louvor.php?id=<?= $louvor['id'] ?>" 
      class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">
      ✏️ Editar
    </a>
  </div>

  <!-- Botão de deletar à direita -->
  <form method="POST" action="deletarLouvor.php" onsubmit="return confirm('Tem certeza que deseja deletar este louvor?');">
    <input type="hidden" name="id" value="<?= $louvor['id'] ?>">
    <button type="submit" class="text-red-500">🗑️</button>
  </form>
</div>


          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>

  <footer class="text-center text-xs py-4 text-gray-500">
    &copy; 2025 Grupo de Louvor - Igreja Família de Deus
  </footer>

</body>
</html>
