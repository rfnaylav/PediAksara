<?php

namespace App\Http\Controllers;

use App\Models\Author;

class AuthorController extends NamedResourceController
{
    protected function modelClass(): string
    {
        return Author::class;
    }

    protected function resource(): string
    {
        return 'authors';
    }

    protected function label(): string
    {
        return 'Penulis';
    }
}
