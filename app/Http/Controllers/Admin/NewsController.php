<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = \App\Models\News::latest()->get();
        return view('admin.news.index', compact('news'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $slug = \Illuminate\Support\Str::slug($request->title) . '-' . time();
        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('news_thumbnails', 'public');
        }

        \App\Models\News::create([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'thumbnail' => $thumbnailPath,
            'is_published' => $request->has('is_published'),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('news.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(\App\Models\News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    public function update(Request $request, \App\Models\News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            // Delete old if exists
             if ($news->thumbnail && \Illuminate\Support\Facades\Storage::disk('public')->exists($news->thumbnail)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($news->thumbnail);
            }
            $news->thumbnail = $request->file('thumbnail')->store('news_thumbnails', 'public');
        }

        $news->update([
            'title' => $request->title,
            'content' => $request->content,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('news.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(\App\Models\News $news)
    {
        if ($news->thumbnail && \Illuminate\Support\Facades\Storage::disk('public')->exists($news->thumbnail)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($news->thumbnail);
        }
        $news->delete();
        return redirect()->route('news.index')->with('success', 'Berita berhasil dihapus.');
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\NewsExport, 'data-news.xlsx');
    }

    public function exportPdf()
    {
        $news = \App\Models\News::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.news.pdf', compact('news'));
        return $pdf->download('data-news.pdf');
    }
}
