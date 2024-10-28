# Teste Técnico InovaFood

## Instruções para rodar o projeto sem Docker
- Clonar repositório:
```bash
git clone https://github.com/Railton98/teste_tecnico_inovafood_2024
cd teste_tecnico_inovafood_2024
```

- Criar arquivo `.env` e preencher `credênciais` para o banco de dados. Ex:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inovafood
DB_USERNAME=root
DB_PASSWORD=
```

- Criar banco de dados:
> Usando um programa gerenciador de banco de dados (MySQL Workbench, DBeaver, etc) execute o seguinte SQL:
```sql
CREATE DATABASE IF NOT EXISTS `inovafood`;
USE `inovafood`;

CREATE TABLE IF NOT EXISTS `checklists` (
    `id` int NOT NULL AUTO_INCREMENT,
    `title` mediumtext NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `questions` (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_checklist` int NOT NULL,
    `title` mediumtext COLLATE utf8mb3_unicode_ci NOT NULL,
    `type` smallint NOT NULL DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_checklist`) REFERENCES `checklists` (`id`) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `alternatives` (
    `id` int NOT NULL AUTO_INCREMENT,
    `id_question` int NOT NULL,
    `title` mediumtext NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`id_question`) REFERENCES `questions` (`id`) ON DELETE CASCADE
);
```

- Instalar dependências:
```bash
composer install
```

- Rodar a aplicação:
```bash
php -S localhost:8080 -t public/
```
> Acesse a aplicação em http://localhost:8080

---
## Instruções para rodar o projeto com Docker
- Clonar repositório:
```bash
git clone https://github.com/Railton98/teste_tecnico_inovafood_2024
cd teste_tecnico_inovafood_2024
```

- Criar arquivo `.env`:
```bash
cp .env.example .env
```
> Isso já traz `credênciais padrão` para o banco de dados

- Subir containers Docker:
```bash
docker compose up -d
```
> Isso já criará o banco de dados (com as tabelas) 

- Instalar dependências:
```bash
docker compose exec php-fpm composer install
```
---
> Acesse a aplicação em http://localhost:80
> - a rota `/` retorna uma simples mensagem de sucesso
> - a rota `/process` realiza o processamento (exemplo de requisição na imagem abaixo)
>
> ![image](https://github.com/user-attachments/assets/9b438be6-8122-4ba1-a1ea-495ee218e70b)
