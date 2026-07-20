CREATE TABLE prefixes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE
);

CREATE TABLE types_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL UNIQUE
);

CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_type_operation INTEGER,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    frais REAL NOT NULL,
    FOREIGN KEY(id_type_operation) REFERENCES types_operation(id)
);

CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    telephone TEXT NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE statut(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL
);
CREATE TABLE statut_client(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    id_statut INTEGER NOT NULL,
    id_client INTEGER NOT NULL,
    date_modification DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(id_statut) REFERENCES statut(id),
    FOREIGN KEY(id_client) REFERENCES clients(id)
);

CREATE TABLE statut_transaction (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL UNIQUE 
);

CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    reference TEXT NOT NULL UNIQUE, -- Ex: TXN-20260720-A1B2C3
    id_client_source INTEGER NOT NULL,
    id_client_destination INTEGER,
    id_type_operation INTEGER NOT NULL,
    id_statut INTEGER NOT NULL DEFAULT 1, -- Clé vers statut_transaction
    montant REAL NOT NULL CHECK (montant > 0),
    frais_appliques REAL DEFAULT 0.0 CHECK (frais_appliques >= 0),
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY(id_client_source) REFERENCES clients(id),
    FOREIGN KEY(id_client_destination) REFERENCES clients(id),
    FOREIGN KEY(id_type_operation) REFERENCES types_operation(id),
    FOREIGN KEY(id_statut) REFERENCES statut_transaction(id),
    
    -- Empêche d'envoyer de l'argent à soi-même
    CHECK (id_client_source <> id_client_destination)
);
INSERT INTO prefixes (prefixe) VALUES ('033'), ('037');
INSERT INTO types_operation (nom) VALUES ('depot'), ('retrait'), ('transfert');
INSERT INTO statut (libelle) VALUES 
('ACTIF'),
('INACTIF'),
('SUSPENDU'),
('BLOQUE');

-- 2. Statuts de transaction
INSERT INTO statut_transaction (code) VALUES 
('EN_COURS'),
('SUCCES'),
('ECHEC'),
('ANNULEE');

INSERT INTO baremes_frais (id_type_operation, montant_min, montant_max, frais) VALUES 
(2, 100, 1000, 50),
(2, 1001, 5000, 50),
(2, 5001, 10000, 100),
(2, 10001, 25000, 200),
(2, 25001, 50000, 400),
(2, 50001, 100000, 800),
(2, 100001, 250000, 1500),
(2, 250001, 500000, 1500),
(2, 500001, 1000000, 2500),
(2, 1000001, 2000000, 3000),
(3, 100, 1000, 50),
(3, 1001, 5000, 50),
(3, 5001, 10000, 100),
(3, 10001, 25000, 200),
(3, 25001, 50000, 400),
(3, 50001, 100000, 800),
(3, 100001, 250000, 1500),
(3, 250001, 500000, 1500),
(3, 500001, 1000000, 2500),
(3, 1000001, 2000000, 3000);