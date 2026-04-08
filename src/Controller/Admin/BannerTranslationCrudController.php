<?php

namespace App\Controller\Admin;

use App\Entity\BannerTranslation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BannerTranslationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BannerTranslation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('banner', 'Banner');
        yield AssociationField::new('locale', 'Idioma');
        yield TextareaField::new('content', 'Contenido');
    }
}
