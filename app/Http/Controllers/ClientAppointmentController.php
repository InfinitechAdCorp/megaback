<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use App\Models\ClientAppointment;
use Illuminate\Http\Request;

class ClientAppointmentController extends Controller
{
    // Fetch all client appointments (GET /api/appointments)
    public function index()
    {
        $appointments = ClientAppointment::all(); // Get all appointments
        return response()->json($appointments);
    }
public function updateStatus(Request $request)
{
    // Validate the request
    $request->validate([
        'id' => 'required|integer', // Ensure the ID is provided in the request body
        'status' => 'required|string',
    ]);

    // Find the appointment using the ID from the request
    $appointment = ClientAppointment::findOrFail($request->id);

    // Update only the status field
    $appointment->update(['status' => $request->status]);

    return response()->json([
        'message' => 'Appointment status updated successfully',
        'appointment' => $appointment,
    ]);
}

   public function store(Request $request)
{
    // Validate the request data
    $request->validate([
        'property_id' => 'required|integer',
        'property_name' => 'required|string',
        'name' => 'required|string',
        'email' => 'required|email',
        'contact_number' => 'required|string',
        'date' => 'required|date', // Laravel validation for a valid date
        'message' => 'nullable|string',
        'status' => 'nullable|string',
        'type' => 'required|string',
    ]);

    // Convert the date to the correct format if needed
    $date = Carbon::parse($request->date)->format('Y-m-d H:i:s'); // Format it to 'Y-m-d H:i:s'

    // Create a new appointment
    $appointment = ClientAppointment::create([
        'property_id' => $request->property_id,
        'property_name' => $request->property_name,
        'name' => $request->name,
        'email' => $request->email,
        'contact_number' => $request->contact_number,
        'date' => $date, // Use the formatted date here
        'message' => $request->message,
        'status' => $request->status ?? 'pending', // Default to 'pending' if not provided
        'type' => $request->type,
    ]);

    return response()->json($appointment, 201); // Return the created appointment
}
    // Fetch a specific client appointment by ID (GET /api/appointments/{appointment})
    public function show(ClientAppointment $clientAppointment)
    {
        return response()->json($clientAppointment);
    }

    // Update an existing client appointment (PUT /api/appointments/{appointment})
    public function update(Request $request, ClientAppointment $clientAppointment)
    {
        $request->validate([
            'property_id' => 'required|integer',
            'property_name' => 'required|string',
            'name' => 'required|string',
            'email' => 'required|email',
            'contact_number' => 'required|string',
            'date' => 'required|date',
            'message' => 'nullable|string',
            'status' => 'nullable|string',
            'type' => 'required|string',
        ]);

        // Update the client appointment with new data
        $clientAppointment->update($request->all());

        return response()->json($clientAppointment);
    }

public function destroy($id)
{
    // Find the appointment by ID
    $clientAppointment = ClientAppointment::find($id);

    // Check if appointment exists
    if (!$clientAppointment) {
        return response()->json(['message' => 'Client appointment not found'], 404);
    }

    // Delete the appointment
    $clientAppointment->delete();

    return response()->json(['message' => 'Client appointment deleted successfully'], 200);
}

}
