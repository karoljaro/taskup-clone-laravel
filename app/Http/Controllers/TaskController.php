<?php

namespace App\Http\Controllers;

use App\Core\Application\Commands\CreateTaskCommand;
use App\Core\Application\Commands\DeleteTaskCommand;
use App\Core\Application\Commands\UpdateTaskCommand;
use App\Core\Application\DTOs\CreateTaskInputDTO;
use App\Core\Application\DTOs\UpdateTaskInputDTO;
use App\Core\Application\Queries\GetAllTaskQuery;
use App\Core\Domain\Enums\TaskStatus;
use App\Core\Domain\VO\TaskId;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetAllTaskQuery $query): JsonResponse
    {
        $tasks = $query->execute();
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CreateTaskCommand $command): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $dto = new CreateTaskInputDTO(
            title: $validated['title'],
            description: $validated['description'] ?? null
        );

        $task = $command->execute($dto);
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        // TODO: Implement GetTaskByIdQuery
        return response()->json(['error' => 'Not implemented yet'], 501);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, UpdateTaskCommand $command): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:TODO,IN_PROGRESS,DONE',
        ]);

        $dto = new UpdateTaskInputDTO(
            title: $validated['title'] ?? null,
            description: $validated['description'] ?? null,
            status: isset($validated['status']) ? TaskStatus::from($validated['status']) : null
        );

        $taskId = new TaskId($id);
        $task = $command->execute($taskId, $dto);
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, DeleteTaskCommand $command): JsonResponse
    {
        $taskId = new TaskId($id);
        $command->execute($taskId);
        return response()->json(null, 204);
    }
}
