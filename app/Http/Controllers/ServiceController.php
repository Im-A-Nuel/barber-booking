<?php

namespace App\Http\Controllers;

use App\Http\Requests\Service\StoreServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the services.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Service::query()->orderBy('name');

        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $services = $query->paginate(10)->appends($request->only('search'));

        return view('services.index', compact('services', 'search'));
    }

    /**
     * Show the form for creating a new service.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created service in storage.
     *
     * @param  StoreServiceRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreServiceRequest $request)
    {
        Service::create($this->normalizePayload($request));

        return redirect()
            ->route('services.index')
            ->with('status', 'Layanan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified service.
     *
     * @param  Service  $service
     * @return \Illuminate\View\View
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified service in storage.
     *
     * @param  UpdateServiceRequest  $request
     * @param  Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($this->normalizePayload($request));

        return redirect()
            ->route('services.index')
            ->with('status', 'Layanan berhasil diperbarui.');
    }

    /**
     * Remove the specified service from storage.
     *
     * @param  Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Service $service)
    {
        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('status', 'Layanan berhasil dihapus.');
    }

    /**
     * Prepare validated payload for persistence.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return array<string, mixed>
     */
    protected function normalizePayload(FormRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active')
            ? (bool) $request->input('is_active')
            : ($data['is_active'] ?? true);

        return $data;
    }
}
