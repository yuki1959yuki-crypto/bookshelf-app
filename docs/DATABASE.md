# データベース設計

## users

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| id | bigint unsigned | ○ | - | ○ | ユーザーID（Auto Increment） |
| name | varchar(255) | - | - | ○ | ユーザー名 |
| email | varchar(255) | - | - | ○ | メールアドレス（UNIQUE） |
| email_verified_at | timestamp | - | - | - | メール確認日時 |
| password | varchar(255) | - | - | ○ | ハッシュ化されたパスワード |
| remember_token | varchar(100) | - | - | - | ログイン保持トークン |
| created_at | timestamp | - | - | - | 作成日時 |
| updated_at | timestamp | - | - | - | 更新日時 |

> **補足**  
> Laravel Fortify の `two_factor_secret`、`two_factor_recovery_codes`、`two_factor_confirmed_at` は本システムでは使用していません。

---

## books

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| id | bigint unsigned | ○ | - | ○ | 書籍ID |
| user_id | bigint unsigned | - | ○ | ○ | 登録ユーザーID |
| title | varchar(255) | - | - | ○ | 書籍タイトル |
| author | varchar(255) | - | - | ○ | 著者名 |
| isbn | varchar(13) | - | - | - | ISBN-13（UNIQUE） |
| published_date | date | - | - | - | 出版日 |
| description | text | - | - | ○ | 書籍概要 |
| image_url | varchar(2048) | - | - | ○ | 書籍画像URL |
| created_at | timestamp | - | - | - | 作成日時 |
| updated_at | timestamp | - | - | - | 更新日時 |

---

## genres

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| id | bigint unsigned | ○ | - | ○ | ジャンルID |
| name | varchar(255) | - | - | ○ | ジャンル名（UNIQUE） |
| created_at | timestamp | - | - | - | 作成日時 |
| updated_at | timestamp | - | - | - | 更新日時 |

---

## book_genre

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| book_id | bigint unsigned | ○ | ○ | ○ | 書籍ID |
| genre_id | bigint unsigned | ○ | ○ | ○ | ジャンルID |

> **複合主キー:** (`book_id`, `genre_id`)

---

## reviews

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| id | bigint unsigned | ○ | - | ○ | レビューID |
| book_id | bigint unsigned | - | ○ | ○ | 対象書籍ID |
| user_id | bigint unsigned | - | ○ | ○ | 投稿ユーザーID |
| rating | tinyint unsigned | - | - | ○ | 評価（1〜5） |
| comment | text | - | - | ○ | レビュー本文 |
| created_at | timestamp | - | - | - | 作成日時 |
| updated_at | timestamp | - | - | - | 更新日時 |

---

## favorites

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| user_id | bigint unsigned | ○ | ○ | ○ | ユーザーID |
| book_id | bigint unsigned | ○ | ○ | ○ | 書籍ID |
| created_at | timestamp | - | - | ○ | お気に入り登録日時 |

> **複合主キー:** (`user_id`, `book_id`)

---

## review_likes

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| review_id | bigint unsigned | ○ | ○ | ○ | レビューID |
| user_id | bigint unsigned | ○ | ○ | ○ | ユーザーID |

> **複合主キー:** (`review_id`, `user_id`)

---

## reading_plans

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| id | bigint unsigned | ○ | - | ○ | 読書計画ID |
| user_id | bigint unsigned | - | ○ | ○ | 計画作成ユーザーID |
| book_id | bigint unsigned | - | ○ | ○ | 対象書籍ID |
| target_date | date | - | - | ○ | 読了予定日 |
| status | varchar(255) | - | - | ○ | 計画状態 |
| created_at | timestamp | - | - | - | 作成日時 |
| updated_at | timestamp | - | - | - | 更新日時 |

---

## notifications

| カラム名 | 型 | PK | FK | NOT NULL | 備考 |
|----------|----|:--:|:--:|:--------:|------|
| id | uuid | ○ | - | ○ | 通知ID |
| type | varchar(255) | - | - | ○ | 通知クラス名 |
| notifiable_type | varchar(255) | - | - | ○ | 通知対象モデル |
| notifiable_id | bigint unsigned | - | - | ○ | 通知対象ユーザーID |
| data | text | - | - | ○ | 通知内容（JSON） |
| read_at | timestamp | - | - | - | 既読日時 |
| created_at | timestamp | - | - | - | 作成日時 |
| updated_at | timestamp | - | - | - | 更新日時 |