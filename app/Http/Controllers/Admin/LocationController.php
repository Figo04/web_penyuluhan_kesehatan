<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('respondents')->orderBy('name')->get();

        return view('admin.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate(
            ['name' => 'required|string|max:255|unique:locations,name'],
            [
                'name.required' => 'Nama lokasi wajib diisi.',
                'name.unique'   => 'Lokasi dengan nama itu sudah ada.',
            ]
        );

        Location::create(['name' => $request->name]);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan!');
    }

    public function update(Request $request, Location $lokasi)
    {
        $request->validate(
            ['name' => ['required', 'string', 'max:255', Rule::unique('locations', 'name')->ignore($lokasi->id)]],
            [
                'name.required' => 'Nama lokasi wajib diisi.',
                'name.unique'   => 'Lokasi dengan nama itu sudah ada.',
            ]
        );

        $lokasi->update(['name' => $request->name]);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil diperbarui!');
    }

    public function destroy(Location $lokasi)
    {
        // locations.id direferensikan respondents dengan nullOnDelete, jadi menghapus
        // lokasi yang masih terpakai akan mengosongkan data lokasi responden —
        // variabel penelitian yang tidak bisa dipulihkan.
        if ($lokasi->respondents()->exists()) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Lokasi ini masih dipakai responden dan tidak bisa dihapus.');
        }

        $lokasi->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil dihapus!');
    }
}
