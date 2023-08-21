<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use ErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/admin/product')]
class AdminProductController extends AbstractController
{
    #[Route('/', name: 'app_admin_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin_product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form->isSubmitted() && $form->isValid()) {

                $image = $form->get('img')->getData();

                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    $finalImage = $originalFilename . '.' . $image->guessExtension();

                    try {
                        $image->move(
                            $this->getParameter('image_directory'),
                            $finalImage
                        );
                    } catch (FileException $e) {
                        throw new ErrorException("un problème est survenue lors de l'upload de l'image");
                    }

                    $product->setImg($finalImage);
                }

                $entityManager->persist($product);
                $entityManager->flush();

                $this->addFlash('success', 'Votre produit a bien été ajouté');

                return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('admin_product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('admin_product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_product_delete', methods: ['POST'])]
    public function deleteProduct(ManagerRegistry $doctrine, Product $product): RedirectResponse
    {
        $entityManager = $doctrine->getManager();

        $reviews = $product->getReview();
        foreach ($reviews as $review) {
            $entityManager->remove($review);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit et ses avis ont bien été supprimés');

        return $this->redirectToRoute('app_admin_product_index');
    }
}
