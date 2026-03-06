# COACHTECH勤怠管理アプリ

## 概要
このプロジェクトは、勤怠管理のためのアプリです。一般ユーザー 又は 管理者ユーザー のマルチログイン方式です。<br>
一般ユーザーは、会員登録・ログイン・打刻による勤怠登録・月毎の勤怠確認・勤怠情報の修正申請を行うことができます。<br>
管理者ユーザーは、日毎又はユーザー毎の勤怠確認・ユーザー情報確認・勤怠情報の修正承認を行うことができます。
### 注意事項
- このアプリは日跨ぎ勤務には対応していません。
- 勤怠情報の修正について、勤務実績をなかった事にする修正は必要性が無いものとして対応していません。

## 環境構築
### Dockerビルド
1. git clone
2. docker-compose up -d --build
※MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。
### Laravel環境構築
1. docker-compose exec php bash
2. composer install
3. .env.exampleファイルから.envを作成し、環境変数を変更
   （DB項目の他に、MAIL_FROM_ADDRESSにもメールアドレスを設定してください）<br>
   例）MAIL_FROM_ADDRESS = "hello@example.com"
5. php artisan key:generate
6. php artisan migrate
7. php artisan db:seed
8. php artisan storage:link<br>

## サンプルアカウント
本アプリには、予めメール認証済みの一般ユーザー６名、管理者ユーザー１名が登録されています。<br>
動作確認の際にご利用ください。
### 一般ユーザー
パスワードは全員共通で「password 」です。
1. 西 伶奈　　reina.n@coachtech.com
2. 山田 太郎　taro.y@coachtech.com
3. 増田 一世　issei.m@coachtech.com
4. 山本 敬吉　keikichi.y@coachtech.com
5. 秋田 朋美　tomomi.a@coachtech.com
6. 中西 教夫　norio.n@coachtech.com
### 管理者ユーザー
- Mail : admin@example.com
- Password : adminuser

## 使用技術
- PHP8.4.1
- JavaScript
- Laravel8.83.8
- MySQL8.0.26
- nginx1.21.1
- mailhog1.0.1

## テスト
本アプリでは、PHPUnitを用いた自動テストを導入しています。
機能ごとにテストケースを用意していますので、下記の方法でご利用ください。

## ER図

## URL
- 一般ユーザーログインURL：http://localhost/login
- 管理者ユーザーログインURL：http://localhost/admin/login
- ユーザー登録： http://localhost/register
- phpMyAdmin: http://localhost:8080/
- mailhog: http://localhost:8025/