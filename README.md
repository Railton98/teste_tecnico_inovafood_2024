# Teste Técnico InovaFood


## Instruções para rodar o projeto em Ambiente Local
> O projeto está baseado em Docker

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
> Acesse a aplicação em http://localhost
> - a rota `/` retorna uma simples mensagem de sucesso
> - a rota `/process` realiza o processamento (exemplo de requisição na imagem abaixo)
>
> ![image](https://github.com/user-attachments/assets/9b438be6-8122-4ba1-a1ea-495ee218e70b)
