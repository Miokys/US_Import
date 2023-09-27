<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order_view')]
    public function showOrders(ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser(); // Obtenez l'utilisateur actuellement connecté
        $orders = $doctrine->getRepository(Order::class)->findBy(['user' => $user]);

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'app_order_detail')]
    public function showOrderDerail($id, ManagerRegistry $doctrine)
    {  
        $order = $doctrine->getRepository(Order::class)->find($id);

        if (!$order) {
            throw $this->createNotFoundException('Cette commande n\'existe pas.');
        }
        
        $products = $doctrine->getRepository(Product::class)->find($id);

        $items = $order->getProduct();

        return $this->render('order/show.html.twig', [
            'order' => $order,
            'products' => $products,
            'items' => $items
        ]);
    }
}
