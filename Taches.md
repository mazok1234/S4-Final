v1 : 
    .ETU004173 : 
        -Initialisation Github - 1mn (ok)
        -Initialisation Code-Igniter Squelette - 1mn (ok)
        -Conception base de donnee
        
# ETU003950 : 
## Connexion base avec SQLite 
  - creation du fichier sqlite3 .db
  - mettre dans writable/db
  - configurer Database.php dans app/Config

## Fonctionnalite Login(Cote client)
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
            