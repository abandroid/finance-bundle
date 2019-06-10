<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\FinanceBundle\Entity;

class TransactionCollection
{
    private $transactions;

    public function __construct()
    {
        $this->transactions = [];
    }

    public function add(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function getIterator(): \Iterator
    {
        foreach ($this->transactions as $transaction) {
            yield $transaction;
        }
    }
}