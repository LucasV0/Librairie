<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\RecapDetails;
use App\Form\OrderType;
use App\Service\CartService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    //créer une nouvelle commande.
    #[Route('/order/create', name: 'order_index')]
    public function index(CartService $cartService): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        //Affichage du formulaire de commande 
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $recapCart = $cartService->getTotal();

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'recapCart' => $recapCart,
        ]);
    }

    //méthode prépare et valide la commande après soumission du formulaire.
    #[Route('/order/verify', name: 'order_prepare', methods: ['POST'])]
    public function prepareOrder(CartService $cartService, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        //Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $datetime = new DateTime('now');
            $transporter = $form->get('transporter')->getData();
            $delivery = $form->get('user')->getData();
            $deliveryForOrder = $delivery->getName();
            $deliveryForOrder .= '<br>' . $delivery->getPhone();
            $deliveryForOrder .= '<br>' . $delivery->getAddress();
            
            //Création de la commande
            $order = new Order();
            $reference = $datetime->format('dmY') . '-' . uniqid();
            $order->setUser($this->getUser());
            $order->setReference($reference);
            $order->setOrderDate($datetime);
            $order->setDelivery($deliveryForOrder);
            $order->setTransporterName($transporter->getTitle());
            $order->setTransporterPrice($transporter->getPrice());
            $order->setIsPaid(0);


            $this->em->persist($order);

            //Persistance des détails de la commande
            foreach ($cartService->getTotal() as $product) {
                $recapDetails = new RecapDetails();
                $recapDetails->setOrderProduct($order);
                $recapDetails->setQuantity($product['quantity']);
                $recapDetails->setPrice($product['product']->getPrice());
                $recapDetails->setProduct($product['product']->getTitle());
                $recapDetails->setTotalRecap($product['product']->getPrice() * $product['quantity']);

                $this->em->persist($recapDetails);
            }

            $this->em->flush();

            //Affichage du récapitulatif 
           return $this->render('order/recap.html.twig', [
    'recapCart' => $cartService->getTotal(),
    'transporter' => $transporter,
    'delivery' => $deliveryForOrder,
    'reference' => $order->getReference(),
]);

        }

        return $this->redirectToRoute('cart_index');
    }
}
