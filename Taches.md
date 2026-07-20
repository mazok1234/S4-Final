v1 : 
    .ETU004173 : 
        -Initialisation Github - 1mn (ok)
        -Initialisation Code-Igniter Squelette - 1mn (ok)
        -Conception base de donnee :
            -- Tables :
               1. prefixes : gestion des prefixes telephoniques
               2. types_operation : types d'operations (depôt, retrait, transfert)
               3. baremes_frais : calcul des frais selon le montant et l'operation
               4. statut : statut des clients
               5. statut_transaction : statut des transactions
               6. clients : informations des clients et solde
               7. transactions : historique des operations effectuees
        -Cote operateur : 
            --Modele : 
                1. \Operateur\Model\PrefixeModel.php 
                2. \Operateur\Model\BaremeFraisModel.php
            --Controller : 
                1.Creation du controller \Operateur\Controller\PrefixeController.php(Crud-prefixe)
                2.Creation du controller \Operateur\Controller\BaremeFraisController.php(Crud-bareme)
            --View : 
                1. Liste des prefixes dans Views\operator\prefixes.php
                2. Bouton ajouter/modifier/supprimer Prefixes 


        
# ETU003950 : 
## Connexion base avec SQLite 
  - creation du fichier sqlite3 .db
  - mettre dans writable/db
  - configurer Database.php dans app/Config

## Fonctionnalite Login(Cote client)
- Route
  - config des routes 
- Model
    - ClientModel
        - acces aux informations
- Controller
    - AuthController
        - Creation du compte si n'existe pas
        - Connexion si compte existe
        - Mise en place de session
        - Redirection vers la page fonctionnelle
    - ClientController

- Vue
    - Page login
        - Formulaire login
           - champ telephone
           - bouton valider


