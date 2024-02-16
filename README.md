<p align="center">
    <a href="https://packagist.org/packages/jhonoryza/bandung-framework">
        <img src="https://poser.pugx.org/jhonoryza/bandung-framework/d/total.svg" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/jhonoryza/bandung-framework">
        <img src="https://poser.pugx.org/jhonoryza/bandung-framework/v/stable.svg" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/jhonoryza/bandung-framework">
        <img src="https://poser.pugx.org/jhonoryza/bandung-framework/license.svg" alt="License">
    </a>
</p>

# Bandung Framework

small php framework

## Feature

- route
- request
- response
- console command

## WIP

- query builder
- migration
- queue

## Getting Started

### create empty project

```bash
mkdir myapp
cd myapp
composer init
```

### installation

```bash
composer require jhonoryza/bandung-framework
cp vendor/jhonoryza/bandung-framework/bandung .
php bandung install
```

### create a simple endpoint

in app directory you can create a class lets say `HomeController` and

lets create a function `index`

```php
    #[Get(uri: '/')]
    public function index(): Response
    {
        return Response::make(HttpHeader::HTTP_200, 'Hello world!');
    }
```

the Attributes `#[Get('/')]` will mark this function as a route `/`

lets run `php bandung serve` and open [http://127.0.0.1:8000](http://127.0.0.1:8000)

### get environment variables

```php
$appName = getenv('APP_NAME');
echo $appName;
```

### console command

you can run like this `php bandung` this will print all available commands

lets create a custom command

in app directory you can create a class lets say `CommandClass` and

lets create a function `testWarning`

```php
    #[Command('test:warning')]
    public function testWarning(): void
    {
        warning('testing warning ok');
    }
```

the Attributes `#[Command('test:warning')]` will mark this function as command with name `test:warning`

you can call it from terminal : `php bandung test:warning`

## Directory

- `app` directory is playground for the framework
- `src` directory is the core of the framework
- `public` directory `index.php` will be called when serve web request
- `bandung` file will be called when running console command
- `tests` directory is where the test of the framework reside

## Test

./vendor/bin/phpunit

## Security

If you've found a bug regarding security please mail jardik.oryza@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see License File for more information.