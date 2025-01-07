# **Micro-Framework PHP**

## **Description**
Ce projet est un micro-framework PHP léger qui permet de développer des applications web en suivant le modèle **MVC** (Modèle-Vue-Contrôleur). Il offre des fonctionnalités simples et extensibles pour gérer les routes, les bases de données, et le rendu des vues.

---

## **Installation**

### **Étapes d'installation**
1. Clonez le dépôt du projet :
   ```bash
   git clone {url_du_depot_git}
   cd micro-framework
   ```
2. Installez les dépendances avec **Composer** :
   ```bash
   composer install
   ```
3. Configurez le fichier `.env` à la racine du projet (voir la section **Configuration** ci-dessous).

---

## **Configuration**

Créez un fichier `.env` à la racine du projet et définissez-y les variables d'environnement nécessaires pour configurer votre application. Voici un exemple :

```
DSN=mysql:host=localhost;dbname=nom_de_la_base_de_donnees
USERNAME=utilisateur_de_la_base_de_donnees
PASSWORD=mot_de_passe_de_la_base_de_donnees
CONTROLLER_NAMESPACE=Sthom\App\Controller\
MODEL_NAMESPACE=Sthom\App\Model\
DEBUG=true
```

### **Explications des variables :**
- **DSN** : Informations de connexion à la base de données (MySQL dans cet exemple).
- **USERNAME / PASSWORD** : Identifiants pour la base de données.
- **CONTROLLER_NAMESPACE** : Namespace racine pour les contrôleurs.
- **MODEL_NAMESPACE** : Namespace racine pour les modèles.
- **DEBUG** : Mode debug activé/désactivé (`true` ou `false`).

---

## **Utilisation**

### **Lancer le serveur PHP**
Pour démarrer un serveur PHP local, utilisez la commande suivante (à exécuter depuis la racine du projet) :
```bash
php -S localhost:8000 -t public
```

Ensuite, ouvrez votre navigateur et accédez à l'application via l'URL suivante :
```
http://localhost:8000
```

### **Lancer webpack**
Pour compiler les fichiers JS et SCSS, utilisez la commande suivante dans un terminal différent :
```bash
npm run watch
```

**Note** : `npm run watch` surveille les modifications des fichiers et recompile automatiquement les assets.
**Note** : Les fichiers JS et SCSS se trouvent dans le dossier `assets` et seront compilés dans le dossier `public` dans le dossier build.
**/!\ Attention /!\** : Quand vous êtes en développement, pensez à bien lancer `npm run watch` en plus du serveur PHP pour que les modifications soient prises en compte.



---

## **Exemple d'implémentation : Page d'accueil**

### **Étape 1 : Création d'un contrôleur**
Pour créer une nouvelle page, commencez par créer un contrôleur dans le dossier `src/Controller` :

```php
<?php

namespace Sthom\App\Controller;

use Sthom\Kernel\Utils\AbstractController;

class HomeController extends AbstractController
{
    public function index()
    {
        return $this->render('home/index.php', ["title" => "Page d'accueil"]);
    }
}
```

### **Étape 2 : Création d'une vue**
Créez ensuite une vue dans `src/Views/home/index.php`. Ce fichier correspond au chemin passé à la méthode `render` du contrôleur (`home/index.php` dans cet exemple).

```php
<h1><?php echo $title ?></h1>
<p>Bienvenue sur la page d'accueil de notre site.</p>
```

### **Étape 3 : Définir une route**
Ajoutez une nouvelle route dans le fichier `routes.php` :

```php
<?php
const ROUTES = [
    '/' => [
        'HTTP_METHODS' => ['GET'],
        'CONTROLLER' => 'HomeController',
        'METHOD' => 'index',
    ],
];
```

---

## **Exemple d'utilisation d'un modèle avec un Repository**

### **Étape 1 : Création d'un modèle**
Les modèles représentent les tables de votre base de données. Créez un modèle dans `src/Model/User.php` :

