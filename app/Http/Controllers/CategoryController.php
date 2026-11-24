<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends NamedResourceController
{
    protected function modelClass(): string
    {
        return Category::class;
    }

    protected function resource(): string
    {
        return 'categories';
    }

    protected function label(): string
    {
        return 'Kategori';
    }
}
