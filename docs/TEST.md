## テストの実施手順

Laravel Sail（Docker環境）を使用して機能テスト（Feature Test）およびユニットテストを実装しています。
※ 以下のコマンドは、VS Codeのターミナル（Ubuntu環境）から実行してください。

### 1. Sailコンテナの起動
テストを実行する前に、Sail環境が起動していることを確認してください。起動していない場合は、以下のコマンドでバックグラウンド起動します。

```bash
# Sailコンテナの起動
./vendor/bin/sail up -d
```

### 2. テストの実行
すべてのテストを一括で実行する場合、および特定のテストファイルのみを実行する場合は、以下のコマンドを実行します。

```bash
# すべてのテストを一括で実行
./vendor/bin/sail artisan test

# 特定のテストファイルのみを実行する場合
（例）./vendor/bin/sail artisan test tests/Feature/Api/BookApiAuthTest.php
```

### テスト仕様書一覧

#### 全体・共通セキュリティ
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | CSRFトークンが存在しない、または不正な状態でPOST/PUT/DELETEリクエストを送信すると、419エラー（またはエラー画面）になる | セキュリティ / 異常系 | `tests/Feature/Security/CsrfTest.php` |
| 2 | 全フォームにおいて、XSS攻撃用のスクリプトタグ（例: `<script>alert(1)</script>`）を含む文字列を入力・保存しても、画面表示時にエスケープされて実行されない | セキュリティ / 正常系 | `tests/Feature/Security/XssTest.php` |

#### 全体・共通（アクセス制御）
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 3 | 未認証のゲストユーザーが保護されたルート（マイページ等）へアクセスした際、ログイン画面へリダイレクトされる | 認証・リダイレクト / 正常系 | `tests/Feature/AuthAndAccessTest.php` |
| 4 | 認証済みのユーザーが保護されたルートへ正常にアクセスできる | 認証 / 正常系 | `tests/Feature/AuthAndAccessTest.php` |

#### 単体テスト (Unit) - モデル
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | User モデルから Book, Review, Favorite へのリレーションが正しく取得できる | リレーション / 正常系 | `tests/Unit/UserTest.php` |
| 2 | Book モデルから User, Genre, Review, Favorite へのリレーションが正しく取得できる | リレーション / 正常系 | `tests/Unit/BookTest.php` |
| 3 | Review モデルから User, Book, ReviewLike へのリレーションが正しく取得できる | リレーション / 正常系 | `tests/Unit/ReviewTest.php` |
| 4 | 書籍の平均評価（average_rating 等）を計算するモデルメソッドが期待通りの数値を返す | モデルロジック / 正常系 | `tests/Unit/BookTest.php` |

#### 会員登録画面 (/register)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）は会員登録画面を正常に表示できる | 動作 / 正常系 | `tests/Feature/Auth/RegistrationTest.php` |
| 2 | ログイン済みユーザーが会員登録画面にアクセスするとホーム画面（/books 等）へリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Auth/RegistrationTest.php` |
| 3 | 必要な入力項目（名前・メール・パスワード等）を正しく入力すると新規ユーザーが登録され、ログイン状態で指定の画面へ遷移する | 動作 / 正常系 | `tests/Feature/Auth/RegistrationTest.php` |
| 4 | 名前が空のまま送信するとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Auth/RegistrationTest.php` |
| 5 | メールアドレスが空、または不正なメール形式だとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Auth/RegistrationTest.php` |
| 6 | すでに登録されているメールアドレスで登録しようとすると重複エラーになる（unique違反） | DB制約 / 異常系 | `tests/Feature/Auth/RegistrationTest.php` |
| 7 | パスワードが空、または規定の文字数未満（例: 8文字未満）だとバリデーションエラーになる | 境界値 / 異常系 | `tests/Feature/Auth/RegistrationTest.php` |
| 8 | パスワードと確認用パスワード（password_confirmation）が一致しないとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Auth/RegistrationTest.php` |