```php
<?php

namespace Sthom\App\Model;

class User
{
    public const TABLE = 'users'; // Définit la table associée

    private ?int $id = null;
    private ?string $username = null;
    private ?string $password = null;

    // Getters et setters pour les propriétés
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
```

**Note importante** : Les noms des propriétés doivent correspondre aux colonnes de la table en base de données.
**Note importante** : Chaque modèle doit définir une constante `TABLE` pour indiquer la table associée. le Framework utilise cette information pour générer les requêtes SQL.

---

### **Étape 2 : Utilisation dans un contrôleur**
Pour interagir avec la base de données, utilisez la classe `Repository`. Voici un exemple dans un contrôleur :

```php
<?php

namespace Sthom\App\Controller;

use Sthom\App\Model\User;
use Sthom\Kernel\Utils\AbstractController;
use Sthom\Kernel\Utils\Repository;

class HomeController extends AbstractController
{
    public function index()
    {
        $userRepo = new Repository(User::class);
        
        // Récupérer un utilisateur par son ID
        $user = $userRepo->getById(1);

        return $this->render('home/index.php', [
            "title" => "Page d'accueil",
            "user" => $user,
        ]);
    }
}
```

### **Étape 3 : Accéder aux données dans la vue**
Dans la vue correspondante (`src/Views/home/index.php`), utilisez les données passées par le contrôleur :

```php
<h1><?php echo $title; ?></h1>
<p>Bienvenue sur la page d'accueil.</p>
<p>Nom d'utilisateur : <?php echo $user->getUsername(); ?></p>
```

---

## **Architecture du Projet**

- **Kernel** : Composants de base du framework.
    - `Router.php` : Gestionnaire des routes.
    - `AbstractController.php` : Contrôleur de base pour l'application.
    - `Repository.php` : Classe générique pour les requêtes SQL (CRUD).
    - `SqlBuilder.php` : Génération dynamique des requêtes SQL.
    - `Hydrator.php` : Transformation des résultats SQL en objets.
    - `Database.php` : Gestion de la connexion à la base de données.

Côté application :
- **Controller** : Dossier pour vos contrôleurs.
- **Model** : Dossier pour vos modèles (entités).
- **Views** : Dossier pour vos vues (HTML avec PHP).

---

## **Liste des Fonctionnalités**

### **1. Gestion des Routes**
Définissez vos routes dans le fichier `routes.php`. Chaque route est associée à un contrôleur et une méthode.

### **2. Gestion des Bases de Données**
Avec `SqlBuilder` et `Repository`, effectuez facilement des opérations CRUD sur vos tables.

### **3. Contrôleurs Dynamiques**
Les contrôleurs héritent de `AbstractController` et peuvent :
- Rendre des vues HTML.
- Envoyer des réponses JSON.
- Effectuer des redirections.

### **4. Hydratation Automatique**
Les résultats SQL sont transformés en objets grâce à la classe `Hydrator`.

---

## **Exemple de Routes**
```php
const ROUTES = [
    '/' => [
        'HTTP_METHODS' => ['GET'],
        'CONTROLLER' => 'HomeController',
        'METHOD' => 'index',
    ],
    '/login' => [
        'HTTP_METHODS' => ['POST'],
        'CONTROLLER' => 'AuthController',
        'METHOD' => 'login',
    ],
];
```
# Workflow pour les stagiaires
On se base sur le repository de Sébastien
Seul Sébastien fera avancer la branche main
Seul Sébastien fera avancer la branche dev-seb

Seul Yvan fera avancer la branche dev-yvan

## Groupes stagiaires
Chaque groupe travaillera sur son propre repository mais après avoir un fait un fork
Chaque groupe pourra ensuite créer ses branches dans son fork, par exemple : feature/nouvelle-fonctionnalite
S'ils souhaitent proposer des modifications,  les groupes  pourront faire une Pull Request vers le dépôt de Sébastien

### Pour créer un fork :
Attention, seuls les "responsables" git de chaque groupe ont besoin de faire un fork.

