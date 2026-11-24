<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class BookController extends Controller
{
    public function index(): View
    {
        return view('admin.books.index', [
            'books' => Book::query()->with(['category', 'author', 'publisher'])->orderBy('title')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return $this->form(new Book());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $cover = $data['cover'] ?? null;
        unset($data['cover']);

        if ($cover) {
            $path = $cover->store('book-covers', 'public');
            if (! is_string($path)) {
                throw new RuntimeException('Sampul buku gagal disimpan.');
            }
            $data['cover_image'] = $path;
        }

        Book::create($data);

        return redirect()->route('books.index')->with('status', 'Buku berhasil ditambahkan.');
    }

    public function edit(Book $book): View
    {
        return $this->form($book);
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validated($request, $book);
        $cover = $data['cover'] ?? null;
        unset($data['cover']);
        $oldCover = $book->cover_image;

        if ($cover) {
            $path = $cover->store('book-covers', 'public');
            if (! is_string($path)) {
                throw new RuntimeException('Sampul buku gagal disimpan.');
            }
            $data['cover_image'] = $path;
        }

        $book->update($data);

        if ($cover && $oldCover) {
            Storage::disk('public')->delete($oldCover);
        }

        return redirect()->route('books.index')->with('status', 'Buku berhasil diperbarui.');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->loanItems()->exists()) {
            return back()->withErrors(['book' => 'Buku sudah tercatat dalam transaksi dan tidak dapat dihapus.']);
        }

        $book->delete();
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        return redirect()->route('books.index')->with('status', 'Buku berhasil dihapus.');
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'author_id' => ['required', 'integer', Rule::exists('authors', 'id')],
            'publisher_id' => ['required', 'integer', Rule::exists('publishers', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'isbn' => ['nullable', 'string', 'max:255', Rule::unique('books', 'isbn')->ignore($book)],
            'year' => ['nullable', 'integer', 'min:1000', 'max:'.now()->year],
            'stock' => ['required', 'integer', 'min:0'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
    }

    private function form(Book $book): View
    {
        return view('admin.books.form', [
            'book' => $book,
            'categories' => Category::query()->orderBy('name')->get(),
            'authors' => Author::query()->orderBy('name')->get(),
            'publishers' => Publisher::query()->orderBy('name')->get(),
        ]);
    }
}
