I spent a little more time on this than intended, but it was a nice to get back to basics with it. As I have become used to relying on existing frameworks, this has allowed for a bit of a research opportunity / learning curve for me.
Without using a full framework, I did opt for building some components such as the RouteMapper and ContainerFactory. This approach is what I use in my current role and I wanted to give examples of that where possible, whilst also trying to make this project easier to expand upon.

<h2>Set up</h2>
I included an OpenAPI spec. This can be imported into a tool such as Postman for ease of testing the API.

<h4>To set up the project using Docker:</h4>

```docker compose up -d```

```docker exec -it octopus-api-php-fpm-1 composer install```

```docker exec -it octopus-api-php-fpm-1 php /application/scripts/migrate.php```

<h4>To run the tests using Docker:</h4>

```docker exec -it octopus-api-php-fpm-1 ./vendor/bin/phpunit tests```
