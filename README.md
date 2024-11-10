# Desafio Oliveira Trust

Este projeto é uma aplicação de importação de arquivos desenvolvida utilizando **Laravel**. Contemplei tudo que foi solicitado no desafio, inclusive os bônus. Além disso, tendo em vista que os arquivos que serão processados serão grandes, optei por adicionar um novo tipo de arquivo no upload: ZIP. O upload poderá ser feito por um arquivo ZIP, CSV ou XSLX, e será feita uma verificação se o arquivo compactado dentro do ZIP é um formato válido para a importação (CSV ou XSLX).

## 💻 Pré-requisitos

Antes de começar, verifique se você atendeu aos seguintes requisitos:
* **Composer:** Para gerenciamento de dependências PHP;
* **Docker:** Para configurar o ambiente de desenvolvimento e bancos de dados;
* **Git:** Para clonar o projeto.

## 🚀 Instalação

Siga as etapas abaixo para instalar o projeto:

### Navegue até a pasta da aplicação
```sh
cd challenge-api
```

### Copie o arquivo exemplo de variáveis de ambiente
```sh
cp .env.example .env
```

### Inicie os containers Docker e construa o ambiente
```sh
docker-compose up -d --build
```

### Gere a chave da aplicação
```sh
docker exec ot-api php artisan key:generate
```

### Inicialize o Replica Set do MongoDB
```sh
docker exec ot-mongo mongosh --eval 'rs.initiate()'
docker exec ot-mongo mongosh --eval 'rs.status()'
```

### Alimente o banco de dados
```sh
docker exec ot-api php artisan migrate:fresh --seed
```

## Utilizar aplicação
Para realizar testes e interagir com a API, foi criado um Postman Collection que pode ser acessado [**aqui**](https://www.postman.com/payload-cosmonaut-36870423/workspace/oliveira-trust-challenge/request/17848575-011c4934-25f8-4e2e-b20a-8b3caa6cdfdf?action=share&creator=17848575&ctx=documentation&active-environment=17848575-73628c9c-78c6-44c5-801b-7766f811220f). É recomendado utilizar a aplicação *Desktop do Postman* para importação da coleção.

**Importante:** Como foi implementada autenticação na API, execute as duas requisições a seguir antes de qualquer outra:

1. **GET: CSRF-Cookie** — Obtém o token CSRF necessário para autenticação.
2. **POST: Login** — Realiza o login com as credenciais de teste.

Como foi implementada autenticação na API, execute primeiro essas duas requisições:
1. `GET:CSRF-Cookie`
2. `POST:Login`

Deixei exemplos de chamadas salvas no Postman.

## 🧑‍💻 Usuário de Teste
> O usuário criado para utilizar o sistema é:<br /><br />
> **E-mail:** `david@example.com`<br />
> **Senha:** `password`