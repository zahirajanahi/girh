# GIRH — Gestion des Intérimaires RH

Plateforme web de gestion des intérimaires, entreprises clientes et missions, développée dans le cadre d'un PFE MIAGE.

## Stack technique

- **Frontend** : HTML5, CSS3, JavaScript (vanilla)
- **Backend** : PHP avec PDO et requêtes préparées
- **Base de données** : MySQL
- **Graphiques** : Chart.js (CDN)

## Fonctionnalités

- Authentification (admin / agent) avec sessions PHP
- CRUD intérimaires (recherche, filtres, fiche détail)
- CRUD entreprises clientes
- CRUD missions avec assignation automatique de disponibilité
- Tableau de bord avec statistiques et graphiques
- Alertes missions se terminant sous 7 jours
- Feuilles de temps et calcul du salaire dû (bonus)

## Prérequis

- [XAMPP](https://www.apachefriends.org/) ou [WAMP](https://www.wampserver.com/) (Apache + MySQL + PHP 8+)
- Navigateur web moderne

## Installation avec XAMPP

### 1. Copier le projet

Copiez le dossier `PFE` dans le répertoire web de XAMPP :

```
C:\xampp\htdocs\PFE
```

### 2. Démarrer les services

1. Ouvrez le **XAMPP Control Panel**
2. Démarrez **Apache** et **MySQL**

### 3. Créer la base de données

1. Accédez à [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Cliquez sur **Importer**
3. Sélectionnez le fichier `sql/schema.sql` du projet
4. Cliquez sur **Exécuter**

Le script crée automatiquement :
- La base `girh`
- Toutes les tables avec clés étrangères
- Des données de test

### 4. Configurer la connexion (si nécessaire)

Par défaut, la configuration dans `php/config.php` est :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'girh');
define('DB_USER', 'root');
define('DB_PASS', '');
```

Modifiez ces valeurs si votre installation MySQL utilise un autre utilisateur ou mot de passe.

### 5. Lancer l'application

Ouvrez votre navigateur à l'adresse :

```
http://localhost/PFE/login.php
```

## Comptes de démonstration

| Rôle  | Email           | Mot de passe |
|-------|-----------------|--------------|
| Admin | admin@girh.ma   | admin123     |
| Agent | agent@girh.ma   | agent123     |

## Installation avec WAMP

1. Copiez le dossier dans `C:\wamp64\www\PFE`
2. Démarrez WAMP (icône verte)
3. Importez `sql/schema.sql` via phpMyAdmin (`http://localhost/phpmyadmin`)
4. Accédez à `http://localhost/PFE/login.php`

## Structure du projet

```
PFE/
├── css/
│   └── style.css           # Styles globaux (thème bleu/blanc RH)
├── js/
│   ├── app.js              # Sidebar, alertes
│   ├── validation.js       # Validation côté client
│   └── dashboard.js        # Graphiques Chart.js
├── php/
│   ├── config.php          # Connexion PDO
│   ├── auth.php            # Authentification
│   ├── interimaires.php    # Logique intérimaires
│   ├── entreprises.php     # Logique entreprises
│   ├── missions.php        # Logique missions
│   └── feuilles_temps.php  # Logique feuilles de temps
├── includes/
│   ├── header.php          # En-tête HTML
│   ├── footer.php          # Pied de page
│   ├── sidebar.php         # Navigation latérale
│   ├── interimaire_form.php
│   ├── entreprise_form.php
│   └── mission_form.php
├── sql/
│   └── schema.sql          # Script BDD + données de test
├── index.php               # Tableau de bord
├── login.php               # Connexion
├── logout.php              # Déconnexion
├── interimaires.php        # Liste intérimaires
├── entreprises.php         # Liste entreprises
├── missions.php            # Liste missions
├── feuilles_temps.php      # Saisie heures
└── README.md
```

## Parcours de test recommandé

1. Se connecter avec `admin@girh.ma` / `admin123`
2. Consulter le tableau de bord (statistiques + graphiques)
3. Ajouter un intérimaire avec disponibilité « Disponible »
4. Ajouter une entreprise cliente
5. Créer une mission en liant l'intérimaire et l'entreprise
6. Vérifier que l'intérimaire passe automatiquement en « En mission »
7. Saisir des heures sur la fiche mission
8. Retourner au tableau de bord et vérifier les statistiques
9. Se déconnecter

## Sécurité

- Mots de passe hashés avec `password_hash()` / `password_verify()`
- Requêtes SQL via PDO avec paramètres préparés
- Validation côté client (JS) et serveur (PHP)
- Protection des pages par session (`requireLogin()`)
- Échappement HTML avec `htmlspecialchars()`

## Auteur

Projet PFE MIAGE — Gestion des Intérimaires RH (GIRH)
# girh
