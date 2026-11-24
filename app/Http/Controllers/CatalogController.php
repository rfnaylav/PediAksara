<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'author_id' => ['nullable', 'integer', 'exists:authors,id'],
            'available' => ['nullable', 'boolean'],
        ]);

        $query = Book::query()
            ->with(['category', 'author', 'publisher'])
            ->when($filters['q'] ?? null, function ($query, $search): void {
                $term = '%'.trim($search).'%';
                $query->where(function ($query) use ($term): void {
                    $query->where('title', 'like', $term)
                        ->orWhere('isbn', 'like', $term)
                        ->orWhereHas('author', fn ($authors) => $authors->where('name', 'like', $term))
                        ->orWhereHas('category', fn ($categories) => $categories->where('name', 'like', $term))
                        ->orWhereHas('publisher', fn ($publishers) => $publishers->where('name', 'like', $term));
                });
            })
            ->when($filters['category_id'] ?? null, fn ($books, $categoryId) => $books->where('category_id', $categoryId))
            ->when($filters['author_id'] ?? null, fn ($books, $authorId) => $books->where('author_id', $authorId))
            ->when($filters['available'] ?? false, fn ($books) => $books->where('stock', '>', 0))
            ->orderBy('title');

        return view('catalog.index', [
            'books' => $query->paginate(12)->withQueryString(),
            'categories' => Category::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
        ]);
    }

    public function show(Book $book): View
    {
        return view('catalog.show', [
            'book' => $book->load(['category', 'author', 'publisher']),
        ]);
    }
}
