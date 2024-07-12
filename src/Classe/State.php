<?php

namespace App\Classe;

class State{
    public const STATE = [ 
        3 => [
            'label' => 'En cours de préparation',
            'email_subject' => 'Votre commande est en cours de préparation',
            'email_template' => 'order_state_3.html',
        ],
        4 => [
            'label' => 'Expédiée',
            'email_subject' => 'Votre commande a bien été expédiée',
            'email_template' => 'order_state_4.html',
        ],
        5 => [
            'label' => 'Annulée',
            'email_subject' => 'Annulation de votre commande',
            'email_template' => 'order_state_5.html',
        ],
    ];
}