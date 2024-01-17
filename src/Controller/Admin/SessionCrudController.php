<?php

namespace App\Controller\Admin;

use App\Entity\Session;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SessionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Session::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('ownerUser', 'user')->hideOnForm();

        yield TextField::new('subject', 'Titre');

        yield TextareaField::new('description', 'Description');

        yield ChoiceField::new('type', 'type')->setChoices([
            'En ligne' => 'online',
            'Sur site' => 'onSite',
        ]);

        yield DateTimeField::new('startTime', 'Début de la session');

        yield DateTimeField::new('endTime', 'Fin de la session');

        yield AssociationField::new('participantUser', 'participants');
    }
}
