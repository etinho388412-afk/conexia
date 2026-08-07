// Caminho: conexia/public/js/avatar.js
// Renderização e customização do avatar vetorial (SVG) do estudante

const AvatarConexia = {
  /** Renderiza o SVG do avatar dentro do elemento alvo, a partir dos dados salvos */
  renderizar(alvoId, dados) {
    const alvo = document.getElementById(alvoId);
    if (!alvo) return;

    alvo.innerHTML = `
      <svg viewBox="0 0 200 200" width="180" height="180" role="img" aria-label="Avatar do estudante">
        <circle cx="100" cy="70" r="45" fill="${dados.cor_pele || '#f2c9a0'}" />
        <path d="M55,55 Q100,20 145,55 L145,70 Q100,50 55,70 Z" fill="${dados.cabelo_cor || '#3b2b20'}" />
        <rect x="60" y="120" width="80" height="60" rx="14" fill="${dados.cor_roupa || '#4f46e5'}" />
        <circle cx="82" cy="65" r="5" fill="#1e293b" />
        <circle cx="118" cy="65" r="5" fill="#1e293b" />
        <path d="M85,85 Q100,95 115,85" stroke="#1e293b" stroke-width="3" fill="none" stroke-linecap="round" />
      </svg>
    `;
  },

  /** Envia as alterações de customização para o back-end */
  async salvar(dados) {
    return Conexia.api('/src/api/avatar.php', {
      method: 'POST',
      body: JSON.stringify(dados),
    });
  },

  /** Preenche a UI de seleção com os itens do inventário desbloqueado */
  montarSeletorDeItens(containerId, itens, aoSelecionar) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = '';
    itens.forEach((item) => {
      const botao = document.createElement('button');
      botao.className = 'btn-secundario';
      botao.type = 'button';
      botao.textContent = item.nome;
      botao.setAttribute('aria-label', `Equipar ${item.nome}`);
      botao.addEventListener('click', () => aoSelecionar(item));
      container.appendChild(botao);
    });
  },
};
