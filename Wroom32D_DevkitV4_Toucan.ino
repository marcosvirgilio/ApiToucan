//ENCODER ROTARY
#include "AiEsp32RotaryEncoder.h"
//REDE WIFI E REQUEST
#include <ArduinoJson.h>
#include <HTTPClient.h>
#include <WiFiMulti.h>
//Sensor Corrente INA2019
#include <Wire.h>
#include <Adafruit_INA219.h>
//ENCODER ROTARY
#define ROTARY_CLK_PIN 34
#define ROTARY_DT_PIN 35
#define ROTARY_SW_PIN 32
#define ROTARY_VCC_PIN -1
#define ROTARY_STEPS 4

//Instanciando objeto encoder
AiEsp32RotaryEncoder rotaryEncoder = AiEsp32RotaryEncoder(ROTARY_CLK_PIN, ROTARY_DT_PIN, ROTARY_SW_PIN, ROTARY_VCC_PIN, ROTARY_STEPS);
//Variáveis Encoder
int voltas = 0;

//ID DISPOSITIVO
int idDispositivo = 1;

//REDE WIFI
const char *AP_SSID = "IFSC";
const char *AP_PWD = "campuschapeco";

//Instanciando objeto wifi
WiFiMulti wifiMulti;

//Instanciando objeto HTTP
HTTPClient http;

//Instanciando Sensor corrente INA219
Adafruit_INA219 ina219;
//Variáveis leitura corrente e tensão
float tensao_V = 0;
float corrente_mA = 0;

//Funcao le valores sensor INA219
void medeCorrenteTensao() {
  tensao_V = ina219.getBusVoltage_V();
  //pega corrente convertida de A para mA
  corrente_mA = abs(ina219.getCurrent_mA()/1000);
  Serial.print("V:");
  Serial.println(tensao_V);
  Serial.print("mah:");
  Serial.println(corrente_mA);
  delay(2000);
}

//Funcao que envia json para servidor quando encoder detecta movimento
void postDataToServer() {
  // Block until we are able to connect to the WiFi access point
  if (wifiMulti.run() == WL_CONNECTED) {
    http.begin("http://marcosvirgilio.dev.br/toucan/cadLeitura.php");
    http.addHeader("Content-Type", "application/json");

    StaticJsonDocument<200> doc;
    //idDispositivo é fixo por placa
    doc["idDispositivo"] = idDispositivo;
    // Adicionar valores lidos
    doc["vlEncoder"] = voltas;
    doc["vlCorrente"] = corrente_mA;
    doc["vlTensao"] = tensao_V;

    String requestBody;
    serializeJson(doc, requestBody);

    Serial.println("Enviando JSON para o servidor...");
    Serial.println(requestBody);
    int httpResponseCode = http.POST(requestBody);

    if (httpResponseCode > 0) {

      String response = http.getString();

      Serial.println(httpResponseCode);
      Serial.println(response);

    } else {

      Serial.printf("Error occurred while sending HTTP POST: %s\n", http.errorToString(httpResponseCode).c_str());
    }
  }
}


void rotary_onButtonClick() {
  Serial.print("Botão acionado");
}

void rotary_loop() {
  //posta dados no servidor a cada volta no eixo
  if (rotaryEncoder.encoderChanged()) {
    Serial.print("Valor Encoder: ");
    Serial.println(rotaryEncoder.readEncoder());
    if (rotaryEncoder.readEncoder() >= 19) {
      //incrementa voltas a cada 19/ 20 posições
      voltas++;
      Serial.print("Voltas Encoder: ");
      Serial.println(voltas);
      //Faz leitura sensor INA2019
      medeCorrenteTensao();
      //manda dados para o servidor
      postDataToServer();
    }
  }
  if (rotaryEncoder.isEncoderButtonClicked()) {
    rotary_onButtonClick();
  }
  //zera voltas a cada 30 mil
  if (voltas >= 30000) {
    voltas = 0;
  }
}

void IRAM_ATTR readEncoderISR() {
  rotaryEncoder.readEncoder_ISR();
}



void setup() {
  Serial.begin(115200);
  //Sensor corrente INA219
  while (!ina219.begin()) {
    Serial.println("Conectando sensor INA219");
    delay(10);
  }
  //MAC ADDRESS
  String macAddress = WiFi.macAddress();
  Serial.print("MAC Address : ");
  Serial.println(macAddress);
  delay(2000);
  //Cria Access Point rede
  wifiMulti.addAP(AP_SSID, AP_PWD);
  //ENCODER
  rotaryEncoder.begin();
  rotaryEncoder.setup(readEncoderISR);
  //definindo limites da volta do encoder
  bool circleValues = true;
  //minimo, maximo, voltas true|false
  rotaryEncoder.setBoundaries(0, 20, circleValues);
  //Desativando aceleração
  rotaryEncoder.disableAcceleration();
  //Passando 0 desativa aceleração
  rotaryEncoder.setAcceleration(0);
}

void loop() {
  //função para ações do encoder
  rotary_loop();
  delay(50);
}
