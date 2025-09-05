<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\NewsNotificationCreated;

class NewsController extends Controller
{
    /**
     * Display all active news on the “news” page.
     */
    public function index()
    {
        // Recupera tutte le news attive, in ordine decrescente
        $news = News::latest()
            ->where('is_active', true)
            ->get();

        return view('frontend.news.index', compact('news'));
    }

    /**
     * Same as index(), but renders the “blogs” view.
     */
    public function blogs()
    {
        $news = News::latest()
            ->where('is_active', true)
            ->get();

        return view('frontend.news.blogs', compact('news'));
    }

    /**
     * Show the form to create a new news item.
     */
    public function create()
    {
        return view('frontend.news.create');
    }

    /**
     * Store a newly created news item.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'event_date' => 'required|date',
            'image'      => 'nullable|image|max:95120', // circa 95 MB
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                                     ->store('news_images', 'public');
        }

        $news = News::create($data);

        // (eventuale broadcast notifiche)
        // event(new NewsNotificationCreated($news));

        return redirect()
            ->route('news.index')
            ->with('success', 'Notizia creata con successo.');
    }

    /**
     * Display a single news item.
     */
    public function show($id)
    {
        $news = News::findOrFail($id);
        return view('frontend.news.show', compact('news'));
    }

    /**
     * Show the form to edit an existing news item.
     */
    public function edit(News $news)
    {
        return view('frontend.news.create', compact('news'));
    }

    /**
     * Update the specified news item.
     */
    public function update(Request $request, News $news)
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'event_date' => 'required|date',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:20048',
        ]);

        if ($request->hasFile('image')) {
            // Se desideri cancellare la vecchia: Storage::disk('public')->delete($news->image);
            $data['image'] = $request->file('image')
                                     ->store('news_images', 'public');
        }

        $news->update($data);

        return redirect()
            ->route('news.index')
            ->with('success', 'Notizia aggiornata con successo.');
    }

    /**
     * Remove the specified news item and its notifications.
     */
    public function destroy(News $news)
    {
        // 1) Rimuovi tutte le notifiche correlate
        $news->notifications()->delete();

        // 2) Elimina la news
        $news->delete();

        return redirect()
            ->route('news.index')
            ->with('success', 'Notizia e notifiche correlate eliminate con successo.');
    }
}
