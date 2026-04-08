<?php

namespace App\Controller\Admin;

use App\Entity\Locale;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LocaleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Locale::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('code', 'Código (ej: es, en, fr)');
        yield BooleanField::new('active', 'Activo');
    }
}
