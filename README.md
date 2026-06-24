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

### Credencial de acesso (seed)

- **Email:** `test@example.com`
- **Senha:** `password` — papel **admin**

Papéis e permissões (`RoleSeeder`): `admin` (tudo), `manager` (gerenciar
funcionários + chamados), `technician` (ver/gerenciar chamados).

## Endpoints da API (prefixo `/api`)

| Método | Rota | Acesso | Descrição |
|---|---|---|---|
| POST | `/login` | público | Autentica; emite o JWT em cookie httpOnly + `XSRF-TOKEN` |
| POST | `/calls` | público* | Abertura de chamado pelo cliente (*rate limit 5/min por IP) |
| GET | `/me` | auth | Usuário autenticado + papéis/permissões |
| POST | `/refresh` | auth | Renova o token (rotaciona o cookie) |
| POST | `/logout` | auth | Invalida o token (blacklist) e limpa os cookies |
| GET | `/calls` | `view calls` | Lista chamados paginada (`?page`, `?per_page`, `?search`) |
| GET | `/calls/{id}` | `view calls` | Detalhe do chamado |
| PUT | `/calls/{id}` | `manage calls` | Atualiza chamado / sincroniza funcionários |
| DELETE | `/calls/{id}` | `delete calls` | Remove chamado |
| GET/POST | `/employees` | `manage employees` | Lista paginada / cria funcionário |
| GET/PUT/DELETE | `/employees/{id}` | `manage employees` | Detalhe / atualiza / remove |

**Autenticação:** o SPA usa o cookie httpOnly automaticamente. Requisições de
escrita autenticadas exigem o header CSRF `X-XSRF-TOKEN` (o axios o envia
automaticamente a partir do cookie `XSRF-TOKEN`). Clientes não-browser podem usar
`Authorization: Bearer <token>` (sem CSRF).

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
