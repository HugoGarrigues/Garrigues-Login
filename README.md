# GARRIGUES-LOGIN

Ce projet est un projet a réaliser dans le cadre du module Cybersécurité. <br>
Ce projet vise à mettre en place un middleware et des API sécurisées utilisant des requêtes préparées pour gérer efficacement les opérations CRUD des utilisateurs. Il assure une sécurité renforcée avec des mots de passe hashé, une protection contre les attaques par brut force, et une gestion des sessions incluant des tokens CSRF. Respectant les standards de sécurité, il offre une architecture robuste contre l'élévation de privilèges et adopte une approche « Secured by Design » pour une sécurité intégrée dès la conception.

## Sommaire

- [Installation](#installation)
- [Utilisation](#utilisation)
- [Base de données](#base-de-données)
- [Architecture](#architecture)
- [Technologies](#technologies)
- [Auteurs](#auteurs)

## Installation

```bash
git clone https://github.com/HugoGarrigues/Garrigues-Login.git

Installer la base de données (voir plus bas)

Créer un fichier pwd.json dans le dossier credentials avec le contenu suivant : 
{
    "host": "localhost",
    "dbname": "login",
    "user": "",
    "password": ""
}
```
## Utilisation

Accés page Index : `view-source:http://localhost/Garrigues-DAL/public/index.php` <br>

## Base de données

```sql
CREATE DATABASE IF NOT EXISTS login;

USE login;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
    );
```

## Architecture

``` bash
├── wamp64
│   ├── www
│   │   └── Garrigues-Login
│   │       ├── public
│   │       │   ├── index.php
│   │       │   ├── signup.php
│   │       │   ├── signin.php
│   │       │   ├── changepwd.php
│   │       │   └── signedin.php
│   │       ├── src
│   │       │   ├── Config
│   │       │   │   └── BddAccess.php
│   │       │   ├── Services
│   │       │   │   └── DataManager.php
│   │       │   └── 
│   │       ├── .gitattributes
│   │       └── README.md
│   ├── crendentials
│   │   ├── pwd.json
└──   └──   └──
```


## Technologies

- PHP 7.4
- MySQL 8.0.23

## Auteurs

- GARRIGUES Hugo - https://github.com/HugoGarrigues
