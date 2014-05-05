# Silex Blog Example

Just a simple blog example created with [silex microframework](http://silex.sensiolabs.org/).

## Installation

1. Clone the repository
2. Run `composer install`
3. Run `bin/console db:create`
4. Run `bin/console db:create-user `. This command has two required arguments: username and password. For example: `bin/console db:create-user admin admin`
5. Set a `remember_me` key in _src/app.php_ file

## Admin section

Admin section available at _/admin_ URL.

## Features

- [Silex microframework](http://silex.sensiolabs.org/)
- [Twig](http://twig.sensiolabs.org/) template engine
- [Twitter Bootstrap](http://getbootstrap.com/)
- [SQLite](https://www.sqlite.org/about.html) database
- [PHP Markdown](http://michelf.ca/projects/php-markdown/) editor (with [Extra](http://michelf.ca/projects/php-markdown/extra/) extension) for posts content
- Friendly URL generating with [Cocur\Slugify](https://github.com/cocur/slugify)
- Syntax highlighting with [highlight.js](http://highlightjs.org)

## Prod and Dev

There are two different environments: `prod` and `dev`. Every console command has an `--env` option. By default it's a `dev`. Each of environments has its own configuration and database.

## Development environment

To run the blog in the development environment use *index_dev.php*. Like this: *http://localhost:8000/index_dev.php/posts/some-kind-of-post*. It works only for localhost by default.

## Console commands

1. `db:create`. Creates a database.
2. `db:create-user`. Creates a user with access to admin section. Requires two arguments: username and password.
3. `translations:extract`. Extracts untranslated messages from views.
4. `translations:add-unit`. Adds a translation unit. Requires two arguments: source message and translation.
5. `faker:populate:posts`. Generates fake posts (for testing).

Add `--help` option to see more information about command.