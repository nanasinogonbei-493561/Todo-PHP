<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Helpers\StructuredLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    // 一覧表示
    public function index()
    {
        $startTime = microtime(true);
        
        $todos = Auth::user()->todos; // 認証済みユーザーのTodoのみ取得
        
        $duration = microtime(true) - $startTime;
        StructuredLogger::performance('todo_index', $duration, [
            'todo_count' => $todos->count(),
            'user_authenticated' => true
        ]);
        
        StructuredLogger::userAction('view_todo_list', [
            'todo_count' => $todos->count()
        ]);
        
        return view('todos.index', compact('todos'));
    }

    // 新規作成フォーム表示
    public function create()
    {
        return view('todos.create');
    }

    // 新規作成処理
    public function store(Request $request)
    {
        try {
            $startTime = microtime(true);
            
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string', 
            ]);

            $todoData = [
                'title' => $request->title,
                'description' => $request->description,
            ];

            // 認証済みユーザーに紐づけて作成
            $todo = Auth::user()->todos()->create($todoData);

            $duration = microtime(true) - $startTime;
            StructuredLogger::performance('todo_create', $duration);
            StructuredLogger::database('create', 'todos', $todoData, $duration);
            StructuredLogger::userAction('create_todo', [
                'todo_id' => $todo->id,
                'title' => $todo->title
            ]);

            return redirect()->route('todos.index')
            ->with('success', 'Todoが作成されました');
            
        } catch (\Exception $e) {
            StructuredLogger::error('Failed to create todo', $e, [
                'title' => $request->title,
                'description' => $request->description
            ]);
            throw $e;
        }
    }

    // 詳細表示（必要に応じて追加）
    public function show($id)
    {
        $todo = Auth::user()->todos()->findOrFail($id);
        return view('todos.show', compact('todo'));
    }

    // 編集フォーム表示
    public function edit($id)
    {
        // 認証済みユーザーのTodoのみ取得
        $todo = Auth::user()->todos()->findOrFail($id);
        return view('todos.edit', compact('todo'));
    }

    // 更新処理
    public function update(Request $request, $id)
    {
        try {
            $startTime = microtime(true);
            
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string', 
            ]);

            $todo = Auth::user()->todos()->findOrFail($id);
            
            $updateData = [
                'title' => $request->title, 
                'description' => $request->description, 
            ];
            
            $todo->update($updateData);

            $duration = microtime(true) - $startTime;
            StructuredLogger::performance('todo_update', $duration);
            StructuredLogger::database('update', 'todos', $updateData, $duration);
            StructuredLogger::userAction('update_todo', [
                'todo_id' => $todo->id,
                'title' => $todo->title
            ]);

            return redirect()->route('todos.index')
            ->with('success', 'Todoが更新されました');
            
        } catch (\Exception $e) {
            StructuredLogger::error('Failed to update todo', $e, [
                'todo_id' => $id,
                'title' => $request->title,
                'description' => $request->description
            ]);
            throw $e;
        }
    }

    // 削除処理
    public function destroy($id)
    {
        try {
            $startTime = microtime(true);
            
            $todo = Auth::user()->todos()->findOrFail($id);
            
            $todoData = [
                'id' => $todo->id,
                'title' => $todo->title,
                'description' => $todo->description
            ];
            
            $todo->delete();

            $duration = microtime(true) - $startTime;
            StructuredLogger::performance('todo_delete', $duration);
            StructuredLogger::database('delete', 'todos', $todoData, $duration);
            StructuredLogger::userAction('delete_todo', [
                'todo_id' => $todoData['id'],
                'title' => $todoData['title']
            ]);

            return redirect()->route('todos.index')
            ->with('success', 'Todoが削除されました');
            
        } catch (\Exception $e) {
            StructuredLogger::error('Failed to delete todo', $e, [
                'todo_id' => $id
            ]);
            throw $e;
        }
    }
}

