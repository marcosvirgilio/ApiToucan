-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Table cliente
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS cliente (
  idCliente INT NOT NULL AUTO_INCREMENT,
  nuCPF VARCHAR(11) NOT NULL,
  nmCliente VARCHAR(150) NOT NULL,
  deEmail VARCHAR(200) NOT NULL,
  deSenha VARCHAR(200) NOT NULL,
  PRIMARY KEY (idCliente));

CREATE UNIQUE INDEX nuCPF_UNIQUE ON cliente (nuCPF);

CREATE UNIQUE INDEX deEmail_UNIQUE ON cliente (deEmail);


-- -----------------------------------------------------
-- Table dispositivo
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS dispositivo (
  idDispositivo INT NOT NULL AUTO_INCREMENT,
  idCliente INT NULL,
  nrMacAddress VARCHAR(100) NULL,
  deDispositivo VARCHAR(200) NOT NULL,
  nrSerie VARCHAR(50) NULL,
  PRIMARY KEY (idDispositivo),
  CONSTRAINT fl_cliente
    FOREIGN KEY (idCliente)
    REFERENCES cliente (idCliente));

CREATE UNIQUE INDEX idDispositivo_UNIQUE ON dispositivo (idDispositivo);

CREATE UNIQUE INDEX nrMacAddress_UNIQUE ON dispositivo (nrMacAddress);

CREATE UNIQUE INDEX nrSerie_UNIQUE ON dispositivo (nrSerie);

CREATE INDEX fl_cliente_idx ON dispositivo (idCliente);


-- -----------------------------------------------------
-- Table leitura
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS leitura (
  idLeitura INT NOT NULL AUTO_INCREMENT,
  idDispositivo INT NULL,
  dtLeitura DATETIME NOT NULL,
  vlEncoder DECIMAL(12,2) NOT NULL,
  vlCorrente DECIMAL(12,2) NOT NULL,
  vlTensao DECIMAL(12,2) NOT NULL,
  PRIMARY KEY (idLeitura),
  CONSTRAINT fk_dispositivo
    FOREIGN KEY (idDispositivo)
    REFERENCES dispositivo (idDispositivo)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
;

CREATE UNIQUE INDEX id_UNIQUE ON leitura (idLeitura);

CREATE INDEX fk_dispositivo_idx ON leitura (idDispositivo);

CREATE UNIQUE INDEX disp_data_UNIQUE ON leitura (idDispositivo, dtLeitura);
