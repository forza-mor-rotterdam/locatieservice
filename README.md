# MOR Locatie Service

Deze applicatie maakt inzichtelijk waar verschillen in woonplaatsen, wijken en buurten zitten tussen huidige landelijke data(pdok) en historische MOR meldingen data. Daarnaast worden de woonplaatsen, wijken en buurten uit de twee bornnen pdok en MOR meldingen gecombineerd als bron en voor andere MOR applicatie ontsloten met deze applicatie. 

## Tech Stack

[Symfony](https://symfony.com/)

## Get Started 🚀

To get started, install [Docker](https://www.docker.com/)

### Clone application code

```
git clone git@github.com:forza-mor-rotterdam/locatieservice.git
```

### Create local dns entry

In a new console window/tab, go to [project-root]/ 
Add '127.0.0.1 locatieservice.mor.local' to your hosts file

### Create docker networks

```bash
docker network create locatieservice_network
docker network create mor_bridge_network
```

### Create env variables

Create ./.env.local file with the content of .env.dev:
```bash
cp ./.env.dev .env.local
```

### Start application

Build and run container:

```bash
docker compose up
```

This will start a webserver.
You can view the website on http://locatieservice.mor.local:8010

### Initial migration

In a new console window/tab, go to [project-root]/
```bash
docker compose exec locatieservice_app php /app/bin/console doctrine:migrations:migrate
```


### Update pdok data

In a new console window/tab, go to [project-root]/
```bash
docker compose exec locatieservice_app php /app/bin/console app:import:pdok
```

### Update MOR-Core data

Start MOR-Core application https://github.com/forza-mor-rotterdam/mor-core  
In a new console window/tab, go to [project-root]/
```bash
docker compose exec locatieservice_app php /app/bin/console app:import:morcore
```
