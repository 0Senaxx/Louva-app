<?php
include '../conexao.php';

function youtubeEmbedUrl($url) {
    // Tenta extrair o ID do vídeo do YouTube a partir do link normal
    if (!$url) return null;

    // Se for um link embed já, retorna direto
    if (strpos($url, 'youtube.com/embed/') !== false) {
        return $url;
    }

    // Parseia a query do URL para pegar o parâmetro 'v'
    $query = parse_url($url, PHP_URL_QUERY);
    parse_str($query, $params);

    if (isset($params['v'])) {
        return "https://www.youtube.com/embed/" . $params['v'];
    }

    // Para links no formato youtu.be/ID
    if (preg_match('/youtu\.be\/([^\?\/]+)/', $url, $matches)) {
        return "https://www.youtube.com/embed/" . $matches[1];
    }

    return null; // não conseguiu extrair
}

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

$playbackEmbedUrl = youtubeEmbedUrl($louvor['playback_url']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($louvor['nome']) ?> - Playback e Letra</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

<header class="bg-indigo-600 text-white py-4 shadow-md sticky top-0 z-50">
  <div class="px-4 flex justify-between items-center">
    <h1 class="text-lg font-semibold"><?= htmlspecialchars($louvor['nome']) ?></h1>
    <a href="base-louvores.php" class="text-sm underline">Voltar</a>
  </div>
</header>

<main class="px-4 py-6 max-w-4xl mx-auto space-y-6">

  <?php if ($playbackEmbedUrl): ?>
    <div class="aspect-w-16 aspect-h-9">
      <iframe
        src="<?= htmlspecialchars($playbackEmbedUrl) ?>"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen
        class="w-full h-96 rounded-lg shadow-md"
      ></iframe>
    </div>
  <?php else: ?>
    <p class="text-center text-red-500 font-semibold">Link de playback inválido ou não disponível.</p>
  <?php endif; ?>

  <section>
    <h2 class="text-xl font-semibold mb-2">Letra do Louvor</h2>
    <pre class="whitespace-pre-wrap bg-white p-4 rounded-lg shadow-md"><?= htmlspecialchars($louvor['letra']) ?: "Letra não disponível." ?></pre>
  </section>

</main>

<footer class="text-center text-xs py-4 text-gray-500">
  &copy; 2025 Grupo de Louvor - Igreja Local
</footer>

</body>
</html>
