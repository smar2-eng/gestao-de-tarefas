# Sa&Rick Tasks

Aplicação web PHP para organizar tarefas pessoais com autenticação, cadastro de contas e listas privadas por usuário.

Cada conta possui seu próprio conjunto de tarefas. Um usuário pode criar, editar, concluir, desfazer e excluir apenas as tarefas vinculadas à própria conta.

## Funcionalidades

- Criação de conta com nome, e-mail e senha.
- Login e logout com sessão PHP.
- Senhas armazenadas com `password_hash()`.
- Lista de tarefas isolada por usuário.
- Criação rápida de tarefas.
- Edição do texto de uma tarefa já criada, pelo ícone de lápis.
- Filtros para todas, concluídas e não concluídas.
- Conclusão e reabertura de tarefas.
- Exclusão de tarefas com confirmação no navegador.
- Proteção CSRF nos formulários de alteração.
- Cookies de sessão com `HttpOnly` e configuração segura para HTTPS.
- Migração automática das tabelas necessárias.
- Interface responsiva preservando o visual original do projeto.

## Requisitos

- PHP 8.0 ou superior.
- Extensão PDO MySQL habilitada.
- MySQL ou MariaDB.
- Servidor web Apache/Nginx ou o servidor embutido do PHP.
- RedBeanPHP incluído em `class/rb.php`.

## Configuração do banco

A aplicação aceita uma URL completa por meio de `DATABASE_URL` ou variáveis individuais:

```env
DATABASE_URL=mysql://usuario:senha@127.0.0.1:3306/todolist
```

Como alternativa:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=todolist
DB_USER=root
DB_PASS=
```

Se nenhuma variável for informada, o projeto usa os valores locais padrão:

- Host: `127.0.0.1`
- Porta: `3306`
- Banco: `todolist`
- Usuário: `root`
- Senha: vazia

Em produção, informe as credenciais por variáveis de ambiente e não as grave diretamente no código.

O banco `todolist` deve existir antes da primeira execução. Depois de conectar, a aplicação cria automaticamente as tabelas `usuario` e `tarefa` e adiciona colunas ausentes usadas pela versão atual.

## Execução local

1. Crie o banco MySQL/MariaDB:

   ```sql
   CREATE DATABASE todolist CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Configure as variáveis de ambiente ou use os valores locais padrão.

3. Entre na pasta do projeto:

   ```bash
   cd gestao-de-tarefas-main
   ```

4. Inicie o servidor embutido do PHP:

   ```bash
   php -S 127.0.0.1:8000
   ```

5. Abra `http://127.0.0.1:8000/login.php` no navegador.

## Estrutura principal

```text
.
├── index.php          # Lista, filtros, criação rápida e edição de tarefas
├── login.php          # Login
├── cadastro.php       # Criação de conta
├── logout.php         # Encerramento da sessão
├── autenticacao.php   # Sessão, autenticação e CSRF
├── db.php             # Conexão e preparação do banco
├── inclusao.php       # Criação de tarefas
├── edicao.php         # Edição do texto de uma tarefa
├── concluir.php       # Concluir ou reabrir tarefa
├── exclusao.php       # Exclusão de tarefa
├── assets/styles/     # Estilos originais da interface
└── class/rb.php       # RedBeanPHP
```

## CRUD de tarefas

| Operação | Arquivo | Descrição |
| --- | --- | --- |
| Criar | `inclusao.php` | Grava uma nova tarefa vinculada ao usuário logado. |
| Ler | `index.php` | Lista as tarefas do usuário, com filtros por status. |
| Atualizar | `edicao.php` | Altera o texto da tarefa. |
| Atualizar | `concluir.php` | Alterna a tarefa entre concluída e não concluída. |
| Excluir | `exclusao.php` | Remove a tarefa e volta para a lista. |

### Como editar uma tarefa

1. Na lista, clique no ícone de lápis ao lado da tarefa.
2. O texto passa a ser um campo editável, com os botões **Salvar** e **Cancelar**.
3. Ao salvar, o `edicao.php` atualiza a descrição no banco e redireciona para a lista, mantendo o filtro que estava ativo.

Uma descrição vazia é ignorada, e **Cancelar** volta para a lista sem alterar nada.

## Modelo de dados

### `usuario`

- `id`: identificador da conta.
- `nome`: nome exibido na aplicação.
- `email`: e-mail usado no login.
- `senha`: hash da senha.
- `criado_em`: data de criação da conta.

### `tarefa`

- `id`: identificador da tarefa.
- `usuario_id`: proprietário da tarefa.
- `descricao`: texto da tarefa.
- `concluida`: indica se a tarefa foi finalizada.
- `criada_em`: data e hora da criação.
- `concluida_em`: data e hora da conclusão, quando aplicável.

Todas as consultas de listagem, edição, conclusão e exclusão consideram o usuário autenticado.

## Segurança

As operações de criação, edição, conclusão e exclusão exigem uma sessão autenticada e um token CSRF válido. Os endpoints de edição, conclusão e exclusão também filtram diretamente pelo `usuario_id`, impedindo que uma conta altere tarefas de outra conta apenas informando outro ID.

As senhas nunca são armazenadas em texto puro. A sessão usa um cookie próprio da aplicação, com `HttpOnly` e `SameSite=None; Secure` quando a requisição chega por HTTPS. Em HTTP local, o cookie usa configuração compatível com desenvolvimento.

## Observações para produção

- Use HTTPS.
- Defina `DATABASE_URL` ou todas as variáveis `DB_*` no ambiente do servidor.
- Use um usuário MySQL exclusivo para a aplicação, sem privilégios administrativos.
- Configure backups do banco de dados.
- Desative a exibição de erros PHP para visitantes.
- Mantenha o PHP, MySQL/MariaDB e as dependências atualizadas.
