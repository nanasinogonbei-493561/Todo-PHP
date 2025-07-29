<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Todo一覧</title>
<style>
body {
font-family: Arial, sans-serif;
margin: 0;
padding: 20px;
}
h1 {
}
}
margin-bottom: 20px;
.todo-container {
max-width: 800px;
.todo-item {
border: 1px solid #ddd;
padding: 10px;
margin-bottom: 10px;
}
.todo-title {
font-weight: bold;
margin-bottom: 5px;
}
.todo-description {
color: #666;
margin-bottom: 10px;
}
.todo-actions {
display: flex;
gap: 10px;
}
.btn {
display: inline-block;
padding: 5px 10px;
text-decoration: none;
color: #fff;
border-radius: 3px;
}
.btn-primary {
background-color: #007bff;
}
.btn-warning {
background-color: #ffc107;
color: #000;
.btn-danger {
background-color: #dc3545;
}
}
.create-btn {
margin-bottom: 20px;
}
form {
display: inline;
}
</style>
</head>
<body>
{{ session('user_id')}}
{{ session('user_name')}}

<div class="todo-container">
<h1>Todo一覧</h1>
<div class="create-btn">
<a href="{{ route('todos.create') }}" class="btn btn-primary">新規作成</a>
</div>
@if(count($todos) > 0)
@foreach($todos as $todo)
<div class="todo-item">
<div class="todo-title">{{ $todo->title }}</div>
@if($todo->description)
<div class="todo-description">{{ $todo->description }}</div>
@endif
<div class="todo-actions">
<a href="{{ route('todos.edit', $todo->id) }}" class="btn btn-
warning">編集</a>
<form action="{{ route('todos.destroy', $todo->id) }}"
method="POST" onsubmit="return confirm('本当に削除しますか？');">
@csrf
@method('DELETE')
<button type="submit" class="btn btn-danger">削除</button>
</form>
</div>
</div>
@endforeach
@else
@endif
</div>
</body>
</html>
<p>Todoがありません。</p>