#### ログイン画面 (/login)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）はログイン画面を正常に表示できる | 動作 / 正常系 | `tests/Feature/Auth/AuthenticationTest.php` |
| 2 | ログイン済みユーザーがログイン画面にアクセスするとホーム画面（/books 等）へリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Auth/AuthenticationTest.php` |
| 3 | 正しいメールアドレスとパスワードを入力すると認証に成功し、ホーム画面へ遷移・ログイン状態になる | 認証 / 正常系 | `tests/Feature/Auth/AuthenticationTest.php` |
| 4 | 登録されていないメールアドレスを入力すると認証エラー（「ログイン情報が一致しません」等）になる | 認証エラー / 異常系 | `tests/Feature/Auth/AuthenticationTest.php` |
| 5 | 正しいメールアドレスだがパスワードが誤っていると認証エラーになり、ログインできない | 認証エラー / 異常系 | `tests/Feature/Auth/AuthenticationTest.php` |
| 6 | メールアドレスまたはパスワードが空のまま送信するとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Auth/AuthenticationTest.php` |
| 7 | ログイン状態のユーザーがログアウト処理を実行すると、未ログイン状態になりログイン画面へ遷移する | 動作 / 正常系 | `tests/Feature/Auth/AuthenticationTest.php` |

#### 書籍一覧画面 (/books)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）が書籍一覧画面へアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Books/BookIndexTest.php` |
| 2 | ログイン済みユーザーは書籍一覧画面を表示でき、登録済みの書籍一覧（タイトル・著者等）が表示される | 表示 / 正常系 | `tests/Feature/Books/BookIndexTest.php` |
| 3 | 登録書籍が1件も存在しない場合でも、エラーにならず「書籍がありません」等のメッセージが表示される | 境界値 / 正常系 | `tests/Feature/Books/BookIndexTest.php` |
| 4 | 書籍が多数存在する場合、ページネーションが正しく機能する（指定件数ごとにページが分割される） | 境界値 / 正常系 | `tests/Feature/Books/BookIndexTest.php` |

#### 書籍詳細画面 (/books/{id})
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）が書籍詳細画面へアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Books/BookShowTest.php` |
| 2 | ログイン済みユーザーは書籍詳細画面を表示でき、書籍情報・レビュー一覧・平均評価・お気に入り状態が正しく表示される | 表示 / 正常系 | `tests/Feature/Books/BookShowTest.php` |
| 3 | 存在しない書籍ID（例: /books/99999）にアクセスすると404エラーが返る | 404 / 異常系 | `tests/Feature/Books/BookShowTest.php` |

#### 書籍登録画面 (/books/create)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）が書籍登録画面にアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Books/BookCreateTest.php` |
| 2 | ログイン済みユーザーは書籍登録画面を表示できる | 動作 / 正常系 | `tests/Feature/Books/BookCreateTest.php` |
| 3 | 必要な入力項目（タイトル・著者・ジャンル等）を正しく入力して送信すると、新規書籍が作成されDBに保存される | 登録 / 正常系 | `tests/Feature/Books/BookCreateTest.php` |
| 4 | タイトルや著者が空、または文字数制限を超えるとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Books/BookCreateTest.php` |
| 5 | ジャンルが未選択（または存在しないジャンルID）だとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Books/BookCreateTest.php` |

#### 書籍編集・削除画面 (/books/{id}/edit)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）が書籍編集画面にアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Books/BookEditTest.php` |
| 2 | 投稿者本人は自身の書籍編集画面に正常にアクセスできる | 動作 / 正常系 | `tests/Feature/Books/BookEditTest.php` |
| 3 | 他人の書籍編集画面にアクセスすると403エラーが返る（Policy認可） | 認可(Policy) / 異常系 | `tests/Feature/Books/BookEditTest.php` |
| 4 | 投稿者本人が正しく項目を変更して更新すると、DBの内容が書き換わり正常に保存される | 更新 / 正常系 | `tests/Feature/Books/BookEditTest.php` |
| 5 | 存在しない書籍ID（例: /books/99999/edit）にアクセスすると404エラーが返る | 404 / 異常系 | `tests/Feature/Books/BookEditTest.php` |
| 6 | 投稿者本人は自身の書籍を削除でき、関連データも含めて正しく処理される | 削除 / 正常系 | `tests/Feature/Books/BookEditTest.php` |

#### 書籍CRUD・ポリシー統合
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 7 | ユーザーによる書籍の基本的な作成・CRUDおよびポリシー制御が正常に行われる | CRUD・ポリシー / 正常系 | `tests/Feature/BookCrudAndPolicyTest.php` |

