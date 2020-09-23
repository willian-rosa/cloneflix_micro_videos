

Lembretes:
- Criar model `php artisan make:model Models/Genre --all`
- Criar uma seeder `php artisan make:seeder GenresTableSeeder`

Relacionamento 
- Criar model `php artisan make:migration create_category_genre_table`
- Obs o many-to-many tem que ser por ordem alfabética
- Criar a tabela `php artisan migrate`




Populando Seeders
- `php artisan migrate --seed`
- `php artisan migrate:refresh --seed`


Permissões
- sudo chown -R willian:willian database/migrations app/


Anotações nord:
- Colocar faker

