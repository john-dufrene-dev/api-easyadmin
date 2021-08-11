<?php

namespace App\Model\Customer;

class UserPasswordModel
{    
    /**
     * password
     *
     * @var mixed
     */
    private $password;
    
    /**
     * plainPassword
     *
     * @var mixed
     */
    private $plainPassword;
    
    /**
     * getPassword
     *
     * @return void
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * setPassword
     *
     * @param  mixed $password
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    
    /**
     * getPlainPassword
     *
     * @return void
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
        
    /**
     * setPlainPassword
     *
     * @param  mixed $plainPassword
     * @return void
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }
}
