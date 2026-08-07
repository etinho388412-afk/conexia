# Arquitetura do Projeto Conexia

## Stack escolhida
- **Front-end:** HTML + CSS + JS puro (sem build tools — funciona 100% editando arquivo por arquivo via ZArchiver)
- **Back-end:** PHP 8+ com PDO (roda em qualquer hospedagem compartilhada: Hostinger, cPanel, etc. — não precisa de terminal nem processo dedicado)
- **Banco de dados:** MySQL/MariaDB (compatível com hospedagem escolar comum ou serviços gerenciados como PlanetScale)
- **Autenticação:** OAuth 2.0 Google (Google Identity Services), validado por domínio institucional `@escola`

## Árvore completa de pastas

```
conexia/
├── public/                        ← tudo que fica acessível pelo navegador
│   ├── index.php                  ← tela de login (Entrar com Google)
│   ├── css/
│   │   ├── style.css              ← estilos base + tema claro
│   │   └── dark-mode.css          ← tema escuro (WCAG 2.1)
│   ├── js/
│   │   ├── main.js                ← utilidades gerais, fetch helper
│   │   ├── avatar.js              ← renderização e customização do avatar SVG
│   │   ├── mascote.js             ← lógica do mascote assistente de UI
│   │   └── trilha.js              ← renderização do mapa de nós (skill tree)
│   ├── img/
│   │   ├── avatars/                ← sprites base do avatar
│   │   ├── mascotes/                ← sprites dos mascotes
│   │   └── icons/                   ← ícones gerais
│   ├── professor/
│   │   ├── dashboard.php          ← visão geral de turmas e engajamento
│   │   ├── criar-tarefa.php       ← criação/agendamento de tarefas
│   │   ├── turmas.php             ← gestão de turmas e alunos
│   │   └── bonus.php              ← envio de bônus de incentivo em moedas
│   └── aluno/
│       ├── dashboard.php          ← ecossistema de aprendizado (home)
│       ├── trilha.php             ← mapa de evolução (skill tree)
│       ├── banco-tempo.php        ← agendamento P2P de aulas
│       ├── avatar.php             ← personalização de avatar e mascote
│       └── tarefas.php            ← central de tarefas pendentes/entregues
├── src/                           ← lógica de aplicação (fora do public, mais seguro)
│   ├── config/
│   │   ├── database.php           ← conexão PDO
│   │   └── oauth.php              ← credenciais e validação do Google OAuth
│   ├── auth/
│   │   ├── google-callback.php    ← callback OAuth + validação domínio @escola
│   │   ├── session.php            ← gestão de sessão
│   │   └── rbac.php               ← controle de acesso por perfil (professor/estudante)
│   ├── controllers/
│   │   ├── TarefaController.php
│   │   ├── BancoTempoController.php
│   │   ├── AvatarController.php
│   │   └── TrilhaController.php
│   ├── models/
│   │   ├── Usuario.php
│   │   ├── Tarefa.php
│   │   ├── AgendamentoP2P.php
│   │   └── Mascote.php
│   └── api/                       ← endpoints JSON consumidos pelo JS do front
│       ├── tarefas.php
│       ├── trilha.php
│       └── mascote-mensagem.php
├── database/
│   ├── schema.sql                 ← criação de todas as tabelas
│   └── seed.sql                   ← dados iniciais (itens cosméticos, mascotes, conquistas)
└── docs/
    └── arquitetura.md             ← este arquivo
```

## Modelo de Entidades (resumo)

| Entidade | Responsabilidade |
|---|---|
| `usuarios` | Conta institucional (professor ou estudante), vinculada ao Google OAuth |
| `turmas` / `turma_alunos` | Estrutura de turmas do professor |
| `tarefas` / `tarefa_entregas` | Ciclo de vida de uma tarefa: criação → entrega → correção |
| `moedas_tempo` / `transacoes_moedas` | Saldo e histórico de Moedas de Tempo |
| `habilidades` / `usuario_habilidades` | O que cada aluno sabe ensinar / precisa aprender |
| `agendamentos_p2p` / `avaliacoes_p2p` | Marketplace de aulas entre alunos (Banco de Tempo) |
| `trilha_progresso` / `conquistas` / `usuario_conquistas` | XP, nível, patentes e badges |
| `avatares` / `itens_cosmeticos` / `inventario_usuario` | Sistema de customização visual do aluno |
| `mascotes` / `mascote_itens` / `mensagens_mascote` | Mascote acompanhante e suas mensagens de UI |

## Fluxo de autenticação
1. Usuário clica "Entrar com Google" em `public/index.php`
2. Google retorna token → `src/auth/google-callback.php`
3. Sistema valida se o e-mail termina em `@escola`
4. Se novo usuário, cria registro em `usuarios`; se existente, recupera perfil
5. `src/auth/session.php` grava sessão PHP com `usuario_id` e `tipo_perfil`
6. `src/auth/rbac.php` é incluído em toda página protegida para checar o perfil e redirecionar (professor → `/professor/dashboard.php`, estudante → `/aluno/dashboard.php`)

## Próximos passos sugeridos
- Substituir `SEU_CLIENT_ID_AQUI` em `src/config/oauth.php` pelas credenciais reais do Google Cloud Console
- Ajustar `src/config/database.php` com host/usuário/senha do MySQL escolhido
- Rodar `database/schema.sql` seguido de `database/seed.sql` no phpMyAdmin ou painel do provedor
