# Processus de contribution projet TODOLIST

### Philosophie du projet

Ce projet à été mis à jour en Mars 2022, nous souhaitons conserver une application pérenne et performante pour nos utilisateurs, c'est pourquoi nous avons décider d'adopter une méthodologie pour la mettre à jour.

## Étapes pour contribuer au projet

### 1 - Créer une banche

Dans ce projet nous avons fait le choix d'utiliser un gitflow :

![git flow: Branch master, dev and feature](assets/gitflow-1.png)

Comme expliqué ci-dessus il vous suffit de créer une nouvelle branche "from master" avec comme nom "feature/nomdemafeature".

Vous pouvez maintenant ajouter votre feature sur cette nouvelle branche

### 2 - Tester

Vous avez terminé de coder votre/vos feature(s). Il vous faut maintenant, tester votre code :

- Installez l'extension php [XDebug](https://xdebug.org/docs/install), écrivez vos tests puis rendez-vous a la racine du projet et lancez la commande : `php bin/phpunit --coverage-html coverage.html --coverage-clover=clover.xml`
  Vérifiez que l'output de la commande ne vous retourne AUCUNE erreur, vérifiez également que le code coverage de l'app ne passe pas en dessous de 80%.

## Merge

Si tout s'est bien passé jusque la, il ne vous restes plus qu'à merge votre branche sur master (Veillez à rebase votre branche depuis master si cela fait quelque temps que vous travaillez sur celle-ci).
