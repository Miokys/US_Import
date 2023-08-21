<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Form\Review1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/article/{id}/review')]
class ReviewController extends AbstractController
{
    #[Route('/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Product $product): Response
    {
        $review = new Review();

        $review->setCreatedAt(new \DateTime('now'));
        $review->setUser($this->getUser());
        $review->setProduct($product);
        $form = $this->createForm(Review1Type::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_show', [
                'id' => $product->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form,
        ]);
    }
}
