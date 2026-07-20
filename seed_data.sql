-- Inserer des clients de test si absents
INSERT OR IGNORE INTO clients (id, telephone, created_at) VALUES (1, '0331234567', CURRENT_TIMESTAMP);
INSERT OR IGNORE INTO clients (id, telephone, created_at) VALUES (2, '0379876543', CURRENT_TIMESTAMP);
INSERT OR IGNORE INTO clients (id, telephone, created_at) VALUES (3, '0335556666', CURRENT_TIMESTAMP);
INSERT OR IGNORE INTO clients (id, telephone, created_at) VALUES (4, '0341112222', CURRENT_TIMESTAMP);

-- Inserer les statuts clients
INSERT OR IGNORE INTO statut_client (id_client, id_statut, date_modification) VALUES (1, 1, CURRENT_TIMESTAMP);
INSERT OR IGNORE INTO statut_client (id_client, id_statut, date_modification) VALUES (2, 1, CURRENT_TIMESTAMP);
INSERT OR IGNORE INTO statut_client (id_client, id_statut, date_modification) VALUES (3, 1, CURRENT_TIMESTAMP);
INSERT OR IGNORE INTO statut_client (id_client, id_statut, date_modification) VALUES (4, 4, CURRENT_TIMESTAMP); -- 4 = BLOQUE

-- Inserer des transactions de test (si non existantes)
INSERT OR IGNORE INTO transactions (id, reference, id_client_source, id_client_destination, id_type_operation, id_statut, montant, frais_appliques, date_transaction)
VALUES (1, 'TXN-20260718-DEP001', 1, NULL, 1, 2, 50000.0, 0.0, '2026-07-18 10:00:00');

INSERT OR IGNORE INTO transactions (id, reference, id_client_source, id_client_destination, id_type_operation, id_statut, montant, frais_appliques, date_transaction)
VALUES (2, 'TXN-20260719-DEP002', 2, NULL, 1, 2, 100000.0, 0.0, '2026-07-19 11:30:00');

INSERT OR IGNORE INTO transactions (id, reference, id_client_source, id_client_destination, id_type_operation, id_statut, montant, frais_appliques, date_transaction)
VALUES (3, 'TXN-20260720-TRF001', 1, 2, 3, 2, 15000.0, 200.0, '2026-07-20 08:15:00');

INSERT OR IGNORE INTO transactions (id, reference, id_client_source, id_client_destination, id_type_operation, id_statut, montant, frais_appliques, date_transaction)
VALUES (4, 'TXN-20260720-RET001', 2, NULL, 2, 2, 30000.0, 400.0, '2026-07-20 09:45:00');

INSERT OR IGNORE INTO transactions (id, reference, id_client_source, id_client_destination, id_type_operation, id_statut, montant, frais_appliques, date_transaction)
VALUES (5, 'TXN-20260720-DEP003', 3, NULL, 1, 2, 25000.0, 0.0, '2026-07-20 10:05:00');

INSERT OR IGNORE INTO transactions (id, reference, id_client_source, id_client_destination, id_type_operation, id_statut, montant, frais_appliques, date_transaction)
VALUES (6, 'TXN-20260720-TRF002', 3, 1, 3, 2, 8000.0, 100.0, '2026-07-20 10:20:00');
