<?php

return [

    /* ASSERTS VALIDATIONS */

    // General constraints
    'asserts.entity.unique' => 'Id and Uuid {{ value }} already exist',
    'asserts.entity.created_at.not_null' => 'Creation date {{ value }} could not be null',
    'asserts.entity.updated_at.not_null' => 'Updated date {{ value }} could not be null',
    'asserts.entity.name.not_null' => 'Name value could not be null',
    'asserts.entity.name.not_blank' => 'Name value could not be empty',
    'asserts.entity.email.not_null' => 'Email value could not be null',
    'asserts.entity.email.not_blank' => 'Email value could not be empty',
    'asserts.entity.email.not_valid' => 'The email {{ value }} is not a valid email',
    'asserts.entity.min_length' => '{{ value }} must be at least {{ limit }} characters long',
    'asserts.entity.max_length' => '{{ value }} cannot be longer than {{ limit }} characters',
    'asserts.entity.country' => 'This value is not a valid country',

    // Form change password
    'asserts.change_password.require' => 'Please enter a password',
    'asserts.change_password.min_length' => 'Your password should be at least {{ limit }} characters',
    'asserts.change_password.match' => 'The password fields must match',

    // Shop Entity
    'asserts.shop.unique' => 'Shop email {{ value }} already exist',

    // User Entity
    'asserts.user.unique' => 'User email {{ value }} already exist',
    'asserts.user.password.not_compromise' => 'This password is compromise, please choose a more difficult one',

    // Admin Entity
    'asserts.admin.unique' => 'Admin email {{ value }} already exist',
    'asserts.admin.password.not_compromise' => 'This password is compromise, please choose a more difficult one',

    // Groups Entity
    'asserts.group.unique' => 'Group name {{ value }} already exist',

];
