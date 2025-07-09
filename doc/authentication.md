# Documentation Authentification - SCPI Portfolio Simulator

## Vue d'ensemble

Le système d'authentification du projet SCPI Portfolio Simulator utilise une architecture moderne avec :
- **Backend** : Symfony 6 avec authentification session-based
- **Frontend** : Vue.js 3 avec Pinia pour la gestion d'état
- **Communication** : API REST avec cookies de session

## Architecture

### Backend (Symfony 6)

#### Entité User
```php
// backend/src/Entity/User.php
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    private ?int $id = null;
    private string $email;
    private array $roles = [];
    private string $password;
    
    // Méthodes getters/setters...
}
```

**Caractéristiques :**
- Implémente `UserInterface` et `PasswordAuthenticatedUserInterface` de Symfony
- Table renommée en `app_user` pour éviter les conflits avec le mot-clé SQL `user`
- Validation des champs avec les annotations Symfony
- Identifiant unique : email

#### Contrôleur d'authentification
```php
// backend/src/Controller/AuthController.php
class AuthController extends AbstractController
{
    // Routes disponibles :
    // POST /api/register - Inscription
    // POST /api/login - Connexion
    // POST /api/logout - Déconnexion
    // GET /api/me - Vérification de l'état d'authentification
}
```

**Fonctionnalités :**
- **Inscription** : Validation email, hashage mot de passe, vérification unicité
- **Connexion** : Vérification credentials, création session, token de sécurité
- **Déconnexion** : Suppression token, invalidation session
- **Vérification** : Endpoint pour vérifier l'état d'authentification

#### Configuration de sécurité
```yaml
# backend/config/packages/security.yaml
security:
    password_hashers:
        App\Entity\User: 'auto'
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email
    firewalls:
        auth:
            pattern: ^/api/(login|register)
            security: false
        api:
            pattern: ^/api
            stateless: false
            provider: app_user_provider
    access_control:
        - { path: ^/api/register, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/api/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/api, roles: ROLE_USER }
```

**Points clés :**
- Firewall séparé pour les routes d'authentification (`auth`)
- Firewall principal pour les routes protégées (`api`)
- Contrôle d'accès basé sur les rôles
- Sessions non-stateless pour la persistance

#### Configuration des sessions
```yaml
# backend/config/packages/framework.yaml
framework:
    session:
        handler_id: null
        cookie_secure: false      # Désactivé pour le développement
        cookie_samesite: lax     # Compatible cross-origin
        cookie_httponly: false   # Permet l'accès JS pour debug
        cookie_lifetime: 3600    # 1 heure
```

**Configuration optimisée pour le développement :**
- `cookie_secure: false` : Permet les cookies en HTTP
- `cookie_httponly: false` : Permet l'accès JavaScript
- `cookie_samesite: lax` : Compatible avec les requêtes cross-origin

#### Configuration CORS
```yaml
# backend/config/packages/nelmio_cors.yaml
nelmio_cors:
    defaults:
        allow_origin: ['http://localhost:5173']
        allow_credentials: true
        allow_headers: ['Content-Type', 'Authorization']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'DELETE']
    paths:
        '^/api/':
            allow_origin: ['http://localhost:5173']
            allow_credentials: true
```

**Points importants :**
- `allow_credentials: true` : Essentiel pour l'envoi des cookies
- Origine autorisée : `http://localhost:5173` (frontend Vite)

### Frontend (Vue.js 3)

#### Configuration Axios
```javascript
// frontend/src/axios.js
import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  withCredentials: true,  // Envoie automatiquement les cookies
});

export default api;
```

**Configuration clé :**
- `withCredentials: true` : Envoie les cookies avec chaque requête
- `baseURL: '/api'` : Préfixe pour toutes les requêtes API

