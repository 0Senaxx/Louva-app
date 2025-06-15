<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Montar Liturgia</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .dropzone {
      min-height: 100px;
      border: 2px dashed #bbb;
      border-radius: 0.5rem;
      padding: 0.5rem;
      background: #f9fafb;
      transition: background-color 0.3s ease;
    }
    .dropzone.dragover {
      background: #dbeafe;
      border-color: #3b82f6;
    }
    .louvor-item {
      background: white;
      padding: 0.5rem 0.75rem;
      margin-bottom: 0.5rem;
      border-radius: 0.375rem;
      box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
      cursor: grab;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .louvor-item.dragging {
      opacity: 0.5;
    }
    .btn-remove {
      cursor: pointer;
      color: #ef4444;
      font-weight: bold;
      font-size: 1.2rem;
      line-height: 1;
      user-select: none;
      margin-left: 0.75rem;
    }
  </style>
</head>
<body class="bg-gray-100 p-4">

  <div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-4">Montar Liturgia</h1>

    <form id="formLiturgia" method="POST" action="salvarLiturgia.php">

      <div class="mb-4">
        <label for="dataCulto" class="block font-medium mb-1">Data do Culto:</label>
        <input type="date" id="dataCulto" name="data_culto" required
          class="border rounded px-3 py-2 w-full max-w-xs" />
      </div>

      <div class="mb-4">
        <label for="tipoCulto" class="block font-medium mb-1">Tipo de Culto:</label>
        <select id="tipoCulto" name="tipo_culto" required
          class="border rounded px-3 py-2 w-full max-w-xs">
          <option value="">-- Selecione --</option>
          <option value="santa_ceia">Santa Ceia</option>
          <option value="doutrina">Doutrina</option>
          <option value="familia">Família</option>
          <option value="missoes">Missões</option>
        </select>
      </div>

      <!-- Containers de louvores -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <section>
          <h2 class="font-semibold text-lg mb-2">
            Abertura 
            <span id="tempoAbertura" class="text-sm text-gray-500">(0:00)</span>
          </h2>
          <div id="abertura" class="dropzone" data-max="2"></div>
        </section>

        <section>
          <h2 class="font-semibold text-lg mb-2">
            Dízimos e Ofertas
            <span id="tempoDizimos" class="text-sm text-gray-500">(0:00)</span>
          </h2>
          <div id="dizimos" class="dropzone" data-max="1"></div>
        </section>

        <section>
          <h2 class="font-semibold text-lg mb-2">
            Reflexão 
            <span id="tempoReflexao" class="text-sm text-gray-500">(0:00)</span>
          </h2>
          <div id="reflexao" class="dropzone" data-max="2"></div>
        </section>

        <section>
          <h2 class="font-semibold text-lg mb-2">
            Fechar o Culto 
            <span id="tempoFechar" class="text-sm text-gray-500">(0:00)</span>
          </h2>
          <div id="fechar" class="dropzone" data-max="4"></div>
        </section>

      </div>

      <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded hover:bg-indigo-700 transition font-semibold">
        💾 Salvar Liturgia
      </button>
    </form>
  </div>

  <script>
    // Exemplo de louvores para teste - normalmente você vai carregar do banco via PHP
    const louvoresDisponiveis = [
      { id: 1, nome: "Louvor A", duracao: "4:15" },
      { id: 2, nome: "Louvor B", duracao: "3:30" },
      { id: 3, nome: "Louvor C", duracao: "5:00" },
      { id: 4, nome: "Louvor D", duracao: "6:20" },
      { id: 5, nome: "Louvor E", duracao: "2:45" },
      { id: 6, nome: "Louvor F", duracao: "4:10" },
      { id: 7, nome: "Louvor G", duracao: "3:55" }
    ];

    // Converter string de duração mm:ss para segundos
    function duracaoParaSegundos(str) {
      const [min, seg] = str.split(':').map(Number);
      return min * 60 + seg;
    }
    // Converter segundos para mm:ss
    function segundosParaDuracao(segundos) {
      const m = Math.floor(segundos / 60);
      const s = segundos % 60;
      return `${m}:${s.toString().padStart(2, '0')}`;
    }

    // Atualiza tempo total em cada dropzone
    function atualizaTempo(dropzoneId) {
      const container = document.getElementById(dropzoneId);
      const tempoSpan = document.getElementById(`tempo${dropzoneId.charAt(0).toUpperCase() + dropzoneId.slice(1)}`);
      let totalSegundos = 0;
      [...container.children].forEach(item => {
        totalSegundos += duracaoParaSegundos(item.dataset.duracao);
      });
      tempoSpan.textContent = `(${segundosParaDuracao(totalSegundos)})`;
    }

    // Criar elemento de louvor
    function criaLouvorElement(louvor) {
      const div = document.createElement('div');
      div.className = 'louvor-item';
      div.draggable = true;
      div.dataset.id = louvor.id;
      div.dataset.duracao = louvor.duracao;
      div.textContent = louvor.nome;

      const btnRemove = document.createElement('span');
      btnRemove.textContent = '❌';
      btnRemove.className = 'btn-remove';
      btnRemove.title = "Remover";
      btnRemove.onclick = () => {
        div.remove();
        atualizaTempo(div.parentElement.id);
      };

      div.appendChild(btnRemove);

      div.addEventListener('dragstart', (e) => {
        div.classList.add('dragging');
        e.dataTransfer.setData('text/plain', louvor.id);
        e.dataTransfer.effectAllowed = 'move';
      });
      div.addEventListener('dragend', () => {
        div.classList.remove('dragging');
      });

      return div;
    }

    // Inicializa a lista de louvores disponíveis para arrastar
    function carregaLouvoresDisponiveis() {
      const lista = document.createElement('div');
      lista.id = 'louvoresDisponiveis';
      lista.className = 'dropzone';
      lista.style.border = '2px solid #999';
      lista.style.minHeight = '150px';
      lista.style.marginBottom = '1rem';

      louvoresDisponiveis.forEach(l => {
        lista.appendChild(criaLouvorElement(l));
      });

      document.querySelector('form').insertBefore(lista, document.querySelector('.grid'));
      const titulo = document.createElement('h2');
      titulo.textContent = 'Louvores Disponíveis';
      titulo.className = 'font-semibold text-lg mb-2';
      lista.before(titulo);

      habilitaDropzones();
    }

    // Habilita drag & drop para todas as dropzones
    function habilitaDropzones() {
      const zones = document.querySelectorAll('.dropzone');

      zones.forEach(zone => {
        zone.addEventListener('dragover', e => {
          e.preventDefault();
          zone.classList.add('dragover');
        });
        zone.addEventListener('dragleave', () => {
          zone.classList.remove('dragover');
        });
        zone.addEventListener('drop', e => {
          e.preventDefault();
          zone.classList.remove('dragover');
          const louvorId = e.dataTransfer.getData('text/plain');
          // Verifica se louvor já está na dropzone (não permite duplicar)
          if ([...zone.children].some(child => child.dataset.id === louvorId)) {
            alert('Este louvor já está nesta lista.');
            return;
          }
          // Limite de louvores na dropzone
          const max = parseInt(zone.dataset.max) || Infinity;
          if (zone.children.length >= max) {
            alert(`Limite máximo de ${max} louvor(es) nesta seção.`);
            return;
          }

          // Pega o louvor da lista disponível ou de outra dropzone
          let louvorObj = louvoresDisponiveis.find(l => String(l.id) === louvorId);
          if (!louvorObj) {
            // pode estar vindo de outra dropzone (reorganizando)
            // nesse caso o elemento já existe, só muda de container
            const elemento = document.querySelector(`.louvor-item.dragging`) || 
                             document.querySelector(`.louvor-item[data-id="${louvorId}"]`);
            if (elemento) {
              // Se está vindo de outra dropzone, remove do antigo container
              if (elemento.parentElement !== zone) {
                elemento.parentElement.removeChild(elemento);
                zone.appendChild(elemento);
                atualizaTempo(elemento.parentElement.id);
                atualizaTempo(zone.id);
              }
              return;
            }
            return; // se não achou louvor, não faz nada
          }

          // Remove louvor da lista disponíveis
          const listaDisp = document.getElementById('louvoresDisponiveis');
          const elemDisp = listaDisp.querySelector(`.louvor-item[data-id="${louvorId}"]`);
          if (elemDisp) elemDisp.remove();

          // Cria novo elemento e adiciona na dropzone
          const novoElem = criaLouvorElement(louvorObj);
          zone.appendChild(novoElem);
          atualizaTempo(zone.id);
        });
      });
    }

    // Atualiza duração sempre que muda tipo de culto (para mostrar/ocultar reflexão)
    function atualizaVisibilidadeReflexao() {
      const tipo = document.getElementById('tipoCulto').value;
      const reflexao = document.getElementById('reflexao');
      if (tipo === 'santa_ceia') {
        reflexao.parentElement.style.display = 'block';
      } else {
        reflexao.parentElement.style.display = 'none';
        // Remove louvores se trocar pra outro tipo que não tem reflexão
        while(reflexao.firstChild) reflexao.removeChild(reflexao.firstChild);
        atualizaTempo('reflexao');
      }
    }

    // Enviar a liturgia via POST, pegando os IDs dos louvores em cada seção
    document.getElementById('formLiturgia').addEventListener('submit', function (e) {
      // Antes de enviar, cria campos hidden com os louvores selecionados
      ['abertura','dizimos','reflexao','fechar'].forEach(secId => {
        // Remove campos antigos
        [...document.querySelectorAll(`input[name^="${secId}[]"]`)].forEach(i => i.remove());

        const container = document.getElementById(secId);
        Array.from(container.children).forEach((item, idx) => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = `${secId}[]`;
          input.value = item.dataset.id;
          this.appendChild(input);
        });
      });
    });

    // Inicialização
    window.onload = () => {
      atualizaVisibilidadeReflexao();
      carregaLouvoresDisponiveis();
      habilitaDropzones();

      document.getElementById('tipoCulto').addEventListener('change', () => {
        atualizaVisibilidadeReflexao();
      });
    };
  </script>

</body>
</html>
