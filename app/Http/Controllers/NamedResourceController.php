<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

abstract class NamedResourceController extends Controller
{
    abstract protected function modelClass(): string;

    abstract protected function resource(): string;

    abstract protected function label(): string;

    public function index(): View
    {
        $model = $this->modelClass();

        return view('admin.named.index', [
            'records' => $model::query()->withCount('books')->orderBy('name')->paginate(15),
            'resource' => $this->resource(),
            'label' => $this->label(),
        ]);
    }

    public function create(): View
    {
        return $this->form(new ($this->modelClass()));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->modelClass()::create($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.$this->resource()],
        ]));

        return redirect()->route($this->resource().'.index')->with('status', $this->label().' berhasil ditambahkan.');
    }

    public function edit(string $id): View
    {
        $record = $this->modelClass()::query()->findOrFail($id);

        return $this->form($record);
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $record = $this->modelClass()::query()->findOrFail($id);
        $record->update($request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:'.$this->resource().',name,'.$record->getKey()],
        ]));

        return redirect()->route($this->resource().'.index')->with('status', $this->label().' berhasil diperbarui.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $record = $this->modelClass()::query()->findOrFail($id);
        if ($record->books()->exists()) {
            return back()->withErrors(['record' => $this->label().' masih digunakan oleh buku.']);
        }

        $record->delete();

        return redirect()->route($this->resource().'.index')->with('status', $this->label().' berhasil dihapus.');
    }

    private function form(Model $record): View
    {
        return view('admin.named.form', [
            'record' => $record,
            'resource' => $this->resource(),
            'label' => $this->label(),
        ]);
    }
}
