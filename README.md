Billy Laravel API v1

[![Postman Collection](https://www.getpostman.com/collections/d7d93005926f35e29ea6)](Postman)

[![Billy Api Documentation](https://www.billy.dk/api/#filtering)](BillyApi)

[![My Task](https://drive.google.com/file/d/1T-V7m8FdA4oXuDHzJI5yQGzwDq3XtPvj/view?usp=sharing)](MyTask)

## How to build

- Download from git
```
git clone https://github.com/fut149/BillyWebApp.git
```
- Install all with composer
```
    composer install
```
- Edit .env file
```
- Database
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=billywebapp
    DB_USERNAME=root
    DB_PASSWORD=
- Billy Setup
    BILLY_ACCESS_TOKEN=749f6c0f873eb98f16257eec9baa47c944617d34
    BILLY_API_URL=https://api.billysbilling.com/v2
```
- Database migrate
```
  php artisan migrate

```
- Database seeds
```
  php artisan db:seed

```
- Run
```
   php artisan serve

```
###Api Request URL
```
http://127.0.0.1:8000/api/

```
###Postman Request Collection

https://www.getpostman.com/collections/d7d93005926f35e29ea6
