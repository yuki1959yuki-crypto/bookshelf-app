# ER図

```mermaid
erDiagram

    users {
        bigint id PK
        varchar name
        varchar email UK
        timestamp email_verified_at
        varchar password
        varchar remember_token
        timestamp created_at
        timestamp updated_at
    }

    books {
        bigint id PK
        bigint user_id FK
        varchar title
        varchar author
        varchar isbn UK nullable
        date published_date nullable
        text description
        varchar image_url
        timestamp created_at
        timestamp updated_at
    }

    genres {
        bigint id PK
        varchar name UK
        timestamp created_at
        timestamp updated_at
    }

    book_genre {
        bigint book_id PK,FK
        bigint genre_id PK,FK
    }

    reviews {
        bigint id PK
        bigint book_id FK
        bigint user_id FK
        tinyint rating
        text comment
        timestamp created_at
        timestamp updated_at
    }

    favorites {
        bigint user_id PK,FK
        bigint book_id PK,FK
        timestamp created_at
    }

    review_likes {
        bigint review_id PK,FK
        bigint user_id PK,FK
    }

    reading_plans {
        bigint id PK
        bigint user_id FK
        bigint book_id FK
        date target_date
        varchar status
        timestamp created_at
        timestamp updated_at
    }

    notifications {
        uuid id PK
        varchar type
        bigint notifiable_id FK
        text data
        timestamp read_at
        timestamp created_at
        timestamp updated_at
    }

    users ||--o{ books : "creates"
    users ||--o{ reviews : "writes"
    users ||--o{ favorites : "favorites"
    users ||--o{ review_likes : "likes"
    users ||--o{ reading_plans : "creates"
    users ||--o{ notifications : "receives"

    books ||--o{ reviews : "has"
    books ||--o{ favorites : "is favorited"
    books ||--o{ reading_plans : "planned"
    books ||--o{ book_genre : ""

    genres ||--o{ book_genre : ""

    reviews ||--o{ review_likes : "has"
```

## 補足

- `book_genre`、`favorites`、`review_likes` は複合主キーを持つ中間テーブルです。
- `books.isbn` および `books.published_date` は `NULL` を許可しています。
- `notifications` は Laravel 標準の通知テーブルを利用しています。ER図では受信ユーザーとの関連のみを表しています。