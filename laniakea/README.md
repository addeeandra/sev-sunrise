# Laniakea

> The name doesn't matter.

```
cp .env.example .env
# fill in DB_USERNAME, DB_PASSWORD, DB_DATABASE in .env

docker-compose up -d

php artisan migrate --database=pgsql-a
php artisan migrate --database=pgsql-b
php artisan migrate --database=pgsql-c

php artisan author:create "Alice" "alice@example.com" --db=pgsql-a
php artisan author:create "Bob" "bob@example.com" --db=pgsql-b

# copy snowflake ID from author:create output
php artisan blog:create "Hello World" <author_snowflake_id> --db=pgsql-a
php artisan blog:create "My Post" <author_snowflake_id> --body="Content" --db=pgsql-a

php artisan blog:list --db=pgsql-a
php artisan blog:list --db=pgsql-b
```

Ini tidak merefleksikan versi production yang sebenarnya, hanya mendemonstrasikan identifikasi melalui ID. Mungkin bagian implementasi Join-nya belum sempurna.I