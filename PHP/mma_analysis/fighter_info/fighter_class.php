<?php
	require "fighter_api.php";


	class c_fighter_info {

		private $nickname;
		private $w_class;
		private $association;
		private $height;
		private $country;
		private $area;
		private $age;
		private $match_result 					= array();		//試合結果
		private $match_op_fig						= array();		//対戦選手
		private $match_name							= array();		//大会名
		private $match_date 						= array();		//試合日時
		private $match_decision					= array();		//決着内容
		private $match_referee					= array();		//レフリー
		private $match_decision_round		= array();		//決着ラウンド
		private $match_decision_time		= array();		//決着時間
		private $win_count 				= 0;	//勝利数
		private $loss_count 			= 0;	//敗北数
		private $draw_count 			= 0;	//引き分け数
		private $nc_count 				= 0;	//ノーコンテスト数
		private $match_num 				= 0;	//試合数
		private $ko_count					= 0;  //KO勝ち数
		private $sub_count				= 0;  //一本勝ち数
		private $dec_count				= 0;  //判定勝ち数
		//ノート文字列
		private $jpn_match_str;					//日本国内団体戦績
		private $mgr_match_str;					//メジャー団体戦績


		const JAN_GROUP_NAME = ['Pancrase','Shooto','Deep','The Outsider','Zst'];
		const MAJOR_GROUP_NAME = ['UFC','Bellator','One','Rizin','PFL','WSOF ','ACB','ACA'];

		function __construct($fig_name) {
			$pdo = new PDO("mysql:dbname=mma_analysis","root");
			//選手プロフィールの取得
			$sql = "SELECT * FROM fighter_info WHERE name = '$fig_name'";
			$st_fig_data = $pdo->query($sql);

			$row = $st_fig_data->fetch();

			$this->nickname 		= $row['nickname'];
			$this->w_class 			= $row['w_class'];
			$this->association 	= $row['association'];
			$this->height 			= $row['height'];
			$this->country 			= $row['country'];
			$this->area 				= $row['area'];

			$now = date("Ymd");
			$birthday = str_replace("-", "", $row['born']);//ハイフンを除去
			$this->age = floor(($now-$birthday)/10000);

			//データベースから選手名と勝者、敗者が一致するレコードを出す
		 	$sql = "SELECT * FROM match_data WHERE win_name = '$fig_name' OR lose_name = '$fig_name' ORDER BY match_date DESC";
		 	$st_match_data = $pdo->query($sql);

			//比較用の文字列（大文字に変換）
			$cmp_name = mb_strtoupper($fig_name);

			while($row = $st_match_data->fetch()) {
				//引き分け
				if((mb_strpos ($row['decision'],"Draw")) !== FALSE) {
					$this->match_result[] = "DRAW";
					$this->draw_count++;
				//ノーコンテスト
				// } else if(mb_strpos ($row['decision'],"NC") !== FALSE){
				} else if((mb_strpos ($row['decision'],"NC") !== FALSE) ||
									(mb_strpos ($row['decision'],"No Contest") !== FALSE)){
					$this->match_result[] = "NC";
					$this->nc_count++;
				//勝利
				} else if(mb_strtoupper($row['win_name']) == $cmp_name) {
					$this->match_result[] = "WIN";
					$this->win_count++;

					//決着内容をカウント
					if(mb_strpos ($row['decision'],"KO") !== FALSE) {
						$this->ko_count++;
					} else if(mb_strpos ($row['decision'],"Submission") !== FALSE) {
						$this->sub_count++;
					} else if((mb_strpos ($row['decision'],"Decision") !== FALSE) &&
										(mb_strpos ($row['decision'],"Draw") === FALSE))
					{
						$this->dec_count++;
					} else {
						//処理なし
					}
				//敗北
				} else if(mb_strtoupper($row['lose_name']) == $cmp_name) {
					$this->match_result[] = "LOSS";
					$this->loss_count++;
				//こないはず、エラーとわかるようにする。
				} else {
					$this->result[] = "ERROR!";
				}

				//対戦相手の判定
				if(mb_strtoupper($row['win_name']) == $cmp_name) {
					$this->match_op_fig[] = mb_strtoupper($row['lose_name']);
				} else {
					$this->match_op_fig[] = mb_strtoupper($row['win_name']);
				}

				$this->match_name[] = $row['match_name'];
				$this->match_date[] = $row['match_date'];
				$this->match_decision[] = $row['decision'];
				$this->match_referee[] = $row['Referee'];
				$this->match_decision_round[] = $row['decision_round'];
				$this->match_decision_time[] = $row['decision_time'];

				$this->match_num++;		//試合数インクリ
			}

			//国内試合のノート
			$this->group_jpn_match_count();
			//メジャー団体の試合のノート
			$this->group_mgr_match_count();
		}

		/***********/
		/* 公開関数 */
		/***********/
		/* ニックネーム取得 */
		public function get_nickname() {
			return $this->nickname;
		}

		/* 階級取得 */
		public function get_class() {
			return $this->w_class;
		}

		/* 所属取得 */
		public function get_association() {
			return $this->association;
		}
		/* 身長取得 */
		public function get_height() {
			return $this->height;
		}

		/* 国、地域取得 */
		public function get_from() {
			$from_str = $this->country."/".$this->area;
			return $from_str;
		}

		/* 年齢取得 */
		public function get_age() {
			return $this->age;
		}

		/* 試合数取得 */
		public function get_match_num() {
			return $this->match_num;
		}

		/* 勝敗文字列取得 */
		public function get_vic_str() {
			$vic_str = $this->win_count." Wins  ".$this->loss_count." Losses  ";
			//引き分けあり
			if ($this->draw_count >= 1) {
				$vic_str = $vic_str.$this->draw_count." Draw  ";
			}
			//ノーコンテストあり
			if ($this->nc_count >= 1) {
				$vic_str = $vic_str.$this->nc_count." NC";
			}
			return $vic_str;
		}

		public function get_finish_rate_str($param) {
			if($param == "KO") {
				$num = $this->ko_count;
				$finish_str = "KO/TKO";
			} elseif ($param == "SUB") {
				$num = $this->sub_count;
				$finish_str = "SUB";
			} elseif ($param == "DEC") {
				$num = $this->dec_count;
				$finish_str = "DEC";
			} else {
				return "ERROR";
			}

			$rate = floor(($num / $this->win_count) * 100);
			return $rate."%(".$num.$finish_str.")";

		}

		public function get_finish_rate_htmsytle($param) {
			if($param == "KO") {
				$num = $this->ko_count;
			} elseif ($param == "SUB") {
				$num = $this->sub_count;
			} elseif ($param == "DEC") {
				$num = $this->dec_count;
			} else {
				return "ERROR";
			}

			$rate = floor(($num / $this->win_count) * 100);
			$style_width = 2 * $rate;
			$style_color_g = dechex(floor(255 - ($rate * 2.55)));

			return "background: #ff".$style_color_g."00; width:".$style_width."px";

		}

		public function get_fighter_exp($fig_name) {
			$exp = calc_fighter_exp($fig_name);

			if($exp == 0) {
				return "-";
			} else {
				return $exp;
			}
		}

		public function get_op_fig_exp($index) {
			$pdo = new PDO("mysql:dbname=mma_analysis","root");

			$f_name = $this->match_op_fig[$index];

			$sql = "SELECT count(*) FROM fighter_info WHERE name = '".addslashes($f_name)."'";
			$st = $pdo->query($sql);

			//選手情報がデータベースにある
			if($st->fetchColumn())
			{
				$exp = calc_fighter_exp($f_name);
				return $exp;
			} else {
				return "-";
			}
		}

		/* 選手ノート作成 */
		//国内団体戦績
		public function get_note_info_jpn_match () {
			return $this->jpn_match_str;
		}
		//メジャー団体戦績
		public function get_note_info_mgr_match () {
			return $this->mgr_match_str;
		}
		//勝利情報
		public function get_win_info() {

			$win_streak_num = 0;
			$win_streak_str = "";
			$win_info_str = "";

			//連勝記録
			for($i = 0;$i < $this->match_num;$i++) {
				if($this->match_result[$i] == "WIN") {
					$win_streak_num++;
				} else {
					break;
				}
			}
			//3連勝以上
			if($win_streak_num >= 3) {
				$win_info_str = $win_streak_num." game winning streak  ";
			}

			//1Rフィニッシュ
			$first_round = 0;
			$first_finish_rate = 0;

			for($i = 0;$i < $this->match_num;$i++) {
				//勝利かつ、1R
				if(($this->match_result[$i] == "WIN") 		&&
					 ($this->match_decision_round[$i] == 1))
					 {
						 //KOもしくはSUB
						 if ((mb_strpos ($this->match_decision[$i],"KO") !== FALSE) ||
						    (mb_strpos ($this->match_decision[$i],"Submission") !== FALSE))
								{
									$first_round++;
								}
					 }
			}

			$first_finish_rate = floor(($first_round / $this->win_count) * 100);
			//1Rフィニッシュが50％以上
			if($first_finish_rate >= 50 ) {
				if($win_info_str == "")
				{
					$win_info_str = $first_round." games ended in the first round";

				} else {
					$win_info_str = $win_info_str.$first_round." games ended in the first round";
				}
			}

			if($win_info_str == "") {
				$win_info_str = "-";
			}

			return $win_info_str;
		}

		public function get_notable_win($name)
		{

			$notable_win = "";
			$pdo = new PDO("mysql:dbname=mma_analysis","root");

			$sql =
			"SELECT DISTINCT fighter_info.name FROM match_data INNER JOIN fighter_info ".
			"ON match_data.lose_name = fighter_info.name WHERE ".
			"match_data.win_name='$name' AND fighter_info.attention = 'ON'";

			$st = $pdo->query($sql);

			while($row = $st->fetch()) {
				$notable_win = $notable_win.$row['name'].",";
			}

			if($notable_win != "") {
				$notable_win = "NOTABELE WIN:".$notable_win;
			} else {
				$notable_win = "-";
			}

			return $notable_win;

		}

		public function get_notable_loss($name)
		{
			$notable_lose = "";
			$pdo = new PDO("mysql:dbname=mma_analysis","root");

			$sql =
			"SELECT DISTINCT fighter_info.name FROM match_data INNER JOIN fighter_info ".
			"ON match_data.win_name = fighter_info.name WHERE ".
			"match_data.lose_name='$name' AND fighter_info.attention = 'ON'";

			$st = $pdo->query($sql);

			while($row = $st->fetch()) {
				$notable_lose = $notable_lose.$row['name'].",";
			}

			if($notable_lose != "") {
				$notable_lose = "NOTABELE LOSS:".$notable_lose;
			} else {
				$notable_lose = "-";
			}

			return $notable_lose;
		}

		/* 試合結果取得 */
		public function get_match_result($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_result[$index];
		}

		/* 対戦相手取得 */
		public function get_match_op_fig($index) {
			$tbl_str;
			$f_name;

			if($index >= $this->match_num ) {
				return "ERROR";
			}

			$f_name = $this->match_op_fig[$index];
			$pdo = new PDO("mysql:dbname=mma_analysis","root");

			$sql = "SELECT count(*) FROM fighter_info WHERE name = '".addslashes($f_name)."'";
			$st = $pdo->query($sql);

			//選手情報がデータベースにある
			if($st->fetchColumn())
			{
				//aタグ
				$tbl_str = "<a href = 'fighter_details.php?fig_name={$f_name}'>$f_name</a>";

			} else{
				$tbl_str = $this->match_op_fig[$index];
			}

			return $tbl_str;
		}

		/* 大会名取得 */
		public function get_match_name($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_name[$index];
		}

		/* 大会日時取得 */
		public function get_match_date($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_date[$index];
		}

		/* 決着内容取得 */
		public function get_match_decision($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_decision[$index];
		}

		/* レフリー取得 */
		public function get_match_referee($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_referee[$index];
		}

		/* 決着ラウンド取得 */
		public function get_match_decision_round($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_decision_round[$index];
		}

		/* 決着時間取得 */
		public function get_match_decision_time($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_decision_time[$index];
		}
		/***********/
		/* 非公開関数 */
		/***********/
		private function calc_finish_rate($param) {
			if($param == "KO") {
				$num = $this->ko_count;
				$finish_str = "KO/TKO";
			} elseif ($param == "SUB") {
				$num = $this->sub_count;
				$finish_str = "SUB";
			} elseif ($param == "DEC") {
				$num = $this->dec_count;
				$finish_str = "DEC";
			} else {
				return "ERROR";
			}

			$rate = floor(($num / $this->win_count) * 100);
			return $rate;

		}

		private function group_jpn_match_count()
		{

			$jpn_match_str = "";

			foreach (self::JAN_GROUP_NAME as $value) {
				$win_count = 0;
				$loss_count = 0;
				$draw_count = 0;

				for($i = 0;$i < $this->match_num;$i++) {
					if(mb_strpos ($this->match_name[$i],$value) !== FALSE) {
						if($this->match_result[$i] == "WIN") {
							$win_count++;
						} else if ($this->match_result[$i] == "LOSS") {
							$loss_count++;
						} else if ($this->match_result[$i] == "DRAW") {
							$draw_count++;
						} else {
							//処理なし
						}
					}
				}
				//試合あり
				$match_count = $win_count + $loss_count + $draw_count;
				if(($match_count) != 0) {
					$this->jpn_match_str = $this->jpn_match_str.$value." ".$match_count."bout"."(".$win_count."WIN ".$loss_count."LOSS)";
				}
			}
		}


		private function group_mgr_match_count()
		{

			$jpn_match_str = "";

			foreach (self::MAJOR_GROUP_NAME as $value) {
				$win_count = 0;
				$loss_count = 0;
				$draw_count = 0;

				for($i = 0;$i < $this->match_num;$i++) {
					if( $value == "Bellator" ) {
						if((mb_strpos ($this->match_name[$i],$value) !== FALSE) ||
							 (mb_strpos ($this->match_name[$i],"BFC") !== FALSE)) {
								 if($this->match_result[$i] == "WIN") {
									 $win_count++;
								 } else if ($this->match_result[$i] == "LOSS") {
									 $loss_count++;
								 } else if ($this->match_result[$i] == "DRAW") {
									 $draw_count++;
								 } else {
									 //処理なし
								 }
						}
					} else {
						if(mb_strpos ($this->match_name[$i],$value) !== FALSE) {
							if($this->match_result[$i] == "WIN") {
								$win_count++;
							} else if ($this->match_result[$i] == "LOSS") {
								$loss_count++;
							} else if ($this->match_result[$i] == "DRAW") {
								$draw_count++;
							} else {
								//処理なし
							}
						}
					}
				}
				//試合あり
				$match_count = $win_count + $loss_count + $draw_count;
				if(($match_count) != 0) {
					$this->mgr_match_str = $this->mgr_match_str.$value." ".$match_count."bout"."(".$win_count."WIN ".$loss_count."LOSS)";
				}
			}
		}
	}
?>