#### Store Pinia (Gestion d'état)
```javascript
// frontend/src/store/userStore.js
export const useUserStore = defineStore('user', {
  state: () => ({
    user: null,
    isAuthenticated: null,  // null = état inconnu
    error: null,
  }),
  actions: {
    async register(email, password) { /* ... */ },
    async login(email, password) { /* ... */ },
    async logout() { /* ... */ },
    async checkAuth() { /* ... */ },
  },
});
```

**Actions disponibles :**
- `register()` : Inscription (ne connecte pas automatiquement)
- `login()` : Connexion et mise à jour du state
- `logout()` : Déconnexion et nettoyage du state
- `checkAuth()` : Vérification de l'état d'authentification

#### API Service
```javascript
// frontend/src/api/index.js
export const register = (email, password) => api.post('/register', { email, password });
export const login = (email, password) => api.post('/login', { email, password });
export const logout = () => api.post('/logout');
export const me = () => api.get('/me');
```

#### Configuration du routeur
```javascript
// frontend/src/router/index.js
router.beforeEach(async (to, from, next) => {
  const userStore = useUserStore();
  if (userStore.isAuthenticated === null) {
    await userStore.checkAuth();
  }
  if (to.name !== 'Login' && to.name !== 'Register' && !userStore.isAuthenticated) {
    next({ name: 'Login' });
  } else {
    next();
  }
});
```

**Fonctionnalités :**
- Guard de route automatique
- Vérification de l'authentification avant navigation
- Redirection vers login si non authentifié

#### Point d'entrée de l'application
```javascript
// frontend/src/main.js
const userStore = useUserStore();
userStore.checkAuth().finally(() => {
  app.mount('#app');
});
```

**Comportement :**
- Vérification de l'authentification au démarrage
- Montage de l'app seulement après la vérification
- Évite les incohérences d'état

#### Configuration Vite (Proxy)
```javascript
// frontend/vite.config.js
export default defineConfig({
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
        secure: false,
      }
    }
  }
});
```

**Avantages :**
- Proxy automatique des requêtes `/api` vers le backend
- Évite les problèmes CORS en développement
- Transparence pour le frontend

## Flux d'authentification

### 1. Inscription
```
1. Utilisateur remplit le formulaire d'inscription
2. Frontend appelle POST /api/register
3. Backend valide l'email et vérifie l'unicité
4. Mot de passe hashé et utilisateur créé
5. Réponse 200 avec userId
6. Redirection automatique vers /login
```

### 2. Connexion
```
1. Utilisateur remplit le formulaire de connexion
2. Frontend appelle POST /api/login
3. Backend vérifie credentials
4. Session créée avec token de sécurité
5. Cookie PHPSESSID envoyé au navigateur
6. State frontend mis à jour (isAuthenticated: true)
7. Redirection vers /portfolio
```

### 3. Persistance de session
```
1. Au refresh de la page
2. Frontend appelle checkAuth() au démarrage
3. Requête GET /api/me avec cookie PHPSESSID
4. Backend vérifie la session
5. Réponse 200 avec données utilisateur ou 401
6. State frontend mis à jour en conséquence
```

### 4. Déconnexion
```
1. Utilisateur clique sur "Déconnexion"
2. Frontend appelle POST /api/logout
3. Backend supprime le token et invalide la session
4. Cookie PHPSESSID supprimé
5. State frontend nettoyé (isAuthenticated: false)
6. Redirection vers /login
```

## Sécurité

### Mesures implémentées

1. **Hashage des mots de passe**
   - Utilisation de `UserPasswordHasherInterface`
   - Algorithme automatique (bcrypt/argon2)

2. **Validation des données**
   - Validation email côté backend
   - Vérification unicité des emails
   - Sanitisation des entrées

3. **Gestion des sessions**
   - Sessions Symfony sécurisées
   - Tokens de sécurité
   - Invalidation propre des sessions

4. **Contrôle d'accès**
   - Routes protégées par rôles
   - Firewalls Symfony
   - Guard de route côté frontend

5. **CORS sécurisé**
   - Origines autorisées limitées
   - Credentials requis
   - Headers autorisés contrôlés

