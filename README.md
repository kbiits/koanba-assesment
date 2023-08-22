# Koanba Assesment

### By Nabiel Omar Syarif

## Requirements
- PHP ^7.4
- Node JS
- npm / yarn
- mysql

## Steps to Install
- Clone this repository
```bash
git clone https://github.com/kbiits/koanba-assesment
cd koanba-assesment
```
- Copy .env from .env.example
```bash
cp .env.example .env
``` 

- Adjust .env value
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kipaskipas_assesment
DB_USERNAME=root
DB_PASSWORD=root
```

- Download dependencies
```bash
composer install
yarn # or npm install
```
- Generate app key
```bash
php artisan key:generate
```
- Migrate and seed database
```bash
php artisan migrate:fresh
php artisan db:seed
```
- Compile frontend assets
```bash
yarn dev # or use yarn watch to detect changes
```
- Run Server
```bash
php artisan serve
```
- Now you can open the website on http://localhost:8000
