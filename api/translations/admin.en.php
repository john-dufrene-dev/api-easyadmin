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

    // Configuration general field informations
    'admin.field.id' => 'ID',
    'admin.field.displayuuid' => 'Username',
    'admin.field.created_at' => 'Created at',
    'admin.field.updated_at' => 'Updated at',

    // Shop Admin Page
    'Shop' => 'Shop',
    'admin.shop.title' => 'Shop Informations',
    'admin.shop.field.name' => 'Name',
    'admin.shop.field.email' => 'Email',
    'admin.shop.field.admins' => 'Admins',

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

];
