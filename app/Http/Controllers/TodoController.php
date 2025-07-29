<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    // 一覧表示
    public function index()
    {
        if (Auth::check()) {
            $todos = Auth::user()->todos; // ユーザーのTodoのみ取得
        } else {
            $todos = collect(); // 空のコレクション
        }
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
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
        ]);

        if (Auth::check()) {
            // 認証済みユーザーに紐づけて作成
            Auth::user()->todos()->create([
                'title' => $request->title,
                'description' => $request->description,
            ]);
        } else {
            // 認証されていない場合はデフォルトユーザーIDで作成
            Todo::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => 1,
            ]);
        }

        return redirect()->route('todos.index')
        ->with('success', 'Todoが作成されました');
    }

    // 詳細表示（必要に応じて追加）
    public function show($id)
    {
        $todo = Todo::findOrFail($id);
        return view('todos.show', compact('todo'));
    }

    // 編集フォーム表示
    public function edit($id)
    {
        if (Auth::check()) {
            // ユーザーのTodoのみ取得
            $todo = Auth::user()->todos()->findOrFail($id);
        } else {
            // 認証されていない場合は全Todoから取得
            $todo = Todo::findOrFail($id);
        }
        return view('todos.edit', compact('todo'));
    }

    // 更新処理
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', 
        ]);

        if (Auth::check()) {
            $todo = Auth::user()->todos()->findOrFail($id);
        } else {
            $todo = Todo::findOrFail($id);
        }
        
        $todo->update([
            'title' => $request->title, 
            'description' => $request->description, 
        ]);

        return redirect()->route('todos.index')
        ->with('success', 'Todoが更新されました');
    }

    // 削除処理
    public function destroy($id)
    {
        if (Auth::check()) {
            $todo = Auth::user()->todos()->findOrFail($id);
        } else {
            $todo = Todo::findOrFail($id);
        }
        $todo->delete();

        return redirect()->route('todos.index')
        ->with('success', 'Todoが削除されました');
    }
}

