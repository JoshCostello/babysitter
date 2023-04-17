# Babysitter Kata
An application based on requirements found as part of the [babysitter kata](https://gist.github.com/jameskbride/5482722).

This is a Laravel 10 and PHP 8.2 application. You may run the app however you prefer, but the easiest solution for
getting up and running is to use Laravel Sail. A `docker-compose.yml` file is included with the project. Assuming
you have Docker Desktop installed, you should be able to run the following command to start getting going:

```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

This should install a small Docker image that is used to run Composer. Once the process finishes, you will want to
create an environment file for your application. Copy the `.env.example` file in the root of the project to a new
file called `.env`. Now, you can then start the application by running `./vendor/bin/sail up -d`. This will install 
additional images, create a container for the application, and set the process in the background. Once finished, you
will need to create a new application key for the app by running the following command: `./vendor/bin/sail artisan
key generate`. With the key generated, the app should be running. You should be able to visit it in your browser
by going to [localhost](http://localhost). You may run the test suite by running the following command: 
`./vendor/bin/sail test`.

*Beware*: If you are developing locally and are encountering port conflicts, you may adjust the port that this
application is connecting to in the `docker-compose.yml` file in the root of the application.

This application also ships with some light styling using Tailwind CSS and Vite. To install and build the assets, 
you may run the following commands: `./vendor/bin/sail npm install && ./vendor/bin/sail npm run build`. 

## Assumptions
- We're only given some small details about when the sitter may arrive. It appears possible that there could be
overlap in timing depending on arrival, bedtime and departure. We're going to assume that we should charge the
post-midnight rate after midnight _even if_ the bedtime is after midnight.
- Related to the previous item, we will assume that the babysitter is maximizing their earnings. Since we don't
have fractional hours, we will calculate times in a manner that result in the most time at the highest available
pay rate. For instance, if the arrival time is 8:49pm, we'd log an arrival of 8:00pm. If the departure time is
10:01pm, we'd log a departure time of 11:00pm.
- Given that there is the potential for an 11-hour shift, we're going to assume that we have to account for
overtime pay and calculate that rate at time-and-a-half.

## Questions
- What is the preferred calculation of the time-and-a-half rate? Should we be using a blended rate or the rate 
for the currently active job? Implemented [ADP's calculation of overtime for multiple pay rates](https://www.adp.com/resources/articles-and-insights/articles/h/how-to-calculate-overtime-pay.aspx).
