<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\FinanceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use setasign\Fpdi\PdfParser\PdfParser;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfReader\PdfReader;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * @Route("/")
 */
class DashboardController
{
    private $renderer;
    private $categories;
    private $transactions;
    private $totals;

    public function __construct(Environment $renderer)
    {
        $this->renderer = $renderer;

        $this->totals = [];
        $this->categories = [];
    }

    /**
     * @Route("/")
     */
    public function __invoke(Request $request): Response
    {
        if ($request->files->has('file')) {
            $this->loadCategories();
            $this->loadTransactions($request->files->get('file'));
        }

        return new Response($this->renderer->render('@EndroidPlay/budget/dashboard.html.twig', [
            'transactions' => $this->transactions,
            'totals' => $this->totals,
        ]));
    }

    private function loadCategories(): void
    {
        $categoryData = Yaml::parseFile(__DIR__.'/../../Resources/data/budget/categories.yaml');
        foreach ($categoryData['categories'] as $category) {
            $this->categories[$category] = [];
        }
    }

    private function loadTransactions(UploadedFile $file): void
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getPathname());
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
                $this->addTransaction($date, $parts[0], (float) str_replace([' ', '.', ','], ['', '', '.'], $parts[1]), $parts[2]);
            }
        }
    }

    private function addTransaction(\DateTime $date, string $type, float $amount, string $description): void
    {
        $category = $this->getCategory($description);
        $month = $date->format('Y-m');

        if (!isset($this->categories[$month])) {
            $this->categories[$month] = [];
            if (!isset($this->totals[$month])) {
                $this->totals[$month] = [];
            }
        }

        $this->transactions[$month][$category][] = [
            'date' => $date,
            'type' => $type,
            'amount' => $amount,
            'description' => $description,
        ];

        $this->totals[$month][$category] += $amount;
    }



    private function getCategory(string $description): string
    {
        $description = trim($description);

        foreach ($this->categories as $categoryLabel => $category) {
            foreach ($category as $substring) {
                if (strpos($description, $substring) !== false) {
                    return $categoryLabel;
                }
            }
        }

        return 'other';
    }
}
