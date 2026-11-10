<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveillanceController extends Controller
{
    public function index(): View
    {
        $cameras = Camera::query()->orderBy('name')->get();

        return view('surveillance.index', compact('cameras'));
    }

    public function create(): View
    {
        return view('surveillance.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Camera::create($this->validateCamera($request));

        return redirect()->route('surveillance.index')
            ->with('success', 'Cámara registrada correctamente.');
    }

    public function edit(Camera $camera): View
    {
        return view('surveillance.edit', compact('camera'));
    }

    public function update(Request $request, Camera $camera): RedirectResponse
    {
        $camera->update($this->validateCamera($request));

        return redirect()->route('surveillance.index')
            ->with('success', 'Cámara actualizada correctamente.');
    }

    public function destroy(Camera $camera): RedirectResponse
    {
        $camera->delete();

        return redirect()->route('surveillance.index')
            ->with('success', 'Cámara eliminada correctamente.');
    }

    private function validateCamera(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'stream_url' => ['nullable', 'url:http,https', 'max:2048'],
            'status' => ['required', 'in:online,offline,maintenance'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
