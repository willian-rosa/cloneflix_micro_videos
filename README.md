# Clonefix

Projeto de uma plataforma de vídeos usando micros serviços.

## Setup

Algumas variáveis do docker compose usa as variáveis de ambiente do sistema operacional.

Exportar as seguintes variáveis:
```shell
export CLONEFLIX_AWS_ACCESS_KEY_ID=xxxxxxxx
export CLONEFLIX_AWS_SECRET_ACCESS_KEY=xxxxxxxx
export CLONEFLIX_AWS_DEFAULT_REGION=us-east-1
export CLONEFLIX_AWS_BUCKET=xxxxxxxx
export CLONEFLIX_AWS_URL=https://xxxxxxxxxxxx.s3.amazonaws.com
```

## Anotações
Lembretes:
- Criar model `php artisan make:model Models/Genre --all`
- Criar uma seeder `php artisan make:seeder GenresTableSeeder`
- Criar uma resource `php artisan make:resource CastMember`

Relacionamento 
- Criar model `php artisan make:migration create_category_genre_table`
- Obs o many-to-many tem que ser por ordem alfabética
- Criar a tabela `php artisan migrate`

- Rodar novamente o migration `php artisan migrate:refresh`

Populando Seeders
- `php artisan migrate --seed`
- `php artisan migrate:refresh --seed`


Permissões
- `sudo chown -R willian:willian database/migrations app/`
- sudo chown -R willian:willian .docker/ frontend/



Anotações nord:
- Colocar faker

