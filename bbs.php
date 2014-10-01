<?php
//データベースに接続

$link = mysql_connect('localhost','root','camp2014');
mysql_set_charset('utf8');

if (!$link){

	die('データベースに接続できません：'.mysql_error());
}

//データベースを選択する
mysql_select_db('online_bbs',$link);


$errors = array();

// $num1 = 1;
// $num2 = "1";
  
//   if($num1 == $num2){
//       echo 'hoge';
//   }
  
//   if($num1 === $num2){
//       echo 'hoge2';
//   }

$errors = array();
//POSTなら保存処理実行
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	//名前が正しく入力されているかチェック
	$name = null;

	//戻り値の配列を受け取る
	$return_name = fnc_validation($_POST['name'],40,'名前');

	if(isset($return_name['error'])){
		$errors['name'] = $return_name['error'];
	}else{
		$name = $return_name['value'];
	}
	// if (!isset($_POST['name']) || !strlen($_POST['name'])){
	// 	$errors['name'] = '名前を入力して下さい';
	// }else if (strlen($_POST['name']) > 40){
	// 	$errors['name'] = '名前は40文字以内で入力して下さい。';
	// }else{
	// 	$name = $_POST['name'];
	// }

	//ひとことが正しく入力されているかチェック
	$comment = null;

	$return_comment = fnc_validation($_POST['comment'],200,'ひとこと');

	if(isset($return_comment['error'])){
		$errors['comment'] = $return_comment['error'];
	}else{
		$comment = $return_comment['value'];
	}

	// if(!isset($_POST['comment']) || !strlen($_POST['comment'])){
	// 	$error['comment'] = 'ひとことを入力して下さい';
	// }else if (strlen($_POST['comment']) > 200){
	// 	$error['comment'] = 'ひとことは200文字以内で入力して下さい。';
	// }else{
	// 	$comment = $_POST['comment'];
	// }

	//エラーがなければ保存
	if (count($errors) === 0){
		//保存するためのSQL文を作成

		$sql = "INSERT INTO `post` (`name`,`comment`,`created_at`) VALUES('";
		$sql .= mysql_real_escape_string($name)."','";
		$sql .= mysql_real_escape_string($comment)."','";
		$sql .= date('Y-m-d H:i:s')."')";

		//保存する
		mysql_query($sql,$link);

		//mysql_close($link);

		//header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

	}



}

//投稿された内容を取得するSQLを作成して結果を取得
$sql = "SELECT * FROM `post` ORDER BY `created_at` DESC";

$result = mysql_query($sql,$link);

$posts = array();
//フェッチしたものを配列に格納しておく（宿題）
while ($post_each = mysql_fetch_assoc($result)){
	$posts[] = $post_each;
}

mysql_close($link);


function fnc_validation($check_value,$check_length,$check_item_name){
	//全部配列に入れて返すパターン
	$return[] = array();
	if (!isset($check_value) || !strlen($check_value)){
		$return['error'] = $check_item_name.'を入力して下さい';
	}else if (strlen($check_value) > $check_length){
		$return['error'] = $check_item_name.'は'.$check_length.'文字以内で入力して下さい。';
	}else{
		$return['value'] = $check_value;
	}

	return $return;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ひとこと掲示板</title>

     <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
	<h1>ひとこと掲示板</h1>
	
	<?php if (count($errors) !== 0){ ?>
	<ul>
		<?php foreach ($errors as $key => $value) { ?>
			<li><?php echo $value; ?></li>
		<?php } ?>
	</ul>	
	<?php } ?>
	<form action="bbs.php" method="post"　class="form-horizontal" role="form">
		<div class="form-group">
		    <label for="inputName" class="col-sm-2 control-label">名前：</label>
		    <div class="col-sm-10">
		      <input type="text" name="name" class="form-control" id="inputName" placeholder="お名前入力して下さい">
		    </div>
		  </div>
		  <div class="form-group">
		    <label for="inputComment" class="col-sm-2 control-label">一言：</label>
		    <div class="col-sm-10">
		      <input type="text" name="comment" class="form-control" id="inputComment" placeholder="一言入力して下さい">
		    </div>
		  </div>
		<input type="submit" name="submit" class="btn btn-primary"　value="送信" />
		
	</form>

	<?php
	//投稿された内容を取得するSQLを作成して結果を取得
	// $sql = "SELECT * FROM `post` ORDER BY `created_at` DESC";

	// $result = mysql_query($sql,$link);

	?>
	<table class="table table-striped" >
		<thead>
		<tr>
			<th>お名前</th>
			<th>一言</th>
			<th>投稿日</th>
		</tr>
		</thead>
	<?php if($result !== false && mysql_num_rows($result)): ?>
		<?php 
		$row_counter = 0;
		foreach ($posts as $post) { 
			if ($row_counter % 2 == 1){
				$class = 'class="warning"';
			}else{
				$class = '';
			}

			?>
		<tr <?php echo $class ?> >
			<td>
				<?php echo htmlspecialchars($post['name'],ENT_QUOTES,'UTF-8'); ?>
			</td>
			<td>
				<?php echo htmlspecialchars($post['comment'],ENT_QUOTES,'UTF-8'); ?>
			</td>
			<td>
				<?php echo htmlspecialchars($post['created_at'],ENT_QUOTES,'UTF-8'); ?>
			</td>
		</tr>
		<?php 
			$row_counter++;
			} ?>
	<?php endif; ?>

	</table>
	<?php 
	//取得結果を開放して接続を閉じる
	//mysql_free_result($result);
	//mysql_close($link);
	?>
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
</body>
</html>