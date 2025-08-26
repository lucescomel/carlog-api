# CarLog API — Fresh (Symfony 7 + API Platform + PostgreSQL)
Prête à installer, sans Doctrine Migrations.
## Installation
```bash
docker compose up -d
composer install
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load -n
symfony server:start
# http://127.0.0.1:8000/api
```
