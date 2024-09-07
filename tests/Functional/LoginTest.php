<?php

namespace App\tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

//vérifie le processus de connexion d'un utilisateur
class LoginTest extends WebTestCase
{
    public function testLoginSuccess(): void
    {
         // Crée un client HTTP pour simuler des requêtes HTTP
        $client = static::createClient();


        $urlGenerator = $client->getContainer()->get("router");

          // Effectue une requête GET à la route de connexion
        $crawler = $client->request('GET', $urlGenerator->generate('app_login'));

        // Filtre le formulaire de connexion par son nom et le remplit avec les données de connexion
        $form = $crawler->filter("form[name=login]")->form([
            "email" => "test@gmail.com",
            "password" => "qh!6[*T448thUAqhqh!6[*T448thUAqh!6[*T448thUAthUA"
        ]);

         // Soumet le formulaire de connexion
        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

       

    }
}
