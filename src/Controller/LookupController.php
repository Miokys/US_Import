<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LookupController extends AbstractController
{
    #[Route('/lookup', name: 'app_lookup')]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $searchTerm = $request->query->get('search');
        $products = $productRepository->findBySearch($searchTerm);

        return $this->render('lookup/index.html.twig', [
            'controller_name' => 'LookupController',
            'products' => $products,
            'searchTerm' => $searchTerm, // Optional: Pass the search term to the view if you want to display it.
        ]);
    }
}