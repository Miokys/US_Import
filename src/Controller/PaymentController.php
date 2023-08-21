<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'app_payment')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }

    #[Route('/checkout', name: 'app_payment_checkout')]
    public function checkout($stripeSK, SessionInterface $session_panier, ManagerRegistry $doctrine): Response
    {
        Stripe::setApiKey($stripeSK);

        $panier = $session_panier->get('panier', []);

        $panier_data = [];
        foreach ($panier as $id => $quantity) {
            $panier_data[] = [
                "product" => $doctrine->getRepository(Product::class)->find($id),
                "quantity" => $quantity
            ];
        }


        foreach ($panier_data as $id => $value) {
            $line_items[] = [
                "price_data" => [
                    "currency" => "eur",
                    "product_data" => [
                        'name' => $value['product']->getModel(),
                    ],
                    "unit_amount" => $value['product']->getPrice() * 100,
                ],
                "quantity" => $value['quantity']
            ];
        }

        $session = Session::create([
            'line_items' => [$line_items],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_payment_checkout_success', [
                'panier_data' => $panier_data
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_payment_checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }

    #[Route('/checkout/success', name: 'app_payment_checkout_success')]
    public function checkoutSuccess(SessionInterface $session)
    {
        $panier_data = $session->get('panier_data', []);
        $session->remove('panier_data');
        $session->remove('panier');

        return $this->render('payment/success.html.twig', [
            'controller_name' => 'PaymentController',
            'panier_data' => $panier_data
        ]);
    }

    #[Route('/checkout/cancel', name: 'app_payment_checkout_cancel')]
    public function checkoutCancel()
    {
        return $this->render('payment/cancel.html.twig');
    }
}
