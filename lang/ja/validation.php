<?php

return [
    'required' => ':attributeは必須項目です。',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'unique' => '指定された:attributeはすでに登録されています。',
    'email' => ':attributeの形式が正しくありません。',

    'attributes' => [
        'name' => 'ユーザー名',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'title' => '書籍タイトル',
        'author' => '著者',
        'isbn' => 'ISBN',
        'published_date' => '出版日',
        'description' => '説明',
        'comment' => 'コメント',
        'rating' => '評価',
    ],
];
