<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // 一覧表示
    public function index()
    {
        $todos = Todo::all();
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
        ]);

        Todo::create([
            'title' => $request->title,
            'user_id' => auth()->id() ?? 1, // 認証されていない場合はデフォルトユーザーID
        ]);

        return redirect()->route('todos.index');
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
        $todo = Todo::findOrFail($id);
        return view('todos.edit', compact('todo'));
    }

    // 更新処理
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $todo = Todo::findOrFail($id);
        $todo->title = $request->title;
        $todo->save();

        return redirect()->route('todos.index');
    }

    // 削除処理
    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return redirect()->route('todos.index');
    }
}

