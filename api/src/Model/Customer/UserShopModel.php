<?php

namespace App\Model\Customer;

class UserShopModel
{
    /**
     * shop
     *
     * @var mixed
     */
    private $shop;

    /**
     * getShop
     *
     * @return void
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * setShop
     *
     * @param  mixed $shop
     * @return void
     */
    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }
}
