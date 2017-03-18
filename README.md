<p align='center'>
  <img src='https://horizon.buongustai.ovh/src/Admin/assets/img/logo.png' width='20%'>
  <h1 align='center'>Horizon</h1>
</p>
<img src='https://i.imgur.com/6eEfqrq.png'>

Store all series and mangas in a library in order not to miss even an episode/chapter

# Installation
This repository must be installed in the "src" folder of Gate Framework in order to work

1) Clone repositories
```
$ git clone https://github.com/EchoWine/Gate [directory]
$ cd [directory]
$ git clone https://github.com/EchoWine/Horizon src
```
2) Install with composer
```
$ composer install
```
3) Setup your environment
```
$ cp config/app.php.default config/app.php
```

4) temp (due to bug)
```
$ mkdir app/cache/views
```

The index file is "/public/index.php"

# Commands - What's today

Update the info about series/anime/manga
```
php app/console wt:update

```

Download the chapters in queue 
```
php app/console wt:download:queue

```
