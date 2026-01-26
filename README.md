# PHP_Laravel12_Scount

A simple Laravel 12 project demonstrating Laravel Scout search functionality using the Database Driver (no third-party services).
This project uses multiple Blade pages with a clean structure and beginner-friendly code.

## Project Overview

This project shows how to implement full-text search in Laravel 12 using Laravel Scout with the built-in database driver. It is suitable for beginners who want to understand Scout without external search services like Algolia or Meilisearch.

## Features

* Laravel 12 framework
* Laravel Scout integration
* Database-based full-text search
* Multi-page Blade views
* Create and search posts
* Clean MVC structure
* Beginner friendly

## Tech Stack

Backend

* PHP 8+
* Laravel 12

Search

* Laravel Scout (Database Driver)

Database

* MySQL

Frontend

* Blade Templates (HTML)

## Project Structure

laravel12-scout/

app/

* Models/Post.php
* Http/Controllers/PostController.php

database/

* migrations/create_posts_table.php
* migrations/create_scout_indexes_table.php

resources/views/

* layout/app.blade.php
* posts/index.blade.php
* posts/create.blade.php

routes/

* web.php

.env
README.md

## Installation Steps

### Step 1: Clone Repository

```bash
git clone https://github.com/your-username/PHP_Laravel12_Scout.git
cd PHP_Laravel12_Scout
```

### Step 2: Install Dependencies

```bash
composer install
```

### Step 3: Environment Setup

Copy the environment file and generate the application key.

```bash
cp .env.example .env
php artisan key:generate
```

### Step 4: Database Configuration

Update the following values in the .env file.

```env
DB_DATABASE=laravel12_scout
DB_USERNAME=root
DB_PASSWORD=
```

Create the database manually in MySQL.

```text
laravel12_scout
```

### Step 5: Install Laravel Scout

```bash
composer require laravel/scout
```

Publish the Scout configuration file.

```bash
php artisan vendor:publish --provider="Laravel\Scout\ScoutServiceProvider"
```

### Step 6: Configure Scout Driver

Set the Scout driver to database in the .env file.

```env
SCOUT_DRIVER=database
```

Create the Scout index table and run migrations.

```bash
php artisan scout:table
php artisan migrate
```

### Step 7: Create Post Model and Migration

```bash
php artisan make:model Post -m
php artisan migrate
```

## Laravel Scout Integration

### Post Model

app/Models/Post.php

```php
use Laravel\Scout\Searchable;

class Post extends Model
{
    use Searchable;

    protected $fillable = ['title', 'content'];

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}
```

## Controller Logic

PostController.php handles the following actions.

* List all posts
* Search posts using Scout
* Create new posts

Example Scout search usage.

```php
Post::search($request->search)->get();
```

## Blade Pages

Layout file

* layout/app.blade.php

Pages

| Page                 | File                   |
| -------------------- | ---------------------- |
| Post List and Search | posts/index.blade.php  |
| Add Post             | posts/create.blade.php |

## Routes

```php
Route::get('/', [PostController::class, 'index']);
Route::get('/posts/create', [PostController::class, 'create']);
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
```

## Run the Project

```bash
php artisan serve
```

Open the application in the browser.

```text
http://127.0.0.1:8000
```

## Screenshot
<img width="1895" height="882" alt="image" src="https://github.com/user-attachments/assets/d2c074b9-8ad3-459f-a9c0-d46d6c5b367b" />
<img width="1867" height="793" alt="image" src="https://github.com/user-attachments/assets/ef3d0f20-ec57-468e-8059-c38e3ce8a6d4" />


## Import Existing Data to Scout

If you already have posts in the database, import them into Scout.

```bash
php artisan scout:import "App\\Models\\Post"
```

## Screenshots

You can add screenshots of the post list and search pages here.

## Future Improvements

* AJAX live search
* Pagination
* Edit and delete posts
* Meilisearch or Algolia integration
* API version using React or Vue

## License

This project is open-source and free to use for learning and educational purposes.

