# la-boutique-de-clement

Boutique en ligne - Symfony 2024

# Prérequis

Avoir un compte Stripe :
    1. https://docs.stripe.com/checkout/quickstart
    2. https://dashboard.stripe.com/test/dashboard

Avoir un compte MailJet :
    1. https://github.com/mailjet/mailjet-apiv3-php
    2. https://dev.mailjet.com/email/guides/getting-started/
    3. https://app.mailjet.com/account/apikeys

# Installation du projet en local

composer install avec php >= 8.2
cp .env.test .env

# Mises en production

1. Commit & push
- git add <fichier1> <fichier2> ...
- git commit -m "Mon message commit"
- git push

2. Sur Hostinger (Dans Gestionnaire de fichiers): 
- Vider le cache : supprimer les sous dossiers à l'intérieur de "public_html/var/cache"

# Compléments mises en production

1. Vérifier le contenu et l'existance d'un .env.local sur le serveur de production
    APP_ENV=prod
    APP_DEBUG=false
    APP_SECRET=
    APP_URL=
    ...

2. Vérifier l'existance d'un .htaccess à la racine "public_html" :
    # php_flag display_errors on
    # php_flag display_startup_errors on
    RewriteEngine On
    RewriteRule ^$ public/index.php [L]
    RewriteRule ^(.*)$ public/$1 [L]

3. Vérifier l'existance d'un .htaccess dans dossier "public_html/public" :
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteBase /
        # Rediriger toutes les requêtes non existantes vers index.php
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^(.*)$ index.php [QSA,L]
    </IfModule>

4. Vérifier l'existance du dossier /var et des sous dossiers /cache et /log

5. Vérifier l'existance d'un fichier .env vide au départ

6. Executer en local "php bin/console assets:install public/" puis copier depuis le local le  dossier "/public/assets" et le coller dans "public_html/public/"

7. Copier depuis le local le dosser "/public/bundles" et le coller dans le dossier "public_html/public/"

# Documentations

https://symfony.com/doc/current/index.html
https://twig.symfony.com/doc/2.x/
https://getbootstrap.com/docs/5.3/getting-started/introduction/

# Lien formation

https://www.udemy.com/course/apprendre-symfony-par-la-creation-dun-site-ecommerce/?couponCode=KEEPLEARNING