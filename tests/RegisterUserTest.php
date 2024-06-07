<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {
        // Créer un faux client (navigateur) de pointer vers une URL
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        // Remplir les champs de mon formulaire d'inscription
        $client->submitForm(
            'Valider',
            [
                "register_user[email]" => "julie@example.fr",
                "register_user[plainPassword][first]" => "1231456",
                "register_user[plainPassword][second]" => "1231456",
                "register_user[firstname]" => "Julie",
                "register_user[lastname]" => "Doe"
            ]
        );

        // tester la route de redirection  ...
        $this->assertResponseRedirects('/connexion');
        // Suivre la redirection ...
        $client->followRedirect();

        // Vérifier que j'ai le message alerte "Votre compte est correctement créé, veuillez vous connecter !"
        $this->assertSelectorExists('div:contains("Votre compte est correctement créé, veuillez vous connecter !")');
    }
}
