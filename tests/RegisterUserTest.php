<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    


    public function testSomething(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/inscription');

        // Check if the request was successful
        $this->assertTrue($client->getResponse()->isSuccessful());

        // Perform assertions on the page content
        $this->assertGreaterThan(0, $crawler->filter('form')->count(), 'The form is not present on the page.');
        $this->assertGreaterThan(0, $crawler->filter('button:contains("Valider")')->count(), 'The button "Valider" is not present on the page.');

        // Select and fill the form
        $form = $crawler->selectButton('Valider')->form();

        $client->submit($form, [
            "register_user[email]" => "julie@example.fr",
            "register_user[plainPassword][first]" => "1231456",
            "register_user[plainPassword][second]" => "1231456",
            "register_user[firstname]" => "Julie",
            "register_user[lastname]" => "Doe"
        ]);

        // Test the redirection route
        $this->assertResponseRedirects('/connexion');

        // Follow the redirection
        $client->followRedirect();

        // Check the success message
        $this->assertSelectorExists('div:contains("Votre compte est correctement créé, veuillez vous connecter !")');
    }
}