#### レビュー編集・削除画面
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）がレビュー編集画面にアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Reviews/ReviewEditTest.php` |
| 2 | 投稿者本人は自身のレビュー編集画面を表示できる | 動作 / 正常系 | `tests/Feature/Reviews/ReviewEditTest.php` |
| 3 | 他人のレビュー編集画面にアクセスすると403エラーが返る（Policy認可） | 認可(Policy) / 異常系 | `tests/Feature/Reviews/ReviewEditTest.php` |
| 4 | 投稿者本人はレビュー内容（評価・コメント等）を正常に更新できる | 更新 / 正常系 | `tests/Feature/Reviews/ReviewEditTest.php` |
| 5 | レビュー評価（星の数など）が未入力または不正な値（範囲外など）だとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Reviews/ReviewEditTest.php` |
| 6 | レビューコメントが空（または文字数制限オーバー）だとバリデーションエラーになる | 境界値 / 異常系 | `tests/Feature/Reviews/ReviewEditTest.php` |
| 7 | 存在しないレビューIDの編集画面にアクセスすると404エラーが返る | 404 / 異常系 | `tests/Feature/Reviews/ReviewEditTest.php` |
| 8 | 投稿者本人は自身のレビューを削除でき、削除後に書籍の平均評価等が再計算される | 削除・再計算 / 正常系 | `tests/Feature/Reviews/ReviewEditTest.php` |
| 9 | 1人のユーザーが同一の書籍に対して重複してレビュー投稿を行おうとするとエラーになる（1本1レビュー制） | 整合性 / 異常系 | `tests/Feature/Reviews/ReviewEditTest.php` |

#### いいね機能 (レビューに対するいいね)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）がいいね処理を実行するとログイン画面へリダイレクトされる（または401/403） | リダイレクト / 正常系 | `tests/Feature/Likes/ReviewLikeTest.php` |
| 2 | ログインユーザーは他人のレビューに対して「いいね」を登録できる | 登録 / 正常系 | `tests/Feature/Likes/ReviewLikeTest.php` |
| 3 | すでに「いいね」済みのレビューに対して再度「いいね」を押すと解除される（トグル動作） | トグル / 正常系 | `tests/Feature/Likes/ReviewLikeTest.php` |
| 4 | 同一レビューに重複して「いいね」の二重保存がされない（Unique制約） | DB制約 / 異常系 | `tests/Feature/Likes/ReviewLikeTest.php` |

#### お気に入り機能・一覧 (/favorites 等)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）がお気に入り一覧画面にアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Favorites/FavoriteTest.php` |
| 2 | ログインユーザーは自身がお気に入りに登録した書籍の一覧を表示できる | 表示 / 正常系 | `tests/Feature/Favorites/FavoriteTest.php` |
| 3 | 他のユーザーがお気に入りに登録した書籍は自分の一覧に表示されない（ユーザー間隔離） | セキュリティ / 正常系 | `tests/Feature/Favorites/FavoriteTest.php` |
| 4 | お気に入りを1件も登録していない場合、エラーにならず「お気に入りがありません」等の空メッセージが表示される | 境界値 / 正常系 | `tests/Feature/Favorites/FavoriteTest.php` |
| 5 | お気に入りボタンを押すことで登録／解除（トグル処理）が正しく行われ、一覧にリアルタイム反映される | トグル / 正常系 | `tests/Feature/Favorites/FavoriteTest.php` |
| 6 | すでにお気に入り登録済みの書籍に重ねて登録リクエストが送られても、重複レコードが作成されない（Unique制約） | DB制約 / 異常系 | `tests/Feature/Favorites/FavoriteTest.php` |

#### ランキング画面 (/ranking)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）でもランキング画面を表示できる（公開画面仕様の場合） | 動作 / 正常系 | `tests/Feature/Ranking/RankingTest.php` |
| 2 | 登録されている書籍が評価順（または登録数順）に正しく並んで表示される | 並び順 / 正常系 | `tests/Feature/Ranking/RankingTest.php` |
| 3 | 評価や登録数が同数の場合、期待通りの並び順（ID順や最新順など）で取得できる | 境界値 / 正常系 | `tests/Feature/Ranking/RankingTest.php` |
| 4 | レビューや読書データが1件も存在しない場合でも、エラーにならず空のランキングが表示される | 境界値 / 正常系 | `tests/Feature/Ranking/RankingTest.php` |
| 5 | TOP 10（指定件数）の書籍のみが正しく取得・表示される | 境界値 / 正常系 | `tests/Feature/Ranking/RankingTest.php` |

