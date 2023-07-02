CREATE TABLE IF NOT EXISTS usuarios (
	id INT AUTO_INCREMENT NOT NULL UNIQUE,
	username VARCHAR(50) NOT NULL,
	user_email VARCHAR(50) NOT NULL,
	user_password VARCHAR(255) NOT NULL,
	email_hash VARCHAR(40) NOT NULL,
	email_verified tinyint(1) NOT NULL,
	acc_code INT(6) NOT NULL,
	user_id INT(10),
	user_token VARCHAR(64),
	PRIMARY KEY (id)
);

create table IF NOT EXISTS user_profiles (
	profile_id INT AUTO_INCREMENT NOT NULL,
  user_id INT NOT NULL,
  profile_name VARCHAR(30) NOT NULL,
  locador VARCHAR(50) NOT NULL,
  locatario VARCHAR(50) NOT NULL,
  inicio_contrato DATE,
  termino_contrato DATE,
  valor_aluguel DECIMAL(7,2) NOT NULL,
  rua VARCHAR(40) NOT NULL,
  numero_casa int(5) NOT NULL,
  bairro VARCHAR(40) NOT NULL,
  cidade VARCHAR(30) NOT NULL,
  casa_fundo TINYINT(1) NOT NULL, /* boolean */
  referente_from DATE NOT NULL,
  referente_to DATE NOT NULL,
  data_recibo DATE NOT NULL,
  PRIMARY KEY (profile_id),
  FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS recibos (
	recibo_id INT AUTO_INCREMENT NOT NULL UNIQUE,
	profile_id INT NOT NULL,
  referente_from DATE NOT NULL,
  referente_to DATE NOT NULL,
  data_recibo DATE NOT NULL,
  temporario TINYINT(1) NOT NULL, /* boolean */
	PRIMARY KEY (recibo_id),
  FOREIGN KEY (profile_id) REFERENCES user_profiles(profile_id) ON DELETE CASCADE
);
