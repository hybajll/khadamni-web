-- Doctrine Migration File Generated on 2026-04-14 15:06:03

-- Version DoctrineMigrations\Version20260410094209
CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4;
ALTER TABLE questions_enquete DROP FOREIGN KEY `fk_attribut_enquete`;
ALTER TABLE reponse_enquete DROP FOREIGN KEY `fk_reponse_enquete`;
DROP TABLE abonnement;
DROP TABLE candidature;
DROP TABLE comment;
DROP TABLE connexions_utilisateur;
DROP TABLE employeur;
DROP TABLE enquete;
DROP TABLE news;
DROP TABLE notification;
DROP TABLE offers;
DROP TABLE paiement;
DROP TABLE questions_enquete;
DROP TABLE reclamation;
DROP TABLE recommendation;
DROP TABLE reponse_enquete;
DROP TABLE reponse_reclamation;
DROP TABLE society;
ALTER TABLE cv CHANGE contenuOriginal contenuOriginal LONGTEXT NOT NULL, CHANGE contenuAmeliore contenuAmeliore LONGTEXT DEFAULT NULL, CHANGE dateUpload dateUpload DATETIME NOT NULL, CHANGE nombreAmeliorations nombreAmeliorations INT NOT NULL, CHANGE estPublic estPublic TINYINT NOT NULL;
ALTER TABLE cv ADD CONSTRAINT FK_B66FFE92FE6E88D7 FOREIGN KEY (idUser) REFERENCES user (id);
CREATE INDEX IDX_B66FFE92FE6E88D7 ON cv (idUser);
ALTER TABLE user CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(255) NOT NULL, CHANGE actif actif TINYINT NOT NULL, CHANGE LocalDateTime LocalDateTime DATETIME DEFAULT NULL, CHANGE role role VARCHAR(255) DEFAULT NULL;
-- Version DoctrineMigrations\Version20260410094209 update table metadata;
INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\Version20260410094209', '2026-04-14 15:06:03', 0);

-- Version DoctrineMigrations\Version20260410115736
CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4;
ALTER TABLE questions_enquete DROP FOREIGN KEY `fk_attribut_enquete`;
ALTER TABLE reponse_enquete DROP FOREIGN KEY `fk_reponse_enquete`;
DROP TABLE abonnement;
DROP TABLE candidature;
DROP TABLE comment;
DROP TABLE connexions_utilisateur;
DROP TABLE employeur;
DROP TABLE enquete;
DROP TABLE news;
DROP TABLE notification;
DROP TABLE offers;
DROP TABLE paiement;
DROP TABLE questions_enquete;
DROP TABLE reclamation;
DROP TABLE recommendation;
DROP TABLE reponse_enquete;
DROP TABLE reponse_reclamation;
DROP TABLE society;
ALTER TABLE cv CHANGE contenuOriginal contenuOriginal LONGTEXT NOT NULL, CHANGE contenuAmeliore contenuAmeliore LONGTEXT DEFAULT NULL, CHANGE dateUpload dateUpload DATETIME NOT NULL, CHANGE nombreAmeliorations nombreAmeliorations INT NOT NULL, CHANGE estPublic estPublic TINYINT NOT NULL;
ALTER TABLE cv ADD CONSTRAINT FK_B66FFE92FE6E88D7 FOREIGN KEY (idUser) REFERENCES user (id);
CREATE INDEX IDX_B66FFE92FE6E88D7 ON cv (idUser);
ALTER TABLE user CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE type type VARCHAR(255) NOT NULL, CHANGE actif actif TINYINT NOT NULL, CHANGE LocalDateTime LocalDateTime DATETIME DEFAULT NULL, CHANGE role role VARCHAR(255) DEFAULT NULL;
-- Version DoctrineMigrations\Version20260410115736 update table metadata;
INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\Version20260410115736', '2026-04-14 15:06:03', 0);

-- Version DoctrineMigrations\Version20260413190000
ALTER TABLE user CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL;
-- Version DoctrineMigrations\Version20260413190000 update table metadata;
INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\Version20260413190000', '2026-04-14 15:06:03', 0);

-- Version DoctrineMigrations\Version20260413191000
ALTER TABLE cv ADD pdfPath VARCHAR(255) DEFAULT NULL;
-- Version DoctrineMigrations\Version20260413191000 update table metadata;
INSERT INTO doctrine_migration_versions (version, executed_at, execution_time) VALUES ('DoctrineMigrations\\Version20260413191000', '2026-04-14 15:06:03', 0);
