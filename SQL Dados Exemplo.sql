INSERT INTO cliente( nuCPF, nmCliente, deEmail, deSenha) 
VALUES ('11111111111','Equipe Mary Keller','marykeller@gmail.com','7a0ed722ef6b4ecd0a2e2e395999b116');

INSERT INTO dispositivo( idCliente, nrMacAddress, deDispositivo, nrSerie) 
VALUES (1,'1111','Turbina P4G','1');

INSERT INTO leitura(idDispositivo, dtLeitura, vlEncoder, vlCorrente, vlTensao) 
VALUES (1,'2024-10-21',1,0.2,1.3);




