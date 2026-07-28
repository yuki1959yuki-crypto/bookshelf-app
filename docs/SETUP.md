# 環境構築手順

本プロジェクトのセットアップは、以下の手順で行ってください。

---

# 1. Laravelプロジェクトの作成

## Laravelプロジェクト作成（Laravel 10.x）

> **注意**
>
> `curl -s "https://laravel.build/..."` は最新版の Laravel をインストールするため、本プロジェクトでは使用しません。

```bash
docker run --rm -u "$(id -u):$(id -g)" \
-v "$(pwd):/var/www/html" \
-w /var/www/html \
-e COMPOSER_CACHE_DIR=/tmp/composer_cache \
laravelsail/php82-composer:latest \
composer create-project laravel/laravel:^10.0 bookshelf-app
```

## プロジェクトディレクトリへ移動

```bash
cd bookshelf-app
```

---

# 2. Laravel Sail のインストール

## Sail のインストール

```bash
docker run --rm -u "$(id -u):$(id -g)" \
-v "$(pwd):/var/www/html" \
-w /var/www/html \
-e COMPOSER_CACHE_DIR=/tmp/composer_cache \
laravelsail/php82-composer:latest \
composer require laravel/sail --dev
```

## Sail のセットアップ

```bash
docker run --rm -u "$(id -u):$(id -g)" \
-v "$(pwd):/var/www/html" \
-w /var/www/html \
-e COMPOSER_CACHE_DIR=/tmp/composer_cache \
laravelsail/php82-composer:latest \
php artisan sail:install --with=mysql
```

> **Apple Silicon（M1 / M2 / M3）Mac を利用している場合**
>
> MySQLコンテナの起動時に `no matching manifest for linux/arm64/v8` エラーが発生する場合は、`compose.yaml` の `mysql` サービスに以下を追加してください。

```yaml
platform: "linux/amd64"
```

---

# 3. 環境設定

## .env ファイルの設定

`.env` を開き、以下のデータベース設定になっていることを確認してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

> **重要**
>
> `DB_HOST` は `localhost` や `127.0.0.1` ではなく、Dockerコンテナ名である `mysql` を指定してください。

## phpMyAdmin の追加

`compose.yaml` の `mysql` サービス定義の下に、以下を追加してください。

```yaml
phpmyadmin:
  image: "phpmyadmin:latest"
  ports:
    - "${FORWARD_PHPMYADMIN_PORT:-8080}:80"
  environment:
    PMA_HOST: mysql
    PMA_USER: "${DB_USERNAME}"
    PMA_PASSWORD: "${DB_PASSWORD}"
  networks:
    - sail
  depends_on:
    - mysql
```

---

# 4. Sail の起動

## Sail の起動

```bash
./vendor/bin/sail up -d
```

## Sail エイリアスの設定

```bash
echo "alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'" >> ~/.zshrc
source ~/.zshrc
```

---

# 5. フロントエンドのセットアップ

## NPM パッケージのインストール

```bash
sail npm install
```

## Alpine.js のインストール

```bash
sail npm install alpinejs
```

## Tailwind CSS のインストール

```bash
sail npm install -D tailwindcss@^3.4.0 @tailwindcss/forms postcss autoprefixer
```

## Tailwind CSS の初期設定

```bash
sail npx tailwindcss init -p
```

## プロジェクトファイルの設定

以下の設定を行ってください。

- `tailwind.config.js` を指定された内容へ変更
- `coachtech-prepared-file/Preparedblade-mockcase-BookShelf` リポジトリ（Basicブランチ）の `resources` ディレクトリをプロジェクトへ配置

## Vite の起動

```bash
sail npm run dev
```

> **注意**
>
> 開発中は Vite 開発サーバーを起動した状態で作業してください。

---

# 6. アプリケーションの初期設定

## アプリケーションキーの生成

```bash
sail artisan key:generate
```

## データベースの作成

```bash
sail artisan migrate --seed
```

データベースを初期化して再作成する場合は、以下を実行してください。

```bash
sail artisan migrate:fresh --seed
```

---

# 7. 注意事項

## 日本語設定

`config/app.php` の `locale` を `ja` に変更してください。

また、`lang/ja/` 配下へ日本語メッセージファイルを**手動で配置**してください。

> **パッケージの導入は禁止**
>
> `laravel-lang/lang` などの `laravel-lang/*` 系パッケージは導入しないでください。

## 脆弱性確認

提出前に以下のコマンドを実行し、既知の脆弱性が存在しないことを確認してください。

```bash
composer audit
```
