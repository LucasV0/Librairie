<?php

namespace App\tests\Unit;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function testSomething(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        //création d'un utilisateur pour le tester
        $user = new User();
        $user -> setEmail("test@gmail.com")
              -> setPassword("qh!6[*T448thUAqhqh!6[*T448thUAqh!6[*T448thUAthUA")
              -> setFirtname("prenom")
              -> setName("nom")
              -> setDateOfBirth(new \DateTime("2001-06-06"))  
              -> setAddress("adresse")
              -> setPhone("0676565445");

        // Valide l'entité User en utilisant le service de validation de Symfony
        $errors = $container->get('validator')->validate($user);

        // Vérifie qu'il n'y a pas d'erreurs de validation
        $this->assertCount(0,$errors);
              
    }
}
