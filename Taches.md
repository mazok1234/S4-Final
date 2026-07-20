v1 : 
    .ETU004173 : 
        -Initialisation Github - (ok)
        -Initialisation Code-Igniter Squelette - (ok)
        -Conception base de donnee  (ok):
            -- Tables :
               1. prefixes : gestion des prefixes telephoniques
               2. types_operation : types d'operations (depôt, retrait, transfert)
               3. baremes_frais : calcul des frais selon le montant et l'operation
               4. statut : statut des clients
               5. statut_transaction : statut des transactions
               6. clients : informations des clients et solde
               7. transactions : historique des operations effectuees
        -Cote operateur : 
            --Modele  : (ok)
                1. \Operateur\Model\PrefixeModel.php 
                2. \Operateur\Model\BaremeFraisModel.php
                3. \Operateur\Model\TypeOperationModel.php
                4. \Operateur\Model\TransactionModel.php
                5.Inclure option frais de retrait lors envoi dans et transfert multiple ClientModel.php

            --Controller : (ok)
                1.Creation du controller \Operateur\Controller\PrefixeController.php(Crud-prefixe)
                2.Creation du controller \Operateur\Controller\TypeOperationController.php(Crud-type)
                3.Creation du controller \Operateur\Controller\BaremeController.php(Crud-bareme par tranche modifiable et on prend les gains adaptation avec ClientModel.php)
                



            --View : (ok)
                1. Liste des prefixes dans Views\operator\prefixes.php
                2. Bouton ajouter/modifier/supprimer Prefixes + formulaire
                3. Liste et creation des types d'operations +bouton ajouter ,supprimer, modifier avec formulaire  dans Views\operator\types_operations.php et dans Views\operator\edit_types_operations.php
                4. Liste des tranches avec frais + bouton modifier et supprimer + formulaire de modif dans Views\operator\gestion_baremes.php
                5. Ajouter une nouvelle fonctionnalite selon la regle du sujet dans ClientModel.php et ajout bouton +Ajout un nouveau destinataire 



        
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

## Fonctionnalite client
### Depot
- Route
    -/depot Post
- Vue
    - Formulaire de depot
        - montant 
        - bouton valider
        - message reussite
        - message echoue
- ClientModel
    - getSolde
    -  fonction depot
    - fonction calculer solde
- ClientController
    - fonction depot
       - prendre les parametres et executer la fonction dans le model
       - rediriger vers vue historique
### Transfert
- Route
    -/transfert Post
- Vue
    - Formulaire de transfert
        - montant 
        - destinataire
        - bouton valider
        - message reussite
        - message echoue
- ClientModel
    -  fonction transfert(verifier solde)
- ClientController
    - fonction transfert
       - prendre les parametres et executer la fonction dans le model
       - rediriger vers vue historique
### Retrait
- Route
    -/retrait Post
- Vue
    - Formulaire de retrait
        - montant 
        - bouton valider
        - message reussite
        - message echoue
- ClientModel
    -  fonction retrait(verfifier solde)
- ClientController
    - fonction retrait
       - prendre les parametres et executer la fonction dans le model
       - rediriger vers vue historique
