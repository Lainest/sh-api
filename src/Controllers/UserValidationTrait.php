<?php

trait UserValidationTrait
{

    private function validateUserLogin($data): array | false
    {
        $validation = Validation::getInstance();

        $validation->setData($data);

        $validation->field('user')->type('text')->pattern("/^[a-z0-9_-]{3,15}$/")->required();
        $validation->field('password')->type('text')->pattern("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/")->required();

        return $validation->hasErrors();
    }

    private function validateUserRegistration($data): array | false
    {
        $validation = Validation::getInstance();

        $validation->setData($data);

        $validation->field('user')->type('text')->pattern("/^[a-z0-9_-]{3,15}$/")->required();
        $validation->field('password')->type('text')->pattern("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/")->required();
        $validation->field('name')->type('text')->required();
        $validation->field('surname')->type('text')->required();
        $validation->field('province')->type('text')->required()->pattern("/BO|FE/");

        return $validation->hasErrors();
    }
}
