PROJET
======

Le projet OpenHarcèlement permet de harceler facilement par mail une personne.

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

LICENCE
=======

http://www.opensource.org/licenses/bsd-license.php

AUTEUR
======

Simon Leblanc <contact@leblanc-simon.eu>
