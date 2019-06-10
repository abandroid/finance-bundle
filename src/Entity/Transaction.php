<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\FinanceBundle\Entity;

class Transaction
{
    private $date;
    private $description;
    private $amount;

    public function __construct(\DateTime $date, string $description, float $amount)
    {
        $this->date = $date;
        $this->description = $description;
        $this->amount = $amount;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}