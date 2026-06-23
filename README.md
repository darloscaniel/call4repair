# call4repair

Sistema de chamados para oficina de funilaria (bodywork). API em **Laravel 12** +
SPA em **Vue 3**, rodando via **Docker**.

## Stack

- **Backend:** Laravel 12 (PHP 8.2), autenticação **JWT** (`tymon/jwt-auth`)
- **Frontend:** Vue 3 + Vue Router + Vite, tabela `vue3-easy-data-table`
- **Banco:** MySQL 8
- **Infra:** Docker Compose (app, mysql, nginx, node)

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
- **Senha:** `password`

## Endpoints da API (prefixo `/api`)

| Método | Rota | Auth | Descrição |
|---|---|---|---|
| POST | `/login` | público | Autentica e retorna o token JWT |
| POST | `/calls` | público* | Abertura de chamado pelo cliente (*rate limit 5/min por IP) |
| GET | `/calls` | JWT | Lista chamados (com funcionários) |
| GET | `/calls/{id}` | JWT | Detalhe do chamado |
| PUT | `/calls/{id}` | JWT | Atualiza chamado / sincroniza funcionários |
| DELETE | `/calls/{id}` | JWT | Remove chamado |
| GET/POST | `/employees` | JWT | Lista / cria funcionário |
| GET/PUT/DELETE | `/employees/{id}` | JWT | Detalhe / atualiza / remove funcionário |

Rotas protegidas exigem o header `Authorization: Bearer <token>`.

## Testes

```bash
docker compose exec app php artisan test
```

A suíte usa SQLite em memória (configurado em `phpunit.xml`) — não toca o banco de
desenvolvimento.

## Estrutura

O código da aplicação Laravel fica em [`app/`](app/). O `docker-compose.yml`,
`Dockerfile` e a config do `nginx/` ficam na raiz do repositório.
