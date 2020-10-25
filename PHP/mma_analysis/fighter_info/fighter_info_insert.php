<?php
	$error_str = "";
	/* 登録ボタン */
	if(@$_POST['ent_match']) {
		$fighter_name					= htmlspecialchars($_POST['fighter_name']);
		$fighter_nickname			= htmlspecialchars($_POST['fighter_nickname']);
		$fighter_born					= htmlspecialchars($_POST['fighter_born']);
		$fighter_class				= htmlspecialchars($_POST['fighter_class']);
		$fighter_association	= htmlspecialchars($_POST['fighter_association']);
		$fighter_height				= htmlspecialchars($_POST['fighter_height']);
		$fighter_country			= htmlspecialchars($_POST['fighter_country']);
		$fighter_area					= htmlspecialchars($_POST['fighter_area']);
		$fighter_comment			= htmlspecialchars($_POST['fighter_comment']);

		//選手名が未入力の場合は登録しない
		if($fighter_name != "") {
			//データベースOpen
			$pdo = new PDO("mysql:dbname=mma_analysis","root");
			//重複チェック
			//選手名が同一のレコード件数取得
			$sql = "SELECT count(*) FROM fighter_info WHERE name = '$fighter_name'";
			$st = $pdo->query($sql);

			if($st->fetchColumn())
			{
				echo "重複あり";
				echo "</br>";
			} else {
				//レコード追加
				$st = $pdo->prepare("INSERT INTO fighter_info(name,nickname,w_class,association,height,country,area,born,comment) VALUES(?,?,?,?,?,?,?,?,?)");
				$st->execute(array($fighter_name,$fighter_nickname,$fighter_class,$fighter_association,$fighter_height,$fighter_country,$fighter_area,$fighter_born,$fighter_comment));
			}
		} else {
			$error_str = "選手名が未入力";
		}

	} else {
		//処理なし
	}

?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>大会入力</title>
		<link rel="stylesheet" type="text/css" href="css/match_list.css">
	</head>
	<body>
		<form action='fighter_info_insert.php' method="post">
			選手名<input type="text" name="fighter_name" value=""><?php echo "$error_str"; ?><br>
			ニックネーム<input type="text" name="fighter_nickname" value=""><br>
			生年月日<input type="text" name="fighter_born" value=""><br>
			階級<input type="text" name="fighter_class" value=""><br>
			所属<input type="text" name="fighter_association" value=""><br>
			身長<input type="text" name="fighter_height" value=""><br>
			国<input type="text" name="fighter_country" value=""><br>
			地域<input type="text" name="fighter_area" value=""><br>
			<label for="comment">コメント</label><br>
			<textarea id="comment" name="fighter_comment" rows="4" cols="40"></textarea>
			<!-- 登録ボタン -->
			<input type="submit" name="ent_match" value="登録">
		</form>
		<a href="../index.html">トップページ</a>

	</body>
</html>
