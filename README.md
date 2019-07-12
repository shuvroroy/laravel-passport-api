# Laravel With Passport Authentication Using TDD

Basically this is a starter kit for you to integrate Laravel with Passport Authentication

## Quick Start

- Clone this repo or download it's release archive and extract it somewhere
- You may delete .git folder if you get this code via git clone
- Run `composer install`
- Configure your .env file for authenticating via database and mail server for email verification
- Run `php artisan key:generate`
- Run `php artisan passport:install`
- Run `php artisan serve`
- Site url `http://localhost:8000`

## Main Features

For each controller there's an already setup route in `routes/api.php` file:

* `POST api/login`, to do the login and get your access token;
* `POST api/register`, to create a new user into your application;
* `POST api/email/verify/{user}`, to verify a user with email verification;
* `POST api/email/resend`, to resend email verification link to email;
* `POST api/password/email`,  to recover your credentials;
* `POST api/password/reset`, to reset your password after the recovery;
* `POST api/logout`, to log out the user by invalidating the passed token;
* `GET api/me`, to get current user data with valid token;
* `POST api/settings/profile`, to update user profile with valid token;
* `GET api/settings/password`, to update user password with valid token;


## Testing

```bash
    vendor/bin/phpunit
```
