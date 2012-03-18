PROJET
======

Le projet OpenHarcèlement permet de harceler facilement par mail une personne.

INSTALLATION
============

Pour installer le logiciel :

* copier le dossier sur un serveur web (PHP >= 5.3)
* modifier le fichier config/config.inc.php selon vos besoins
* vous avez un exemple debase sqlite dans le dossier db ou le fichier SQL dans le dossier db
* Ajouter le fichier cron.php dans votre crontab

USAGE
=====

Création d'un harcèlement
-------------------------

Pour créer un harcèlement, il faut appeler l'API avec les paramètres suivants :

* URL : www/api.php
* Méthode : POST
* Données : name, email, email_victime, subject, message, time
* L'api retourne un code HTTP 201 en cas de succès, un autre code en cas d'échec

Suppression d'un harcèlement
----------------------------

Lors de la créaction d'un harcèlement, vous avez reçu un email contenant un hash sha1 vous permettant de supprimer le hash. La suppression nécessite l'appel à l'API avec les paramètres suivants :

* URL : www/api.php?[hash de suppression]
* Méthode : DELETE
* L'api retourne un code HTTP 200 en cas de succès, un autre code en cas d'échec

CLIENT
======

Il existe un client lourd pour cette API : QOpenHarcèlement
Les sources de ce client se trouve dans le dossier client, il vous suffit de le compiler, de modifier le fichier qopenharcelement.ini avec la bonne valeur pour l'URL de l'api et vous pourrez utilisez l'API simplement

Attention : le client actuel est un proof of concept, il n'est pas réputé stable ou fini.

LICENCE
=======

http://www.opensource.org/licenses/bsd-license.php

AUTEUR
======

Simon Leblanc <contact@leblanc-simon.eu>
