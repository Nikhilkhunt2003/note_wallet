<?php

use App\Livewire\Notes;
use App\Models\Note;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::view('/{slug}', 'components.note-editor');

Route::get('/{url?}', Notes::class)->name('notes');

Route::get('/check-url', function (Request $request) {
    $url = trim($request->input('url'));
    $currentSlug = trim($request->input('current_url'));

    // Query the database, but exclude the current slug so it doesn't flag itself
    $query = Note::where('slug', $url);


    if ($currentSlug) {
        $query->where('slug', '!=', $currentSlug);
    }

    return response()->json(['exists' => $query->exists()]);
});
