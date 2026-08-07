// Caminho: conexia/public/js/main.js
// Utilidades gerais: fetch helper, tema claro/escuro, navegação por teclado

const Conexia = {
  async api(url, opcoes = {}) {
    const resposta = await fetch(url, {
      headers: { 'Content-Type': 'application/json' },
      ...opcoes,
    });
    if (!resposta.ok) {
      throw new Error(`Erro na requisição: ${resposta.status}`);
    }
    return resposta.json();
  },

  iniciarTema() {
    const temaSalvo = localStorage.getItem('conexia-tema');
    if (temaSalvo === 'escuro') {
      document.body.classList.add('tema-escuro');
    }

    const botao = document.getElementById('alternar-tema');
    if (botao) {
      botao.addEventListener('click', () => {
        document.body.classList.toggle('tema-escuro');
        const temaAtual = document.body.classList.contains('tema-escuro') ? 'escuro' : 'claro';
        localStorage.setItem('conexia-tema', temaAtual);
        botao.setAttribute('aria-pressed', temaAtual === 'escuro');
      });
    }
  },
};

document.addEventListener('DOMContentLoaded', () => {
  Conexia.iniciarTema();
});
