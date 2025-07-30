# 構造化ログ設定ガイド

このプロジェクトでは、構造化ログを使用してアプリケーションの動作を詳細に記録し、VSCodeで効率的に検索・分析できるように設定されています。

## 📁 設定ファイル

### ログ設定
- `config/logging.php` - ログチャンネルの設定
- `app/Helpers/StructuredLogger.php` - 構造化ログヘルパークラス

### VSCode設定
- `.vscode/settings.json` - VSCodeの基本設定
- `.vscode/tasks.json` - ログ表示用タスク
- `.vscode/log-viewer.code-snippets` - ログ検索用スニペット
- `.vscode/extensions.json` - 推奨拡張機能

## 🚀 使用方法

### 1. 構造化ログの記録

```php
use App\Helpers\StructuredLogger;

// アプリケーションログ
StructuredLogger::application('User logged in', ['user_id' => 123]);

// エラーログ
try {
    // 何らかの処理
} catch (\Exception $e) {
    StructuredLogger::error('Failed to process data', $e, ['data' => $data]);
}

// ユーザーアクション
StructuredLogger::userAction('create_todo', ['todo_id' => 456, 'title' => 'New Task']);

// パフォーマンスログ
$startTime = microtime(true);
// 処理
$duration = microtime(true) - $startTime;
StructuredLogger::performance('database_query', $duration, ['query' => $sql]);

// データベース操作
StructuredLogger::database('create', 'todos', ['title' => 'Task'], 0.05);
```

### 2. ログファイルの場所

- `storage/logs/structured.log` - 構造化ログ（JSON形式）
- `storage/logs/application.log` - アプリケーションログ（JSON形式）
- `storage/logs/laravel.log` - 標準Laravelログ

### 3. VSCodeでの検索

#### タスクを使用したログ表示
1. `Ctrl+Shift+P` (または `Cmd+Shift+P`) でコマンドパレットを開く
2. "Tasks: Run Task" を選択
3. 以下のタスクから選択：
   - `View Latest Application Log` - アプリケーションログをリアルタイム表示
   - `View Latest Structured Log` - 構造化ログをリアルタイム表示
   - `Search Logs for Error` - エラーログを検索
   - `Clear All Logs` - 全ログファイルをクリア

#### スニペットを使用した検索
検索ボックスで以下のスニペットを使用：
- `log-error` → `ERROR` を検索
- `log-user` → `User action` を検索
- `log-db` → `Database:` を検索
- `log-perf` → `Performance:` を検索
- `log-userid` → 特定のユーザーIDを検索
- `log-date` → 特定の日付を検索

### 4. Artisanコマンド

#### ログ検索
```bash
# 基本的な検索
php artisan log:search "error"

# 特定のファイルを検索
php artisan log:search "user action" --file=application.log

# 結果数を制限
php artisan log:search "database" --limit=10

# JSON形式で出力
php artisan log:search "performance" --json
```

#### ログ統計
```bash
# 基本統計
php artisan log:stats

# 特定のファイルの統計
php artisan log:stats --file=application.log

# 過去30日間の統計
php artisan log:stats --days=30
```

## 📊 ログの構造

### 構造化ログの例
```json
{
    "message": "User action: create_todo",
    "context": {
        "todo_id": 123,
        "title": "New Task"
    },
    "user_id": 1,
    "user_email": "user@example.com",
    "ip_address": "127.0.0.1",
    "user_agent": "Mozilla/5.0...",
    "url": "http://localhost/todos",
    "method": "POST",
    "timestamp": "2024-01-15T10:30:00.000000Z",
    "level_name": "INFO",
    "channel": "structured"
}
```

### ログレベル
- `ERROR` - エラーと例外
- `WARNING` - 警告
- `INFO` - 一般的な情報
- `DEBUG` - デバッグ情報

## 🔧 カスタマイズ

### 新しいログチャンネルの追加
`config/logging.php` に新しいチャンネルを追加：

```php
'custom' => [
    'driver' => 'daily',
    'path' => storage_path('logs/custom.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => env('LOG_DAILY_DAYS', 30),
    'replace_placeholders' => true,
    'formatter' => \Monolog\Formatter\JsonFormatter::class,
],
```

### 新しいログメソッドの追加
`app/Helpers/StructuredLogger.php` に新しいメソッドを追加：

```php
public static function custom(string $type, array $data = []): void
{
    $logData = [
        'type' => $type,
        'data' => $data,
        'user_id' => Auth::id(),
        'timestamp' => now()->toISOString(),
    ];

    Log::channel('structured')->info("Custom: {$type}", $logData);
}
```

## 🛠️ トラブルシューティング

### ログファイルが作成されない
1. `storage/logs` ディレクトリの権限を確認
2. `php artisan config:clear` を実行
3. `composer dump-autoload` を実行

### VSCodeでログが表示されない
1. 推奨拡張機能をインストール
2. VSCodeを再起動
3. タスクが正しく設定されているか確認

### ログの検索が遅い
1. ログファイルのサイズを確認
2. 古いログファイルを削除
3. ローテーション設定を調整

## 📈 パフォーマンス監視

構造化ログを使用して以下の指標を監視できます：

- レスポンスタイム
- エラー率
- ユーザーアクションの頻度
- データベース操作の統計
- ユーザー別の使用状況

これらの情報を定期的に確認することで、アプリケーションの健全性を監視できます。 