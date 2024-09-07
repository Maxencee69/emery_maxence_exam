
# Filmcameras

## Technologies utilisées

- **Symfony** : Un framework PHP puissant et flexible pour la création d'applications web robustes et maintenables.
- **Bootstrap** : Un framework CSS qui permet de créer des interfaces modernes et réactives rapidement et facilement.

## Prérequis

Avant de récupérer et installer ce projet, assurez-vous d'avoir les outils suivants installés sur votre machine :

- [PHP](https://www.php.net/) >= 7.4
- [Composer](https://getcomposer.org/) pour gérer les dépendances PHP
- [Node.js](https://nodejs.org/) et [npm](https://www.npmjs.com/) pour gérer les dépendances front-end
- [Symfony CLI](https://symfony.com/download) (optionnel mais recommandé)

## Récupérer le projet

1. **Clonez le dépôt** :
   ```bash
   git clone https://github.com/Maxencee69/emery_maxence_exam.git

2. **Installation**

- Créer la base de données : 
    ```bash
    php bin/console d:d:c
- Exécuter les migrations : 
    ```bash
    php bin/console d:f:l
- Charger les données de test (fixtures) : 
    ```bash
    php bin/console d:f:l


### BDD

Le projet inclut une gestion de la table `Camera` dans la base de données.

## Présentation

Le site Filmcameras permet à chacun de ses utilisateurs d'avoir accès à un catalogue d'appareils photos argentiques. Ce catalogue s'agrandit au fur et à mesure des contributions des utilisateurs. 

Sur chaque fiche d'appareil photo un utilisateur trouvera le nom de la marque, le nom du modèle, une description de l'appareil, le format de pellicule utilisé par l'appareil en question, son année de sortie ainsi que la possibilité de voir sur une autre page le manuel au format PDF. Les utilisateurs peuvent s'inscrire et créér des fiches caméra et gérer leur profil et leurs fiches au besoin. 

## Droits des utilisateurs

### Utilisateurs non connectés

Les utilisateurs non connectés ont accès aux fonctionnalités suivantes :

- Accès à la page d'accueil
- Visualisation des pages publiques
- Possibilité de s'inscrire 

### Utilisateurs connectés

Les utilisateurs connectés disposent des droits supplémentaires suivants :

- Accès au tableau de bord utilisateur
- Gestion de leur profil
- Possibilités de modifier leur mot de passe et de supprimer leur compte
- Possibilités de modifier ou supprimer la caméra créée

### Administrateurs

Les administrateurs ont les droits suivants :

- Gestion des utilisateurs (modification, suppression)
- Gestion des caméras (modification, suppression)

## A l'intention de Lucas D. 

Bonjour Lucas, comme demandé je t'explique à présent comment mon projet est articulé afin de ta faciliter son analyse. Par souci d'éfficacité je vais te le présenter de manière succinte dossier par dossier. Il y aura certes des rappels évidents mais je tiens à être exhaustif. Bonne lecture.  

### Dossiers /asset/images
A l'intérieur de ce dossier tu trouveras l'image correspondant à la bannière de ma page d'accueil. 

### Dossier /public/camera_manuels
Dans ce dosssier tu trouveras les manuels d'appareils photo (au format PDF) que j'ai mis afin de créér les premières caméras.

### Dossier /public/camera_photos

Tu trouveras les photos des appareils photo (au format webp) que j'ai mis afin de créér les premières caméras. 

### Dossier /public/vidéos
Dans ce dossier se trouvent les vidéos présentes sur la page d'accueil

### Dossier src/Controller/BrandController
Les marques sont classées par ordre alphabétique. Si une marque n'a plus de caméra associée alors elle est supprimée et la liste des marques est mise à jour dans la base de données. 

### Dossier src/Controller/CameraController
Les appareils photos sont présentés du plus récent au plus ancien. L'utilisateur connecté peut créér une caméra et elle sera associé à son compte. Afin de normaliser les marques, la première lettre est toujours mise en majuscule et les suivantes en minsucules. Si la marque n'existait pas, elle sera donc créée. 

On trouve dans ce fichier une gestion des photos téléchargées et des manuels envoyés pour créer les fiches caméra. 

L'ensemble est sauvegardé dans la bdd. 

On trouve aussi une gestion des messages flash que l'utilisateur verra en cas de mofifications, suppressions d'appareils photo, etc. 

### Dossier src/Controller/SecurityController
Le fichier SecurityController.php a pour objectif de gérer l'authentification et l'inscription des utilisateurs. On trouve une fonction connexion, déconnexion, register (qui gère le processus d'inscription des nouveaux utilisateurs) et la confirmation d'inscription. 

### Dossier src/Controller/UserController

Ce contrôleur gère les fonctionnalités liées à la gestion du compte utilisateur, telles que la suppression d'un compte, l'affichage des caméras de l'utilisateur, et la modification du mot de passe. Il offre également des fonctionnalités administratives pour supprimer des utilisateurs et leurs caméras associées.

### Dossier src/DataFixtures/AppFixtures
Dans ce dossier Fixture, je créé des marques d'appareils photo, je crée les caméras pour chaque marque, je gère l'ajout des photos et des manuels. Je créé un utilisateur connecté normal et un administrateur. Enfin je gère la persistance de toutes les entités : Les marques, caméras, photos, manuels, et utilisateurs sont sauvegardés dans la base de données.

### Dossier src/Entity/Brand
L'entité Brand représente une marque d'appareil photo, avec un identifiant unique, un nom, et une relation avec plusieurs entités Camera. Elle permet de gérer l'ajout et la suppression de caméras associées à la marque, tout en fournissant des méthodes pour accéder à ses propriétés et la relation avec les caméras.

### Dossier src/Entity/Camera
L'entité Camera représente un appareil photo dans le système. Elle stocke des informations comme le modèle, l'année, le format du film, et la description. Elle est reliée à une marque, à un utilisateur, à une photo, et à un manuel d'utilisation. Cette entité gère ses relations avec les autres entités, permettant une gestion complète des informations relatives à chaque caméra.

### Dossier src/Entity/Manual
L'entité Manual représente un manuel d'utilisation associé à une caméra. Elle contient des informations comme le chemin du fichier manuel et son format. La relation OneToOne avec l'entité Camera permet d'associer un manuel à une seule caméra. Les méthodes de cette classe gèrent l'accès et la modification de ces données.

### Dossier src/Entity/Photo
L'entité Photo représente une photo associée à une caméra. Elle contient des informations telles que le chemin du fichier image et la caméra à laquelle elle est liée. La relation ManyToOne avec l'entité Camera permet d'associer plusieurs photos à une caméra, facilitant ainsi la gestion des images pour chaque modèle d'appareil photo.

### Dossier src/Entity/User
L'entité User représente un utilisateur. Elle contient des informations de base telles que l'email, les rôles et le mot de passe, et est reliée à plusieurs caméras via une relation OneToMany. Elle implémente les interfaces nécessaires pour être compatible avec le système de sécurité de Symfony, et gère les rôles, l'authentification et les relations avec les autres entités.

### Dossier src/Form/DataTransformer/CameraType
La classe CameraType représente le formulaire utilisé pour créer ou modifier une caméra dans l'application. Elle contient des champs pour les informations importantes d'une caméra (modèle, année, description, format de film, marque, photo, et manuel). Le champ brand utilise un transformateur de données pour convertir une chaîne de caractères en entité Brand, et des contraintes spécifiques sont appliquées aux fichiers téléchargés, comme les photos et les manuels.

### Dossier src/Form/DataTransformer/ChangePassword
Le formulaire ChangePasswordType permet à l'utilisateur de changer son mot de passe en saisissant à la fois l'ancien et le nouveau mot de passe. Les deux champs sont de type mot de passe et ne sont pas directement liés à l'entité de l'utilisateur. Ce formulaire est utilisé dans le cadre d'un processus sécurisé pour modifier le mot de passe après validation de l'ancien.

### Dossier src/Form/DataTransformer/UserType
Le formulaire UserType est utilisé pour gérer les données d'un utilisateur. Il comprend des champs pour saisir l'adresse e-mail et le mot de passe, avec une confirmation du mot de passe pour renforcer la sécurité. Le formulaire est mappé à l'entité User et est utilisé pour l'inscription ou la modification des informations de l'utilisateur.

### Dossier src/Security/Voter/CameraVoter
Le CameraVoter est un composant de sécurité qui contrôle les permissions d'édition et de suppression d'une caméra. Seuls le propriétaire de la caméra ou un administrateur peuvent modifier ou supprimer une caméra. 

### Dossier src/Security/Voter/UserVoter
Le UserVoter contrôle les permissions d'affichage et de modification des profils utilisateur. Un utilisateur peut voir ou modifier son propre profil, tandis qu'un administrateur peut voir les profils de tous les utilisateurs. 

### Dossier templates/admin/cameras.html.twig
Ce template fournit une interface d'administration permettant de gérer les caméras et les utilisateurs. Les administrateurs peuvent visualiser toutes les caméras associées aux utilisateurs, modifier ou supprimer les caméras, ainsi que supprimer les utilisateurs. La page utilise un tableau pour afficher les données et inclut des mesures de sécurité comme la protection CSRF pour les actions destructives.

### Dossier templates/brand/camera.html.twig
Ce template offre une interface utilisateur permettant de rechercher des appareils photo par marque. Il inclut une barre de recherche qui permet de filtrer les marques en temps réel, ainsi que des menus déroulants pour afficher les appareils photo disponibles pour chaque marque. L'utilisation de JavaScript et de Twig permet une expérience utilisateur interactive, tandis que le contenu est géré par des boucles Twig pour afficher les marques et leurs appareils photo associés.

### Dossier templates/camera/camera.html.twig
Ce template affiche les détails d'une caméra spécifique. Il présente les informations de la caméra (nom, description, caractéristiques), affiche la première photo de l'appareil, et propose des actions comme modifier ou supprimer la caméra si l'utilisateur en a les droits. Il inclut également une fenêtre modale pour agrandir l'image de la caméra.

### Dossier templates/camera/cameralist.html.twig
Ce template affiche une liste d'appareils photo sous forme de catalogue. Chaque caméra est affichée dans une grille et est cliquable pour afficher plus de détails. Le template utilise un fichier partiel (camera/card.html.twig) pour afficher les informations de chaque caméra dans la grille.

Merci pour le temps accordé et bonne visite sur Filmcameras. 























