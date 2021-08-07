// Default shop js file
import '@@css/admin/crud/shop.scss';
import EaShopField from "@@js/admin/crud/fields/shop_hour";

window.addEventListener('DOMContentLoaded', (event) => {
    document.querySelectorAll('[data-ea-collection-field]').forEach((collection) => {

        // Shop hour break enable/disable
        // @todo : better system tp resolve error id null
        if (null !== collection.querySelector('.form-widget .form-widget-compound > div')) {
            if (collection.querySelector('.form-widget .form-widget-compound > div').id === 'Shop_shop_info_shop_hour') {
                EaShopField.handleShopHour();
            }
        }
    });
});
