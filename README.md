<h1 align="center">
    <a href="https://proto.utwente.nl">
        <img alt="Proto logo" src="https://raw.githubusercontent.com/saproto/saproto/master/public/images/logo/banner-regular.png" width="100%">
    </a>
    <br>
    S.A. Proto
</h1>

<p align="center"> 
    <b>The website of S.A. Proto</b>.<br> 
    The study association of BSc. Creative Technology and MSc. Interaction Technology.<br>
    <a href="https://github.com/saproto/saproto/issues">
        <img alt="issues badge" src="https://img.shields.io/github/issues/saproto/saproto?color=%2503b71a">
    </a>
    <a href="https://github.com/saproto/saproto/graphs/contributors">
        <img alt="contributors badge" src="https://img.shields.io/github/contributors/saproto/saproto?color=%2503b71a">
    </a>
    <img alt="open source badge" src="https://badges.frapsoft.com/os/v2/open-source.svg?v=103">
</p>

## Contributors

[Here](https://github.com/saproto/saproto/graphs/contributors) you can find the people that have contributed to the code
to this project. But, let's not forget the other members of
the [HYTTIOAOAc](https://www.proto.utwente.nl/committee/haveyoutriedturningitoffandonagain)!

## Prerequisites

This README is tailored to using Docker in WSL2 (Ubuntu) on Windows using Laravel Sail. Laravel Sail also supports MacOS
and Linux, however you may encounter issues with the installation instruction below when using a different operating
system.

Before following the installation instructions, you need to have a working installation of WSL2, Docker and Git. You can
install docker using the official instructions at [docs.docker.com](https://docs.docker.com/get-docker).
JetBrains [PhpStorm IDE](https://www.jetbrains.com/help/phpstorm/installation-guide.html) is strongly recommended for
development on this project, especially with the laravel plugin for proper code completion and reverences.

## Setup

This website can be run locally through Docker by using Laravel Sail. For more information on using Laravel Sail check
out their [documentation](https://laravel.com/docs/sail).

### Download

First you need to clone the repository into a folder in WSL2.

#### If you set an SSH key:

```
git clone git@github.com:saproto/saproto.git
```

#### Otherwise, use HTTPS:

```
git clone https://github.com/saproto/saproto.git
```

### Installation

After installing Docker and cloning the repository the following instructions can be run in the terminal in the source
folder of the project.

#### Configuration

Copy and rename `.env.dev` to `.env`.

```
cp .env.dev .env
```

After that, open the new `.env` file and set the `PERSONAL_PROTO_KEY` to your personal Proto key, which can be
found/generated on the bottom of [your dashboard](https://www.proto.utwente.nl/user/dashboard) on the ***live*** Proto
website.

#### Server-side dependencies

When first cloning the project you may not have a functional installation of the correct version of PHP or Composer. To
install Laravel sail and its dependencies it is therefore necessary to spin up a temporary container.

```shell
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs --no-scripts
```

#### Laravel Sail alias

By default, Laravel Sail commands are invoked using the `vendor/bin/sail` script. That is a lot of characters to type
every time you want to execute a command. So, instead you can create an alias. By adding the alias definition
to `~/.bash_aliases` (WSL2/Linux) or `~/.zshenv` (macOS) the alias will persist between terminal restarts.

*The rest of these instruction will assume that you successfully added the `sail` alias.*

WSL2/Linux/macOS High Sierra or earlier:
```shell
echo "alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'" > ~/.bash_aliases
```
macOS Catalina or newer:
```shell
echo "alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'" > ~/.zshenv
```

#### Initial application setup

```
sail up -d
sail composer install
sail artisan key:generate
sail artisan migrate --seed
```

#### Client-side dependencies

To install the client-side dependencies you'll need to run `sail npm install` to install all client-side dependencies.

To compile the project assets (JS/CSS) run `sail npm run build` to compile once or `sail npm run dev` to keep checking
for changes to scripts or stylesheets.

When adding a new library or client-side dependency through npm don't forget to require the scripts in `application.js`
and the stylesheet in `vendor.scss`.

#### Localhost

When you have finished the above setup the following port will be exposed on localhost.

- `8080` = Website
- `8081` = PhpMyAdmin
- `8082` = [Mailhog](https://github.com/mailhog/MailHog)

You can sign in with the same Proto username you use on the ***live*** website and the password given to you during the
database seeding. This user will have full admin rights on the ***local*** website.

#### Known WSL2 + Git issue

Due to the permission changes, Git might detect that all files have been changed. If this is the case,
run `git config core.filemode false` to make git ignore the permission changes.

### Useful commands

#### Start server

```
sail up -d
```

#### Stop server

```
sail stop
```

#### Access to app container

```
sail shell
```

#### Nuke your database *(run in container)*

```
sail artisan migrate:fresh --seed
```

### Code completion, style and static analysis

##### IDE-helper

When writing code it is useful to have tools such as code completion and linting in an integrated development
environment (IDE). As mentioned before [PHPStorm](https://www.jetbrains.com/phpstorm/) is the recommended IDE for this
project. To add additional code completion for Laravel you can run `sail composer ide-helper` in the docker container to
let [Laravel-IDE-Helper](https://github.com/barryvdh/laravel-ide-helper) generate an `_ide_helper.php` file which tells
PHPStorm what certain classes and function are, so it can perform proper code completion and show documentation.

##### PHP-CS-Fixer

Run `sail composer fix` in the docker container to fix stylistic errors in your code
using [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer). This will also be done automatically when creating a
pull-request or pushing to a branch with an open pull-request.

##### Laravel Rector

Run `sail composer rector` in the docker container to automatically upgrade your code
using [Laravel-Rector](https://getrector.com/documentation) to meet the rules set in rector.php.
This will also be done automatically when creating a
pull-request or pushing to a branch with an open pull-request.

##### Larastan

There is also the option for static analysis of your code. Run `sail composer analyse` in the docker container to
let [Larastan](https://github.com/nunomaduro/larastan) find any potential bugs in your code.

## Debugging

### Xdebug

Xdebug is activated in Laravel Sail to aid you while debugging the website. Xdebug enables breakpoints and step
debugging which can easily be controlled from your IDE. For this to work, you will have to set up your IDE correctly.

#### Xdebug in PhpStorm

PhpStorm for zero-configuration debugging. In case of zero-configuration debugging, you do not need to create any debug
configuration. Instead, you open the starting page of your PHP application in the browser manually, and then activate
the debugging engine from the browser, while PhpStorm listens to incoming debugger connections. For full instructions on
how to use zero-configuration debugging, check out
the [PhpStorm documentation](https://www.jetbrains.com/help/phpstorm/zero-configuration-debugging.html)

### Clockwork

[Clockwork](https://underground.works/clockwork) is a php dev tool in your browser. When running the website in debug
mode you can access the clockwork debug page at <localhost:8080/clockwork>. The application has various debugging
features such as timelines of runtime requests, database queries and client-metrics.

### Testing

##### For testing we use the standard laravel implementation of tests which uses phpunit.

You can run the tests with the following command:
```sail composer test```.

To make a new test you can use the following command:
```sail artisan make:test {{TestName}}```.

To make a unit test you can use the following command:
```sail artisan make:test {{TestName}} --unit```.
