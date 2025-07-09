# Documentation des tests backend - SCPI Portfolio Simulator

## Vue d'ensemble

Le backend Symfony du projet SCPI Portfolio Simulator est couvert par une suite de tests fonctionnels automatisés avec PHPUnit. Ces tests garantissent la robustesse de l'authentification, la gestion de session, la simulation de portefeuille, et la cohérence des API exposées.

- **Framework de test** : PHPUnit 12
- **Base de données** : PostgreSQL (base dédiée aux tests)
- **Couverture** : Authentification, session, endpoints API, simulation portefeuille

## Organisation des tests

Les tests sont situés dans :
- `backend/tests/Controller/AuthControllerTest.php` : tests d'authentification et de session
- `backend/tests/Controller/PortfolioControllerTest.php` : tests des endpoints métier (SCPI, simulation, etc.)

## Préparation de l'environnement de test

1. **Base de données dédiée**
   - Les tests utilisent une base séparée (`scpi_portfolio_test`) pour éviter toute pollution des données de développement.
   - Création manuelle :
     ```bash
     createdb scpi_portfolio_test
     ```
2. **Migrations et fixtures**
   - Appliquer les migrations :
     ```bash
     php bin/console doctrine:migrations:migrate --env=test --no-interaction
     ```
   - Charger les fixtures (utilisateurs, SCPIs, etc.) :
     ```bash
     php bin/console doctrine:fixtures:load --env=test --no-interaction
     ```

## Lancement des tests

Pour exécuter tous les tests :
```bash
php bin/phpunit
```

Pour exécuter un fichier ou un test précis :
```bash
php bin/phpunit tests/Controller/AuthControllerTest.php
php bin/phpunit --filter testLoginSuccess
```

## Cas de test couverts

### Authentification & Session (`AuthControllerTest.php`)
- **Inscription**
  - Succès avec email unique
  - Échec si email déjà utilisé
  - Échec si email invalide
- **Connexion**
  - Succès avec credentials valides
  - Échec avec mauvais mot de passe
- **Session**
  - Vérification de la persistance de session après login
  - Vérification de l'accès à `/api/me` authentifié et non authentifié
- **Déconnexion**
  - Succès et invalidation de session

### Endpoints métier (`PortfolioControllerTest.php`)
- **Inscription** (email unique à chaque exécution)
- **Connexion et session** (vérification du cookie PHPSESSID)
- **Liste des SCPI** (requête authentifiée)
- **Simulation de portefeuille** (requête authentifiée, vérification du calcul et du format de réponse)

## Particularités et bonnes pratiques

- **Email unique pour l'inscription** :
  - Les tests d'inscription génèrent un email unique à chaque exécution pour éviter les conflits avec les fixtures.
- **Gestion de la session dans les tests** :
  - Les tests récupèrent explicitement le cookie de session (`PHPSESSID`) après login et le transmettent dans les requêtes suivantes pour simuler un vrai utilisateur connecté.
- **Vérification des erreurs 401** :
  - Les tests tiennent compte du comportement standard de Symfony (page HTML ou JSON d'erreur générique) pour les accès non authentifiés.
- **Compatibilité PHPUnit 12** :
  - Les assertions utilisent `assertStringContainsString` ou le décodage JSON explicite, car `assertJsonContains` n'est pas disponible.

## Résultat attendu

Après configuration correcte de la base de test et des fixtures, la commande :
```bash
php bin/phpunit
```
doit afficher :
```
............                                                      12 / 12 (100%)

OK (12 tests, 37 assertions)
```

## Conseils de maintenance

- **Purger la base de test** si besoin :
  ```bash
  php bin/console doctrine:database:drop --env=test --force
  php bin/console doctrine:database:create --env=test
  php bin/console doctrine:migrations:migrate --env=test
  php bin/console doctrine:fixtures:load --env=test
  ```
- **Ajouter de nouveaux tests** pour chaque nouvelle fonctionnalité ou endpoint protégé.
- **Vérifier la compatibilité des assertions** après mise à jour de PHPUnit.

---

*Dernière mise à jour : juillet 2025* 