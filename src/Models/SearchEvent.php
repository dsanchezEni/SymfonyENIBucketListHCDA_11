<?php

namespace App\Models;

use Symfony\Component\Validator\Constraints as Assert;

class SearchEvent
{
    #[Assert\NotBlank()]
    public \DateTimeImmutable $dateEvent;
    #[Assert\NotBlank()]
    #[Assert\Length(max: 255)]
    public string $city;
}