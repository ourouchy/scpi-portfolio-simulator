# Structure du projet SCPI Portfolio Simulator

```
scpi-portfolio-simulator/
├── backend/ - Application Symfony 6 fournissant l'API REST
│   ├── config/ - Fichiers de configuration de l'application Symfony
│   │   ├── packages/ - Configuration des bundles Symfony
│   │   │   ├── cache.yaml - Configuration du cache
│   │   │   ├── debug.yaml - Configuration du mode debug
│   │   │   ├── doctrine.yaml - Configuration de Doctrine (connexion PostgreSQL, paramètres ORM)
│   │   │   ├── doctrine_migrations.yaml - Configuration des migrations Doctrine
│   │   │   ├── framework.yaml - Configuration du framework Symfony (sessions, etc.)
│   │   │   ├── mailer.yaml - Configuration du service mail
│   │   │   ├── messenger.yaml - Configuration du système de messages
│   │   │   ├── monolog.yaml - Configuration des logs
│   │   │   ├── nelmio_cors.yaml - Configuration CORS pour le développement
│   │   │   ├── notifier.yaml - Configuration des notifications
│   │   │   ├── routing.yaml - Configuration du routage
│   │   │   ├── security.yaml - Configuration de la sécurité (authentification par session, droits d'accès)
│   │   │   ├── translation.yaml - Configuration des traductions
│   │   │   ├── twig.yaml - Configuration du moteur de templates
│   │   │   ├── validator.yaml - Configuration de la validation
│   │   │   └── web_profiler.yaml - Configuration du profiler web
│   │   ├── routes/ - Définition des routes par bundle
│   │   ├── bundles.php - Enregistrement des bundles Symfony
│   │   ├── preload.php - Préchargement des classes
│   │   ├── routes.yaml - Définition des routes principales de l'API
│   │   └── services.yaml - Configuration des services Symfony
│   ├── src/ - Code source du backend Symfony
│   │   ├── Controller/ - Contrôleurs Symfony gérant les différentes routes de l'API
│   │   │   ├── AuthController.php - Contrôleur pour l'inscription des utilisateurs et la connexion (gestion de la session via cookie)
│   │   │   ├── PortfolioController.php - Contrôleur pour les fonctionnalités de portefeuille (calcul du rendement simulé)
│   │   │   └── ScpiController.php - Contrôleur pour les opérations liées aux SCPI (récupération de la liste des SCPI)
│   │   ├── Entity/ - Entités Doctrine représentant les tables de la base de données
│   │   │   ├── User.php - Entité utilisateur (email, mot de passe chiffré, rôles)
│   │   │   ├── Scpi.php - Entité SCPI (nom de la société, taux de rendement annuel)
│   │   │   └── PortefeuilleEntry.php - Entité pour les entrées de portefeuille (relation User-SCPI)
│   │   ├── Repository/ - Repositories Doctrine pour interagir avec les entités
│   │   │   ├── UserRepository.php - Classe repository pour l'entité User (accès aux utilisateurs en base, méthodes de requête spécialisées)
│   │   │   ├── ScpiRepository.php - Classe repository pour l'entité Scpi (accès aux SCPI en base, méthodes de requête spécialisées)
│   │   │   └── PortefeuilleEntryRepository.php - Classe repository pour l'entité PortefeuilleEntry
│   │   ├── DataFixtures/ - Données initiales chargées en base pour le développement/démonstration
│   │   │   └── AppFixtures.php - Insère des données de test (liste de SCPI prédéfinies et compte utilisateur de démonstration) dans la base de données
│   │   └── Kernel.php - Classe principale initialisant le noyau Symfony (configuration de base de l'application)
│   ├── public/ - Répertoire public contenant le point d'entrée HTTP
│   │   └── index.php - Front controller qui initialise l'application Symfony pour chaque requête HTTP
│   ├── tests/ - Tests automatisés (PHPUnit) du backend
│   │   ├── Controller/ - Tests des contrôleurs
│   │   │   ├── AuthControllerTest.php - Tests PHPUnit pour l'authentification (inscription, connexion, session, déconnexion)
│   │   │   └── PortfolioControllerTest.php - Tests PHPUnit vérifiant le calcul du rendement du portefeuille simulé
│   │   └── bootstrap.php - Configuration de bootstrap pour les tests
│   ├── migrations/ - Migrations Doctrine pour la gestion du schéma de base de données
│   │   └── Version20250708205623.php - Migration initiale créant les tables User, Scpi et PortefeuilleEntry
│   ├── assets/ - Assets frontend (CSS, JS, images) pour Symfony
│   ├── bin/ - Scripts binaires Symfony (console, phpunit, etc.)
│   ├── templates/ - Templates Twig (non utilisés dans cette API)
│   ├── translations/ - Fichiers de traduction
│   ├── var/ - Fichiers temporaires (cache, logs, uploads)
│   ├── vendor/ - Dépendances Composer (packages PHP)
│   ├── .phpunit.cache/ - Cache PHPUnit
│   ├── cookies.txt - Fichier de cookies pour les tests
│   ├── composer.json - Dépendances PHP et configuration du projet Symfony (packages requis, autoload, scripts)
│   ├── composer.lock - Verrouillage des versions des dépendances
│   ├── symfony.lock - Verrouillage des versions des bundles Symfony
│   ├── importmap.php - Configuration de l'import map pour les assets
│   ├── phpunit.dist.xml - Configuration de PHPUnit (bootstrap, configuration des suites de tests)
│   ├── compose.yaml - Configuration Docker Compose pour le développement
│   ├── compose.override.yaml - Surcharge de la configuration Docker Compose
│   └── .gitignore - Fichiers et dossiers ignorés par Git pour le backend
├── frontend/ - Application front-end Vue.js 3 (interface utilisateur du simulateur)
│   ├── src/ - Code source de l'application Vue.js
│   │   ├── api/ - Fonctions d'appel API
│   │   │   └── index.js - Fonctions centralisées pour les appels API (register, login, logout, me, getScpis, simulatePortfolio)
│   │   ├── components/ - Composants Vue réutilisables au sein des pages
│   │   │   └── SCPIList.vue - Composant affichant la liste des SCPI avec un champ montant d'investissement pour chacune
│   │   ├── views/ - Composants de pages correspondant aux vues de l'application (utilisés par le routeur)
│   │   │   ├── RegisterView.vue - Page d'inscription avec le formulaire de création de compte utilisateur
│   │   │   ├── LoginView.vue - Page de connexion utilisateur (formulaire de login avec email/mot de passe)
│   │   │   └── PortfolioView.vue - Page principale du simulateur où l'utilisateur compose son portefeuille SCPI et voit le rendement simulé
│   │   ├── store/ - Stores Pinia pour la gestion d'état
│   │   │   ├── userStore.js - Store pour la gestion de l'authentification (user, isAuthenticated, actions login/logout/register/checkAuth)
│   │   │   └── scpiStore.js - Store pour la gestion des données SCPI (scpis, loading, fetchScpis)
│   │   ├── router/ - Configuration du routeur Vue
│   │   │   └── index.js - Configuration du routeur Vue (définit les routes de l'application, protection des routes, guards d'authentification)
│   │   ├── assets/ - Assets statiques (images, icônes, etc.)
│   │   ├── App.vue - Composant racine de l'application Vue (structure générale de l'interface, navigation, gestion de l'authentification)
│   │   ├── axios.js - Configuration globale d'Axios pour les appels HTTP (URL de base de l'API Symfony et envoi automatique des cookies de session)
│   │   ├── main.js - Point d'entrée de l'application front-end qui initialise Vue, le routeur, Pinia, etc., puis monte l'app sur la page HTML
│   │   └── style.css - Styles CSS globaux
│   ├── public/ - Fichiers statiques et HTML public de l'application front-end
│   │   └── index.html - Fichier HTML principal dans lequel l'application Vue est insérée
│   ├── node_modules/ - Dépendances Node.js (packages npm)
│   ├── .vscode/ - Configuration VS Code pour le développement
│   ├── package.json - Dépendances Node.js du front-end et scripts npm (commande de développement, build de production, etc.)
│   ├── package-lock.json - Verrouillage des versions des dépendances npm
│   ├── vite.config.js - Configuration de Vite (outil de build/dev) pour le projet front-end (proxy vers l'API Symfony, plugins Vue et Tailwind)
│   ├── README.md - Documentation spécifique au frontend
│   └── .gitignore - Fichiers et dossiers ignorés par Git pour le frontend
├── doc/ - Documentation du projet
│   ├── authentication.md - Documentation complète de l'authentification (backend, frontend, API, sécurité, déploiement, dépannage)
│   ├── frontend.md - Documentation technique du frontend Vue.js 3 (architecture, composants, gestion d'état, workflow utilisateur)
│   ├── simulation.md - Documentation de la simulation de portefeuille (SCPI disponibles, calculs, API, exemples)
│   ├── structure.md - Ce fichier : structure complète du projet
│   ├── tests.md - Documentation des tests backend (stratégie, workflow, cas couverts, bonnes pratiques)
│   └── tests/ - Documentation spécifique aux tests (si nécessaire)
├── README.md - Instructions pour installer le backend Symfony et le frontend Vue, et lancer l'application en local
└── .gitignore - Fichiers et dossiers ignorés par Git au niveau racine
```

