<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Todo::all(), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'assignee' => 'nullable|string',
            'due_date' => 'required|date|after_or_equal:today',
            'time_tracked' => 'numeric|min:0',
            'status' => 'in:pending,open,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
        ]);

        $todo = Todo::create($request->all());
        return response()->json(['message' => 'Todo created successfully', 'todo' => $todo], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
