<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Grupo de Louvor - Painel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 text-gray-800">

    <header class="bg-indigo-600 text-white py-4 shadow-md">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <h1 class="text-2xl font-bold inline-block">Painel do Grupo de Louvor</h1>
        </div>
    </header>


    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="grid md:grid-cols-3 gap-6">

            <a href="../02-Louvores/base-louvores.php"
                class="bg-white rounded-2xl shadow p-6 hover:bg-indigo-100 transition">
                <h2 class="text-xl font-semibold mb-2">🎵 Base de Louvores</h2>
                <p>Gerencie e cadastre os louvores com todos os detalhes.</p>
            </a>

            <a href="../Liturgias/criar-liturgia.php"
                class="bg-white rounded-2xl shadow p-6 hover:bg-indigo-100 transition">
                <h2 class="text-xl font-semibold mb-2">🗓️ Organizar Liturgia</h2>
                <p>Monte a liturgia dos cultos e envie via WhatsApp.</p>
            </a>

            <a href="relatorios.html" class="bg-white rounded-2xl shadow p-6 hover:bg-indigo-100 transition">
                <h2 class="text-xl font-semibold mb-2">📊 Relatórios</h2>
                <p>Veja os louvores mais cantados e os esquecidos.</p>
            </a>

            <a href="ensaios.html" class="bg-white rounded-2xl shadow p-6 hover:bg-indigo-100 transition">
                <h2 class="text-xl font-semibold mb-2">🎤 Lista de Ensaios</h2>
                <p>Organize os louvores que precisam ser ensaiados.</p>
            </a>

            <a href="configuracoes.html" class="bg-white rounded-2xl shadow p-6 hover:bg-indigo-100 transition">
                <h2 class="text-xl font-semibold mb-2">⚙️ Configurações</h2>
                <p>Preferências do sistema e formatações de envio.</p>
            </a>

        </div>
    </main>

    <footer class="text-center py-4 text-sm text-gray-500">
        &copy; 2025 Grupo de Louvor - Igreja Família de Deus
    </footer>

</body>

</html>