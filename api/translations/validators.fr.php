<?php

return [

    /* ASSERTS VALIDATIONS */

    // General constraints
    'asserts.entity.unique' => 'Id and Uuid {{ value }} existent déjà',
    'asserts.entity.created_at.not_null' => 'La date de création {{ value }} ne peux pas être nulle',
    'asserts.entity.updated_at.not_null' => 'La date de mise à jour {{ value }} ne peux pas être nulle',
    'asserts.entity.generic.not_null' => 'La valeur ne peut pas être nulle',
    'asserts.entity.generic.not_blank' => 'La valeur ne peut pas être vide',
    'asserts.entity.name.not_null' => 'Le nom ne peut pas être nul',
    'asserts.entity.name.not_blank' => 'Le nom ne peut pas être vide',
    'asserts.entity.ulid.not_null' => 'La reference ne peut pas être nulle',
    'asserts.entity.ulid.not_blank' => 'La reference ne peut pas être vide',
    'asserts.entity.email.not_null' => 'L\'adresse email ne peut pas être vide',
    'asserts.entity.email.not_blank' => 'L\'adresse email ne peut pas être nulle',
    'asserts.entity.email.not_valid' => 'L\'adresse email {{ value }} n\'est pas valide',
    'asserts.entity.min_length' => '{{ value }} doit au moins contenir {{ limit }} caractères',
    'asserts.entity.max_length' => '{{ value }} ne peux pas avoir plus de {{ limit }} caractères',
    'asserts.entity.country' => 'Cette valeur n\'est pas un pays valide',
    'asserts.entity.bool' => 'Cette valeur {{ value }} n\'est pas valide',
    'asserts.entity.file.not_null' => 'Une image est requise',
    'asserts.entity.int.not_valid' => 'La valeur {{ value }} n\'est pas un {{ type }} valide',
    'asserts.entity.phone' => 'La valeur {{ value }} n\'est pas un format valide',
    'asserts.entity.postal_code' => 'La valeur {{ value }} n\'est pas un code postal valide',
    'asserts.entity.range_localization' => 'La valeur doit être comprise entre {{ min }} et {{ max }}',
    'asserts.entity.gender' => 'Le genre n\'existe pas',
    'asserts.entity.valid.type' => 'La valeur {{ value }} ,\'est pas un type {{ type }}',
    'asserts.entity.secret.not_null' => 'La clé secrète ne peut pas être nulle',
    'asserts.entity.token.not_null' => 'La clé token ne peut pas être nulle',
    'asserts.entity.requested_at.not_null' => 'La date de requête {{ value }} ne peux pas être nulle',
    'asserts.entity.expired_at.not_null' => 'La date d\'expiration {{ value }} ne peux pas être nulle',
    'asserts.entity.int.less_than' => 'Cette valeur doit être inférieur à {{ compared_value }}',
    'asserts.entity.range.between' => 'La valeur doit être comprise entre {{ min }} et {{ max }}',

    // Form change password
    'asserts.change_password.require' => 'Veuillez entrer un mot de passe',
    'asserts.change_password.min_length' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
    'asserts.change_password.match' => 'Les champs du mot de passe doivent correspondre',

    // General validators
    'asserts.unique.reference' => 'La référence {{ value }} existe déjà',

    // Shop Entity
    'asserts.shop.unique' => 'L\'adresse email {{ value }} existe déjà',

    // User Entity
    'asserts.user.unique' => 'L\'adresse email {{ value }} existe déjà',
    'asserts.user.password.not_compromise' => 'Ce mot de passe est compromis, merci d\'en choisir un plus difficile',

    // Admin Entity
    'asserts.admin.unique' => 'L\'adresse email {{ value }} existe déjà',
    'asserts.admin.password.not_compromise' => 'Ce mot de passe est compromis, merci d\'en choisir un plus difficile',

    // Groups Entity
    'asserts.group.unique' => 'Le nom du groupe de rôles {{ value }} existe déjà',

];
