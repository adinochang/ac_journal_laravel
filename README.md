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

## Unit Tests

``` 
php artisan test
```

## Manual Testing Guide

Follow the steps below to test the core functionality of the application manually.

### 1. Main Page
- A list of existing entries are displayed, sorted by newest first
- An entry will display questions and answers 
- Each page shows two entries
- Pagination function works

### 2. Navigation menu 
- Questions
- Entries
- Reports
- Home (AC Journal)
- New Entry

### 3. Questions
- A list of existing questions are displayed, sorted by oldest first
- Data displayed in each row and column is correct
- Test search function - exact match, partial match, no match  

#### A. New Question
- Question field, Required dropdown, Enabled dropdown
- Question field is required
- Save function works to save all entered values correctly
- After Save, close New Question form
- Cancel button without saving, then clicking New to make sure form is cleared

#### B. Edit Question
- Fields are populated correctly
- Question field is required 
- Save function works to save all entered values correctly
- After Save, close Edit Question form
- Cancel button without saving

#### C. Delete Question
- Click cancel at confirmation prompt
- Click OK at confirmation prompt
- Record is really deleted
- After deletion, close Edit Question form

### 3. Entries
- A list of existing entries are displayed, sorted by newest first
- An excerpt of each entry is displayed together with the Entry Date
- Test date filter function

#### A. New Entry
- Displays a Question and Answer form
- Only Enabled Questions are displayed
- Required Questions must be answered
- Save function works to save all entered values correctly
- After Save, close New Entry form and show Entries list
- Cancel button without saving, then clicking New to make sure form is cleared

#### B. Edit Question
- Fields are populated correctly
- Only Enabled Questions are displayed
- Required Questions must be answered
- Save function works to save all entered values correctly
- After Save, close Edit Entry form and show Entries list
- Cancel button without saving

#### C. Delete Question
- Click cancel at confirmation prompt
- Click OK at confirmation prompt
- Record is really deleted
- After deletion, close Edit Entry form and show Entries list 

### 4. New Entry from Home Page
- Loads New Entry form
- After Save, close New Entry form and show Home Page

## Authors

[adinochang](https://github.com/adinochang/)

## License

This package is licensed under the [MIT license](https://github.com/adinochang/ac_journal_laravel/blob/master/LICENSE).

