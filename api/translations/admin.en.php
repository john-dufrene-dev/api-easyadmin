<?php

return [

    /* DEFAULT */

    // Error page
    'error.get_error_title' => 'Error',
    'error.return' => 'Return',

    /* LOGIN PAGE */

    'login.username_label' => 'Your username',
    'login.password_label' => 'Your password',
    'login.log_in' => 'Log in',
    'login.forget_password' => 'Forget password ?',
    'login.intercept.logout' => 'This method can be blank - it will be intercepted by the logout key on your firewall',

    /* RESET PAGES */

    // Request password page
    'request.title.password' => 'Reset your password',
    'request.text.password' => 'Enter your email address and we we will send you a link to reset your password',
    'request.submit.password' => 'Send',
    'request.login.password' => 'Login',

    // check email page
    'check.email.title' => 'Password Reset Email Sent',
    'check.email.info_1' => 'An email has been sent that contains a link that you can click to reset your password.',
    'check.email.info_2' => 'This link will expire in',
    'check.email.hour' => 'hour',
    'check.email.alert' => 'If you don\'t receive an email please check your spam folder or',
    'check.email.try_again' => 'try again',

    // reset password (token) page
    'reset.title.password' => 'Reset your password',
    'reset.new_password.placeholder' => 'New password',
    'reset.repeat_password.placeholder' => 'Repeat password',
    'reset.return.error.validating' => 'There was a problem validating your reset request',
    'reset.return.error.problem' => 'There was a problem handling your password reset request',
    'reset.return.error.not_found' => 'No reset password token found in the URL or in the session',

    /* ADMIN PAGES */

    // Configuration general informations
    'admin.general.title.edit' => 'Edit of - ',
    'admin.general.title.detail' => 'Detail of - ',

    // Configuration Days
    'admin.field.monday' => 'Monday',
    'admin.field.tuesday' => 'Tuesday',
    'admin.field.wednesday' => 'Wednesday',
    'admin.field.thursday' => 'Thursday',
    'admin.field.friday' => 'Friday',
    'admin.field.saturday' => 'Saturday',
    'admin.field.sunday' => 'Sunday',

    // Configuration User Menu
    'admin.dashboard.my_profile' => 'My profile',

    // Dashboard Admin Page
    'admin.dashboard.home' => 'Dashboard',
    'admin.dashboard.logout' => 'Logout',
    'admin.dashboard.menu.shop' => 'Shop Management',
    'admin.dashboard.menu.shops' => 'Shops',
    'admin.dashboard.menu.admin' => 'Admin Management',
    'admin.dashboard.menu.admins' => 'Admins',
    'admin.dashboard.menu.groups' => 'Groups',
    'admin.dashboard.menu.settings' => 'Core Settings',
    'admin.dashboard.menu.settings_general' => 'General Settings',
    'admin.dashboard.menu.documentation' => 'Documentation',
    'admin.dashboard.menu.api_doc' => 'API Documentation',
    'admin.dashboard.menu.monitoring' => 'Monitoring',
    'admin.dashboard.menu.logs' => 'Logs',

    // Configuration general field informations
    'admin.field.id' => 'ID',
    'admin.field.displayuuid' => 'Username',
    'admin.field.created_at' => 'Created at',
    'admin.field.updated_at' => 'Updated at',

    // Shop Admin Page
    'Shop' => 'Shop',
    'Shop Files' => 'Shop Files',
    'Image file' => 'Image File',
    'admin.shop.title' => 'Shop Informations',
    'admin.shop.panel_shop' => 'Shop informations identity',
    'admin.shop.panel_shop_info' => 'Shop informations hours/localization',
    'admin.shop.panel_shop_files' => 'Shop Files',
    'admin.shop.panel_shop_admin' => 'Shop informations admin',
    'admin.shop.field.name' => 'Name',
    'admin.shop.field.email' => 'Email',
    'admin.shop.field.admins' => 'Admins',
    'admin.shop.field.country' => 'Country',
    'admin.shop.field.shop_hour' => 'Shop hour',
    'admin.shop.field.open' => 'Open',
    'admin.shop.field.break' => 'Break',
    'admin.shop.field.shipping_click' => 'Shipping Click & Collect',
    'admin.shop.field.shipping_delivery' => 'Shipping delivery',
    'admin.shop.field.collection_shop_images.no_files' => 'No files',
    'admin.shop.field.is_active' => 'Activate',

    // Admin Admin Page
    'Admin' => 'Admin',
    'admin.admin.title' => 'Admin Informations',
    'admin.admin.field.email' => 'Email',
    'admin.admin.field.roles' => 'Roles',
    'admin.admin.field.groups' => 'Groups',
    'admin.admin.field.shops' => 'Shops',
    'admin.admin.field.password' => 'Password',
    'admin.admin.field.plain_password' => 'Plain Password',

    // Group Admin Page
    'AdminGroup' => 'Group',
    'admin.group.title' => 'Group Informations',
    'admin.group.field.name' => 'Name',
    'admin.group.field.roles' => 'Roles',
    'admin.group.field.admins' => 'Admins',

    // Config Admin Page
    'Config' => 'Config',
    'admin.config.field.name' => 'Name',
    'admin.config.field.description' => 'Description',
    'admin.config.field.value' => 'Value',
    'admin.config.field.is_active' => 'Activate',
    'admin.config.panel_general' => 'General configuration information',

    // Log Admin Page
    'Log' => 'Log',
    'admin.log.panel_info' => 'Log informations identity',
    'admin.log.field.message' => 'Message',
    'admin.log.field.user' => 'Admin',
    'admin.log.field.context' => 'Context',
    'admin.log.field.level' => 'Level',
    'admin.log.field.level_name' => 'level Name',
    'admin.log.badge__1' => '-1',
    'admin.log.badge_1' => '1',
    'admin.log.badge_error' => 'ERROR',
    'admin.log.badge_notice' => 'NOTICE',

    /* APP PAGES */

    // Default Page
    'pages.default.header' => 'Default Page',
    'pages.default.title' => 'Default Page',
    'pages.default.member' => 'Member Space',

    // Callback Page
    'pages.callback.header' => 'Callback Page',
    'pages.callback.title' => 'Callback Page',

    /* Statut Error PAGES */

    'Not Found' => 'Not Found',
    'Internal Server Error' => 'Internal Server Error',

];
