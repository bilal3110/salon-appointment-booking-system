<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staffMembers = Staff::with(['user', 'services'])->get();
        return response()->json($staffMembers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'service_id' => 'required|array',
            'service_id.*' => 'exists:services,id',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = User::where('id', $validated['user_id'])
            ->where('type', 'staff')
            ->firstOrFail();

        $staff = new Staff();
        $staff->user_id = $user->id;
        $staff->bio = $validated['bio'] ?? null;

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('staff_photos', 'public');
            $staff->photo = $path;
        }

        $staff->save();

        $staff->services()->attach($validated['service_id']);

        return response()->json([
            'message' => 'Staff created successfully',
            'staff' => $staff
        ], 201);
    }

    public function show($id)
    {
        $staff = Staff::with(['user','services'])->findOrFail($id);
        return response()->json($staff);
    }
    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'service_id' => 'sometimes|array',
            'service_id.*' => 'exists:services,id',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if (isset($validated['bio'])) {
            $staff->bio = $validated['bio'];
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('staff_photos', 'public');
            $staff->photo = $path;
        }

        $staff->save();
        $staff->refresh();

        if (isset($validated['service_id'])) {
            $staff->services()->sync($validated['service_id']);
        }

        return response()->json([
            'message' => 'Staff updated successfully',
            'staff' => $staff
        ]);
    }
    public function destroy($id)
    {
        $staff = Staff::findOrFail($id);
        $staff->services()->detach();
        $staff->delete();

        return response()->json(['message' => 'Staff deleted successfully']);
    }
}
