<?php

namespace App\Http\Controllers;

use App\Models\Publisher;

class PublisherController extends NamedResourceController
{
    protected function modelClass(): string
    {
        return Publisher::class;
    }

    protected function resource(): string
    {
        return 'publishers';
    }

    protected function label(): string
    {
        return 'Penerbit';
    }
}
