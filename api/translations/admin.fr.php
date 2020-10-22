<?php

return [

    /* DEFAULT */

    // Error page
    'error.get_error_title' => 'Erreur',
    'error.return' => 'Retour',

    /* LOGIN PAGE */

    'login.username_label' => 'Votre identifiant',
    'login.password_label' => 'Votre mot de passe',
    'login.log_in' => 'Connexion',
    'login.forget_password' => 'Mot de passe oublié ?',
    'login.intercept.logout' => 'Cette méthode peut être vide, elle sera interceptée lors de votre déconnexion',

    /* RESET PAGES */

    // Request password page
    'request.title.password' => 'Réinitialiser votre mot de passe',
    'request.text.password' => 'Entrez votre adresse email pour réinitialiser votre mot de passe',
    'request.submit.password' => 'Réinitialiser votre mot de passe',
    'request.login.password' => 'Connexion',

    // check email page
    'check.email.title' => 'Email de réinitialisation du mot de passe envoyé',
    'check.email.info_1' => 'Un email a été envoyé contenant un lien sur lequel vous pouvez cliquer pour réinitialiser votre mot de passe.',
    'check.email.info_2' => 'Ce lien expirera dans',
    'check.email.hour' => 'heure',
    'check.email.alert' => 'Si vous ne recevez pas d\'email, veuillez vérifier votre dossier spam ou ',
    'check.email.try_again' => 'réessayer',

    // reset password (token) page
    'reset.title.password' => 'Réinitialiser votre mot de passe',
    'reset.new_password.placeholder' => 'Nouveau mot de passe',
    'reset.repeat_password.placeholder' => 'Confirmation de mot de passe',
    'reset.return.error.validating' => 'Un problème est survenu lors de la validation de votre demande de réinitialisation',
    'reset.return.error.problem' => 'Un problème est survenu lors du traitement de votre demande de réinitialisation de mot de passe',
    'reset.return.error.not_found' => 'Aucun token de réinitialisation du mot de passe trouvé dans l\'URL ou dans la session',

    /* ADMIN PAGES */

    // Configuration Days
    'admin.field.monday' => 'Lundi',
    'admin.field.tuesday' => 'Mardi',
    'admin.field.wednesday' => 'Mercredi',
    'admin.field.thursday' => 'Jeudi',
    'admin.field.friday' => 'Vendredi',
    'admin.field.saturday' => 'Samedi',
    'admin.field.sunday' => 'Dimanche',

    // Configuration User Menu
    'admin.dashboard.my_profile' => 'Mon profil',

    // Dashboard Admin Page
    'admin.dashboard.home' => 'Tableau de bord',
    'admin.dashboard.logout' => 'Déconnexion',
    'admin.dashboard.menu.shop' => 'Shop Management',
    'admin.dashboard.menu.shops' => 'Shops',
    'admin.dashboard.menu.admin' => 'Admin Management',
    'admin.dashboard.menu.admins' => 'Admins',
    'admin.dashboard.menu.groups' => 'Groups',
    'admin.dashboard.menu.settings' => 'Paramètres avancées',
    'admin.dashboard.menu.documentation' => 'Documentation',
    'admin.dashboard.menu.api_doc' => 'Documentation de l\'API',

    // Configuration general field informations
    'admin.field.id' => 'ID',
    'admin.field.displayuuid' => 'Identifiant',
    'admin.field.created_at' => 'Crée le',
    'admin.field.updated_at' => 'Mis à jour le',

    // Shop Admin Page
    'Shop' => 'Shop',
    'Shop Files' => 'Shop Files',
    'Image file' => 'Image File',
    'admin.shop.title' => 'Information Shop',
    'admin.shop.panel_shop' => 'Informations d\identité Shop',
    'admin.shop.panel_shop_info' => 'Informations heures/localisation Shop',
    'admin.shop.panel_shop_files' => 'Photos du Shop',
    'admin.shop.panel_shop_admin' => 'Informations admin Shop',
    'admin.shop.field.name' => 'Nom',
    'admin.shop.field.email' => 'Email',
    'admin.shop.field.admins' => 'Administrateurs',
    'admin.shop.field.country' => 'Pays',
    'admin.shop.field.shop_hour' => 'Horaires',
    'admin.shop.field.open' => 'Ouvert',
    'admin.shop.field.break' => 'Pause',
    'admin.shop.field.shipping_click' => 'Livraison Click & Collect',
    'admin.shop.field.shipping_delivery' => 'Livraison de proxmité',
    'admin.shop.field.collection_shop_images.no_files' => 'Pas de fichiers',
    'admin.shop.field.is_active' => 'Activation',

    // Admin Admin Page
    'Admin' => 'Admin',
    'admin.admin.title' => 'Admin Informations',
    'admin.admin.field.email' => 'Email',
    'admin.admin.field.roles' => 'Roles',
    'admin.admin.field.groups' => 'Groups',
    'admin.admin.field.shops' => 'Shops',
    'admin.admin.field.password' => 'Mot de passe',
    'admin.admin.field.plain_password' => 'Mot de passe',

    // Group Admin Page
    'AdminGroup' => 'Group',
    'admin.group.title' => 'Information Group',
    'admin.group.field.name' => 'Nom',
    'admin.group.field.roles' => 'Rôles',
    'admin.group.field.admins' => 'Administrateurs',

    /* APP PAGES */

    // Default Page
    'pages.default.header' => 'Page par défaut',
    'pages.default.title' => 'Page par défaut',
    'pages.default.member' => 'Espace Membre',

    /* Statut Error PAGES */

    'Not Found' => 'Page Introuvable',
    'Internal Server Error' => 'Une erreur est survenue',

];
