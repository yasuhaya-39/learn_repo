<?php
	class fighter_match_details {

		private $opp_name 						= array();
		private $opp_record 					= array();
		private $opp_country					= array();
		private $match_result 				= array();
		private $opp_rate							= array();
		private $match_decision 			= array();
		private $match_r_time 				= array();
		private $match_result_details = array();
		private $match_date 					= array();
		private $match_class 					= array();
		private $match_category 			= array();
		private $match_name 					= array();
		private $match_no							= array();
		private $match_num = 0;

		function __construct($fig_name) {
			$pdo = new PDO("mysql:dbname=mma_record","root");
			$st_match_result  = $pdo->query("SELECT * FROM ufc_match_result WHERE win_name = '$fig_name' OR lose_name = '$fig_name' ORDER BY match_date DESC");

			while($row = $st_match_result->fetch()) {
				$w_name 			= htmlspecialchars($row['win_name']);
				$l_name 			= htmlspecialchars($row['lose_name']);

				$tmp_name = "";

				if( $w_name == $fig_name ) {
					$this->opp_country[] 	= htmlspecialchars($row['lose_cont']);
					$this->opp_name[]			= $l_name;
					$tmp_name							= $l_name;
				} else {
					$this->opp_country[] 	= htmlspecialchars($row['win_cont']);
					$this->opp_name[]			= $w_name;
					$tmp_name							= $w_name;
				}

				$st_opponent  = $pdo->query("SELECT COUNT(*) FROM ufc_match_result WHERE win_name = '$tmp_name'
				                             AND decision != '引き分け' AND decision != 'ノーコンテスト' ORDER BY match_date DESC");
				$w_opponent = $st_opponent->fetchColumn();

				$st_opponent  = $pdo->query("SELECT COUNT(*) FROM ufc_match_result WHERE lose_name = '$tmp_name'
				                             AND decision != '引き分け' AND decision != 'ノーコンテスト' ORDER BY match_date DESC");
				$l_opponent = $st_opponent->fetchColumn();

				$this->opp_record[] = $w_opponent."勝".$l_opponent."敗";
				if( $w_opponent != 0 ) {
					$this->opp_rate[]	= floor(($w_opponent / ($w_opponent + $l_opponent)) * 100);
				} else {
					$this->opp_rate[]	= 0;
				}

				$tmp_decision = htmlspecialchars($row['decision']);
				$this->match_decision[] = $tmp_decision;

				if($tmp_decision == "引き分け") {
					 $this->match_result[]  = "△";
				} else if($tmp_decision == "ノーコンテスト") {
					$this->match_result[]  = "NC";
				} else if($tmp_decision == "中止") {
					$this->match_result[]  = "-";
				} else if($w_name == $fig_name) {
					$this->match_result[]  = "〇";
				} else if($l_name == $fig_name) {
					$this->match_result[]  = "×";
				} else {
					/* 不明なケース分かるように表示する */
					$this->match_result[] = "■";
				}

				$this->match_r_time[] 				= htmlspecialchars($row['m_r_time']);
				$this->match_result_details[]	= htmlspecialchars($row['details']);
				$this->match_date[]						= htmlspecialchars($row['match_date']);
				$this->match_class[]   				= htmlspecialchars($row['m_class']);
				$this->match_category[]				= htmlspecialchars($row['category']);

				$match_no 		= htmlspecialchars($row['match_no']);

				$st_match_data = $pdo->query("SELECT * FROM ufc_match_data WHERE no = $match_no");
				/* noはユニークだから、ヒットするレコードは一つだけ */
				$row_2 = $st_match_data->fetch();

				$this->match_name[] = htmlspecialchars($row_2['name']);
				$this->match_no[]   = htmlspecialchars($row_2['no']);
			}

			$this->match_num = count($this->match_name);
		}
		/* 対戦相手の選手名取得 */
		protected function get_opp_name($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->opp_name[$index];
		}

		/* 対戦相手の国籍取得 */
		protected function get_opp_country($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->opp_country[$index];
		}

		/* 対戦相手の戦績取得 */
		protected function get_opp_record($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->opp_record[$index];
		}

		/* 試合勝敗取得 */
		protected function get_match_result($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_result[$index];
		}

		/* 対戦相手の勝率取得 */
		protected function get_opp_rate($index) {
			if($index >= $this->match_num ) {
				/* エラーメッセージ */
			}
			return $this->opp_rate[$index];
		}

		/* 決着内容取得 */
		protected function get_match_decision($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_decision[$index];
		}

		/* 決着時間取得 */
		protected function get_match_r_time($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_r_time[$index];
		}

		/* 決着詳細時間取得 */
		protected function get_match_result_details($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_result_details[$index];
		}

		/* 試合日時取得 */
		protected function get_match_date($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_date[$index];
		}

		/* 階級試合内容取得 */
		protected function get_match_class($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_class[$index];
		}

		/* 試合カテゴリー取得 */
		protected function get_match_category($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_category[$index];
		}

		/* 大会名取得 */
		protected function get_match_name($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_name[$index];
		}

		protected function get_match_no($index) {
			if($index >= $this->match_num ) {
				return "ERROR";
			}
			return $this->match_no[$index];
		}

		/* 試合数取得 */
		protected function get_match_num() {
			return $this->match_num;
		}

	}

	class fighter_match_data extends fighter_match_details {

		private	$win_count 		= 0;
		private	$lose_count 	= 0;
		private $drow_count 	= 0;
		private $nc_count 		= 0;
		private $sub_count 		= 0;
		private $dec_count 		= 0;
		private $ko_count 		= 0;
		private $other_count  = 0;
		private $finish_rate 	= 0;
		private $opp_rate_ave = 0;

		function __construct($fig_name) {

			parent::__construct($fig_name);

			$m_count = parent::get_match_num();
			$opp_rate_sum = 0;

			for($i = 0;$i < $m_count;$i++) {
				$m_result = parent::get_match_result($i);
				if($m_result == "△") {
					 $this->drow_count++;
				} else if($m_result == "NC") {
					$this->nc_count++;
				} else if($m_result == "〇") {
					$this->win_count++;
					$m_decision = parent::get_match_decision($i);
					if($m_decision == "KO" || $m_decision == "TKO") {
						$this->ko_count++;
					} else if ($m_decision == "一本") {
						$this->sub_count++;
					} else if ($m_decision == "判定") {
						$this->dec_count++;
					} else if ($m_decision == "反則") {
						$this->other_count++;
					} else {
						/* エラーメッセージを出す予定 */
					}
				} else if($m_result == "×") {
					$this->lose_count++;
				} else {
					/* エラーメッセージを出す予定 */
				}
				$opp_rate_sum = $opp_rate_sum + parent::get_opp_rate($i);
			}

			if ( ($this->ko_count + $this->sub_count) != 0 ) {
				$this->finish_rate =  floor(((($this->ko_count + $this->sub_count) / ($this->ko_count + $this->sub_count + $this->dec_count + $this->other_count)) * 100));
			}
			if( $opp_rate_sum != 0 ) {
				$this->opp_rate_ave = floor($opp_rate_sum/$m_count);
			}
		}

		public function get_win_count() {
			return $this->win_count;
		}

		public function get_lose_count() {
			return $this->lose_count;
		}

		public function get_drow_count() {
			return $this->drow_count;
		}

		public function get_nc_count() {
			return $this->nc_count;
		}

		public function get_other_count() {
			return $this->other_count;
		}

		public function get_sub_count() {
			return $this->sub_count;
		}

		public function get_ko_count() {
			return $this->ko_count;
		}

		public function get_dec_count() {
			return $this->dec_count;
		}

		public function get_finish_rate() {
			return $this->finish_rate;
		}

		public function get_opp_rate_ave() {
			return $this->opp_rate_ave;
		}

		public function get_d_opp_name($index) {
			return parent::get_opp_name($index);
		}

		public function get_d_opp_country($index) {
			return parent::get_opp_country($index);
		}

		public function get_d_opp_record($index) {
			return parent::get_opp_record($index);
		}

		public function get_d_match_result($index) {
			return parent::get_match_result($index);
		}

		public function get_d_opp_rate($index) {
			return parent::get_opp_rate($index);
		}

		public function get_d_match_decision($index) {
			return parent::get_match_decision($index);
		}

		public function get_d_match_r_time($index) {
			return parent::get_match_r_time($index);
		}

		public function get_d_match_result_details($index) {
			return parent::get_match_result_details($index);
		}

		public function get_d_match_date($index) {
			return parent::get_match_date($index);
		}

		public function get_d_match_class($index) {
			return parent::get_match_class($index);
		}
		
		public function get_d_match_category($index) {
			return parent::get_match_category($index);
		}

		public function get_d_match_name($index) {
			return parent::get_match_name($index);
		}

		public function get_d_match_no($index) {
			return parent::get_match_no($index);
		}

		public function get_d_match_num() {
			return parent::get_match_num();
		}
	}
?>
