# InstaApp

## Dockerized (Image-Based)

To build an image with name `instapp:main`:

1. Copy `.env.docker` to `.env`
2. Run command below:

```
docker buildx build -t instapp:main \
--cache-from=type=gha \
--cache-to=type=gha,mode=max \
--progress=plain \
.
```

3. Run `docker compose up -d` or `./nge up -d`.
4. Access `http://localhost:8085/up` or depends on `DOCKER_PORT` you set.
5. You may access swagger at `http://localhost:4505`
6. Finally, run `./nge artisan migrate` to run migrations.

## For Development

1. Copy `.env.example` and setup neccessary database, or use sqlite
2. Run `composer dev`