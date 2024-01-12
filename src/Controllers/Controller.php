<?php

interface Controller
{
    /**
     * Responds with all resources
     */
    public function getAll(): void;

    /**
     * Responds with one resource
     */
    public function getOne(string $id): void;

    /**
     * Creates one resource
     */
    public function create(): void;
}
