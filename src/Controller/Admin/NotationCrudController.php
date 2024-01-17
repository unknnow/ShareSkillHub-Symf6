<?php

namespace App\Controller\Admin;

use App\Entity\Notation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NotationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Notation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield AssociationField::new('session', 'Session');

        yield NumberField::new('note', 'Note');

        yield TextEditorField::new('comment', 'Commentaire');

        yield AssociationField::new('ownerUser', 'user')->hideOnForm();

        yield DateTimeField::new('timestamp')->hideOnForm();
    }
}
