<?php

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