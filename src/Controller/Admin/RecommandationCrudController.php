<?php

namespace App\Controller\Admin;

use App\Entity\Recommandation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RecommandationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recommandation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('receiverUser', 'Destinataire');

        yield TextEditorField::new('content', 'Message');

        yield AssociationField::new('senderUser', 'user')->hideOnForm();

        yield DateTimeField::new('timestamp')->hideOnForm();
    }
}
