<?php

namespace App\Model\Customer;

class UserModel
{
    /**
     * email
     *
     * @var mixed
     */
    private $email;

    /**
     * firstname
     *
     * @var mixed
     */
    private $firstname;

    /**
     * lastname
     *
     * @var mixed
     */
    private $lastname;

    /**
     * birthday
     *
     * @var mixed
     */
    private $birthday;

    /**
     * gender
     *
     * @var mixed
     */
    private $gender;

    /**
     * phone
     *
     * @var mixed
     */
    private $phone;

    /**
     * getEmail
     *
     * @return void
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * setEmail
     *
     * @param  mixed $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * getFirstname
     *
     * @return void
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * setFirstname
     *
     * @param  mixed $firstname
     * @return void
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * getLastname
     *
     * @return void
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * setLastname
     *
     * @param  mixed $lastname
     * @return void
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * getBirthday
     *
     * @return void
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * setBirthday
     *
     * @param  mixed $birthday
     * @return void
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * getGender
     *
     * @return void
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * setGender
     *
     * @param  mixed $gender
     * @return void
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * getPhone
     *
     * @return void
     */
    /**
     * getPhone
     *
     * @return void
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * setPhone
     *
     * @param  mixed $phone
     * @return void
     */
    /**
     * setPhone
     *
     * @param  mixed $phone
     * @return void
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }
}
