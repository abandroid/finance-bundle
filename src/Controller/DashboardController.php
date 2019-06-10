<?php

declare(strict_types=1);

/*
 * (c) Jeroen van den Enden <info@endroid.nl>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Endroid\FinanceBundle\Controller;

use Endroid\FinanceBundle\Loader\IngPdfLoader;
use Endroid\FinanceBundle\Splitter\CategorySplitter;
use Endroid\FinanceBundle\Splitter\MonthSplitter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class DashboardController
{
    private $loader;
    private $categorySplitter;
    private $monthSplitter;
    private $renderer;

    public function __construct(
        IngPdfLoader $loader,
        CategorySplitter $categorySplitter,
        MonthSplitter $monthSplitter,
        Environment $renderer
    ) {
        $this->loader = $loader;
        $this->categorySplitter = $categorySplitter;
        $this->monthSplitter = $monthSplitter;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request): Response
    {
        $transactions = [];

        if ($request->files->has('file')) {
            $transactions = $this->loader->load($request->files->get('file')->getPathname());
            $transactions = $this->categorySplitter->split($transactions);
            foreach ($transactions as $categoryLabel => &$categoryTransactions) {
                $categoryTransactions = $this->monthSplitter->split($categoryTransactions);
            }
        }

        return new Response($this->renderer->render('@EndroidFinance/dashboard.html.twig', [
            'transactions' => $transactions,
        ]));
    }





}
