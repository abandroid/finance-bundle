<?php

namespace Endroid\FinanceBundle\Splitter;

class CategorySplitter
{
    public function split(TransactionCollection $transactions): array
    {
        $months = [];

        /** @var Transaction $transaction */
        foreach ($transactions->getIterator() as $transaction) {
            $month = $transaction->getDate()->format('Y-m');
            $months[$month][] = $transaction;
        }

        return $months;
    }
}