# Semibreve
Very simple multi-user authentication.

Every so often, you build a website that needs:
  * to run without a database
  * to have an administrator backend
  * to be accessible by a few (say, 10 or so) users

Semibreve is designed for this purpose; to be a secure, multi-user authentication system that doesn't do anything silly like leak the users password (or store it in plain text) or operate over insecure (non-HTTPS) connections unless you want it to.

## Installation
Install Semibreve via Composer like this:

```bash
composer require semibreve/semibreve
```

Or alternatively, if you're using the PHAR (make sure the `php.exe` executable is in your PATH):

```
php composer.phar require semibreve/semibreve
```

## Configuration
Semibreve will require you to create a configuration file that looks something like this:

```yaml
# Don't commit this file to source control, it contains your secret settings.

secret_key: 7WCPTI3of3cp # The secret key the application uses for symmetric encryption
token_length: 32 # The length, in bytes, of any generated authentication tokens.
token_ttl: 1200 # The time to live for authentication tokens, in seconds.
cookie_name: semibreve_auth # The name of the authentication cookie.
config_folder_name: /var/www/semibreve/configs # The path to the Minim configs directory.
user_folder_name: /var/www/semibreve/users # The path to the user configs directory.
session_folder_name: /var/www/semibreve/sessions # The path to the sessions directory.
cookie_ssl_only: false # Whether or not cookies are enables for HTTPS only. If enabled, non-HTTPS requests will fail.
cookie_http_only: true # Whether to restrict cookies to HTTP only and disallow access by client-side script.
```

And at least one file in the directory pointed to by `user_folder_name` that contains a file that looks something like this:

```yaml
username: me@example.com
password_hash: $2y$10$anQ73SyMTt6qeQwKkDL5D.gufN3JLTLwb60AZAq3idG32ev.nf7ae
role: admin
```

The above file should be named `2e0d5407ce8609047b8255c50405d7b1.yaml` because that's the MD5 hash of the `username`. This is important. Never use an online hashing service for something like this, but convert hashes created by [this service](http://onlinemd5.com/) to lowercase and that will work.

The above file specifies some default credentials:

```
Email: me@example.com
Password: demo
```

These *must* be changed before you go into production. Also you need to do the following:

* Copy the demo configuration file above into your project. Make sure it is ignored by any version control systems.
* Open it up in your favorite text editor.
* Change the `secret_key` field to a randomly-generated string at least 12 characters long.
* Change the `salt` field to a randomly-generated string at least 12 characters long.
* The default value of 32 for the `token_length` field should be okay for most applications.
* The default value for the `token_ttl` field of 1200 seconds (20 minutes) should be okay for most applications.
* Change the `config_folder_name`, `user_folder_name` and `session_folder_name` fields to the absolute path of writable directories on your server that Semibreve can read and write, but that your server _will not serve_.
* Change `cookie_ssl_only` field to `true` if you're operating over HTTPS. If you're not, take a long hard look at your application and ask yourself why you're considering asking for user credentials over an insecure connection when amazing, free tools like [Let's Encrypt](https://letsencrypt.org/) exist.
* Leave `cookie_http_only` as `true` to make the authentication cookie readable only over HTTP and not by client-side script.

To see an example usage of Semibreve, [check out the demo repository](https://github.com/semibreve/semibreve-demo).

## Usage
Load your Semibreve configuration file like this:

```php
$semibreve = new Manager(new BaseConfiguration('my-config-file.yml'));
```

From here you can log the user in:

```php
$semibreve->authenticate('email', 'password'); // Authenticate user, user object on success null on failure.
```

Or redirect away from a page based on whether they're logged in or not:

```php
// Check if user is authenticated.
if (!$semibreve->getAuthenticatedUser() === null) {
    header('Location: /forbidden.php'); // Not logged in, go to jail.
    die();
}
```

## Limitations
Don't rely on Semibreve to be secure out of the box and always perform your own penetration testing.
