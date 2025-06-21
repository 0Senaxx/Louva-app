<?php
session_start();

// Configuração da conexão PDO (ajuste conforme seu banco)
include '../conexao.php';

// Salvar liturgia (quando form submetido)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salvar_liturgia'])) {
    // Dados liturgia
    $data_culto = $_POST['data_culto'] ?? null;
    $tipo_culto = $_POST['tipo_culto'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';

    // Louvores JSON enviados via input hidden
    $louvores_json = $_POST['louvores_json'] ?? '[]';
    $louvores_arr = json_decode($louvores_json, true);

    if ($data_culto && !empty($louvores_arr)) {
        try {
            // Inserir liturgia
            $stmt = $pdo->prepare("INSERT INTO liturgias (data_culto, tipo_culto, observacoes, criada_em) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$data_culto, $tipo_culto, $observacoes]);
            $liturgia_id = $pdo->lastInsertId();

            // Inserir louvores da liturgia
            $ordem = 1;
            $stmtIns = $pdo->prepare("INSERT INTO liturgia_louvores (liturgia_id, louvor_id, ordem, momento) VALUES (?, ?, ?, ?)");

            foreach ($louvores_arr as $momento => $louvores) {
                foreach ($louvores as $louvor_id) {
                    $stmtIns->execute([$liturgia_id, $louvor_id, $ordem++, $momento]);
                }
            }

            $_SESSION['msg'] = "Liturgia salva com sucesso!";
            header("Location: criar_liturgia.php");
            exit;
        } catch (Exception $e) {
            $erro = "Erro ao salvar liturgia: " . $e->getMessage();
        }
    } else {
        $erro = "Informe a data do culto e adicione pelo menos um louvor.";
    }
}

// Pesquisar louvores
$pesquisa = $_GET['pesquisa'] ?? '';
$louvoresEncontrados = [];
if ($pesquisa) {
    $stmt = $pdo->prepare("SELECT id, nome, cantor FROM louvores WHERE nome LIKE ? ORDER BY nome LIMIT 20");
    $stmt->execute(["%$pesquisa%"]);
    $louvoresEncontrados = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Criar Liturgia de Louvores</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { display: flex; gap: 20px; }
        .lista, .momentos { border: 1px solid #ccc; padding: 10px; width: 30%; }
        h2 { font-size: 18px; }
        ul { list-style: none; padding: 0; min-height: 150px; background: #f9f9f9; }
        li { padding: 6px; margin: 3px 0; background: #eee; cursor: pointer; }
        button { margin-left: 10px; }
        .mensagem { padding: 10px; margin-bottom: 20px; border-radius: 5px; }
        .erro { background-color: #fdd; border: 1px solid #f99; }
        .sucesso { background-color: #dfd; border: 1px solid #9f9; }
    </style>
</head>
<body>

<h1>Criar Liturgia de Louvores</h1>

<?php if (!empty($_SESSION['msg'])): ?>
    <div class="mensagem sucesso"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
<?php endif; ?>

<?php if (!empty($erro)): ?>
    <div class="mensagem erro"><?= $erro; ?></div>
<?php endif; ?>

<!-- Form liturgia -->
<form method="post" id="formLiturgia" onsubmit="return prepararEnvio()">
    <label>Data do Culto: <input type="date" name="data_culto" ></label><br><br>
    <label>Tipo de Culto: <input type="text" name="tipo_culto" placeholder="Ex: Domingo, Quarta..." ></label><br><br>
    <label>Observações:<br>
        <textarea name="observacoes" rows="3" cols="40" placeholder="Observações..."></textarea>
    </label><br><br>

    <div class="container">

        <!-- Pesquisa louvores -->
        <div class="lista">
            <h2>Pesquisar Louvores</h2>
            <form method="get" action="">
                <input type="text" name="pesquisa" placeholder="Nome do louvor" value="<?= htmlspecialchars($pesquisa) ?>" />
                <button type="submit">Pesquisar</button>
            </form>

            <ul id="louvoresEncontrados">
                <?php foreach ($louvoresEncontrados as $louvor): ?>
                    <li data-id="<?= $louvor['id'] ?>" data-nome="<?= htmlspecialchars($louvor['nome']) ?>">
                        <?= htmlspecialchars($louvor['nome']) ?> - <?= htmlspecialchars($louvor['cantor']) ?>
                        <button type="button" onclick="adicionarLouvor(<?= $louvor['id'] ?>, '<?= addslashes(htmlspecialchars($louvor['nome'])) ?>')">Adicionar</button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Louvores adicionados -->
        <div class="lista">
            <h2>Louvores na Liturgia</h2>
            <p>Clique e arraste para mover entre os momentos</p>
            <div style="display:flex; gap: 10px;">
                <div class="momentos" ondrop="drop(event)" ondragover="allowDrop(event)" id="abrir">
                    <h3>Abrir Culto</h3>
                    <ul id="listaAbrir" ondrop="drop(event)" ondragover="allowDrop(event)"></ul>
                </div>
                <div class="momentos" ondrop="drop(event)" ondragover="allowDrop(event)" id="dizimos">
                    <h3>Dízimos e Ofertas</h3>
                    <ul id="listaDizimos" ondrop="drop(event)" ondragover="allowDrop(event)"></ul>
                </div>
                <div class="momentos" ondrop="drop(event)" ondragover="allowDrop(event)" id="fechar">
                    <h3>Fechar Culto</h3>
                    <ul id="listaFechar" ondrop="drop(event)" ondragover="allowDrop(event)"></ul>
                </div>
            </div>
        </div>

    </div>

    <input type="hidden" name="louvores_json" id="louvores_json" />
    <br>
    <button type="submit" name="salvar_liturgia">Salvar Liturgia</button>
</form>

<script>
// Guardar louvores na liturgia separando por momento
const liturgia = {
    abrir: [],
    dizimos: [],
    fechar: []
};

// Adicionar louvor ao Abrir culto por padrão
function adicionarLouvor(id, nome) {
    // Evita duplicados
    if (liturgia.abrir.includes(id)) return alert('Louvor já adicionado!');

    liturgia.abrir.push(id);
    renderLista('abrir');
}

// Renderiza a lista de um momento
function renderLista(momento) {
    const ul = document.getElementById('lista' + capitalize(momento));
    ul.innerHTML = '';

    liturgia[momento].forEach(id => {
        const li = document.createElement('li');
        li.textContent = `Louvor ID ${id}`;
        li.setAttribute('draggable', 'true');
        li.dataset.id = id;
        li.ondragstart = drag;
        li.onclick = () => {
            // Remover ao clicar
            if (confirm('Remover louvor deste momento?')) {
                removerLouvor(momento, id);
            }
        };
        ul.appendChild(li);
    });
}

function removerLouvor(momento, id) {
    liturgia[momento] = liturgia[momento].filter(lid => lid != id);
    renderLista(momento);
}

// Funções para drag and drop
function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.dataset.id);
    ev.dataTransfer.setData("from", ev.target.parentElement.id);
}

function drop(ev) {
    ev.preventDefault();
    const id = ev.dataTransfer.getData("text");
    const from = ev.dataTransfer.getData("from"); // ex: listaAbrir
    const to = ev.target.closest('ul').id;

    // remover do array antigo
    let fromMomento = from.replace('lista','').toLowerCase();
    let toMomento = to.replace('lista','').toLowerCase();

    if(fromMomento === toMomento) return; // mesmo lugar

    // remove do de onde estava
    liturgia[fromMomento] = liturgia[fromMomento].filter(lid => lid != id);

    // adiciona ao novo momento se não estiver
    if (!liturgia[toMomento].includes(Number(id))) {
        liturgia[toMomento].push(Number(id));
    }

    renderLista(fromMomento);
    renderLista(toMomento);
}

function capitalize(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
}

// Antes de enviar o form, atualizar input hidden com louvores JSON
function prepararEnvio() {
    // remover louvores repetidos em todos os momentos (precaução)
    ['abrir','dizimos','fechar'].forEach(momento => {
        liturgia[momento] = Array.from(new Set(liturgia[momento]));
    });

    // verificar se tem ao menos um louvor
    if (!liturgia.abrir.length && !liturgia.dizimos.length && !liturgia.fechar.length) {
        alert('Adicione pelo menos um louvor.');
        return false;
    }

    document.getElementById('louvores_json').value = JSON.stringify(liturgia);
    return true;
}
</script>

</body>
</html>
