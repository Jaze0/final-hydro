<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    /**
     * Store sensor data (POST method).
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'soil_moisture' => 'required|numeric',
            'water_level' => 'required|numeric',  // Added water level validation
        ]);
    
        // Create and save the new sensor data
        $sensorData = SensorData::create([
            'temperature' => $validated['temperature'],
            'humidity' => $validated['humidity'],
            'soil_moisture' => $validated['soil_moisture'],
            'water_level' => $validated['water_level'],  // Add the water level here
        ]);
    
        // Return a JSON response indicating success
        return response()->json([
            'message' => 'Data saved successfully',
            'data' => $sensorData
        ], 201);  // HTTP Status Code 201 indicates successful creation
    }

    /**
     * Show the specified sensor data (GET method).
     */
    public function show($id)
    {
        $data = SensorData::findOrFail($id);
        return response()->json($data);
    }

    /**
     * Update the specified sensor data in storage (PUT/PATCH method).
     */
    public function update(Request $request, $id)
    {
        $sensorData = SensorData::findOrFail($id);

        // Validate incoming data for update
        $validated = $request->validate([
            'temperature' => 'sometimes|numeric',
            'humidity' => 'sometimes|numeric',
            'soil_moisture' => 'sometimes|numeric',
            'water_level' => 'sometimes|numeric',  // Allow water level to be updated
        ]); 

        // Update the sensor data
        $sensorData->update($validated);

        return response()->json([
            'message' => 'Data updated successfully',
            'data' => $sensorData
        ]);
    }

    /**
     * Remove the specified sensor data from storage (DELETE method).
     */
    public function destroy($id)
    {
        $sensorData = SensorData::findOrFail($id);
        $sensorData->delete();

        return response()->json([
            'message' => 'Data deleted successfully'
        ]);
    }

    /**
     * Retrieve all sensor data (GET method).
     */
    public function index()
    {
        // Retrieve the sensor data, including water_level
        $sensorData = SensorData::latest()->paginate(10);
    
        // Return the dashboard view and pass the sensor data
        return view('dashboard', compact('sensorData'));
    }
}
