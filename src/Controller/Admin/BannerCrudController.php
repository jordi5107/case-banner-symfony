<?php

namespace App\Controller\Admin;

use App\Entity\Banner;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BannerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Banner::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('internalName', 'Nombre interno');
        yield ColorField::new('backgroundColor', 'Color de fondo');
        yield DateTimeField::new('startDate', 'Fecha inicio');
        yield DateTimeField::new('endDate', 'Fecha fin');
        yield BooleanField::new('active', 'Activo');
    }
}
