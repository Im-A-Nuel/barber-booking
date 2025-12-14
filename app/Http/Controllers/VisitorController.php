<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Service;

class VisitorController extends Controller
{
    /**
     * Menampilkan halaman search service untuk pengunjung
     */
    public function searchService(Request $request)
    {
        $query = Service::where('is_active', true);

        // Filter berdasarkan pencarian nama
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan harga minimum
        if ($request->has('min_price') && $request->min_price != '') {
            $query->where('price', '>=', $request->min_price);
        }

        // Filter berdasarkan harga maksimum
        if ($request->has('max_price') && $request->max_price != '') {
            $query->where('price', '<=', $request->max_price);
        }

        $services = $query->orderBy('name', 'asc')->paginate(9);

        return view('visitor.search-service', compact('services'));
    }

    /**
     * Menampilkan detail service untuk pengunjung
     */
    public function actSearchService($id)
    {
        $service = Service::where('is_active', true)->findOrFail($id);

        return view('visitor.service-detail', compact('service'));
    }
}
