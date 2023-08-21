<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, ManagerRegistry $doctrine): Response
    {
        $panier = $session->get('panier', []);

        $panierData = []; //créer un tableau vide pour stocker les données du panier.

        $totalquantity = 0;
        $totalprice = 0;

        foreach ($panier as $id => $quantity) { // boucle sur chaque produit dans le panier.
            $panierData[] = [
                'product' => $doctrine->getRepository(Product::class)->find($id), // récupérer les données du produit dans la BDD lié à l'id.
                'quantity' => $quantity
            ];

            $totalquantity += $quantity; // ajouter la quantité du produit dans le total.
        }

        foreach ($panierData as $id => $value) // ajouter le prix du produit dans le total.
        {
            $totalprice += $value['product']->getPrice() * $value['quantity'];
        }


        return $this->render('cart/index.html.twig', [
            'items' => $panierData,
            'totalquantity' => $totalquantity,
            'totalprice' => $totalprice
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add($id, SessionInterface $session, Product $product)
    {
        $panier = $session->get('panier', []); // récupérer le panier ou le créer.

        if (!empty($panier[$id])) { // si le produit est déjà dans le panier, incrémenter la quantité, sinon ajouter le produit à la liste.
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier); // enregistrer le nouveau panier dans la session.

        $this->addFlash('success', 'le produit a bien été ajouté à votre panier');

        return $this->redirectToRoute('app_product_show', [
            'id' => $product->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

    #[Route('/cart/addcart/{id}', name: 'app_cart_increment')]
    public function cartincrement($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []); // récupérer le panier ou le créer.

        if (!empty($panier[$id])) { // si le produit est déjà dans le panier, incrémenter la quantité, sinon ajouter le produit à la liste.
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier); // enregistrer le nouveau panier dans la session.

        $this->addFlash('success', 'le produit a bien été ajouté à votre panier');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{id}', name: 'app_cart_delete')]
    public function delete($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []); // récupérer le panier ou le créer.
        unset($panier[$id]); // supprimer le produit du panier.
        $session->set('panier', $panier); // enregistrer le nouveau panier dans la session.

        $this->addFlash('success', 'le produit a bien été supprimé de votre panier');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrement/{id}', name: 'app_cart_decrement')]
    public function cartdecrement($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {  // si le produit est déjà dans le panier, décrémenter la quantité.
            $panier[$id]--;

            if ($panier[$id] <= 0) {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier); // enregistrer le nouveau panier dans la session.

        $this->addFlash('success', 'le produit a bien été supprimé de votre panier');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clear(SessionInterface $session)
    {
        $session->remove('panier');

        $this->addFlash('success', 'le panier a été supprimé');

        return $this->redirectToRoute('app_cart');
    }
}
