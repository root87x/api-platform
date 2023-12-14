<?php

namespace App\Entity\Interfaces;

use App\Entity\User;

interface EntityUserInterface
{
    public function setUser(?User $user): self;

    public function getUser(): ?User;
}