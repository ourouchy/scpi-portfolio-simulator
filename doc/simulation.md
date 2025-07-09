# Simulation de portefeuille SCPI - Documentation technique

## SCPI disponibles (fixtures de base)

À l'initialisation, la base de données contient trois SCPI de démonstration :

| ID | Nom         | Taux de rendement annuel (%) |
|----|-------------|------------------------------|
| 1  | SCPI Alpha  | 4.5                          |
| 2  | SCPI Beta   | 5.1                          |
| 3  | SCPI Gamma  | 4.2                          |

Ces SCPI sont créées dans `AppFixtures.php` et accessibles via l'API.

## Endpoint d'accès à la liste des SCPI

- **Route** : `GET /api/scpis`
- **Réponse** :
```json
[
  { "id": 1, "nom": "SCPI Alpha", "tauxRendementAnnuel": 4.5 },
  { "id": 2, "nom": "SCPI Beta", "tauxRendementAnnuel": 5.1 },
  { "id": 3, "nom": "SCPI Gamma", "tauxRendementAnnuel": 4.2 }
]
```

## Simulation de portefeuille

### Endpoint
- **Route** : `POST /api/portfolio`
- **Payload attendu** :
```json
{
  "portefeuille": [
    { "scpiId": 1, "montant": 10000 },
    { "scpiId": 2, "montant": 5000 }
  ]
}
```

### Logique de calcul
Pour chaque entrée du portefeuille :
- On récupère la SCPI par son `id`.
- On additionne le montant investi pour obtenir le `montantTotal`.
- On calcule le rendement pondéré : `rendementPondere += montant * tauxRendementAnnuel`.
- On calcule le revenu annuel pour chaque SCPI : `revenu = montant * tauxRendementAnnuel / 100`.
- On additionne tous les revenus annuels pour obtenir le revenu total.

**Formules principales :**
- `montantTotal = somme des montants investis`
- `rendementMoyen = rendementPondere / montantTotal` (si montantTotal > 0)
- `revenuAnnuel = somme des revenus annuels de chaque SCPI`
- `revenuMensuel = revenuAnnuel / 12`

### Exemple de réponse
```json
{
  "montantTotal": 15000,
  "rendementMoyen": 4.7,
  "revenuAnnuel": 705,
  "revenuMensuel": 58.75,
  "details": [
    { "scpiId": 1, "montant": 10000, "rendement": 4.5, "revenuAnnuel": 450 },
    { "scpiId": 2, "montant": 5000, "rendement": 5.1, "revenuAnnuel": 255 }
  ]
}
```

### Gestion des erreurs
- Si le portefeuille est vide ou mal formé : code 400, `{ "error": "Portefeuille invalide" }`
- Si une entrée est invalide (montant négatif, id manquant) : code 400, `{ "error": "Entrée portefeuille invalide" }`
- Si une SCPI n'existe pas : code 404, `{ "error": "SCPI id X non trouvée" }`

## Résumé du process
1. Le frontend récupère la liste des SCPI via `/api/scpis`.
2. L'utilisateur compose son portefeuille (choix des SCPI, montants).
3. Le frontend envoie le portefeuille à `/api/portfolio`.
4. Le backend calcule :
   - Le montant total investi
   - Le rendement moyen pondéré
   - Le revenu annuel et mensuel
   - Les détails par SCPI
5. Le backend retourne le résultat au frontend pour affichage.

---

*Voir aussi : `PortfolioController.php`, `ScpiController.php`, `AppFixtures.php` pour la logique complète.* 