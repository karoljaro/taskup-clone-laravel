<?php

use App\Core\Domain\Entities\Task;
use App\Core\Domain\Enums\TaskStatus;
use App\Core\Domain\VO\TaskId;
use App\Persistence\Eloquent\TaskEloquentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;

uses(RefreshDatabase::class);

describe('Task API Endpoints', function () {
    describe('POST /tasks', function () {
        it('creates a new task with title and description', function () {
            $response = $this->postJson('/tasks', [
                'title' => 'Buy groceries',
                'description' => 'Milk, eggs, and bread'
            ]);

            $response->assertStatus(Response::HTTP_CREATED)
                ->assertJsonStructure([
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at'
                ]);

            expect($response['title'])->toBe('Buy groceries')
                ->and($response['description'])->toBe('Milk, eggs, and bread')
                ->and($response['status'])->toBe(TaskStatus::TODO->value);
        });

        it('creates a task with only title', function () {
            $response = $this->postJson('/tasks', [
                'title' => 'Complete project'
            ]);

            $response->assertStatus(Response::HTTP_CREATED)
                ->assertJsonPath('title', 'Complete project')
                ->assertJsonPath('status', TaskStatus::TODO->value);

            expect($response['description'])->toBe('');
        });

        it('persists task to database', function () {
            $response = $this->postJson('/tasks', [
                'title' => 'Test task',
                'description' => 'Test description'
            ]);

            $response->assertStatus(Response::HTTP_CREATED);

            $this->assertDatabaseHas('tasks', [
                'id' => $response['id'],
                'title' => 'Test task',
                'description' => 'Test description'
            ]);
        });

        it('fails when title is missing', function () {
            $response = $this->postJson('/tasks', [
                'description' => 'Missing title'
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        it('fails when title is empty', function () {
            $response = $this->postJson('/tasks', [
                'title' => ''
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        it('fails when title is too short', function () {
            $response = $this->postJson('/tasks', [
                'title' => 'ab' // too short (min 3)
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        it('fails when title is too long', function () {
            $response = $this->postJson('/tasks', [
                'title' => str_repeat('a', 256) // too long (max 255)
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        it('fails when description is too long', function () {
            $response = $this->postJson('/tasks', [
                'title' => 'Valid title',
                'description' => str_repeat('a', 2001) // too long (max 2000)
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        it('returns proper JSON response', function () {
            $response = $this->postJson('/tasks', [
                'title' => 'JSON test',
                'description' => 'Check JSON structure'
            ]);

            $response->assertStatus(Response::HTTP_CREATED)
                ->assertJsonStructure([
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at'
                ]);
        });
    });

    describe('GET /tasks', function () {
        it('returns empty array when no tasks exist', function () {
            $response = $this->getJson('/tasks');

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonCount(0);
        });

        it('returns all tasks from database', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Task 1',
                'description' => 'Description 1',
                'status' => TaskStatus::TODO->value
            ]);

            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d480',
                'title' => 'Task 2',
                'description' => 'Description 2',
                'status' => TaskStatus::IN_PROGRESS->value
            ]);

            $response = $this->getJson('/tasks');

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonCount(2)
                ->assertJsonPath('0.title', 'Task 1')
                ->assertJsonPath('1.title', 'Task 2');
        });

        it('returns tasks with correct structure', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Test task',
                'description' => 'Test description',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->getJson('/tasks');

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonStructure([
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]);
        });

        it('returns tasks with all statuses', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'TODO task',
                'status' => TaskStatus::TODO->value
            ]);

            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d480',
                'title' => 'IN_PROGRESS task',
                'status' => TaskStatus::IN_PROGRESS->value
            ]);

            $response = $this->getJson('/tasks');

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonCount(2)
                ->assertJsonPath('0.status', TaskStatus::TODO->value)
                ->assertJsonPath('1.status', TaskStatus::IN_PROGRESS->value);
        });
    });

    describe('GET /tasks/{id}', function () {
        it('returns task when exists', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Specific task',
                'description' => 'Find me',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->getJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonPath('title', 'Specific task')
                ->assertJsonPath('description', 'Find me');
        });

        it('returns 404 when task does not exist', function () {
            $response = $this->getJson('/tasks/00000000-0000-0000-0000-000000000000');

            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });

        it('returns correct task structure', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Test',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->getJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonStructure([
                    'id',
                    'title',
                    'description',
                    'status',
                    'created_at',
                    'updated_at'
                ]);
        });

        it('returns task with updated status', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Test',
                'status' => TaskStatus::IN_PROGRESS->value
            ]);

            $response = $this->getJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonPath('status', TaskStatus::IN_PROGRESS->value);
        });
    });

    describe('PUT /tasks/{id}', function () {
        it('updates task title', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Original title',
                'description' => 'Description',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->putJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479', [
                'title' => 'Updated title'
            ]);

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonPath('title', 'Updated title')
                ->assertJsonPath('description', 'Description');
        });

        it('updates task description', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Title',
                'description' => 'Original description',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->putJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479', [
                'description' => 'Updated description'
            ]);

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonPath('title', 'Title')
                ->assertJsonPath('description', 'Updated description');
        });

        it('updates task status', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Title',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->putJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479', [
                'status' => 'in_progress'
            ]);

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonPath('status', TaskStatus::IN_PROGRESS->value);
        });

        it('updates multiple properties at once', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Original',
                'description' => 'Original',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->putJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479', [
                'title' => 'Updated',
                'description' => 'Updated description',
                'status' => 'in_progress'
            ]);

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonPath('title', 'Updated')
                ->assertJsonPath('description', 'Updated description')
                ->assertJsonPath('status', TaskStatus::IN_PROGRESS->value);
        });

        it('persists updates to database', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Original',
                'status' => TaskStatus::TODO->value
            ]);

            $this->putJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479', [
                'title' => 'Updated'
            ]);

            $this->assertDatabaseHas('tasks', [
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Updated'
            ]);
        });

        it('fails when task does not exist', function () {
            $response = $this->putJson('/tasks/00000000-0000-0000-0000-000000000000', [
                'title' => 'Updated'
            ]);

            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });

        it('fails with invalid title', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Original',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->putJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479', [
                'title' => 'ab' // too short
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        it('fails with invalid status', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Title',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->putJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479', [
                'status' => 'INVALID_STATUS'
            ]);

            $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        it('allows partial updates', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Original title',
                'description' => 'Original description',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->putJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479', [
                'title' => 'Updated title'
                // description and status are not provided
            ]);

            $response->assertStatus(Response::HTTP_OK)
                ->assertJsonPath('title', 'Updated title')
                ->assertJsonPath('description', 'Original description')
                ->assertJsonPath('status', TaskStatus::TODO->value);
        });
    });

    describe('DELETE /tasks/{id}', function () {
        it('deletes task from database', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Delete me',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->deleteJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $response->assertStatus(Response::HTTP_NO_CONTENT);

            $this->assertDatabaseMissing('tasks', [
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479'
            ]);
        });

        it('returns 204 No Content on success', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Task',
                'status' => TaskStatus::TODO->value
            ]);

            $response = $this->deleteJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $response->assertStatus(Response::HTTP_NO_CONTENT)
                ->assertNoContent();
        });

        it('fails with 404 when task does not exist', function () {
            $response = $this->deleteJson('/tasks/00000000-0000-0000-0000-000000000000');

            $response->assertStatus(Response::HTTP_NOT_FOUND);
        });

        it('does not affect other tasks', function () {
            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d479',
                'title' => 'Delete this',
                'status' => TaskStatus::TODO->value
            ]);

            TaskEloquentModel::create([
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d480',
                'title' => 'Keep this',
                'status' => TaskStatus::TODO->value
            ]);

            $this->deleteJson('/tasks/f47ac10b-58cc-4372-a567-0e02b2c3d479');

            $this->assertDatabaseHas('tasks', [
                'id' => 'f47ac10b-58cc-4372-a567-0e02b2c3d480'
            ]);
        });
    });
});

