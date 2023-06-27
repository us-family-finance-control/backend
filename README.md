# Us

Core da aplicação

# Development

Para realizar uma manutenção corretiva ou evolutiva, você precisará instalar em seu computador o GIT, Docker CE e Docker Compose.

* GIT (https://git-scm.com/downloads)
* Docker CE (https://docs.docker.com/install/)
* Docker Compose (https://docs.docker.com/compose/install/)
* NodeJS 18.15.0

Após instar essas ferramentas, siga os seguintes passos:

#### 1) Faça o clone desse repoitório:

```shell
$ git clone <REPO_URL>
```

#### 2) Suba o(s) container(s):

```shell
$ docker-compose up -d
```

#### 3) Instale as dependencias do projeto:

```shell
$ docker-compose exec us composer create-project -vvv
```

#### 4) Acesse o MySQL e crie os bancos de dados do projeto:

```shell
$ docker-compose exec mysql8-database mysql -uroot -proot -e "CREATE DATABASE us;"
```

#### 5) Execute o comando abaixo para criar as tabelas com alguns dados iniciais.

```shell
$ docker-compose exec us php artisan migrate --seed
```

#### 6) (Opcional) - Para preencher o sistema com alguns Clientes:

```shell
$ docker-compose exec us php artisan db:seed StartSeeder
```
****
Agora basta acessar pelo browser o endereço http://localhost:8080/graphql

## Quando você puxar as atualizações

Observe se aconteceu alguma alteração no `composer.json`. Se sim, significa que as dependencias do projeto foram atualizadas e você deverá rodar o comando abaixo em sua maquina local para deixar tudo certinho:

```shell
$ docker-compose exec us composer update
```

Também vale observar se ocorreu alguma alteração nas migrations. Caso positivo você deverá executar o comando abaixo para atualizar o seu banco de dados local:

```shell
$ docker-compose exec us php artisan migrate:refresh --seed
```

## Documentação da API

Estamos utilizando Swagger-PHP para documentar.

Acesse http://localhost:8080/api/documentation para ler a documentação

### Para realizar a manutenção

Depois de fazer mudanças nos arquivos, execute esse comando:

```shell
$ docker-compose exec us php artisan l5-swagger:generate
```
