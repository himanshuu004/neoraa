<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $testimonials = Review::query()->orderBy('display_order')->orderBy('id')->get();

        return view('landing.home', compact('testimonials'));
    }

    public function about()
    {
        return view('landing.about');
    }

    public function services()
    {
        return view('landing.services');
    }

    public function gallery()
    {
        return view('landing.gallery');
    }

    public function contact()
    {
        return view('landing.contact');
    }

    public function careers()
    {
        return view('landing.careers');
    }

    public function testimonials()
    {
        $testimonials = Review::query()->orderBy('display_order')->orderBy('id')->get();

        return view('landing.testimonials', compact('testimonials'));
    }

    public function reviews()
    {
        $testimonials = Review::query()->orderBy('display_order')->orderByDesc('id')->get();

        return view('landing.reviews', compact('testimonials'));
    }

    public function applyConfirmation()
    {
        return view('landing.apply-confirmation');
    }

    public function apply(Request $request)
    {
        return view('landing.apply', [
            'error' => '',
            'generatedBy' => (int) $request->query('ref', 0) ?: null,
        ]);
    }
}
