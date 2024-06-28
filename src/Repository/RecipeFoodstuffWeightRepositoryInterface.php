<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RecipeFoodstuffWeight;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

interface RecipeFoodstuffWeightRepositoryInterface extends ServiceEntityRepositoryInterface
{
    public function create(RecipeFoodstuffWeight $foodstuffWeight): RecipeFoodstuffWeight;

    public function update(): void;

    public function delete(RecipeFoodstuffWeight $foodstuffWeight): void;
}
