# SCPI Portfolio Simulator

Un simulateur de portefeuille SCPI moderne développé avec Symfony 6 (backend API) et Vue.js 3 (frontend), permettant aux utilisateurs de simuler leurs investissements en SCPI et de calculer leurs rendements potentiels.

## Fonctionnalités

- **Authentification complète** : Inscription, connexion et gestion de session
- **Simulation de portefeuille** : Calcul de rendements basé sur des SCPI réelles
- **Interface moderne** : Design responsive avec Tailwind CSS
- **API REST** : Backend Symfony 6 avec authentification session-based
- **Tests automatisés** : Suite de tests PHPUnit complète
- **Documentation complète** : Guides détaillés pour chaque aspect du projet

## Architecture

Le projet suit une architecture **backend/frontend séparés** :

- **Backend** : Symfony 6 API REST avec PostgreSQL et Doctrine ORM
- **Frontend** : Vue.js 3 SPA avec Pinia, Vue Router et Axios
- **Communication** : HTTP/JSON avec authentification par session
- **Développement** : Proxy Vite pour éviter les problèmes CORS

## Prérequis

- **PHP** 8.1 ou supérieur
- **Composer** (gestionnaire de dépendances PHP)
- **Node.js** 16 ou supérieur
- **npm** ou **yarn**
- **PostgreSQL** 12 ou supérieur
- **Symfony CLI** (optionnel, pour les commandes Symfony)

### Vérification des prérequis

```bash
# Vérifier PHP
php --version

# Vérifier Composer
composer --version

# Vérifier Node.js
node --version

# Vérifier npm
npm --version

# Vérifier PostgreSQL
psql --version

# Vérifier Symfony CLI (optionnel)
symfony --version
```

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/ourouchy/scpi-portfolio-simulator
cd scpi-portfolio-simulator
```

### 1.5. Configuration initiale

```bash
# Copier le fichier d'environnement
cd backend
cp ".env.copy" .env

# Éditer le fichier .env avec vos paramètres de base de données
# DATABASE_URL="postgresql://username:password@127.0.0.1:5432/scpi_portfolio?serverVersion=15&charset=utf8"
# APP_SECRET=votre_cle_secrete_ici
```

### 2. Configuration du backend

```bash
cd backend

# Installer les dépendances PHP
composer install

# Configuration de la base de données
# Éditer le fichier .env avec vos paramètres de connexion PostgreSQL
# DATABASE_URL="postgresql://username:password@127.0.0.1:5432/scpi_portfolio?serverVersion=15&charset=utf8"

# Créer la base de données
createdb scpi_portfolio
# Ou via psql : CREATE DATABASE scpi_portfolio;

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les données de démonstration
php bin/console doctrine:fixtures:load

# Démarrer le serveur Symfony
symfony serve
# Ou avec PHP : php -S localhost:8000 -t public/
```

### 3. Configuration du frontend

```bash
cd frontend

# Installer les dépendances Node.js
npm install

# Démarrer le serveur de développement
npm run dev
```

## Utilisation

### Accès à l'application

- **Frontend** : http://localhost:5173
- **Backend API** : http://localhost:8000/api

### Compte de démonstration

Un compte utilisateur de démonstration est créé automatiquement :
- **Email** : `user@example.com`
- **Mot de passe** : `password`

### Workflow utilisateur

1. **Inscription** : Créer un nouveau compte
2. **Connexion** : Se connecter avec ses identifiants
3. **Simulation** : Composer un portefeuille SCPI et voir les résultats
4. **Navigation** : Interface intuitive avec barre de navigation

## SCPI disponibles

Le simulateur inclut 3 SCPI de démonstration :

| SCPI | Taux de rendement annuel |
|------|-------------------------|
| SCPI Alpha | 4.5% |
| SCPI Beta | 5.1% |
| SCPI Gamma | 4.2% |

## Tests

### Préparation de l'environnement de test

```bash
cd backend

# Créer la base de données de test
createdb scpi_portfolio_test

# Appliquer les migrations de test
php bin/console doctrine:migrations:migrate --env=test --no-interaction

# Charger les fixtures de test
php bin/console doctrine:fixtures:load --env=test --no-interaction
```

### Exécution des tests

```bash
# Tous les tests
php bin/phpunit

# Tests spécifiques
php bin/phpunit tests/Controller/AuthControllerTest.php
php bin/phpunit --filter testLoginSuccess
```

## Documentation

Le projet inclut une documentation complète dans le dossier `doc/` :

- **[Authentification](doc/authentication.md)** : Guide complet de l'authentification (backend, frontend, API, sécurité)
- **[Frontend](doc/frontend.md)** : Documentation technique du frontend Vue.js 3
- **[Simulation](doc/simulation.md)** : Détails de la logique de simulation de portefeuille
- **[Tests](doc/tests.md)** : Guide des tests backend et bonnes pratiques
- **[Structure](doc/structure.md)** : Architecture complète du projet
## Utilisation rapide avec Makefile

Le projet inclut un `Makefile` pour automatiser les tâches les plus courantes en une seule commande :

### Installation complète (backend + frontend + base de données) :

```bash
make setup
```

### Commandes utiles

| Commande        | Description                                              |
| --------------- | -------------------------------------------------------- |
| `make backend`  | Installe les dépendances PHP (Symfony)                   |
| `make frontend` | Installe les dépendances Node.js (Vue.js)                |
| `make db`       | Crée la base de données locale (scpi\_portfolio)         |
| `make migrate`  | Applique les migrations Doctrine                         |
| `make fixtures` | Charge les données de démonstration                      |
| `make serve`    | Démarre le serveur Symfony (backend)                     |
| `make dev`      | Démarre le serveur de développement Vite (frontend)      |
| `make test`     | Prépare la BDD de test et exécute tous les tests PHPUnit |

> Assurez-vous d'avoir modifié le fichier `.env` avant de lancer `make setup`.


---

