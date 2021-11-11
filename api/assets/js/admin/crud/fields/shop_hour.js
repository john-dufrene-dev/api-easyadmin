// js for shop field
const EaShopField = {
    handleShopHour: () => {
        const selectors = document.getElementsByClassName('target-click-shop');
        if (selectors.length > 0) {
            Array.from(selectors).forEach((selector, i) => {
                const start = document.getElementById(`Shop_shop_info_shop_hour_${i}_startBreakTime`)
                const end = document.getElementById(`Shop_shop_info_shop_hour_${i}_endBreakTime`)
                if (selector.checked) {
                    start.style.display = '';
                    end.style.display = '';
                }
                selector.addEventListener('click', (e) => {
                    (start.style.display == '' || start.style.display == 'block')
                        ? start.style.display = 'none'
                        : start.style.display = '';

                    (end.style.display == '' || end.style.display == 'block')
                        ? end.style.display = 'none'
                        : end.style.display = '';
                });
            });
        }
    }
};

export default EaShopField
