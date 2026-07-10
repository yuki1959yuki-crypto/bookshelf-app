# 環境構築手順

本プロジェクトのセットアップは、以下の手順で行ってください。

---

# 1. プロジェクト作成と Sail 導入

## Laravelプロジェクト作成（Laravel 10.x）

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
> MySQLコンテナの起動でエラーが発生する場合は、`compose.yaml` の `mysql` サービスに以下を追加してください。

```yaml
platform: "linux/amd64"
```

---

# 2. 設定と起動

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

# 3. アプリケーション構築

## フロントエンドセットアップ

```bash
sail npm install
```

```bash
sail npm install alpinejs
```

```bash
sail npm install -D tailwindcss@^3.4.0 @tailwindcss/forms postcss autoprefixer
```

```bash
sail npx tailwindcss init -p
```

> **補足**
>
> - 指定された `tailwind.config.js` の設定を反映してください。
> - 指定リポジトリから `resources` ディレクトリをインポートしてください。

## アプリケーションキー生成

```bash
sail artisan key:generate
```

## マイグレーション・シーディング

```bash
sail artisan migrate --seed
```

---

# 4. 注意事項

## 言語設定

`config/app.php` の `locale` を `ja` に変更してください。

また、`lang/ja/` 配下へメッセージファイルを**手動で配置**してください。

> **パッケージの導入は禁止**です。

## 脆弱性確認

提出前に必ず以下を実行し、既知の脆弱性が存在しないことを確認してください。

```bash
composer audit
```