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
    'admin.field.day.open' => 'Open',
    'admin.field.day.close' => 'Close',
    'admin.field.day.on' => 'on',

    // Configuration Months
    'admin.field.january' => 'January',
    'admin.field.february' => 'Fébruary',
    'admin.field.march' => 'March',
    'admin.field.april' => 'April',
    'admin.field.may' => 'May',
    'admin.field.june' => 'June',
    'admin.field.july' => 'July',
    'admin.field.august' => 'August',
    'admin.field.september' => 'September',
    'admin.field.october' => 'October',
    'admin.field.november' => 'November',
    'admin.field.december' => 'Décember',

    // Configuration User Menu
    'admin.dashboard.my_profile' => 'My profile',

    // Dashboard Admin Page
    'admin.dashboard.home' => 'Dashboard',
    'admin.dashboard.logout' => 'Logout',
    'admin.dashboard.menu.shop' => 'Shop Management',
    'admin.dashboard.menu.shops' => 'Shops',
    'admin.dashboard.menu.user' => 'User Management',
    'admin.dashboard.menu.users' => 'Users',
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
    'admin.field.reference' => 'Reference',
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
    'admin.shop.field.collection_shop_images.no_hour' => 'No set schedules',
    'admin.shop.field.is_active' => 'Activate',
    'admin.shop.field.city' => 'City',
    'admin.shop.field.postal_code' => 'Postal code',
    'admin.shop.field.address' => 'Address',
    'admin.shop.field.latitude' => 'Latitude',
    'admin.shop.field.longitude' => 'Longitude',
    'admin.shop.field.phone' => 'Phone',
    'admin.shop.flash.no_delete.admin' => 'Cannot delete Shop as long as an Admin is associated',

    // User Admin Page
    'User' => 'User',
    'None' => 'None',
    'admin.user.panel_user' => 'User parameters',
    'admin.user.panel_user_info' => 'User informations',
    'admin.user.panel_shop_id' => 'User informations Shop linked',
    'admin.user.field.email' => 'Email',
    'admin.user.field.is_active' => 'Is active',
    'admin.user.field.is_verified' => 'is Verified',
    'admin.user.field.password' => 'Password',
    'admin.user.field.plain_password' => 'Plain Password',
    'admin.user.field.shop' => 'Shop selected',
    'admin.user.field.firstname' => 'Firstname',
    'admin.user.field.lastname' => 'Lastname',
    'admin.user.field.birthday' => 'Birthday',
    'admin.user.field.gender' => 'Gender',
    'admin.user.field.phone' => 'Phone',
    'admin.user.field.gender_male' => 'Male',
    'admin.user.field.gender_female' => 'Female',
    'admin.user.field.gender_other' => 'Other',

    // Admin Admin Page
    'Admin' => 'Admin',
    'admin.admin.title' => 'Admin Informations',
    'admin.admin.field.email' => 'Email',
    'admin.admin.field.roles' => 'Roles',
    'admin.admin.field.groups' => 'Groups',
    'admin.admin.field.shops' => 'Shops',
    'admin.admin.field.password' => 'Password',
    'admin.admin.field.plain_password' => 'Plain Password',
    'admin.admin.flash.no_delete.shop' => 'Cannot delete Admin as long as an Shop is associated',

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
