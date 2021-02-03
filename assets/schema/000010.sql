drop database if exists recibeira;

create database recibeira default character set utf8 collate utf8_unicode_ci;

use recibeira;

create table usuarios (
	id int AUTO_INCREMENT NOT NULL UNIQUE,
	username varchar(50) NOT NULL,
	user_email varchar(50) NOT NULL,
	user_password varchar(255) NOT NULL,
	email_hash varchar(40) NOT NULL,
	email_verified tinyint(1) NOT NULL,
	acc_code int(6) NOT NULL,
	user_id int(10),
	user_token varchar(64),
	PRIMARY KEY (id)
);

create table user_profiles (
	profile_id int AUTO_INCREMENT NOT NULL,
  user_id int NOT NULL,
  profile_name varchar(30) NOT NULL,
  locador varchar(50) NOT NULL,
  locatario varchar(50) NOT NULL,
  inicio_contrato date,
  termino_contrato date,
  valor_aluguel decimal(7,2) NOT NULL,
  rua varchar(40) NOT NULL,
  numero_casa int(5) NOT NULL,
  bairro varchar(40) NOT NULL,
  cidade varchar(30) NOT NULL,
  casa_fundo tinyint(1) NOT NULL,
  referente_from date NOT NULL,
  referente_to date NOT NULL,
  data_recibo date NOT NULL,
  PRIMARY KEY (profile_id),
  FOREIGN KEY (user_id) REFERENCES usuarios(id)
);