Se rendre sur le repository de Sébastien
https://github.com/SebastienThomasDEV/FilRouge
 et cliquer sur créer un fork (Fork > create a new fork)
Cela va créer un repository sur votre propre github avec le même nom ex :
https://github.com/VotreNom/FilRouge

Attention, si vous avez bien cliqué sur "Sync fork", le repository ne sera pas automatiquement mis à jour

### Cloner localement le fork

Créer dans votre arborescence un clone :
Git clone https://github.com/VotreNom/FilRouge.git
Aller dans le répertoire créé
Lier ce repository avec le repository originel (celui de Seb)
git remote add upstream https://github.com/SebastienThomasDEV/FilRouge

### Récupérer les dernières modifications du dépôt original
#### Si "Sync fork" a été mis en place, vous pouvez demander via l'interface de github à faire la synchronisation et ensuite il suffit de faire un git pull depuis main
#### Si "Sync fork" n'a pas été mis en place
##### 1.git fetch upstream

##### 2. Se placer sur la branche main locale
git checkout main

##### 3. Fusionner les modifications de upstream/main dans la branche main locale git merge upstream/main

##### 4. Pousser ces modifications vers leur fork sur GitHub
git push origin main



# Ajout du TypeScript
## Rappel
Tel qu'il a été pensé, le framework permet d'ajouter du js via assets/app.js

Nous allons voir comment utiliser TypeScript 
## Installation
```bash
npm install --save-dev typescript ts-loader @types/node
```
## Ajout de tsconfig.json
```json
{
  "compilerOptions": {
    "outDir": "./dist/",
    "sourceMap": true,
    "strict": true,
    "module": "es6",
    "moduleResolution": "node",
    "target": "es5",
    "allowJs": true
  }
}
```
## Modification webpack.config.js 
```js

const path = require('path');

module.exports = {
    entry: './assets/app.ts', // Nouveau point d'entrée en TypeScript
    output: {
        filename: 'app.bundle.js',
        path: path.resolve(__dirname, 'public/build'),
        publicPath: '/build/',
    },
    module: {
        rules: [
            {
                test: /\.tsx?$/, // Ajoute le support de .ts et .tsx
                use: 'ts-loader',
                exclude: /node_modules/,
            },
            {
                test: /\.scss$/,
                use: ['style-loader', 'css-loader', 'sass-loader'],
            },
            {
                test: /\.(png|jpe?g|gif|svg)$/,
                use: [
                    {
                        loader: 'file-loader',
                        options: {
                            name: '[name].[hash].[ext]',
                            outputPath: 'images/',
                        },
                    },
                ],
            },
        ],
    },
    resolve: {
        extensions: ['.tsx', '.ts', '.js'], // Permet l'import sans extension
    },
    mode: 'development',
};
```

# Ajout de bootstrap 5
## Installation
```bash
npm i bootstrap@5
```

## Appel et compilation via app.ts
```ts
import "bootstrap/scss/bootstrap.scss";
import "./styles/styles.scss";
```

# Utilisation de fetch pour supprimer et ajouter des utilisateurs
## Routes
Ajout des routes nécessaires pour :
 - créer des utilisateurs 
 - voir les utilisateurs et charger le js
 - créer un endpoint pour supprimer les utilisateurs
```php
"/users" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "index",
        "HTTP_METHODS" => "GET",
    ],
"/api/delete/user" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "deleteApiUser",
        "HTTP_METHODS" => "GET",
    ],
"/api/add/user" => [
        "CONTROLLER" => "UsersController",
        "METHOD" => "addApiUser",
        "HTTP_METHODS" => "POST",
    ],
```
## Contrôleurs
- deleteApiUser
- addApiUser

## TypeScript
Dans assets/ts/services/Apis.ts:  contient les méthodes qui permettent de faire appel aux endpoints via fetch

Dans assets/ts/viewUser.ts : appel des fonctions (manageDelete et ManageAdd) qui gèrent les événements qui déclencheront l'appel des méthodes du service Api.ts

Attention, manageAdd et addUserFromApi ne sont pas finies