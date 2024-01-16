<?php

interface Gateway
{
    /**
     * Retrieves all resource from database
     */
    public function getAll(): array;

    /**
     * Retrieves one resource from database given the ID
     */
    public function getById(string $id): array | false;

    /**
     * Creates a resource given the data
     */
    public function create(array $data): string;
}
