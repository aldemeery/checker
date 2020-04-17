
# Check in/out for attendance on [hr.my](https://hr.my)

If you are like me and hate/forget to check in/out for your attendance, and if you are using [hr.my](https://hr.my), then this is for you.
A tool the automates check in/out for attendance on [hr.my](https://hr.my)

* [Installation](#installation)
* [Configuration](#configuration)
  * [Email and Passwod](#email-and-password)
  * [Check in/out times](#check-inout-times)
  * [Timezone](#timezone)
  * [Holidays](#holidays)
  * [Attendance Mode](#attendance-mode)

## Installation

Cone...[configure](#configuration)...install dependencies...start:

``` bash
git clone https://github.com/aldemeery/checker.git
cd checker
composer install
composer start
```

## Configuration
Once the application is cloned, before starting it, go and configure your data in `config.php`

### Email and Password
```php
    // ...
    'email' => 'mail@example.com', // Your email.
    'password' => 'password', // Your password.
```
### Check in/out times
```php
    // ...
    'check-in' => '8:00 am', // Time you are supposed to check-in at
    'check-out' => '4:30 pm', // Time you are supposed to check-out at
```
And you have two approaches for checking out:
```php
'check-out' => '4:00 pm' // Will check-out at 4:00 pm, no matter what..
```
Or
```php
'check-out' => '+9 hours' // Will check-out after 8 hours have passed since you checked-in
```

> NOTE:
> Values for `check-in` and `check-out` can be any [supported date and time formats](https://www.php.net/manual/en/datetime.formats.php) in PHP.

### Timezone
Be sure to provide a timezone...
```php
// ...
'timezone' => 'Africa/Cairo',
```

Here's a [list of the supported timezones](https://www.php.net/manual/en/timezones.php)

### Holidays
You can add weekend days and holidays as well...
In these days you don't ckeck in/out
```php
    // ...
    'holidays' => [
        'friday',
        'saturday',
        '2020-08-02' // dates must be in Y-m-d forma
    ]
```
### Attendance mode
Normally an employee does not show up every day at the exact same moment...*(unless he/she is a psycho or something)*, that's why you have three attendance modes to choose from:

   * `psycho` You check in and out every day at the exact same second.
   * `normal` Gives you `1` to `15` minutes tolerance, so you check in or out every day  a bit randomly, but never later than `15` minutes.
   * `asshole`For those how sleep untill half of the day is gone, gives you `1` to `4` hours tolerance, so you will never be less than hour late.

```php
    // psycho (+0 seconds)
    // normal (+1 to +15 minutes)
    // asshole (+1 to +4 hours)
    'mode' => 'normal',
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
