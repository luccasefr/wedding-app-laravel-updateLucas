## Projeto Api Wedding

Projeto de Api para o aplicativo e site pracasaradm.approx.com.br. Aplicação para administração de casamentos

## Bibliotecas Externas

Guzzle - HTTP CLient usada para fazer requisições em api externa - http://docs.guzzlephp.org/en/stable/
Swagger - PAcote para fazer a documentação da api - https://github.com/DarkaOnLine/L5-Swagger

## Instalação

- clonar o projeto (https://github.com/approx/wedding-app-laravel)
- Criar um banco de dados de sua preferencia (para fazer o projeto foi usado o Mysql) para população de dados da Api
- Fazer o comando "composer update" para atualizar as dependencias
- Criar e configurar o .ENV com as informações do banco de dados
- Fazer o comando "php artisan key:generate"
- Fazer o comando "php artisan migrate" para criar as tabelas do banco de dados
- Fazer o comando "php artisan serve" para rodar o projeto
- Testar os endpoint da documentação

## Documentação da API

A documentação pode ser acessada pelo localhost no endpoint api/documentation, 
http://127.0.0.1:8000/api/documentation

## Contato
admin@proprodutores.com.br


