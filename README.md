# attendance_app

## 環境構築
①クローンするフォルダを置くディレクトリにコマンドを実行  
git clone git@github.com:(user_name)/~~~~~~~~~

②該当フォルダにディレクトリ遷移  
cd ~~~~~~~~/

③ビルド
docker-compose up -d --build  

④(コンテナ内)コンポーザDL  
composer install

⑤(コンテナ内).envコピー  
cp .env.example .env

⑥.env修正(mysql接続) .envに権限付与、mysqlに接続、修正後権限を戻す  
DB_CONNECTION=mysql  
DB_HOST=mysql  
DB_PORT=3306  
DB_DATABASE=laravel_db  
DB_USERNAME=laravel_user  
DB_PASSWORD=laravel_pass  

⑦表示の権限付与  
sudo chmod -R 777 src/*

⑧(コンテナ内)アプリケーションキー作成  
php artisan key:generate

⑨(コンテナ内)migration, seeder実行  
php artisan migrate --seed

⑩閲覧、テスト等  

## 使用技術(実行環境)
Laravel:8.83.29  
PHP:8.1.33 
nginx:1.21.1  
mysql:8.0.26  
Docker:28.3.2  
VScode:1.103.1  

## ER図
ー  

## URL
開発環境 : http://localhost/  
phpMyadmin : http://localhost:8080/  