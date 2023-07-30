# Laravel VisitLog

## Introduction ##

VisitLog is a simple Laravel 5 package that can be used to log visitor information and save it into database. 

## Features ##

 - Other than basic log such as IP, Browser and OS, it can also log Location information.
 - Allows to log both unique and non-unique visits based on IP.
 - Allows to cache the visits based on IP.
 - Allows to log authenticated user info.
 - Provides log viewer page out of box.
 - Provides basic http authentication for app users.
 - Ability to ban users by their IP


**Note:** VisitLog cannot detect same user/IP coming from some anonymizer so it cannot differentiate that.

## Screenshot ##

![Main Window](https://raw.github.com/srdev93/visitlog/master/screen.png)

*Note: Info in above screenshot is fake.*

## Requirements ##

 - PHP >= 5.5.9
 - Laravel 5

## Installation ##

Install via composer
```
composer require srdev93/visitlog
```

For Laravel < 5.5:

Add Service Provider to `config/app.php` in `providers` section
```php
SrDev93\VisitLog\VisitLogServiceProvider::class,
```

Add Facade to `config/app.php` in `aliases` section
```php
'VisitLog' => SrDev93\VisitLog\Facades\VisitLog::class,
```

---

Run `php artisan vendor:publish` to publish package's config and migration file. You should now have `config/visitlog.php` file published. It will also publish migration file in `database/migrations` folder.

Run `php artisan migrate` to create `visitlogs` table in your database.

## Config Options ##

 - `route` : Route where visit log will be available.
 - `iptolocation` : By default, only IP, Browser and OS info is logged. However if you set this option to `true`, it will also log Location info through  http://ip-api.com/ service.
 - `cache` : If `iptolocation` is set to `true`, this option can be used to cache the results instead of requesting Location info each time from http://ip-api.com/. 
 - `unique` : If `true`, it will only log unique visits based on IP address. If `false`, it will log each visit even from same IP.
 - `log_user` : If `true`, it will also log authenticated user info.
 - `user_name_fields` : If `log_user` is `true`, this option can be used to specify name fields of user from your Users table in database.
 - `visitlog_page` : If `true`, a default log viewer page can be viewed at `http//yourapp.com/your_route` to see all the logs. Here `your_route` is the first option above.
 - `http_authentication` : If `visitlog_page` is `true`, this option can be used to show visit log page to only authenticated app users by asking them email and password via basic http authentication.

## Saving Log Info ##

To save logs, just call `save` method of `VisitLog` facade:
`VisitLog::save();`

**Tip:** If you want to save only unique logs based on IP, login or post-login page is good place to use the `save` method on because generally login page isn't visited again after user is authenticated. If you also want to save authenticated user, calling `save` method inside login post method seems to be good idea.

If however, you have set `unique` option to `false` and want to log all visits, calling `save` method in common location is good idea like base controller of your app.

## Viewing Log Info ##

If config option `visitlog_page` is set to `true`, you can view all visit logs by browsing to `http//yourapp.com/your_route`.

**Note:** If you don't want to allow `visitlog` page to be publicly seen, set the option `visitlog_page` to `false` and now `http//yourapp.com/your_route` will result in `404` error.

In this case, you can still show log info in some authenticated area of your app by using `all` method of `VisitLog` facade: `$visitLogs = VisitLog::all();` and it will give you `Collection` that you can iterate over and show in your own view file.

## License ##

This code is published under the [MIT License](http://opensource.org/licenses/MIT).
This means you can do almost anything with it, as long as the copyright notice and the accompanying license file is left intact.

[link-author]: https://github.com/SrDev93
[link-contributors]: https://github.com/SrDev93/visitlog/graphs/contributors
