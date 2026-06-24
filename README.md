# call4repair

Sistema de chamados para oficina de funilaria (bodywork). API em **Laravel 12** +
SPA em **Vue 3**, rodando via **Docker**.

## Stack

- **Backend:** Laravel 12 (PHP 8.2), autenticação **JWT** (`tymon/jwt-auth`) em
  **cookie httpOnly** com proteção **CSRF**, autorização por papéis
  (`spatie/laravel-permission`)
- **Frontend:** Vue 3 + Vue Router + Vite + **vue-i18n** (PT-BR), tabela
  `vue3-easy-data-table` (paginação server-side)
- **Banco:** MySQL 8
- **Infra:** Docker Compose (app, mysql, nginx, node)

> **Idioma:** o código (nomes, comentários, valores de enum) é em inglês; os textos
> de usuário final ficam em arquivos de tradução (`app/lang` no back, `vue-i18n` no
> front). O idioma das respostas da API segue o header `Accept-Language`
> (padrão `pt_BR`).

## Subindo o ambiente

```bash
docker compose up -d
# aguarde o MySQL ficar pronto, então rode as migrations + seeders:
docker compose exec app php artisan migrate --seed
```

> Comandos `artisan`/`composer` rodam **dentro do container `app`**:
> `docker compose exec app php artisan <comando>`

### URLs

| Serviço | URL |
|---|---|
| Aplicação (nginx) | http://localhost:8080 |
| Vite dev server | http://localhost:5174 |
| Health check | http://localhost:8080/up |

### Credenciais de acesso (seed)

| Papel | Email | Senha |
|---|---|---|
| **admin** | `test@example.com` | `password` |
| **technician** | `joao@empresa.com` | `password` |

Papéis e permissões (`RoleSeeder`): `admin` (tudo), `manager` (gerenciar
funcionários + chamados, vê todos os chamados), `technician` (vê/gerencia
**apenas os chamados atribuídos a ele**).

> O técnico `joao@empresa.com` está vinculado ao funcionário João Silva
> (`employees.user_id`), então enxerga só os chamados onde esse funcionário
> está atribuído.

## Endpoints da API (prefixo `/api`)

| Método | Rota | Acesso | Descrição |
|---|---|---|---|
| POST | `/login` | público | Autentica; emite o JWT em cookie httpOnly + `XSRF-TOKEN` |
| POST | `/calls` | público* | Abertura de chamado pelo cliente (*rate limit 5/min por IP) |
| GET | `/me` | auth | Usuário autenticado + papéis/permissões |
| POST | `/refresh` | auth | Renova o token (rotaciona o cookie) |
| POST | `/logout` | auth | Invalida o token (blacklist) e limpa os cookies |
| GET | `/stats` | `view calls` | Métricas para os gráficos do painel (escopadas por visibilidade) |
| GET | `/calls` | `view calls` | Lista chamados paginada (`?page`, `?per_page`, `?search`) — escopada ao usuário |
| GET | `/calls/{id}` | `view calls` | Detalhe do chamado |
| PUT | `/calls/{id}` | `manage calls` | Atualiza chamado / sincroniza funcionários |
| DELETE | `/calls/{id}` | `delete calls` | Remove chamado |
| GET/POST | `/employees` | `manage employees` | Lista paginada / cria funcionário |
| GET/PUT/DELETE | `/employees/{id}` | `manage employees` | Detalhe / atualiza / remove |

**Autenticação:** o SPA usa o cookie httpOnly automaticamente. Requisições de
escrita autenticadas exigem o header CSRF `X-XSRF-TOKEN` (o axios o envia
automaticamente a partir do cookie `XSRF-TOKEN`). Clientes não-browser podem usar
`Authorization: Bearer <token>` (sem CSRF).

## Regras de negócio

- **Status do chamado (máquina de estados):** as transições são restritas pelo enum
  `App\Enums\CallStatus`: `open → in_progress|rejected`, `in_progress → done|rejected`,
  `rejected → open` (reabertura), `done` é terminal. Manter o mesmo status é sempre
  permitido (ex.: ao só reatribuir funcionários). Transição inválida retorna `422`.
- **Exclusão (soft delete):** `calls` e `employees` usam soft delete — a linha é
  mantida (`deleted_at`) e some das listagens, preservando o histórico de atribuições.
- **Autoria do chamado:** chamados abertos por um usuário autenticado registram
  `created_by`; o formulário público anônimo deixa o campo `null`.
- **Visibilidade dos chamados:** quem tem `view all calls` (admin/manager) vê
  todos os chamados; os demais (técnico) veem **apenas os atribuídos a si**
  (via `employees.user_id`). Vale para listagem, detalhe e edição (`/stats`
  também é escopado). Reatribuir funcionários exige `manage employees`.
- **Rate limit:** `POST /login` e `POST /calls` são limitados a 5/min por IP.
- **Painel:** após o login o usuário cai em `/dashboard`, que reúne os atalhos de
  gestão (chamados e, para quem tem `manage employees`, funcionários).

## Produção & segurança

- **Secrets:** o `.env` **não é versionado** (só o `.env.example`). Gere por ambiente:
  ```bash
  docker compose exec app php artisan key:generate   # APP_KEY
  docker compose exec app php artisan jwt:secret      # JWT_SECRET
  ```
- **HTTPS:** em produção use `APP_ENV=production` (cookies viram `Secure`) e
  `APP_URL=https://...`. O app confia no proxy nginx (`TrustProxies`) para detectar HTTPS.
- **CORS:** `config/cors.php` permite credenciais; defina `FRONTEND_URL` com a origem
  do SPA (origens com credenciais não podem ser `*`).

## Testes

```bash
docker compose exec app php artisan test
```

A suíte usa SQLite em memória (configurado em `phpunit.xml`) — não toca o banco de
desenvolvimento.

## Estrutura

O código da aplicação Laravel fica em [`app/`](app/). O `docker-compose.yml`,
`Dockerfile` e a config do `nginx/` ficam na raiz do repositório.
