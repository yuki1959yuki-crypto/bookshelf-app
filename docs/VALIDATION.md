# バリデーション設計

## 書籍登録（POST /books）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| title | required, string, max:255 | タイトルは必須です。255文字以内で入力してください。 | |
| author | required, string, max:255 | 著者名は必須です。255文字以内で入力してください。 | |
| isbn | required, string, size:13, unique:books,isbn | ISBNは13桁の半角数字で入力してください。このISBNは既に登録されています。 | 応用で `nullable` に変更予定 |
| published_date | required, date | 出版日を入力してください。 | カレンダーから選択する設計（案）。応用で `nullable` に変更予定 |
| genre_ids | required, array, min:1 | ジャンルを少なくとも1つ選択してください。 | |
| image_url | nullable, url, max:2048 | 画像URLを入力する場合は、正しいURL形式で入力してください。 | |
| description | nullable, string | （デフォルトメッセージを利用） | 任意入力のためカスタムメッセージは作成しない |

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/StoreBookRequest.php`）へ切り出す。
- コントローラーの責務を軽減する設計とする。
- `messages()` メソッドで日本語メッセージを定義する。

---

## 書籍編集（PUT /books/{book}）

※ISBN以外は書籍登録と同等のバリデーションを行う。

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| isbn | required, string, size:13, unique:books,isbn,{自身のID} | ISBNは13桁の半角数字で入力してください。このISBNは既に登録されています。 | 自分自身のISBNは重複チェック対象から除外する |

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/UpdateBookRequest.php`）へ切り出す。
- `messages()` メソッドで日本語メッセージを定義する。

---

# 公開API

## 書籍一覧（GET /api/books）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| keyword | nullable, string, max:255 | 検索キーワードは255文字以内で入力してください。 | DoS攻撃防止 |
| genre_id | nullable, integer, exists:genres,id | 指定されたジャンルは存在しません。 | |
| page | nullable, integer, min:1 | （デフォルトメッセージを利用） | ページネーション用 |
| per_page | nullable, integer, min:1, max:100 | 取得件数は1〜100の間で指定してください。 | 取得件数の上限を設け、サーバー負荷を防ぐ（仮） |

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/IndexBookRequest.php`）へ切り出す。
- `messages()` メソッドで日本語メッセージを定義する。

---

## 公開API 書籍登録（POST /api/books）

※`user_id`以外はWeb版の書籍登録と同等のバリデーションを行う。

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| user_id | required, integer, exists:users,id | 登録者IDが正しくありません。 | 登録者IDの妥当性を検証 |

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/StoreBookApiRequest.php`）へ切り出す。
- `messages()` メソッドで日本語メッセージを定義する。

---

## 公開API 書籍編集（PUT /api/books/{book}）

※ISBN以外は公開API書籍登録と同等のバリデーションを行う。

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| isbn | required, string, size:13, unique:books,isbn,{自身のID} | ISBNは13桁の半角数字で入力してください。このISBNは既に登録されています。 | 自分自身のISBNは重複チェック対象から除外する |

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/UpdateBookApiRequest.php`）へ切り出す。
- `messages()` メソッドで日本語メッセージを定義する。

---

# レビュー

## レビュー投稿（POST /books/{book}/reviews）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| rating | required, integer, min:1, max:5 | 評価は1〜5の星を選択してください。 | デザインUI参照 |
| comment | required, string, max:1000 | コメントは必須です。1000文字以内で入力してください。 | 一般的なレビューとして十分な量である1000文字制限（仮） |

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/StoreReviewRequest.php`）へ切り出す。
- `messages()` メソッドで日本語メッセージを定義する。

---

## レビュー編集（PUT /books/{book}/reviews/{review}）

※レビュー投稿と同等のバリデーションを行う。

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/UpdateReviewRequest.php`）へ切り出す。
- `messages()` メソッドで日本語メッセージを定義する。

---

# ジャンル

## ジャンル登録（POST /genres）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| name | required, string, max:255, unique:genres,name | ジャンル名は必須です。255文字以内で入力してください。このジャンル名は既に登録されています。 | ジャンル名の一意性を検証 |

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/StoreGenreRequest.php`）へ切り出す。
- `messages()` メソッドで日本語メッセージを定義する。

---

## ジャンル編集（PUT /genres/{genre}）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| name | required, string, max:255, unique:genres,name,{自身のID} | ジャンル名は必須です。255文字以内で入力してください。このジャンル名は既に登録されています。 | 自分自身のジャンル名は重複チェック対象から除外する |

### 実装方針

- バリデーションは **FormRequest**（`app/Http/Requests/UpdateGenreRequest.php`）へ切り出す。
- `messages()` メソッドで日本語メッセージを定義する。

---

# 認証

## 会員登録（POST /register）

> Laravel Fortifyを使用する。  
> Fortify標準の `app/Actions/Fortify/CreateNewUser.php` の `Validator::make()` に記述する。

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| name | required, string, max:255 | 名前は必須です。255文字以内で入力してください。 | |
| email | required, string, email, max:255, unique:users,email | メールアドレスは正しく入力してください。このメールアドレスは既に登録されています。 | メールアドレスの形式・一意性を検証 |
| password | required, string, min:8, confirmed | パスワードは8文字以上で入力してください。確認用パスワードと一致しません。 | Fortify標準の `PasswordValidationRules` トレイトを使用 |

---

## ログイン（POST /login）

| 項目 | バリデーションルール | エラーメッセージ案 | 備考 |
|------|----------------------|--------------------|------|
| email | required, string, email | メールアドレスを入力してください。 | Fortify標準の必須・形式チェック |
| password | required, string | パスワードを入力してください。 | Fortify標準の必須チェック |

---

# 共通実装方針

- プロジェクト全体の日本語エラーメッセージは **`lang/ja/validation.php`** に定義し、システム全体で共通利用する。
- 各 `FormRequest` クラスでは、必要なカスタムメッセージのみ `messages()` メソッドに定義する。
- バリデーションロジックはコントローラーではなく `FormRequest` に集約し、責務を分離する。