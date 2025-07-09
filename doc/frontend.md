# Frontend Vue.js 3 - Documentation technique

## Vue d'ensemble

Le frontend du projet SCPI Portfolio Simulator est développé avec Vue.js 3, utilisant Vite comme bundler, Pinia pour la gestion d'état, Vue Router pour la navigation, et Axios pour les appels API. L'interface utilisateur est stylée avec Tailwind CSS.

**Technologies principales :**
- Vue.js 3 (Composition API)
- Vite (bundler et dev server)
- Pinia (gestion d'état)
- Vue Router 4 (routage)
- Axios (HTTP client)
- Tailwind CSS (styling)

## Architecture du projet

```
frontend/src/
├── main.js              # Point d'entrée de l'application
├── App.vue              # Composant racine avec navigation
├── axios.js             # Configuration Axios
├── api/
│   └── index.js         # Fonctions d'appel API
├── store/
│   ├── userStore.js     # Gestion de l'authentification
│   └── scpiStore.js     # Gestion des données SCPI
├── router/
│   └── index.js         # Configuration des routes
├── views/
│   ├── LoginView.vue    # Page de connexion
│   ├── RegisterView.vue # Page d'inscription
│   └── PortfolioView.vue # Page de simulation
└── components/          # Composants réutilisables (vide)
```

## Configuration et démarrage

### Configuration Vite
Le serveur de développement est configuré avec un proxy pour rediriger les appels `/api` vers le backend Symfony :

```javascript
// vite.config.js
server: {
  proxy: {
    '/api': {
      target: 'http://localhost:8000',
      changeOrigin: true,
      secure: false,
    }
  }
}
```

### Configuration Axios
```javascript
// axios.js
const api = axios.create({
  baseURL: '/api',
  withCredentials: true, // Important pour les cookies de session
});
```

## Gestion d'état avec Pinia

### UserStore (Authentification)
Le store `userStore` gère l'état d'authentification de l'utilisateur :

**État :**
- `user` : données de l'utilisateur connecté
- `isAuthenticated` : statut de connexion (null, true, false)
- `error` : messages d'erreur

**Actions principales :**
- `register(email, password)` : inscription d'un nouvel utilisateur
- `login(email, password)` : connexion utilisateur
- `logout()` : déconnexion
- `checkAuth()` : vérification de l'authentification au chargement

### ScpiStore (Données SCPI)
Le store `scpiStore` gère les données des SCPI disponibles :

**État :**
- `scpis` : liste des SCPI
- `loading` : état de chargement
- `error` : messages d'erreur

**Actions :**
- `fetchScpis()` : récupération de la liste des SCPI depuis l'API

## Routage et navigation

### Configuration des routes
```javascript
const routes = [
  { path: '/register', name: 'Register', component: RegisterView },
  { path: '/login', name: 'Login', component: LoginView },
  { path: '/portfolio', name: 'Portfolio', component: PortfolioView },
  { path: '/', redirect: '/portfolio' }
];
```

### Protection des routes
Le routeur utilise des guards pour protéger les routes :
- Vérification de l'authentification au chargement de l'app
- Redirection automatique vers `/login` si non authentifié
- Accès libre aux pages de connexion et d'inscription

## Composants et vues

### App.vue (Composant racine)
- **Navigation** : barre de navigation avec liens conditionnels
- **Authentification** : affichage de l'email utilisateur et bouton déconnexion
- **Responsive** : design adaptatif avec Tailwind CSS

### LoginView.vue
**Fonctionnalités :**
- Formulaire de connexion (email + mot de passe)
- Validation des champs requis
- Gestion des erreurs d'authentification
- Redirection automatique vers `/portfolio` après connexion
- Lien vers la page d'inscription

**Workflow :**
1. Saisie des identifiants
2. Appel à `userStore.login()`
3. Vérification de la réponse
4. Redirection ou affichage d'erreur

### RegisterView.vue
**Fonctionnalités :**
- Formulaire d'inscription (email + mot de passe)
- Validation des champs
- Gestion des erreurs (email déjà utilisé, etc.)
- Message de succès avec redirection automatique
- Lien vers la page de connexion

**Workflow :**
1. Saisie des informations
2. Appel à `userStore.register()`
3. Affichage du succès
4. Redirection vers `/login` après 1 seconde

### PortfolioView.vue
**Fonctionnalités :**
- Affichage de la liste des SCPI disponibles
- Formulaire de saisie des montants par SCPI
- Simulation de portefeuille en temps réel
- Affichage détaillé des résultats
- Gestion des états de chargement

**Workflow de simulation :**
1. Chargement automatique des SCPI au montage
2. Saisie des montants par l'utilisateur
3. Validation des données (au moins un montant > 0)
4. Appel API `/api/portfolio`
5. Affichage des résultats calculés

## API et communication backend

### Fonctions API (api/index.js)
```javascript
export const getScpis = () => api.get('/scpis');
export const register = (email, password) => api.post('/register', { email, password });
export const login = (email, password) => api.post('/login', { email, password });
export const logout = () => api.post('/logout');
export const me = () => api.get('/me');
export const simulatePortfolio = (portefeuille) => api.post('/portfolio', { portefeuille });
```

### Gestion des erreurs
- Interception des erreurs HTTP dans les stores
- Affichage des messages d'erreur dans l'interface
- Gestion des codes d'erreur (400, 401, 409, etc.)

## Workflow utilisateur

### 1. Première visite
1. L'application se charge et vérifie l'authentification
2. Si non connecté → redirection vers `/login`
3. Affichage du formulaire de connexion

### 2. Inscription (nouvel utilisateur)
1. Navigation vers `/register`
2. Saisie email + mot de passe
3. Validation et création du compte
4. Redirection vers `/login`

### 3. Connexion
1. Saisie des identifiants
2. Authentification via l'API
3. Création de la session (cookie)
4. Redirection vers `/portfolio`

### 4. Simulation de portefeuille
1. Affichage de la liste des SCPI disponibles
2. Saisie des montants d'investissement
3. Validation des données
4. Calcul et affichage des résultats :
   - Montant total investi
   - Rendement moyen pondéré
   - Revenu annuel et mensuel
   - Détail par SCPI

### 5. Navigation et déconnexion
- Navigation libre entre les pages (si authentifié)
- Déconnexion via le bouton dans la navbar
- Destruction de la session et redirection vers `/login`

## Gestion de session

### Persistance de session
- Utilisation de cookies de session (Symfony)
- Configuration `withCredentials: true` dans Axios
- Vérification automatique au chargement de l'app

### État d'authentification
- `null` : état initial, vérification en cours
- `true` : utilisateur authentifié
- `false` : utilisateur non authentifié

## Styling et UI/UX

### Tailwind CSS
- Framework CSS utilitaire
- Classes prédéfinies pour le styling
- Design responsive et moderne

### Composants réutilisables
- Structure modulaire avec Vue 3 Composition API
- Séparation claire des responsabilités
- Réactivité automatique avec les stores Pinia

## Développement et déploiement

### Scripts disponibles
```bash
npm run dev      # Serveur de développement
npm run build    # Build de production
npm run preview  # Prévisualisation du build
```

### Variables d'environnement
- Configuration du proxy API dans `vite.config.js`
- Base URL configurée pour `/api`

---

*Voir aussi : `doc/authentication.md` pour les détails de l'authentification backend, `doc/simulation.md` pour la logique de simulation.* 