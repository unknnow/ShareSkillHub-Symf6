# [WIP] - ShareSkillHub

*Ce projet est réalisé à des fins strictement démonstratives.*

ShareSkillHub est une plateforme en ligne qui connecte des personnes cherchant à partager leurs compétences avec celles qui souhaitent apprendre. Le site vise à créer une communauté dynamique où les utilisateurs peuvent échanger leurs connaissances, que ce soit dans le domaine professionnel, artistique, sportif, ou autre.

## Fonctionnalités

- **Profils Utilisateurs :** Les utilisateurs peuvent créer des profils détaillés mettant en avant leurs compétences, expériences et domaines d'expertise. Personnalisez les profils avec des photos, des badges de compétences et des recommandations d'autres utilisateurs.

- **Planification des Sessions :** Intégration d'un calendrier pour permettre aux utilisateurs de planifier des sessions d'apprentissage en ligne ou en personne.

- **Système de Recommandation :** Les utilisateurs peuvent recommander et laisser des commentaires sur les profils des autres, renforçant ainsi la confiance au sein de la communauté.

- **Système de Notation :** Les utilisateurs peuvent noter et évaluer les sessions d'apprentissage, ce qui contribue à la transparence et à la qualité de l'expérience.

- **Tableau de Bord Personnalisé :** Chaque utilisateur a un tableau de bord personnalisé qui affiche les dernières activités, les messages, les recommandations et les suggestions basées sur leurs préférences.

- **[WIP] - Recherche et Filtrage :** Un moteur de recherche avancé permet aux utilisateurs de trouver des mentors ou des apprenants en fonction de critères tels que la localisation, la catégorie de compétence, les niveaux d'expérience, etc.

- **[WIP] - Système de Messagerie :** Les utilisateurs peuvent communiquer via une messagerie interne pour discuter des détails, planifier des sessions d'apprentissage et partager des ressources.

- **[WIP] - Forum de Communauté :** Un espace dédié aux discussions communautaires, où les utilisateurs peuvent poser des questions, partager des conseils et discuter de sujets liés à leurs compétences.

- **[WIP] - Intégration des Réseaux Sociaux :** Les utilisateurs peuvent connecter leurs profils à des réseaux sociaux pour faciliter le partage de leurs réalisations et compétences.

*WIP : Work In Progress (Travail en Cours) - Ces fonctionnalités ne sont pas encore implémentées mais sont prévues dans le développement.*

## Technologies Utilisées

- Symfony (backend)
- Doctrine ORM (gestion de base de données)
- HTML, CSS, JavaScript (frontend)
- Bootstrap (design réactif)
- Système de gestion d'authentification
- Calendrier intégré (FullCalendar)
- Base de données relationnelle (MySQL)
- [WIP] -Intégration de services de messagerie (Symfony Messenger)

## Démarrage

Suivez ces étapes pour configurer et exécuter le projet ShareSkillHub en local :

1. Clonez le dépôt : `git clone https://github.com/votre-nom-utilisateur/ShareSkillHub.git`
2. Installez les dépendances : `composer install`
3. Configurez la base de données : `php bin/console doctrine:database:create && php bin/console doctrine:migrations:migrate`
4. Chargez des données d'exemple (optionnel) : `php bin/console doctrine:fixtures:load`
5. Lancez le serveur de développement Symfony : `symfony serve`

Rendez-vous sur `http://localhost:8000` dans votre navigateur web pour accéder à l'application ShareSkillHub.
