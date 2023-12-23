<?php

class UserController
{
    public function __construct(private UserGateway $gateway)
    {
    }

    public function getUsers()
    {

        $users = $this->gateway->getAll();

        new Response($users, 200);
    }

    public function getUser(string $id)
    {
        $user = $this->gateway->getById($id);

        new Response($user, 200);
    }
}
