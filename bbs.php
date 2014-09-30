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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>ひとこと掲示板</title>
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
	<form action="bbs.php" method="post">
		名前：<input type="text" name="name" /><br />
		一言：<input type="text" name="comment" size="60" /><br />
		<input type="submit" name="submit" value="送信" />
	</form>

	<?php
	//投稿された内容を取得するSQLを作成して結果を取得
	// $sql = "SELECT * FROM `post` ORDER BY `created_at` DESC";

	// $result = mysql_query($sql,$link);

	?>

	<?php if($result !== false && mysql_num_rows($result)): ?>
	<ul>
		<?php foreach ($posts as $post) { ?>
		<li>
			<?php echo htmlspecialchars($post['name'],ENT_QUOTES,'UTF-8'); ?>
			<?php echo htmlspecialchars($post['comment'],ENT_QUOTES,'UTF-8'); ?>
			- <?php echo htmlspecialchars($post['created_at'],ENT_QUOTES,'UTF-8'); ?>
		</li>
		<?php } ?>
	</ul>
	<?php endif; ?>
	<?php 
	//取得結果を開放して接続を閉じる
	//mysql_free_result($result);
	//mysql_close($link);
	?>
</body>
</html>