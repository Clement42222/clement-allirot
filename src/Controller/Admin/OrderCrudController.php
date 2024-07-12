<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Classe\State;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;

class OrderCrudController extends AbstractCrudController
{
    private $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $show = Action::new('Afficher')->linkToCrudAction('show'); // permet d'afficher un btn 'Afficher' et de rediriger vers fonction 'show'

        return $actions
            ->add(Crud::PAGE_INDEX, $show)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    public function changeState($order, $state)
    {
        $stateInfos = State::STATE[$state];

        // Modifier le state de notre order
        $order->setState($state);
        $this->entityManagerInterface->flush();

        // Message de succès
        $this->addFlash(
            'success',
            'Statut de la commande modifié avec succès !'
        );
        
        // Obtenir l'email de l'utilisateur
        $emailTo = $order->getUser()->getEmail();

        // Envoie email avec classe Mail
        $mail = new Mail();
        $mail->send(
            $emailTo,
            $order->getUser()->getFirstname() . ' ' . $order->getUser()->getLastname(),
            $stateInfos['email_subject'],
            $stateInfos['email_template'],
            [
                'firstname' => $order->getUser()->getFirstname(),
                'order_id' => $order->getId()
            ]
        );
    }

    public function show(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, Request $request)
    {
        $order = $context->getEntity()->getInstance(); // récupère l'entité order concerné

        // Récupérer l'url de notre action "show"
        $url = $adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('show')
            ->setEntityId($order->getId())
            ->generateUrl();

        // Récupérer le state de request
        $state = $request->query->get('state');

        // Si le state est défini
        if ($state) {
            // Modifier le state de notre order avec changeState
            $this->changeState($order, $state);
        }

        return $this->render('admin/order.html.twig', [
            'order' => $order,
            'currentUrl' => $url,
        ]);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande') //nom titre crud en haut page au singulier
            ->setEntityLabelInPlural('Commandes') //nom titre crud en haut page au pluriel
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('createdAt')
                ->setLabel('Date'),
            NumberField::new('state')
                ->setLabel('Statut')
                ->setTemplatePath('admin/state.html.twig'),
            AssociationField::new('user')
                ->setLabel('Utilisateur'),
            TextField::new('carrierName')
                ->setLabel('Transporteur'),
            TextField::new('totalTva')->setLabel('Total TVA'),
            TextField::new('totalWt')->setLabel('Total TTC'),
        ];
    }
}
