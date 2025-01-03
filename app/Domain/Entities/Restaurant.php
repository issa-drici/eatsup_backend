<?php

namespace App\Domain\Entities;

class Restaurant
{
    private string $id;
    private string $ownerId;
    private string $name;
    private ?string $address;
    private ?string $phone;

    public function __construct(string $id, string $ownerId, string $name, ?string $address = null, ?string $phone = null)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->name = $name;
        $this->address = $address;
        $this->phone = $phone;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
}
