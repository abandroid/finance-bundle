<?php

namespace Endroid\FinanceBundle\Splitter;

use Endroid\FinanceBundle\Entity\Transaction;
use Endroid\FinanceBundle\Entity\TransactionCollection;

class MonthSplitter
{
    public function split(TransactionCollection $transactions): array
    {
        $months = [];

        /** @var Transaction $transaction */
        foreach ($transactions->getIterator() as $transaction) {
            $month = $transaction->getDate()->format('Y-m');
            if (!isset($months[$month])) {
                $months[$month] = new TransactionCollection();
            }
            $months[$month]->add($transaction);
        }

        return $months;
    }
}