# Kalkulator OC/AC - recruitment-task

Repozytorium z kalkulatorem OC/AC stworzone na potrzeby rekrutacji. Frontend został napisany z wykorzystaniem `Vue3`, natomiast backend z wykorzystaniem `PHP 8.1`.


## Demo
W razie gdyby podczas uruchomienia z poradnika poniżej wystąpił jakiś błąd, to zostawiam link do strony gdzie owy projekt jest hostowany.

[calculator.pbielanin.pl](http://calculator.pbielanin.pl)


## Uruchomienie

Aby uruchomić projekt należy sklonować go do wybranego folderu, a następnie z poziomu folderu `/docker` włączyć budowanie obrazów za pomocą komendy poniżej.

#### Informacja
- Całe repozytorium może budować się nawet do `10 minut` ze względu na pobierane paczki przez composer'a oraz npm'a
- Nie należy edytować plików `.env` ze względu na mapowanie woluminów w kontenerach

#### Sklonuj repozytorium
```sh
git clone git@github.com:PatrykBielanin/v-recruitment-task.git .
```

#### Przejdź do katalogu
```sh
cd /docker
```


#### Uruchom budowanie dockera
```sh
docker-compose up --build -d
```

## Sprawdzone wersje Dockera

Projekt testowany był na dwóch systemach operacyjnych i różnych wersjach `dockera` oraz `docker-compose`.

**Windows 11 Pro**
- Docker Compose version v2.15.1
- Docker version 20.10.23, build 7155243

#

**Ubuntu 20.04.3 LTS**
- Docker Compose version 1.25.0
- Docker version 20.10.17, build 100c701

#

**Ubuntu 20.04.6 LTS**
- Docker Compose version 1.25.0
- Docker version 23.0.2, build 569dd73
