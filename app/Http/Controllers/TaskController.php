<?php

namespace App\Http\Controllers;

use App\Persistence\Eloquent\TaskEloquentModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $tasks = TaskEloquentModel::query()->paginate(15);
        return response()->json($tasks);
    }

//    /**
//     * Show the form for creating a new resource.
//     */
//    public function create()
//    {
//        //
//    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = TaskEloquentModel::query()->create($validated);
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskEloquentModel $taskEloquentModel)
    {
        return response()->json($taskEloquentModel);
    }

//    /**
//     * Show the form for editing the specified resource.
//     */
//    public function edit(TaskEloquentModel $taskEloquentModel)
//    {
//        //
//    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TaskEloquentModel $taskEloquentModel): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $taskEloquentModel->update($validated);
        return response()->json($taskEloquentModel);
    }

    /**
     * Remove the specified resource from storage.
     */
  public function destroy(TaskEloquentModel $taskEloquentModel): JsonResponse
    {
        $taskEloquentModel->delete();
        return response()->json(null, 204);
    }
}
