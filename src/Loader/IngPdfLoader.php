<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\FinanceBundle\Loader;

use Endroid\FinanceBundle\Entity\Transaction;
use Endroid\FinanceBundle\Entity\TransactionCollection;
use Smalot\PdfParser\Parser;

class IngPdfLoader
{
    public function load(string $path)
    {
        $transactions = new TransactionCollection();

        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();
        $lines = explode("\n", $text);

        while (count($lines) > 0) {
            $line = array_pop($lines);
            $date = \DateTime::createFromFormat('d-m-Y', trim($line));
            if ($date instanceof \DateTime) {
                do {
                    $description = array_pop($lines);
                    $parts = explode("\t", $description);
                } while (count($parts) !== 3 && count($lines) > 0);
                $transaction = new Transaction($date, $parts[2], (float) str_replace([' ', '.', ','], ['', '', '.'], $parts[1]));
                $transactions->add($transaction);
            }
        }

        return $transactions;
    }
}