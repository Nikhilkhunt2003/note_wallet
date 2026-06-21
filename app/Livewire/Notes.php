<?php

namespace App\Livewire;

use App\Models\Note;
use Illuminate\Support\Str;
use Livewire\Component;

class Notes extends Component
{
    public string $url = "";
    public string $note = "";

    public function mount(?string $url = null)
    {
        try {
            // If no url is provided
            if (empty($url)) {
                do {
                    $url = Str::upper(Str::random(6));
                } while (Note::where('slug', $url)->exists());


                $note = Note::create([
                    'slug' => $url,
                ]);

                return redirect('/' . $note->slug);
            }

             $this->url = $url;

            // Find note by url
            $noteData = Note::where('slug',  $this->url)->firstOrFail();
            $this->note = $noteData->note ?? "";

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {

            abort(404, 'Note not found.');

        } catch (\Exception $e) {

            report($e);
            dd($e->getMessage());

            abort(500, 'Something went wrong.');
        }
    }

    /**
     * Automatically save the note content to the database
     * whenever the 'note' property is updated from the frontend.
     */
    public function updatedNote($value)
    {
        Note::where('slug',  $this->url)->update(['note' => $value]);
    }

    public function saveNewSlug($newUrl)
    {
        $newUrl = trim($newUrl);

        // 1. Basic Validation
        if (empty($newUrl)) {
            return $this->dispatch('url-error', message: 'URL cannot be empty.');
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $newUrl)) {
            return $this->dispatch('url-error', message: 'Invalid URL format.');
        }

        // 2. Check if it already exists in the database (excluding current url)
        if (Note::where('slug', $newUrl)->where('slug', '!=',  $this->url)->exists()) {
            return $this->dispatch('url-error', message: 'This URL is already taken.');
        }

        // 3. Update and Redirect
        $note = Note::where('slug',  $this->url)->first();
        if ($note) {
            $note->slug = $newUrl;
            $note->save();

            // Redirect to the new URL
            return redirect()->to('/' . $newUrl);
        }
    }

    public function render()
    {
        return view('livewire.notes')->layout('layouts.app');
    }
}
