<?php

return [

    /* ASSERTS VALIDATIONS */

    // General constraints
    'asserts.entity.unique' => 'Id and Uuid {{ value }} existent déjà',
    'asserts.entity.created_at.not_null' => 'La date de création {{ value }} ne peux pas être nulle',
    'asserts.entity.updated_at.not_null' => 'La date de mise à jour {{ value }} ne peux pas être nulle',
    'asserts.entity.name.not_null' => 'Le nom ne peut pas être nul',
    'asserts.entity.name.not_blank' => 'Le nom ne peut pas être vide',
    'asserts.entity.email.not_null' => 'L\'adresse email ne peut pas être vide',
    'asserts.entity.email.not_blank' => 'L\'adresse email ne peut pas être nulle',
    'asserts.entity.email.not_valid' => 'L\'adresse email {{ value }} n\'est pas valide',
    'asserts.entity.min_length' => '{{ value }} doit au moins contenir {{ limit }} caractères',
    'asserts.entity.max_length' => '{{ value }} ne peux pas avoir plus de {{ limit }} caractères',

    // Shop Entity
    'asserts.shop.unique' => 'L\'adresse email {{ value }} existe déjà',

    // Admin Entity
    'asserts.admin.unique' => 'L\'adresse email {{ value }} existe déjà',
    'asserts.admin.password.not_compromise' => 'Ce mot de passe est compromis, merci d\'en choisir un plus difficile',

    // Groups Entity
    'asserts.group.unique' => 'Le nom du groupe de rôles {{ value }} existe déjà',

];