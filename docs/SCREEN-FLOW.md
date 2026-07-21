flowchart TD
    %% トップ・一覧系
    Top["📚 書籍一覧（トップ）"]
    Ranking["🏆 ランキング画面"]
    Fav["⭐ お気に入り一覧画面"]
    GenreList["🏷️ ジャンル一覧画面"]
    GenreDetail["📂 ジャンル詳細画面"]

    %% 詳細・フォーム系
    BookDetail["📖 書籍詳細画面"]
    BookEdit["✏️ 書籍編集画面"]
    BookCreate["➕ 書籍登録画面"]
    ReviewEdit["💬 レビュー編集画面"]
    GenreCreate["➕ ジャンル登録画面"]
    GenreEdit["✏️ ジャンル編集画面"]

    %% 認証系
    Login["🔑 ログイン画面"]
    Register["📝 会員登録画面"]

    %% 遷移関係
    Top -->|書籍タイトル| BookDetail
    Top -->|「書籍を登録」or「書籍登録」| BookCreate
    Ranking -->|書籍タイトル| BookDetail
    Fav -->|書籍タイトル| BookDetail

    %% 書籍登録画面
    BookCreate -->|「登録」ボタン<br>『書籍を登録しました』| Top
    BookCreate -->|「キャンセル」ボタン| Top
    BookCreate -.->|※未認証時| Login

    %% 書籍詳細画面
    BookDetail -->|「一覧に戻る」ボタン| Top
    BookDetail -->|書籍「編集」ボタン| BookEdit
    BookDetail -->|書籍「削除」ボタン<br>『書籍を削除しました』| Top
    BookDetail -->|レビュー「投稿する」ボタン<br>『レビューを投稿しました』| Top
    BookDetail -->|自分のレビュー「編集」ボタン| ReviewEdit
    BookDetail -->|自分のレビュー「削除」ボタン<br>『レビューを削除しました』| Top

    %% 書籍編集画面
    BookEdit -->|「更新」ボタン<br>『書籍を編集しました』| BookDetail

    %% レビュー編集画面
    ReviewEdit -->|「更新する」ボタン<br>『レビューを更新しました』| BookDetail
    ReviewEdit -->|「キャンセル」ボタン| BookDetail

    %% ジャンル関連
    GenreList -->|「ジャンルを登録」ボタン| GenreCreate
    GenreList -->|ジャンル名| GenreDetail
    GenreList -->|「編集」ボタン| GenreEdit
    
    GenreCreate -->|「登録」ボタン<br>『ジャンルを登録しました』| GenreList
    GenreCreate -->|「キャンセル」ボタン| GenreList

    GenreDetail -->|「←書籍一覧へ戻る」ボタン| Top
    GenreDetail -->|書籍タイトル| BookDetail

    GenreEdit -->|「更新」ボタン<br>『ジャンル名を更新しました』| GenreList
    GenreEdit -->|「キャンセル」ボタン| GenreList
    GenreEdit -->|「削除」ボタン<br>『書籍を削除しました』| GenreList

    %% 認証・会員関連
    Login -->|「ログイン」ボタン| Top
    Login -->|「アカウントをお持ちでない方」| Register
    Register -->|「登録」ボタン<br>『会員登録が完了しました。ログインしてください。』| Login
    Register -->|「アカウントをお持ちの方」| Login
    Top -.->|「ログアウト」ボタン| Login
