<?php

return [

    /* ASSERTS VALIDATIONS */

    // General constraints
    'asserts.entity.unique' => 'Id and Uuid {{ value }} already exist',
    'asserts.entity.created_at.not_null' => 'Creation date {{ value }} could not be null',
    'asserts.entity.updated_at.not_null' => 'Updated date {{ value }} could not be null',
    'asserts.entity.generic.not_null' => 'Value could not be null',
    'asserts.entity.generic.not_blank' => 'Value could not be empty',
    'asserts.entity.name.not_null' => 'Name value could not be null',
    'asserts.entity.name.not_blank' => 'Name value could not be empty',
    'asserts.entity.ulid.not_null' => 'Reference value could not be null',
    'asserts.entity.ulid.not_blank' => 'Reference value could not be empty',
    'asserts.entity.email.not_null' => 'Email value could not be null',
    'asserts.entity.email.not_blank' => 'Email value could not be empty',
    'asserts.entity.email.not_valid' => 'The email {{ value }} is not a valid email',
    'asserts.entity.min_length' => '{{ value }} must be at least {{ limit }} characters long',
    'asserts.entity.max_length' => '{{ value }} cannot be longer than {{ limit }} characters',
    'asserts.entity.country' => 'This value is not a valid country',
    'asserts.entity.bool' => 'The value {{ value }} is not a valid',
    'asserts.entity.file.not_null' => 'File Upload could not be null',
    'asserts.entity.int.not_valid' => 'The value {{ value }} is not a valid {{ type }}.',
    'asserts.entity.phone' => 'The value {{ value }} is not a valid format',
    'asserts.entity.postal_code' => 'The value {{ value }} is not a valid postal code',
    'asserts.entity.range_localization' => 'Value must be between {{ min }} and {{ max }}',
    'asserts.entity.gender' => 'Gender does not exist',
    'asserts.entity.valid.type' => 'The value {{ value }} is not a valid {{ type }}',
    'asserts.entity.secret.not_null' => 'Secret could not be null',
    'asserts.entity.token.not_null' => 'Token could not be null',
    'asserts.entity.requested_at.not_null' => 'Requested date {{ value }} could not be null',
    'asserts.entity.expired_at.not_null' => 'Expired date {{ value }} could not be null',
    'asserts.entity.int.less_than' => 'This value should be less than {{ compared_value }}',
    'asserts.entity.range.between' => 'You must be between {{ min }} and {{ max }}',

    // Form change password
    'asserts.change_password.require' => 'Please enter a password',
    'asserts.change_password.min_length' => 'Your password should be at least {{ limit }} characters',
    'asserts.change_password.match' => 'The password fields must match',

    // General validators
    'asserts.unique.reference' => 'Reference {{ value }} already exist',

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