#### 公開API (認証不要エンドポイント)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | ゲスト権限で公開API（例: /api/books 等）にGETアクセスすると、200 OKと正しくフォーマットされたJSONデータが返る | API / 正常系 | `tests/Feature/Api/PublicApiTest.php` |
| 2 | 存在しないリソースの公開API（例: /api/books/99999）にアクセスすると、404 JSONエラーが返る | API / 異常系 | `tests/Feature/Api/PublicApiTest.php` |

#### 書籍API（CRUD・バリデーション）
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 認証済みの状態でAPI経由の書籍一覧取得が成功し、期待通りのJSON構造が返る | API / 正常系 | `tests/Feature/BookApiTest.php` |
| 2 | API経由で有効なデータを送信し、新規書籍が正常に登録されて201が返る | API / 正常系 | `tests/Feature/BookApiTest.php` |
| 3 | 不正なデータ（空のタイトル等）をAPI経由で送信した際、422ステータスと日本語のバリデーションエラーが返る | API / 異常系 | `tests/Feature/BookApiTest.php` |
| 4 | 登録済みの書籍をAPI経由で正常に削除でき、204ステータスが返る | API / 正常系 | `tests/Feature/BookApiTest.php` |

#### ジャンル一覧画面 (/genres)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）がジャンル一覧画面にアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 2 | ログインユーザーはジャンル一覧を表示できる | 表示 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 3 | 登録されているジャンル名およびそのジャンルに属する書籍数が正しく表示される | 表示 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 4 | ログインユーザーはジャンル登録・編集画面にアクセスできる（※制限なし） | 動作 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 5 | ジャンルデータが1件も存在しない場合でも、エラーにならず空の一覧が表示される | 境界値 / 正常系 | `tests/Feature/Genres/GenreTest.php` |

#### ジャンル登録画面 (/genres/create)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）がジャンル登録画面にアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 2 | ログインユーザーはジャンル登録画面にアクセスできる | 動作 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 3 | 正しいジャンル名を入力して送信するとジャンルが作成されDBに保存される | 登録 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 4 | ジャンル名が空だとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Genres/GenreTest.php` |
| 5 | すでに存在するジャンル名を登録しようとすると重複エラー（unique違反）になる | DB制約 / 異常系 | `tests/Feature/Genres/GenreTest.php` |

#### ジャンル詳細画面 (/genres/{id})
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）がジャンル詳細画面にアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 2 | ログインユーザーはジャンル詳細画面を表示でき、ジャンル名等が正しく表示される | 表示 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 3 | 指定したジャンルに紐づく書籍のみが一覧で正しく取得・表示される（別ジャンルの書籍が含まれない） | 絞り込み / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 4 | 該当ジャンルに登録されている書籍が1件もない場合でも、エラーにならず空の書籍一覧が表示される | 境界値 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 5 | 存在しないジャンルID（例: /genres/99999）にアクセスすると404エラーが返る | 404 / 異常系 | `tests/Feature/Genres/GenreTest.php` |

#### ジャンル編集・削除画面 (/genres/{id}/edit)
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 未ログインユーザー（ゲスト）がジャンル編集画面にアクセスするとログイン画面にリダイレクトされる | リダイレクト / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 2 | ログインユーザーはジャンル編集画面を正常に表示でき、既存のジャンル名がフォームに初期表示される | 動作 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 3 | ログインユーザーはジャンル名を正しい値に変更して更新（保存）できる | 更新 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 4 | ジャンル名を変更せずにそのまま保存した場合でも、自分自身の名前と重複しているというエラーにならず正常に更新できる | 境界値 / 正常系 | `tests/Feature/Genres/GenreTest.php` |
| 5 | 既に存在する他のジャンル名と同じ名前に変更して更新しようとすると重複エラー（unique違反）になる | DB制約 / 異常系 | `tests/Feature/Genres/GenreTest.php` |
| 6 | ジャンル名を空にして更新しようとするとバリデーションエラーになる | バリデーション / 異常系 | `tests/Feature/Genres/GenreTest.php` |
| 7 | 存在しないジャンルID（例: /genres/99999/edit）にアクセスすると404エラーが返る | 404 / 異常系 | `tests/Feature/Genres/GenreTest.php` |

#### ★ 検索・フィルタ
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | キーワード（書籍名や著者名の一部）を指定して書籍を部分一致検索できる | 検索 / 正常系 | `tests/Feature/Api/BookSearchTest.php` |
| 2 | ジャンルやタグなどのフィルター条件を組み合わせて書籍を絞り込み検索できる | 検索・フィルタ / 正常系 | `tests/Feature/Api/BookSearchTest.php` |

