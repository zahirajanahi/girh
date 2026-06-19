# GIRH — Gestion des Intérimaires RH (v2)

Plateforme web SaaS de gestion RH pour sociétés d'intérim au Maroc. Projet PFE MIAGE.

## Stack

- **Frontend** : HTML5, CSS3 (design SaaS moderne), JavaScript vanilla, Font Awesome, Google Fonts Inter
- **Backend** : PHP 8+ avec PDO et requêtes préparées
- **Base de données** : MySQL
- **Graphiques** : Chart.js

## Fonctionnalités

- Authentification admin / agent
- Gestion intérimaires (contrat CDI/CDD/ANAPEC, statuts, calcul auto date fin CDD)
- Gestion entreprises clientes
- Tableau de bord avec alertes fin de contrat (30 jours) + export STC (CSV / imprimable)
- Feuilles de temps par intérimaire
- Simulation de paie marocaine 2026 (CNSS, AMO, IPE, IR)

## Installation (XAMPP)

1. Copier le projet dans `C:\xampp\htdocs\PFE`
2. Démarrer Apache et MySQL
3. Importer `sql/schema.sql` via phpMyAdmin
4. Configurer `php/config.php` si besoin (user/password MySQL)
5. Ouvrir `http://localhost/PFE/login.php`

### Lancer en ligne de commande

```powershell
# Démarrer MySQL (XAMPP) puis :
C:\xampp\php\php.exe -S localhost:8000 -t "C:\Users\hp\Desktop\PFE"
```

Puis ouvrir : **http://localhost:8000/login.php**

## Comptes de démo

| Rôle  | Email         | Mot de passe |
|-------|---------------|--------------|
| Admin | admin@girh.ma | admin123     |
| Agent | agent@girh.ma | agent123     |

## Structure

```
PFE/
├── css/style.css              # Charte graphique SaaS
├── js/
│   ├── app.js
│   ├── validation.js
│   ├── interimaire-contrat.js # Logique CDD/CDI/ANAPEC
│   └── dashboard.js
├── php/
│   ├── config.php, auth.php
│   ├── interimaires.php
│   ├── entreprises.php
│   ├── feuilles_temps.php
│   └── paie.php               # Algorithme paie marocaine 2026
├── sql/schema.sql
├── index.php                  # Tableau de bord
├── interimaires.php           # Liste + filtres
├── feuilles_temps.php
├── simulation_paie.php
├── alerte_fin_contrat.php
└── export_stc.php
```

## Parcours de test

1. Connexion admin
2. Ajouter un intérimaire CDD → vérifier date fin auto (+6 mois)
3. Tableau de bord → carte alerte fin de contrat
4. Export STC (CSV ou version imprimable)
5. Feuilles de temps → clic sur salarié → simulation paie
6. Vérifier le bulletin CNSS/AMO/IR

## Note légale

La simulation de paie est pédagogique (PFE). Ne pas utiliser pour une paie réelle sans validation expert-comptable.
