# AlsaceMarket

AlsaceMarket est une boutique en ligne développée avec Symfony. Ce projet de
portfolio met en œuvre le parcours d’achat complet : consultation du catalogue,
création de compte, panier, adresses, paiement Stripe et confirmation de commande
par e-mail.

## Fonctionnalités

- catalogue de produits et pages de détail ;
- inscription, connexion et gestion du profil ;
- panier persistant associé à l’utilisateur ;
- gestion des adresses de livraison ;
- création de commandes avec conservation du nom et du prix des produits ;
- paiement via Stripe Checkout ;
- traitement sécurisé du webhook Stripe ;
- envoi asynchrone de l’e-mail de confirmation avec Symfony Messenger ;
- migrations Doctrine, fixtures et tests fonctionnels ;
- environnement Docker pour le développement et la production.

## Stack technique

- PHP 8.4+
- Symfony 8.1
- Doctrine ORM et PostgreSQL 16
- Twig, Symfony Forms, Validator et Security
- Stripe PHP SDK
- Symfony Messenger et Mailer
- Docker Compose, PHP-FPM et Nginx
- PHPUnit et Zenstruck Foundry

## Installation locale

Prérequis : Docker et Docker Compose.

```bash
cp .env.local.example .env.local
```

Modifiez `.env.local` avec des valeurs locales et ajoutez les variables
nécessaires à Stripe, au mailer et à Messenger si elles ne sont pas déjà
définies dans l’environnement.

Lancez les services :

```bash
docker compose up -d --build
```

Initialisez la base de données :

```bash
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction
docker compose exec php php bin/console doctrine:fixtures:load --no-interaction
```

L’application est disponible sur [http://localhost](http://localhost).

## Tests

Exécutez la suite de tests depuis le conteneur PHP :

```bash
docker compose exec php php bin/phpunit
```

Les tests couvrent notamment le catalogue, les adresses, la création de
commande et le webhook Stripe. Le plan d’extension de la couverture se trouve
dans [`docs/testing-plan.md`](docs/testing-plan.md).

## Déploiement de production

Créez le fichier de configuration de production :

```bash
cp .env.prod.local.example .env.prod.local
```

Remplacez les valeurs fictives par de vrais secrets, puis démarrez la stack :

```bash
docker compose --env-file .env.prod.local \
  -f compose.yaml -f compose.prod.yaml \
  -p symfony-shop-prod up -d --build
```

Après le déploiement, appliquez les migrations et videz le cache :

```bash
docker compose -p symfony-shop-prod exec php \
  php bin/console doctrine:migrations:migrate --no-interaction
docker compose -p symfony-shop-prod exec php \
  php bin/console cache:clear --env=prod
```

Le service web écoute sur le port 80. La terminaison TLS et le nom de domaine
public doivent être configurés au niveau du proxy inverse du VPS.

## Architecture

- `src/Controller` : contrôleurs HTTP minces ;
- `src/Service` : logique métier du panier, des commandes, des profils et du
  paiement ;
- `src/Entity` et `src/Repository` : modèle de données et accès Doctrine ;
- `src/Message` et `src/MessageHandler` : traitement asynchrone des e-mails ;
- `templates/` : interface serveur rendue avec Twig ;
- `migrations/` : évolution versionnée du schéma PostgreSQL ;
- `docker/` et `compose*.yaml` : environnements de développement et de
  production.

## Sécurité

Les fichiers `.env.local` et `.env.prod.local` contiennent des secrets et ne
doivent jamais être commités. Utilisez le webhook Stripe officiel plutôt qu’une
confirmation de paiement basée uniquement sur la redirection du navigateur.