### Configuration de développement

**Paramètres optimisés pour le développement local :**
- `cookie_secure: false` : Permet les cookies en HTTP
- `cookie_httponly: false` : Permet l'accès JavaScript
- `cookie_samesite: lax` : Compatible cross-origin

**⚠️ Attention :** Ces paramètres ne sont pas sécurisés pour la production.

## API Endpoints

### POST /api/register
**Inscription d'un nouvel utilisateur**

**Request :**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200) :**
```json
{
  "success": true,
  "userId": 1
}
```

**Response (400) :**
```json
{
  "error": "Email et mot de passe requis"
}
```

**Response (409) :**
```json
{
  "error": "Email déjà utilisé"
}
```

### POST /api/login
**Connexion d'un utilisateur**

**Request :**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (200) :**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "user@example.com",
    "roles": ["ROLE_USER"]
  }
}
```

**Response (401) :**
```json
{
  "error": "Email ou mot de passe incorrect"
}
```

### POST /api/logout
**Déconnexion de l'utilisateur**

**Request :** Aucun body requis

**Response (200) :**
```json
{
  "success": true
}
```

### GET /api/me
**Vérification de l'état d'authentification**

**Request :** Aucun body requis

**Response (200) :**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "user@example.com",
    "roles": ["ROLE_USER"]
  }
}
```

**Response (401) :**
```json
{
  "error": "Non authentifié"
}
```

## Déploiement

### Configuration de production

**Modifications nécessaires :**

1. **Sécurité des cookies :**
```yaml
framework:
    session:
        cookie_secure: true      # HTTPS requis
        cookie_httponly: true    # Sécurité renforcée
        cookie_samesite: strict  # Sécurité maximale
```

2. **CORS :**
```yaml
nelmio_cors:
    defaults:
        allow_origin: ['https://votre-domaine.com']
```

3. **Proxy Vite :**
- Supprimer la configuration proxy
- Configurer un reverse proxy (nginx/apache)
- Ou utiliser un serveur de production

### Variables d'environnement

**Backend (.env) :**
```env
APP_SECRET=votre-secret-symfony
DATABASE_URL=postgresql://user:pass@host:port/db
```

**Frontend :**
- Configurer l'URL de l'API de production
- Adapter la configuration Axios

## Dépannage

### Problèmes courants

1. **Session perdue au refresh**
   - Vérifier `cookie_httponly: false` en dev
   - Vérifier `withCredentials: true` côté Axios
   - Vérifier la configuration CORS

2. **Erreur CORS**
   - Vérifier `allow_credentials: true`
   - Vérifier les origines autorisées
   - Vérifier le proxy Vite

3. **Déconnexion qui ne fonctionne pas**
   - Vérifier que le firewall n'a pas de config logout
   - Vérifier que le contrôleur supprime bien le token

4. **Validation d'inscription**
   - Vérifier la validation côté backend
   - Vérifier les contraintes de l'entité User

### Debug

**Côté backend :**
```bash
# Vérifier les routes
php bin/console debug:router | grep api

# Tester l'authentification
curl -X POST http://localhost:8000/api/login -H "Content-Type: application/json" -d '{"email":"user@example.com","password":"password"}' -c cookies.txt
curl -X GET http://localhost:8000/api/me -b cookies.txt
```

**Côté frontend :**
```javascript
// Vérifier les cookies
console.log(document.cookie);

// Vérifier l'état du store
console.log(useUserStore().$state);
```

## Tests

### Tests backend
```bash
# Lancer les tests
php bin/phpunit tests/Controller/AuthControllerTest.php
```

### Tests frontend
```bash
# Lancer les tests (si configurés)
npm run test
```

## Maintenance

### Base de données
```bash
# Créer une migration
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate

# Charger les fixtures
php bin/console doctrine:fixtures:load
```

### Cache
```bash
# Vider le cache
php bin/console cache:clear
```

---

