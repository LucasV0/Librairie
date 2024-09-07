<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Service\CartService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $generator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $generator)
    {
        $this->em = $em;
        $this->generator = $generator;
    }

    #[Route('/order/create-session-stripe/{reference}', name: 'payment_stripe', methods:['POST'])]
public function index($reference, CartService $cartService): RedirectResponse
{
    $productStripe = [];
     // Récupère la commande par sa référence
    $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);
    if (!$order) {
        return $this->redirectToRoute('cart_index');
    }

    // Crée les items pour Stripe à partir des produits de la commande
    foreach ($order->getRecapDetails()->getValues() as $product) {
        $productData = $this->em->getRepository(Product::class)->findOneBy(['title'=> $product->getProduct()]);

        $productStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($productData->getPrice() * 100, 0, '', ''),
                'product_data' => [
                    'name' => $productData->getTitle(),
                ],
            ],
            'quantity' => $product->getQuantity(),
        ];
    }

 // Ajoute les frais de livraison s'ils existent
    $shippingCost = $order->getTransporterPrice(); 
    if ($shippingCost > 0) {
        $productStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($shippingCost * 100, 0, '', ''), 
                'product_data' => [
                    'name' => 'Frais de livraison',
                ],
            ],
            'quantity' => 1,
        ];
    }



    
        Stripe::setApiKey('sk_test_51PSHxZ2LCMUzHRLCusN7LcvXviTWITjcSc6AMlDetyjKR3us3btymr33h2S3qsgUH6viEA1S1Q6haI32TOxf7HsQ00EoQBpavG'); 

        $checkout_session = Session::create([
            // Crée une session de paiement Stripe
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [$productStripe],
            'mode' => 'payment',
            'success_url' => $this->generator->generate('payment_success', [
                'reference' => $order->getReference(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generator->generate('payment_cancel', [
                'reference' => $order->getReference(),
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        

        $order->setStripeSessionId($checkout_session->id);
        $this->em->flush();
        

        return new RedirectResponse($checkout_session->url);
    }

    #[Route('/order/success/{reference}', name: 'payment_success')]
    public function stripeSuccess($reference, CartService $cartService): Response
    {
        $cartService->removeCartAll();

        return $this->render('order/success.html.twig');
    }

    #[Route('/order/cancel/{reference}', name: 'payment_cancel')]
    public function stripeCancel($reference, CartService $cartService): Response
    {
        return $this->render('order/cancel.html.twig');
    }
}
