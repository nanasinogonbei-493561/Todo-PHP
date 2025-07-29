<!-- resources/views/items/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>買い物リスト</title>
</head>
<body>
    <h1>買い物リスト</h1>
    <a href="{{ route('items.create') }}">新規作成</a>
    <ul>
        @foreach ($items as $item)
            <li>
                {{ $item->name }}
                <a href="{{ route('items.edit', $item->id) }}">編集</a>
                <form action="{{ route('items.destroy', $item->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">削除</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
