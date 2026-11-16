# AlsaceMarket

Docker Compose deployment for local development and a single VPS.

## Deploy

Create `.env.local` from the example. This file is untracked and contains the environment-specific values:

```bash
cp .env.local.example .env.local
docker compose up -d --build
```

To test the production image locally:

```bash
cp .env.prod.local.example .env.prod.local
# replace the placeholder secret and password
docker compose --env-file .env.prod.local \
  -f compose.yaml -f compose.prod.yaml \
  -p symfony-shop-prod up -d --build
```

The VPS uses the same production command with its real `.env.prod.local` file.

The application listens on port 80. Put TLS termination and the public domain in front of it using the VPS reverse proxy or load balancer.

Required application commands run inside the PHP container:

```bash
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console cache:clear --env=prod
```

Never commit `.env.local` or environment secrets.
