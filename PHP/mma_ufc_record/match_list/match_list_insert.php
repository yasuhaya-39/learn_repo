<?php

	if(@$_POST['ent_match']) {

		$name				= htmlspecialchars($_POST['match_name']);
		$country 		= htmlspecialchars($_POST['match_cont']);
		$prace 			= htmlspecialchars($_POST['match_prace']);
		$m_date 		= htmlspecialchars($_POST['match_date']);

		if($name != "" && $country != "" && $prace != "" && $m_date != "") {

			$pdo = new PDO("mysql:dbname=mma_record","root");

			$st = $pdo->prepare("INSERT INTO ufc_match_data(name,country,prace,m_date) VALUES(?,?,?,?)");
			$st->execute(array($name,$country,$prace,$m_date));
			header('Location: match_list.php');
		} else {
			echo "<h3>未入力の項目があります</h3>";
		}
	} else {
		/* 処理なし */
	}

	if(@$_POST['file_input']) {

		if (($handle = fopen("input/match_list.csv", "r")) !== FALSE) {
    // 1行ずつfgetcsv()関数を使って読み込む
	    while (($data = fgetcsv($handle))) {
				$col_count = 0;
	      foreach ($data as $value) {
						if ($col_count == 0) {
							/* 読み込みんだ文字列をsjisからutf8に変換 */
							$name = mb_convert_encoding($value,"utf-8","sjis");
						} else if($col_count == 1) {
							$m_date = mb_convert_encoding($value,"utf-8","sjis");
							$year_chr 	= mb_substr($m_date, 0 ,4);
							$month_chr 	= mb_substr($m_date, 5 ,mb_strpos( $m_date, '月' ) - 5);
							$day_chr 		= mb_substr($m_date, mb_strpos( $m_date, '月' ) + 1 ,mb_strpos( $m_date, '日' ) - mb_strpos( $m_date, '月' )-1);
							if( mb_strlen($month_chr) == 1) {
								$month_chr = "0".$month_chr;
							}
							if( mb_strlen($day_chr) == 1) {
								$day_chr = "0".$day_chr;
							}
							$date_chr = $year_chr."-".$month_chr."-".$day_chr;
						} else if($col_count == 2) {
							$prace = mb_convert_encoding($value,"utf-8","sjis");
						} else if($col_count == 3) {
							$country = mb_convert_encoding($value,"utf-8","sjis");
						} else {
							//処理なし
						}
						$col_count++;
	      }

				$pdo = new PDO("mysql:dbname=mma_record","root");

				$st = $pdo->prepare("INSERT INTO ufc_match_data(name,country,prace,m_date,match_date) VALUES(?,?,?,?,?)");
				$st->execute(array($name,$country,$prace,$m_date,$date_chr));

    	}
    	fclose($handle);
		}
	} else {
		/* 処理なし */
	}

?>

<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>大会入力</title>
	</head>
	<body>
		<form action='match_list_insert.php' method="post">
			大会名<input type="text" name="match_name" value=""><br>
			開催国<input type="text" name="match_cont" value=""><br>
			開催場所<input type="text" name="match_prace" value=""><br>
			日時<input type="text" name="match_date" value=""><br>

			<input type="submit" name="ent_match" value="登録">
		</form>
		<form action="match_list_insert.php" method="post">
			<input type="submit" name="file_input" value="ファイル読み込み">
		</form>
		<a href='match_list.php'>大会一覧へ戻る</a>
	</body>
</html>
