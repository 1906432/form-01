<?php 
             // DB接続設定
            //DSN(Data Source Name)を指定
            $dsn ='データベース名';
            //ユーザー名を指定
            $user = 'ユーザー名';
            $password = 'パスワード';
            //PDO(PHP Data Objects)を使用して、PHPからデータベースにアクセスしている
            //array以降は上手くいかなかった時に警告をするために記載している
            $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
            //テーブルの作成(IF NOT EXISTSは２回目以降に同じテーブルを作成しないようにしている)
            $sql = "CREATE TABLE IF NOT EXISTS bulletin_board"
            ."("
            //AUTO_INCREMENT:データ型は整数型,カラムに指定なしの時にidを1ずつ増加する
            //PRIMARY KEY(主キー):テーブル内でレコード(行)を一意に識別することができるように指定される列。重複もNULLも格納不可。
            //idは自動で登録される
            ."id INT AUTO_INCREMENT PRIMARY KEY,"
            //名前を入れるカラム(項目)(32文字)
            ."name char(32),"
            //コメントを入れるカラム(項目)
            ."comment TEXT,"
            //時刻を入れるカラム(項目)
            ."date datetime,"
            //パスワードを入れるカラム(項目)
            ."pass char(32)"
            .");";
            //queryメソッドでSQLを実行
            $stmt = $pdo->query($sql);
            $date = date("Y-m-d H:i:s");
           
            // "Y年m月d日 H時i分s秒"
            
            // if(file_exists($filename)){
            //         $num = count(file($filename))+1;
                    
            // }else{ $num=1;
            //         }
                    
            if (isset($_POST["name"]) && $_POST["name"]!="" && isset($_POST["comment"]) && $_POST["comment"]!=""){
                $name = $_POST["name"];
                $comment = $_POST["comment"];
                $post_pass = $_POST["password"];
                
                
                if($_POST["password"] == "pass"){
                
                    if(isset($_POST["edit"]) && $_POST["edit"]!=""){
                        if($_POST["pass_edit"] == "pass"){
                            $edit = $_POST["edit"];
                            //編集する投稿番号を取得
                            
                            // $edit_out = $_POST["edit_out"];
                            // //編集する投稿番号
                            $id = $edit;
                            
                            //指定した投稿番号の投稿内容を上書きして編集する
                            $sql = 'UPDATE bulletin_board SET name=:name,comment=:comment,date=:date WHERE id=:id';
                            //prepareメソッドでSQLを取得
                            $stmt = $pdo->prepare($sql);
                             //bindParamを使って、指定された変数に値をバインド(繋げる)する
                            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                             //SQLを実行
                             //executeはprepareで作成された文を実行する
                            $stmt->execute();
                            
                        }else{
                            echo "編集パスワードが正しくありません <br>";
                            } 
                }else{
                
                 //テーブルにデータを入力する
                        //prepareメソッドでSQLを取得している
                        //insert文でデータを登録する列名と内容を指定している
                        //形はINSERT INTO テーブル名 (列名1, 列名2,...) VALUES (値1, 値2,...);
                        $sql = $pdo -> prepare("INSERT INTO bulletin_board (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
                        //bindParamを使って、指定された変数に値をバインド(埋め込み)する
                        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                        $sql -> bindParam(':pass', $post_pass, PDO::PARAM_STR);
                        //SQLを実行
                        //executeはprepareで作成された文を実行する
                        $sql -> execute();
                        echo "投稿を受け付けました <br>";
                }
                
                }else{
                    echo "パスワードが正しくありません <br>";
                    }
            }
        
            
            if (isset($_POST["edit"])&& $_POST["edit"]!="") {
                if($_POST["pass_edit"] == "pass"){
                        $name = $_POST["name"];
                        $comment = $_POST["comment"];
                    	$edit = $_POST["edit"];
                    	
                    	 //SELECT文でテーブルからデータを抽出する
                         //'*'はテーブルの中身全てを指定している
                        $sql = 'SELECT * FROM bulletin_board';
                        //queryメソッドでSQLを実行
                        $stmt = $pdo->query($sql);
                        //取得したデータを全て一括で配列に取り込む
                        $edit_array = $stmt->fetchAll();
                        //ファイルの中身を１行ずつループして編集したい行を探す
                        foreach($edit_array as $row){
                        //編集対象番号と行番号が一致するか
                            if($row['id'] == $edit){
                                 //Yes：行内容を送信テキストボックスに出力する
                                $id = $row['id'];
                                $newname = $row['name'];
                                $newcomment = $row['comment'];
                            }
                        }
                    
            	}else{
            	    echo "編集パスワードが正しくありません  <br>";
            	}
            }
            // 	編集の時は選んだ行だけ影響与えたいから　== を使う。
            
            if (isset($_POST["delete"]) && $_POST["delete"]!="") {
                if($_POST["pass_delete"] == "pass"){
                    $delete = $_POST["delete"];
                    	
                    //削除する投稿番号
                    $id = $delete;
                     //指定した投稿番号の投稿内容を削除する
                    $sql = 'delete from bulletin_board where id=:id';
                     //prepareメソッドでSQLを取得
                    $stmt = $pdo->prepare($sql);
                     //bindParamを使って、指定された変数に値をバインド(繋げる)する
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                     //SQLを実行
                     //executeはprepareで作成された文を実行する
                    $stmt->execute();
                    
                }else{
                     echo "削除パスワードが正しくありません";
                }
            
            }
                // 削除の時は選んだ行以外残しておきたいから　!=　を使う
                
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5_01</title>
</head>
<body>
    <form action="" method="post">
        <input type= "name" name="name" placeholder="名前" value="<?php if(isset($newname)){echo $newname;}?>">
        <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($newcomment)){echo $newcomment;}?>">
        <input type="password" name="password" placeholder="パスワード">
        <input type="submit" name="submit">
        <!--<br>削除対象番号-->
        <br>
        <input type="number" name="delete" placeholder="削除対象番号">
        <input type="password" name="pass_delete" placeholder="パスワード">
        <input type="submit" value="削除"><br>
        <input type="number" name="edit" placeholder="編集対象番号">
        <input type="password" name="pass_edit" placeholder="パスワード">
        <input type="submit" name="edit_submit" value="編集"><br>
    </form>
    
    <?php
         ///テーブルの内容を表示する
            echo "【投稿一覧】";
            echo "<hr>";
             //SELECT文でテーブルからデータを抽出する
             //'*'はテーブルの中身全てを指定している
            $sql = 'SELECT * FROM bulletin_board';
             //queryメソッドでSQLを実行
            $stmt = $pdo->query($sql);
             //SQLで検索したデータを全て一括で配列に取り込む
            $result_array = $stmt->fetchAll();
            //配列を１行(row)ずつ配列に入れる
            foreach ($result_array as $row){
                 //１行ずつ表示する
                 //$rowの中にはテーブルのカラム名が入る
                echo $row['id'].'.';
                echo $row['name'].' : ';
                echo $row['comment'].' | ';
                echo $row['date'].'<br>';
                 //水平の横線を引く
                echo "<hr>";
             }
    
          
    ?>
    
</body>
</html>