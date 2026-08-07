// Caminho: conexia/public/js/mascote.js
// Mascote acompanhante: busca mensagens (notificações, dicas, alertas de prazo)
// e exibe como um balão de fala flutuante.

const MascoteConexia = {
  intervaloMs: 30000, // verifica novas mensagens a cada 30s

  iniciar() {
    this.criarElementoFlutuante();
    this.verificarMensagens();
    setInterval(() => this.verificarMensagens(), this.intervaloMs);
  },

  criarElementoFlutuante() {
    if (document.getElementById('mascote-flutuante')) return;

    const wrapper = document.createElement('div');
    wrapper.id = 'mascote-flutuante';
    wrapper.style.cssText = `
      position: fixed; bottom: 20px; right: 20px; max-width: 260px;
      display: flex; align-items: flex-end; gap: 8px; z-index: 50;
    `;
    wrapper.innerHTML = `
      <div id="mascote-balao" role="status" aria-live="polite" style="
        display:none; background:#fff; border:1px solid #e2e8f0; border-radius:12px;
        padding:10px 14px; font-size:0.9rem; box-shadow:0 2px 8px rgba(0,0,0,0.12);
      "></div>
      <img id="mascote-sprite" src="/img/mascotes/padrao.svg" alt="Seu mascote"
           width="56" height="56" style="cursor:pointer;" />
    `;
    document.body.appendChild(wrapper);
  },

  async verificarMensagens() {
    try {
      const mensagens = await Conexia.api('/src/api/mascote-mensagem.php');
      if (mensagens.length > 0) {
        this.exibirMensagem(mensagens[0]);
      }
    } catch (erro) {
      console.warn('Não foi possível buscar mensagens do mascote:', erro);
    }
  },

  exibirMensagem(mensagem) {
    const balao = document.getElementById('mascote-balao');
    if (!balao) return;

    balao.textContent = mensagem.texto;
    balao.style.display = 'block';

    balao.onclick = async () => {
      await Conexia.api('/src/api/mascote-mensagem.php', {
        method: 'POST',
        body: JSON.stringify({ mensagem_id: mensagem.id }),
      });
      balao.style.display = 'none';
    };

    // Some sozinho depois de 8 segundos se não for clicado
    setTimeout(() => { balao.style.display = 'none'; }, 8000);
  },
};

document.addEventListener('DOMContentLoaded', () => MascoteConexia.iniciar());
