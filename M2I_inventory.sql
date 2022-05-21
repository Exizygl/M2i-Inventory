CREATE DATABASE Inventory;

USE Inventory;

CREATE TABLE agence (
    id_agence INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50) NOT NULL,
    adresse VARCHAR(50) NOT NULL,
    email VARCHAR(320) NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE password_reset (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(320) NOT NULL,
    token VARCHAR(100) NOT NULL
);

CREATE TABLE materiel (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    code_bar BIGINT,
    description TEXT,
    nombre_total INT,
    disponible int,
    id_agence_origine INT,
    id_agence_actuelle INT,
    FOREIGN KEY (id_agence_origine) references agence(id),
    FOREIGN KEY (id_agence_actuelle) references agence(id)
);



CREATE TABLE ordinateur (
    id_ordi INT PRIMARY KEY AUTO_INCREMENT,
    modele VARCHAR(50),
    ram int,
    processeur VARCHAR(50),
    disque_dur VARCHAR(20),
    id_mat INT,
    FOREIGN KEY (id_mat) references materiel(id)
);


CREATE TABLE composants (
    id_comp INT PRIMARY KEY AUTO_INCREMENT,
    type_composant VARCHAR(50),
    detail_composant VARCHAR(255),
    id_mat INT,
    FOREIGN KEY (id_mat) references materiel(id)
);


CREATE TABLE peripheriques (
    id_peri INT PRIMARY KEY AUTO_INCREMENT,
    type_catalogue VARCHAR(50),
    type_peripherique VARCHAR(50),
    detail_peripheriques VARCHAR(255),
    id_mat INT,
    FOREIGN KEY (id_mat) references materiel(id)
);

select * from  materiel 
left join ordinateur on materiel.id = ordinateur.id_mat
left join peripheriques on materiel.id = peripheriques.id_mat
left join composants on materiel.id = composants.id_mat

SELECT *  , sum(materiel.disponible) as ordi_dispo, sum(materiel.nombre_total) as ordi_total FROM materiel left join ordinateur on materiel.id = ordinateur.id_mat
                        left join peripheriques on materiel.id = peripheriques.id_mat
                        left join composants on materiel.id = composants.id_mat WHERE materiel.id_agence_origine = materiel.id_agence_actuelle 
                        AND materiel.id_agence_origine = :id AND (materiel.nom LIKE  concat('%' ,:input, '%') OR materiel.code_bar LIKE  concat('%' ,:input, '%'))
                        group by ram, processeur, disque_dur, modele
SELECT * FROM materiel 
left join ordinateur on materiel.id = ordinateur.id_mat 
left join peripheriques on materiel.id = peripheriques.id_mat 
left join composants on materiel.id = composants.id_mat 
WHERE materiel.id_agence_origine = materiel.id_agence_actuelle 
AND materiel.id_agence_origine = :id 
AND (materiel.nom LIKE concat('%' ,:recherche, '%') 
OR materiel.code_bar LIKE concat('%' ,:recherche, '%'));


SELECT *  , sum(materiel.disponible) as ordi_dispo, sum(materiel.nombre_total) as ordi_total, IFNULL(modele,id) as groupe_null FROM materiel left join ordinateur on materiel.id = ordinateur.id_mat
                        left join peripheriques on materiel.id = peripheriques.id_mat
                        left join composants on materiel.id = composants.id_mat WHERE materiel.id_agence_origine = materiel.id_agence_actuelle 
                        AND materiel.id_agence_origine = :id AND (materiel.nom LIKE  concat('%' ,:input, '%') OR materiel.code_bar LIKE  concat('%' ,:input, '%'))
                        group by ram, processeur, disque_dur, modele, groupe_null