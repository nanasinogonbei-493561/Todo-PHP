<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Todo編集</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
}
h1 {
    margin-bottom: 20px;
}
.todo-container {
    max-width: 800px;
}
.form-group {
    margin-bottom: 15px;
}
label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}
input[type="text"], textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
}
textarea {
    height: 100px;
}
.btn {
    display: inline-block;
    padding: 8px 16px;
    text-decoration: none;
    color: #fff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}
.btn-primary {
    background-color: #007bff;
}
.btn-secondary {
    background-color: #6c757d;
}
.btn-logout {
    background-color: #6c757d;
}
.error {
    color: #dc3545;
    margin-top: 5px;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}
.user-info span {
    color: #333;
    font-weight: 500;
}
</style>
</head>
<body>
<div class="todo-container">
    <div class="header">
        <h1>Todo編集</h1>
        <div class="user-info">
            <span>ようこそ、{{ Auth::user()->name ?? Auth::user()->email }}さん</span>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-logout" onclick="return confirm('ログアウトしますか？')">ログアウト</button>
            </form>
        </div>
    </div>
@if ($errors->any())
<div class="alert alert-danger">
<ul>
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif
<form action="{{ route('todos.update', $todo->id) }}" method="POST">
@csrf
@method('PUT')
<div class="form-group">
<label for="title">タイトル</label>
<input type="text" id="title" name="title" value="{{ old('title', $todo->title) }}" required>
@error('title')
<div class="error">{{ $message }}</div>
@enderror
</div>
<div class="form-group">
<label for="description">説明（オプション）</label>
<textarea id="description" name="description">{{ old('description', $todo->description) }}</textarea>
</div>
<div class="form-group">
<button type="submit" class="btn btn-primary">更新</button>
<a href="{{ route('todos.index') }}" class="btn btn-secondary">キャンセル
</a>
</div>
</form>
</div>
</body>
</html>
