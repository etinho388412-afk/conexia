// Caminho: conexia/public/js/trilha.js
// Renderiza o mapa de progresso (skill tree / linha de evolução) em Canvas simples

const TrilhaConexia = {
  async carregar() {
    const dados = await Conexia.api('/src/api/trilha.php');
    this.renderizarBarraDeXp(dados.progresso);
    this.renderizarConquistas(dados.conquistas);
    this.renderizarMapaDeNos(dados.progresso.nivel_atual);
  },

  renderizarBarraDeXp(progresso) {
    const container = document.getElementById('trilha-xp');
    if (!container) return;

    const percentual = Math.min(100, (progresso.xp_total / progresso.xp_proximo_nivel) * 100);

    container.innerHTML = `
      <p><strong>${progresso.patente_atual}</strong> — Nível ${progresso.nivel_atual}</p>
      <div style="background:#e2e8f0; border-radius:999px; height:14px; overflow:hidden;">
        <div style="width:${percentual}%; background:#4f46e5; height:100%;"
             role="progressbar" aria-valuenow="${percentual}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <p style="font-size:0.85rem; color:#64748b;">${progresso.xp_total} / ${progresso.xp_proximo_nivel} XP</p>
    `;
  },

  renderizarConquistas(conquistas) {
    const container = document.getElementById('trilha-conquistas');
    if (!container) return;

    container.innerHTML = conquistas.map((c) => `
      <div class="card" style="text-align:center;">
        <img src="/${c.icone}" alt="" width="40" height="40" />
        <p><strong>${c.nome}</strong></p>
        <p style="font-size:0.8rem; color:#64748b;">${c.descricao}</p>
      </div>
    `).join('') || '<p>Nenhuma conquista ainda. Continue evoluindo!</p>';
  },

  /** Mapa de nós simples: um nó por nível, conectados em linha, estilo RPG */
  renderizarMapaDeNos(nivelAtual) {
    const canvas = document.getElementById('trilha-mapa');
    if (!canvas || !canvas.getContext) return;

    const ctx = canvas.getContext('2d');
    const totalNos = 15;
    const espacamento = canvas.width / (totalNos + 1);

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    for (let i = 1; i <= totalNos; i++) {
      const x = espacamento * i;
      const y = canvas.height / 2 + Math.sin(i) * 30;

      if (i < totalNos) {
        const proximoX = espacamento * (i + 1);
        const proximoY = canvas.height / 2 + Math.sin(i + 1) * 30;
        ctx.strokeStyle = i < nivelAtual ? '#4f46e5' : '#cbd5e1';
        ctx.lineWidth = 3;
        ctx.beginPath();
        ctx.moveTo(x, y);
        ctx.lineTo(proximoX, proximoY);
        ctx.stroke();
      }

      ctx.fillStyle = i <= nivelAtual ? '#4f46e5' : '#e2e8f0';
      ctx.beginPath();
      ctx.arc(x, y, 12, 0, Math.PI * 2);
      ctx.fill();

      ctx.fillStyle = i <= nivelAtual ? '#fff' : '#94a3b8';
      ctx.font = '11px sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText(i, x, y + 4);
    }
  },
};

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('trilha-xp')) {
    TrilhaConexia.carregar();
  }
});