#### ★ ソート
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 評価順（降順・昇順）に書籍一覧を並び替えるクエリパラメータが正しく機能する | ソート / 正常系 | `tests/Feature/Api/BookSearchTest.php` |
| 2 | 登録日時（新着順・古い順）やタイトル順に書籍一覧を並び替えることができる | ソート / 正常系 | `tests/Feature/Api/BookSearchTest.php` |

#### ★ 書籍CRUD（認可詳細）
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 所有者（作成者）以外のユーザーが書籍の更新（PUT）を実行した際、BookPolicyにより 403 Forbidden が返る | 認可(Policy) / 異常系 | `tests/Feature/Api/BookApiAuthTest.php` |
| 2 | 所有者以外のユーザーが書籍の削除（DELETE）を実行した際、BookPolicyにより 403 Forbidden が返る | 認可(Policy) / 異常系 | `tests/Feature/Api/BookApiAuthTest.php` |

#### ★ ISBN検索
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | ISBNコードを指定して外部書籍検索APIを叩いた際、Http::fake() によりモックされた正確な書籍データを取得できる | 外部APIモック / 正常系 | `tests/Feature/Books/IsbnSearchTest.php` |
| 2 | 外部API側でエラーや該当データなし（404等）が返った場合でも、システムがクラッシュせず適切なエラーメッセージを返す | 外部APIモック / 異常系 | `tests/Feature/Books/IsbnSearchTest.php` |

#### ★ マイ読書レポート
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | ログインユーザー自身の読書データ（月別・年別の読了数やページ数）が正しく集計されて表示される | 集計ロジック / 正常系 | `tests/Feature/Reports/ReadingReportTest.php` |
| 2 | 他人の読書データが混ざらず、自分自身のレポートのみが正確に抽出される（ユーザー間隔離） | セキュリティ / 正常系 | `tests/Feature/Reports/ReadingReportTest.php` |

#### ★ Sanctum認証
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | APIリクエスト時にAuthorizationヘッダー（Bearerトークン）がない場合、401 Unauthorizedが返る | API認証 / 異常系 | `tests/Feature/Api/BookApiAuthTest.php` |
| 2 | 無効なフォーマットや期限切れの不正なトークンでAPIリクエストを送った場合、401 Unauthorizedが返る | API認証 / 異常系 | `tests/Feature/Api/BookApiAuthTest.php` |
| 3 | 有効なSanctumトークンを付与してリクエストを送った場合、正常に認証が通り 200/201 応答が返る | API認証 / 正常系 | `tests/Feature/Api/BookApiAuthTest.php` |

#### ★ 読書計画
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 読書計画（目標日・ページ数等）を正常に新規登録でき、DBに正しく保存される | CRUD / 正常系 | `tests/Feature/ReadingPlans/ReadingPlanTest.php` |
| 2 | 同一の書籍に対して、既に進行中のアクティブな読書計画がある場合に重複して登録しようとするとバリデーションエラーになる | 整合性 / 異常系 | `tests/Feature/ReadingPlans/ReadingPlanTest.php` |

#### ★ 読書計画（期限変更）
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 計画の投稿者本人は、自身の読書計画の目標日やステータスを正常に変更・更新できる | 計画変更 / 正常系 | `tests/Feature/ReadingPlans/ReadingPlanPolicyTest.php` |
| 2 | 本人以外のユーザーが他人の読書計画の期限やステータスを変更しようとした際、PlanPolicyによりブロックされ403が返る | 認可(Policy) / 異常系 | `tests/Feature/ReadingPlans/ReadingPlanPolicyTest.php` |

#### ★ リマインダーバッチ
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 日次バッチ（Artisanコマンド）を実行した際、通知対象条件に合致するユーザーに対してリマインダー通知が飛ぶ | バッチ・通知 / 正常系 | `tests/Feature/Console/ManageReadingPlansTest.php` |

#### ★ 自動失効バッチ
| # | テスト内容 | 観点 | 該当テストファイル（ファイルパス） |
| :---: | :--- | :--- | :--- |
| 1 | 日次バッチを実行した際、目標期日を過ぎた未着手の読書計画のステータスが自動的に「失効 (expired)」に更新される | バッチ・状態遷移 / 正常系 | `tests/Feature/Console/ManageReadingPlansTest.php` |
