## AC Journal Laravel

A daily journalling app.  

## Features

- **Configure Questions** - Customize the template for journal entries by setting up the questions to ask. 
Questions can be marked as mandatory or optional.   
- **Create Journal Entries** - Enter new journal entries or edit previous entries
- **Read Entries** - View journal entries on the home page in a blog format
- **View Reports** - See reports on the number of entries per month, or the average number of words per article 

## Project Status

This project is not actively maintained. 

## Built With
- Laravel 7

## Requirements
- PHP 7.x 
- Composer 2.8 and above
- Sqlite or MySql database ready

## Setup

1. Install required libraries:
``` 
composer install
```
2. Create an .env file with an app key:
``` 
cp dev.env .env
php artisan key:generate  
```
3. Configure the database connection in config/database.php (use sqlite or mysql)
4. If using sqlite, create a new database file:
``` 
touch database/database.sqlite
```
5. Run migration to create tables
``` 
php artisan migrate
```
6. (Dev environment only) Seed database with some test data
``` 
php artisan db:seed
```
7. Start the Laravel application
``` 
php artisan serve
```

## Authors

[adinochang](https://github.com/adinochang/)

## License

This package is licensed under the [MIT license](https://github.com/adinochang/ac_journal_laravel/blob/master/LICENSE).

