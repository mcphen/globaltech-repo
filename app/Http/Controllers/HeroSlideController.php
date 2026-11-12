<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class HeroSlideController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/HeroSlides/HeroSlidesIndex', [
            'slides' => HeroSlide::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/HeroSlides/HeroSlideForm', [
            'slide' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('hero_slides', 'public');
        }
        $data['sort_order'] ??= (int) HeroSlide::max('sort_order') + 1;

        HeroSlide::create($data);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide ajouté.');
    }

    public function edit(HeroSlide $heroSlide)
    {
        return Inertia::render('Admin/HeroSlides/HeroSlideForm', [
            'slide' => $heroSlide,
        ]);
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $this->deleteImage($heroSlide);
            $data['image_path'] = $request->file('image')->store('hero_slides', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($heroSlide);
            $data['image_path'] = null;
        }
        $data['sort_order'] ??= $heroSlide->sort_order;

        $heroSlide->update($data);

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide mis à jour.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        $this->deleteImage($heroSlide);
        $heroSlide->delete();

        return redirect()->route('admin.hero-slides.index')
            ->with('success', 'Slide supprimé.');
    }

    public function toggle(HeroSlide $heroSlide)
    {
        $heroSlide->update(['active' => ! $heroSlide->active]);

        return back();
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:hero_slides,id',
        ]);

        foreach ($request->ids as $index => $id) {
            HeroSlide::whereKey($id)->update(['sort_order' => $index + 1]);
        }

        return back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'title'           => 'nullable|string|max:255',
            'subtitle'        => 'nullable|string|max:255',
            'text'            => 'nullable|string|max:2000',
            'button1_label'   => 'nullable|string|max:100',
            'button1_url'     => 'nullable|string|max:500',
            'button2_label'   => 'nullable|string|max:100',
            'button2_url'     => 'nullable|string|max:500',
            'text_position'   => 'nullable|in:left,center,right',
            'overlay_opacity' => 'nullable|integer|min:0|max:90',
            'sort_order'      => 'nullable|integer|min:0',
            'active'          => 'nullable|boolean',
        ]);

        unset($data['image']);
        $data['text_position']   = $data['text_position'] ?? 'left';
        $data['overlay_opacity'] = $data['overlay_opacity'] ?? 50;
        $data['active']          = $request->boolean('active', true);

        return $data;
    }

    private function deleteImage(HeroSlide $slide): void
    {
        if ($slide->image_path) {
            Storage::disk('public')->delete($slide->image_path);
        }
    }
}
