<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TasksResource;
use App\Models\Task;
use App\Traits\HttpReponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;

class TasksController extends Controller
{
    use HttpReponses;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TasksResource::collection(
            Task::where('user_id', Auth::user()->id)->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $validated = $request->validated($request->all());

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = Storage::disk('public')->put('images/' . Auth::user()->id, request()->file('image'));
            $validated['image'] = $imagePath;
        }

        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
            'image' => $imagePath,
            'user_id' => Auth::user()->id
        ]);

        return new TasksResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $this->isNotAuthorize($task) ? $this->isNotAuthorize($task) : new TasksResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return $this->error('', 'You are not allowed to make this request', 403);
        }

        $validated = $request->validated($request->all());

        if ($request->hasFile('image')) {
            // delete image
            Storage::disk('public')->delete($task->image);

            $imagePath = Storage::disk('public')->put('images/' . Auth::user()->id, request()->file('image'));
            $validated['image'] = $imagePath;
        }

        $task->update($request->all());

        return new TasksResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Storage::disk('public')->delete($task->image);

        return $this->isNotAuthorize($task) ? $this->isNotAuthorize($task) : $task->delete();
    }

    private function isNotAuthorize($task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return $this->error('', 'You are not allowed to make this request', 403);
        }
    }
}
