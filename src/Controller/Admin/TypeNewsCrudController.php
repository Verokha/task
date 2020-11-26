<?php

namespace App\Controller\Admin;

use App\Entity\TypeNews;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TypeNewsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TypeNews::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