## Technologies utilisées

### Backend (Symfony 6)
- **Framework** : Symfony 6 (PHP 8+)
- **Base de données** : PostgreSQL avec Doctrine ORM
- **Authentification** : Session-based avec cookies
- **API** : REST avec JSON
- **Tests** : PHPUnit 12
- **CORS** : Configuration pour le développement local

### Frontend (Vue.js 3)
- **Framework** : Vue.js 3 (Composition API)
- **Bundler** : Vite
- **Gestion d'état** : Pinia
- **Routage** : Vue Router 4
- **HTTP Client** : Axios
- **Styling** : Tailwind CSS
- **Proxy** : Configuration pour rediriger `/api` vers le backend

## Architecture générale

Le projet suit une architecture **backend/frontend séparés** :
- **Backend Symfony** : API REST pure, sans interface utilisateur
- **Frontend Vue.js** : Application SPA qui consomme l'API backend
- **Communication** : HTTP/JSON avec authentification par session
- **Développement** : Proxy Vite pour éviter les problèmes CORS en local

## Points d'entrée

- **Backend** : `backend/public/index.php` (serveur Symfony)
- **Frontend** : `frontend/public/index.html` (serveur Vite)
- **Tests** : `backend/bin/phpunit` (tests backend)
- **Documentation** : `doc/` (documentation complète du projet)
