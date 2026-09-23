<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserTask;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = UserTask::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.tasks.index', compact('tasks'));
    }
}
