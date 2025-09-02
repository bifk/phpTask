<?php

class User {
    public $id;
    public $username;
    public $phone;
    public $email;


    public function __construct($id, $username, $phone, $email) {
        $this->id = $id;
        $this->username = $username;
        $this->phone = $phone;
        $this->email = $email;
    }
}
