# バリデーション設計

# 書籍登録（POST /books）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| title | `required`, `string`, `max:255` | タイトルは必須です。255文字以内で入力してください。 | |
| author | `required`, `string`, `max:255` | 著者名は必須です。255文字以内で入力してください。 | |
| isbn | `required`, `string`, `size:13`, `unique:books,isbn` | ISBNは13桁の半角数字で入力してください。このISBNは既に登録されています。 | |
| published_date | `required`, `date` | 出版日を入力してください。 | |
| genre_ids | `required`, `array`, `min:1` | ジャンルを少なくとも1つ選択してください。 | |
| image_url | `nullable`, `url`, `max:2048` | 画像URLを入力する場合は、正しいURL形式で入力してください。 | |
| description | `nullable`, `string` | （デフォルトのメッセージを利用） | |

**備考**

- FormRequestクラス（`StoreBookRequest`）を使用する。
- `messages()` メソッドで日本語メッセージを定義する。

## ★ 応用機能

応用機能では、一部の入力項目を任意入力に変更する。

| 項目 | 変更内容 |
|------|----------|
| isbn | `required` → `nullable` |
| published_date | `required` → `nullable` |

---

# 書籍編集（PUT /books/{book}）

> ※ ISBN以外は、書籍登録と同等のバリデーションを行う。

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| isbn | `required`, `string`, `size:13`, `unique:books,isbn,{自身のID}` | ISBNは13桁の半角数字で入力してください。このISBNは既に登録されています。 | 自分自身のISBNは重複エラーから除外する設定が必要 |

**備考**

- FormRequestクラス：`app/Http/Requests/UpdateBookRequest.php`
- `messages()` メソッドで日本語を定義する。

---

# 公開API 書籍一覧（GET /api/books）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| keyword | `nullable`, `string`, `max:255` | 検索キーワードは255文字以内で入力してください。 | DoS攻撃防止 |
| genre_id | `nullable`, `integer`, `exists:genres,id` | 指定されたジャンルは存在しません。 | |
| page | `nullable`, `integer`, `min:1` | （デフォルトのメッセージを利用） | ページネーション用 |
| per_page | `nullable`, `integer`, `min:1`, `max:100` | 取得件数は1〜100の間で指定してください。 | 1回で取得できる上限を制限し、サーバー負荷を防ぐ（仮） |

**備考**

- FormRequestクラス：`app/Http/Requests/IndexBookRequest.php`
- `messages()` メソッドで日本語を定義する。

---

# 公開API 書籍登録（POST /api/v1/books）

> ※ user_id以外はWeb版と同等のバリデーションを行う。

...

**備考**

- FormRequestクラス：`StoreBookApiRequest`

## ★ 応用機能

Laravel Sanctumを導入するため、`user_id`はリクエストから受け取らず、認証ユーザー（`Auth::id()`）から取得する。


---

# 公開API 書籍編集（PUT /api/books/{book}）

> ※ ISBN以外は公開API書籍登録と同等のバリデーションを行う。

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| isbn | `required`, `string`, `size:13`, `unique:books,isbn,{自身のID}` | ISBNは13桁の半角数字で入力してください。このISBNは既に登録されています。 | 自分自身のISBNは重複エラーから除外する設定が必要 |

**備考**

- FormRequestクラス：`app/Http/Requests/UpdateBookApiRequest.php`
- `messages()` メソッドで日本語を定義する。

---

# レビュー投稿（POST /books/{book}/reviews）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| rating | `required`, `integer`, `min:1`, `max:5` | 評価は1〜5の星を選択してください。 | デザインUI参照 |
| comment | `required`, `string`, `max:1000` | コメントは必須です。1000文字以内で入力してください。 | 一般的なレビューとして十分な量である1000文字制限とした（仮） |

**備考**

- FormRequestクラス：`app/Http/Requests/StoreReviewRequest.php`
- `messages()` メソッドで日本語を定義する。

---

# レビュー編集（PUT /books/{book}/reviews/{review}）

> ※ レビュー投稿と同等のバリデーションを行う。

**備考**

- FormRequestクラス：`app/Http/Requests/UpdateReviewRequest.php`
- `messages()` メソッドで日本語を定義する。

---

# ジャンル登録（POST /genres）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| name | `required`, `string`, `max:255`, `unique:genres,name` | ジャンル名は必須です。255文字以内で入力してください。このジャンル名は既に登録されています。 | ジャンル名の一意性を検証 |

**備考**

- FormRequestクラス：`app/Http/Requests/StoreGenreRequest.php`
- `messages()` メソッドで日本語を定義する。

---

# ジャンル編集（PUT /genres/{genre}）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| name | `required`, `string`, `max:255`, `unique:genres,name,{自身のID}` | ジャンル名は必須です。255文字以内で入力してください。このジャンル名は既に登録されています。 | 自分自身のジャンル名は重複エラーから除外する設定が必要 |

**備考**

- FormRequestクラス：`app/Http/Requests/UpdateGenreRequest.php`
- `messages()` メソッドで日本語を定義する。

---

# 会員登録（POST /register）

> Laravel Fortifyを使用する。Fortify標準の `app/Actions/Fortify/CreateNewUser.php` 内の `Validator::make` メソッドに記述する。

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| name | `required`, `string`, `max:255` | お名前は必須項目です。お名前は255文字以内で入力してください。 | |
| email | `required`, `string`, `email`, `max:255`, `unique:users,email` | メールアドレスは必須項目です。メールアドレスの形式が正しくありません。指定されたメールアドレスは既に登録されています。 | メールアドレスの形式チェックと一意性を検証 |
| password | `required`, `string`, `min:8`, `confirmed` | パスワードは8文字以上で入力してください。確認用パスワードと一致しません。 | Fortify標準の `PasswordValidationRules` トレイトを使用する |
| password_confirmation | - | - | `confirmed`ルールにより連動して判定 |

---

# ログイン（POST /login）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| email | `required`, `string`, `email` | メールアドレスは必須項目です。ログイン情報が登録されていません。 | Fortify標準の必須チェック・形式チェック |
| password | `required`, `string` | パスワードは必須項目です。 | Fortify標準の必須チェック |

**備考**

- プロジェクト全体の言語ファイル（`lang/ja/validation.php`）を設定し、システム全体のエラーメッセージを一括で日本語化する。

---
# ★ 読書計画作成（POST /reading-plans）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|-------------------|------|
| book_id | `required`, `exists:books,id` | 書籍を選択してください。 | |
| target_date | `required`, `date`, `after_or_equal:today` | 本日以降の日付を入力してください。 | |
| status | `nullable`, `in:planned,completed,overdue` | 状態が正しくありません。 | |

**備考**

- FormRequestクラス：`StoreReadingPlanRequest`
- 編集時も同等のバリデーションを行う。
- 重複登録や編集制限は要件に応じて実装する。

