/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '@@css/admin/admin.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

import EaShopField from "@@js/admin/fields/shop";

window.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('[data-ea-collection-field]').forEach((collection) => {

        // Shop hour break enable/disable
        if (collection.querySelector('.form-widget .form-widget-compound > div').id === 'Shop_shop_info_shop_hour') {
            EaShopField.handleShopHour();
        }

    });
});