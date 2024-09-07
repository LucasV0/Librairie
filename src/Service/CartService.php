<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService 
{
    private RequestStack $requestStack;
    private EntityManagerInterface $em;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $em) 
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    // Ajoute un produit au panier en fonction de son identifiant
    public function addToCart(int $id): void 
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        // Si le produit est déjà dans le panier, augmente la quantité
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

      // Augmente la quantité d'un produit spécifique dans le panier
    public function increaseQuantity(int $id): void 
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
         // Si le produit est dans le panier, augmente la quantité
        if (isset($cart[$id])) {
            $cart[$id]++;
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

    // Diminue la quantité d'un produit dans le panier ou le retire si la quantité atteint 0
    public function decreaseQuantity(int $id): void 
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
         // Si le produit est dans le panier et que la quantité est supérieure à 1, diminue la quantité
        if (isset($cart[$id]) && $cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]); 
        }
        $this->requestStack->getSession()->set('cart', $cart);
    }

    // Retire un produit spécifique du panier
    public function removeToCart(int $id): void 
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        unset($cart[$id]);
        $this->requestStack->getSession()->set('cart', $cart);
    }

    public function removeCartAll(): void
    {
        $this->requestStack->getSession()->remove('cart');
    }

    public function getTotal(): array 
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $cartData = [];
        
        foreach ($cart as $id => $quantity) {
            $product = $this->em->getRepository(Product::class)->find($id);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }
        
        return $cartData;
    }
}
