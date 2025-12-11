<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['staff.user', 'service'])->latest()->get();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_contact' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'staff_id' => 'required|integer|exists:staff,id',
            'service_id' => 'required|integer|exists:services,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required|string|max:10',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $service = Service::find($validated['service_id']);
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        $staff = Staff::find($validated['staff_id']);

        if (!$staff || !$staff->services->contains($service->id)) {
            return response()->json(['error' => 'Staff not found or does not provide the selected service'], 404);
        }
        $booking = Booking::create($validated);
        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking
        ], 201);
    }

    public function show($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        return response()->json($booking);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $validated = $request->validate([
            'customer_name' => 'sometimes|required|string|max:255',
            'customer_contact' => 'sometimes|required|string|max:20',
            'customer_email' => 'sometimes|required|email|max:255',
            'staff_id' => 'sometimes|required|integer|exists:staff,id',
            'service_id' => 'sometimes|required|integer|exists:services,id',
            'booking_date' => 'sometimes|required|date',
            'booking_time' => 'sometimes|required|string|max:10',
            'status' => 'sometimes|required|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $booking->update($validated);
        return response()->json(['message' => 'Booking updated successfully', 'booking' => $booking]);
    }

    public function destroy($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        $booking->delete();
        return response()->json(['message' => 'Booking deleted successfully']);
    }

    public function confirm($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        $booking->status = 'confirmed';
        $booking->save();
        return response()->json(['message' => 'Booking confirmed successfully', 'booking' => $booking]);
    }

    public function cancel($id)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        $booking->status = 'canceled';
        $booking->save();
        return response()->json(['message' => 'Booking canceled successfully', 'booking' => $booking]);
    }

    public function getStaffByService(Service $service)
    {
        $staff = $service->staff()->with('user')->get()->map(function ($s) {
            return [
                'id' => $s->id,
                'name' => $s->user->name,
            ];
        });

        return response()->json($staff);
    }
}
