<?php

namespace Endroid\FinanceBundle\Splitter;

use Endroid\FinanceBundle\Entity\Transaction;
use Endroid\FinanceBundle\Entity\TransactionCollection;

class CategorySplitter
{
    private $categories;

    public function __construct(array $categories)
    {
        $this->categories = $categories;
    }

    public function split(TransactionCollection $transactions): array
    {
        $categories = [];

        /** @var Transaction $transaction */
        foreach ($transactions->getIterator() as $transaction) {
            $category = $this->getCategory($transaction->getDescription());
            if (!isset($categories[$category])) {
                $categories[$category] = new TransactionCollection();
            }
            $categories[$category]->add($transaction);
        }

        return $categories;
    }

    private function getCategory(string $description): string
    {
        $description = trim(strtolower($description));

        foreach ($this->categories as $category) {
            foreach ($category['terms'] as $substring) {
                if (strpos($description, $substring) !== false) {
                    return $category['name'];
                }
            }
        }

        return '-';
    }
}