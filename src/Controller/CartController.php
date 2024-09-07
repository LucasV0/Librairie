<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal(),
        ]);
    }

    #[Route('/cart/add/{id<\d+>}', name: 'app_cart_add')]
    public function addToCart(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/remove/{id<\d+>}', name: 'app_cart_remove')]
    public function removeToCart(CartService $cartService, int $id): Response
    {
        $cartService->removeToCart($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/increase/{id<\d+>}', name: 'app_cart_increase')]
    public function increaseQuantity(CartService $cartService, int $id): Response
    {
        $cartService->increaseQuantity($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/decrease/{id<\d+>}', name: 'app_cart_decrease')]
    public function decreaseQuantity(CartService $cartService, int $id): Response
    {
        $cartService->decreaseQuantity($id);
        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/removeAll', name: 'app_remove_all')]
    public function removeAll(CartService $cartService): Response
    {
        $cartService->removeCartAll();
        return $this->redirectToRoute('app_product_index');
    }
}
