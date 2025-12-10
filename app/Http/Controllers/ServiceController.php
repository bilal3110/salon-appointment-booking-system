<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'service_type' => 'required|string|max:100',
        ]);

        $service = Service::create($validated);

        return response()->json(['message' => 'Service created successfully', 'service' => $service], 201);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'service_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'service_type' => 'sometimes|required|string|max:100',
        ]);

        $service->update($validated);

        return response()->json(['message' => 'Service updated successfully', 'service' => $service]);
    }
    
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
}
