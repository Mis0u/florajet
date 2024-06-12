# Florajet

Faire un `git clone` du projet puis aller à la racine.

Effectuer :

```bash
composer install
```

Et lancer les containers Docker :

```bash
docker compose up -d
```

Créer la base de donnée :

```bash
symfony console d:d:c
symfony console make:migration
symfony console d:m:m
```

Lancer la commande `symfony console app:create-articles` qui vous permettra de récupérer les différents articles à partir de l'api **https://saurav.tech/NewsAPI/top-headlines/category/health/fr.json**,
du flux RSS **http://www.lemonde.fr/rss/une.xml** et du CSV se trouvant dans le répertoire `src/File`

Lancer votre server :

```bash
symfony server:start
```

Et rendez-vous sur la page principale. Si vous rajoutez à la fin de l'url `/api` vous pourrez rechercher, modifier, supprimer les différents articles
