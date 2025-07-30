<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo詳細</title>
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
        .todo-item {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .todo-title {
            font-size: 1.5em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        .todo-description {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .todo-meta {
            color: #999;
            font-size: 0.9em;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #000;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-logout {
            background-color: #6c757d;
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
            <h1>Todo詳細</h1>
            <div class="user-info">
                <span>ようこそ、{{ Auth::user()->name ?? Auth::user()->email }}さん</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-logout" onclick="return confirm('ログアウトしますか？')">ログアウト</button>
                </form>
            </div>
        </div>

        <div class="todo-item">
            <div class="todo-title">{{ $todo->title }}</div>
            @if($todo->description)
                <div class="todo-description">{{ $todo->description }}</div>
            @else
                <div class="todo-description">説明はありません。</div>
            @endif
            <div class="todo-meta">
                作成日: {{ $todo->created_at->format('Y年m月d日 H:i') }}<br>
                更新日: {{ $todo->updated_at->format('Y年m月d日 H:i') }}
            </div>
            <div class="todo-actions">
                <a href="{{ route('todos.edit', $todo->id) }}" class="btn btn-warning">編集</a>
                <a href="{{ route('todos.index') }}" class="btn btn-secondary">一覧に戻る</a>
            </div>
        </div>
    </div>
</body>
</html> 