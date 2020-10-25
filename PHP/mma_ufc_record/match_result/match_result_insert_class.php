<?php 
	class match_data_file {
		function __construct($init_flg){
			/* 初期化済みなら処理終了 */
			if ($init_flg == true) {
				return;
			}

			$fp = fopen("input/match_result.txt", "r");

			$array_f_class = array();
			$array_f_name = array();
			$array_f_decision = array();
			$count = 0;

			/* ファイルから文字取得 */
			while ($line = fgets($fp)) {
				if ($count == 0) {
					/* 階級部分の取得 */
					$f_array_class[] = mb_convert_encoding($line,"utf-8","sjis");
					$count++;
				} else if($count == 1) {
					/* 選手名部分の取得 */
					$f_array_name[] = mb_convert_encoding($line,"utf-8","sjis");
					$count++;
				} else if($count == 2) {
					/* 判定部分の取得 */
					$array_f_decision[] = mb_convert_encoding($line,"utf-8","sjis");
					$count = 0;
				}
			}

			for($i = 0;$i < count($f_array_class);$i++) {
				//階級部分の抜き出し
				$chr_p = mb_strpos($f_array_class[$i], "試合");
				$tmp_str = mb_substr( $f_array_class[$i], $chr_p + 3,mb_strlen($f_array_class[$i])-($chr_p + 3));
				$_SESSION['class'][$i] = $tmp_str;

				//勝利選手の抜き出し
				$chr_p 		= mb_strpos($f_array_name[$i], "○  ");
				$chr_p_2 	= mb_strpos($f_array_name[$i], " ",$chr_p+3);
				$tmp_str = mb_substr( $f_array_name[$i], $chr_p + 3,$chr_p_2 - 3);
				$_SESSION['win_name'][$i] = $tmp_str;

				//敗北選手の抜き出し
				$chr_p 		= mb_strpos($f_array_name[$i], " vs.  ");
				$chr_p_2 	= mb_strpos($f_array_name[$i], " ×",$chr_p);
				$tmp_str = mb_substr( $f_array_name[$i], $chr_p + 6,mb_strlen($f_array_name[$i]) - ($chr_p + 10));
				$_SESSION['lose_name'][$i] = $tmp_str;

				//判定、時間、決着詳細の抜き出し */
				if (mb_strpos($array_f_decision[$i], "終了") != 0) {
					$chr_p_2 = mb_strpos($array_f_decision[$i], "判定");
					$tmp_str = mb_substr( $array_f_decision[$i], $chr_p_2 + 2,3);
					if($tmp_str == "1-0" || $tmp_str == "0-0" || $tmp_str == "0-1" || $tmp_str == "1-1") {
						$_SESSION['decision'][$i] = "引き分け";
					} else {
						$_SESSION['decision'][$i] = "判定";
					}
					$_SESSION['r_time'][$i] 	= mb_substr( $array_f_decision[$i], 0,4);
					$_SESSION['details'][$i] 	= mb_substr( $array_f_decision[$i], 5);

				} else if (mb_strpos($array_f_decision[$i], " KO") != 0) {
					$_SESSION['decision'][$i] = "KO";
					$_SESSION['r_time'][$i] 	= mb_substr( $array_f_decision[$i], 0,7);
					$_SESSION['details'][$i] 	= mb_substr( $array_f_decision[$i], 10);
				} else if ( mb_strpos($array_f_decision[$i], " TKO") != 0) {
					$_SESSION['decision'][$i] = "TKO";
					$_SESSION['r_time'][$i] 	= mb_substr( $array_f_decision[$i], 0,7);
					$_SESSION['details'][$i] 	= mb_substr( $array_f_decision[$i], 11);
				} else {
					if ( (mb_strpos($array_f_decision[$i], "ノーコンテスト") == 0) &&
							 (mb_strpos($array_f_decision[$i], "失格") == 0) &&
							 (mb_strpos($array_f_decision[$i], "反則") == 0) )
							 {
								 $_SESSION['decision'][$i] 	= "一本";
								 $_SESSION['r_time'][$i] 		= mb_substr( $array_f_decision[$i], 0,7);
								 $_SESSION['details'][$i] 	= mb_substr( $array_f_decision[$i], 8);
							 }
				}

				$_SESSION['order_num'] = count($f_array_class);
			}
		}

		function get_class($order) {
			if($order < $_SESSION['order_num']) {
				return $_SESSION['class'][$order];
			} else {
				return "ERROR";
			}
		}

		function get_win_cont($order) {
			if($order < $_SESSION['order_num']) {
				return $_SESSION['win_cont'][$order];
			} else {
				return "ERROR";
			}
		}

		function get_win_name($order) {
			if($order < $_SESSION['order_num']) {
				return $_SESSION['win_name'][$order];
			} else {
				return "ERROR";
			}
		}

		function get_lose_cont($order) {
			if($order < $_SESSION['order_num']) {
				return $_SESSION['lose_cont'][$order];
			} else {
				return "ERROR";
			}
		}

		function get_lose_name($order) {
			if($order < $_SESSION['order_num']) {
				return $_SESSION['lose_name'][$order];
			} else {
				return "ERROR";
			}
		}

		function get_decision($order) {
			if($order < $_SESSION['order_num']) {
				return $_SESSION['decision'][$order];
			} else {
				return "ERROR";
			}
		}

		function get_r_time($order) {
			if($order < $_SESSION['order_num']) {
				return $_SESSION['r_time'][$order];
			} else {
				return "ERROR";
			}
		}

		function get_details($order) {
			if($order < $_SESSION['order_num']) {
				return $_SESSION['details'][$order];
			} else {
				return "ERROR";
			}
		}

		function get_order_num() {
			return $_SESSION['order_num'];
		}

		/* 選手名から選手の国籍を取得する */
		function get_fig_country($fig_name,&$fig_cont) {

			$return_code = false;

			/*--- 選手名から選手の国籍を検索する ---*/
			$pdo = new PDO("mysql:dbname=mma_record","root");
			$sql = "SELECT * FROM ufc_match_result WHERE win_name=\"{$fig_name}\"";
			$st = $pdo->query($sql);

			$tmp = "";
			while($row = $st->fetch()) {
				$tmp = $row['win_cont'];
			}

			if($tmp == "") {
				$sql = "SELECT * FROM ufc_match_result WHERE lose_name=\"{$fig_name}\"";
				$st = $pdo->query($sql);
				while($row = $st->fetch()) {
					$tmp = $row['lose_cont'];
				}
			}
			/* レコードあり */
			if($tmp != "") {
				$return_code = true;
				/* 国名を参照渡しの引数にセット */
				$fig_cont = $tmp;
			}
			return $return_code;
		}
	}
?>
