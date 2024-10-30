<?php
/*
Plugin Name: Holiday class post calendar
Plugin URI: https://strix.main.jp/?diys=holiday_class_post_calendar
Description: This plug-in displays posts calendar with css-class of Sunday,Saturday,holiday and own holiday.Enable choose Japanese or English and display monthly archives list.
Version: 7.1
Requires PHP: 7.0
Author: Hironori Masuda
Author URI: https://strix.main.jp/?page_id=16227
License: GPL v2 or later

Copyright 2016 Hironori Masuda (email : strix.ss@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//delete_option('hldycls_pcldr_options');//既存のオプション値を強制的にデフォルトに戻す時使用

// クラスが定義済みか調べる
if ( ! class_exists( 'hldycls_post_calendar' ) ) {

class hldycls_post_calendar {

	private $current;
	private $default;
	private $ret_option;
	private $option_name;
	private $version;
	private $call_count;
	private $call_bl_count;
	private $call_fs_flg;
	private $styleflg;
	private $cache_cont;


	public function __construct() {
		$this->option_name = 'hldycls_pcldr_options';
		$this->version = '7.1';
		$this->call_count = 0;
		$this->call_bl_count = 0;
		$this->call_fs_flg = false;
		$this->styleflg =array();
		$this->load_option();
	}

	private function load_option() {

		// オプションのデフォルト値設定
		$this->default = array(
			'lang' => 'e', //日本語か英語表示かの指定　'j'か'e'、デフォルトは'e'で英語。
			'wf' => 's', // 週の始めを日曜か月曜かの指定　's'か'm'、デフォルトは's'で日曜。
			'capt' => '', // 表題のcaptionに表示する文字列。
			'footer' => '', // 下部に表示する文字列。
			'closewd' => '', // 独自休日を曜日で指定する場合。毎週水曜日だとか。1が日曜、7が土曜で1～7までの数字で指定。複数ももちろん可でその場合は配列で指定。一つの時は配列でなくてもOK。
			'closel' => '', // 独自休日を日にちで指定。年月日８ケタ、月日４ケタないし３ケタ、日にち２ケタ以下で指定。日にちだけなら毎月に設定され、月日なら毎年。
			'anniver' => '', // それらとは別に特別な日を設定するときはanniverを使用し、これは毎年の事と考えて４ケタもしくは３ケタのみ使用可
			'monthly' => '1', // 月別アーカイブのリストも表示させたい場合は1を指定。'0'で非表示。デフォルトは1で表示。
			'postype' => 'post', // カレンダーに表示させる投稿の投稿タイプ、デフォルトは'post'。
			'loadstyle' => '1', //カレンダー用のデフォルトスタイルシートのロード。デフォルトは'1'でロードする、'0'でロードしない。
			'acvheader' => '', //月別アーカイブリストのヘッダー文字指定。
			'acvoptorlist'=>'1',//月別アーカイブリスト表示がプルダウンかリストか、デフォルトは'1'でプルダウン、'0'でリスト表示。
			'loadacvlststyle' => '1', //月別アーカイブのリスト表示用のデフォルトスタイルシートのロード。デフォルトは'1'でロードする、'0'でロードしない。
			'daypostlink' => '0',// 投稿がある日にオンマウスで表示させるのはツールチップかその投稿へのリンクか。デフォルトは'0'でツールチップ、'1'で投稿へのリンク。
			'dplinkstyle' => '0',// 投稿がある日にオンマウスで表示させる投稿へのリンクのデフォルトのスタイルをヘッダーに排出する。デフォルトは'0'で排出しない、'1'で排出する。
			'parentstyle' => '',// 親div id="wp-calendar" にインラインで書き出すスタイル
			'colorstyle' => '',// 日土休日それぞれのスタイル設定。基本パターンは'曜日-文字色-背景色'。例、日曜なら'n-white-#ff0000'でそれぞれの指定は,で区切ってつなげる。n:日、d:土、w:closewd、l:closel、a:anniver、v:lanniver。
			'myholidays' => '',// デフォルトで設定されている日本の祝祭日ではなく、独自の祝祭日だけを表示する場合にその日付を','で区切ったリストにして入力。デフォルトは空文字。
			'adddeldays' => '',// デフォルトで設定されている祝祭日リストに加える、または削除する日付。
			'en_cache' => '1',// cache設定。'0'でdisable。ここで指定した数字がキャッシュファイル名の末尾に付加される。テンプレートにおいて引数でこのオプション値と違う数値を指定することで別のキャッシュファイルを設定できる。
            'en_gutenblock' => '0',// gutenberg block editor での custom widget block の登録、1:enable、0:disable
			'en_grid' => '0',// '0':table、'1':grid -> grid default style: display:grid;grid-template-columns:repeat( 7, 1fr );text-align:center;',
		);
		$this->current = get_option( $this->option_name );

		if ( false === $this->current ) {//初使用の時などオプションが設定されていない時

			update_option( $this->option_name, $this->default );//デフォルトでオプション設定
			$this->ret_option = $this->default;
		} else {

			$deff = array_intersect_key( $this->default, $this->current );
			$countary = array( count ( $this->default ) , count ( $this->current ) , count ( $deff ) );

			if ( 1 !== count ( array_unique( $countary ) ) ) {
				foreach ( $this->current as $key => $val ) {
					if ( isset ( $this->default[ $key ] ) ) {//　デフォルトオプションにそのキーが存在する要素だけ保存されている値で上書き。
						$this->default[ $key ] = $val;
					}
				}
				update_option( $this->option_name, $this->default );
				$this->ret_option = $this->default;

			} else {
				$this->ret_option = $this->current;
			}
		}

		$stylebit = 0;// bit flg 0b00000
		$styleary = array();

		// 下の foreach で key の値でビットを左シフトした後、flg と bit or させる
		$styleary[] = $this->ret_option['monthly'];// 0b0001
		// $styleary[] = $this->ret_option['acvoptorlist'];// 0b00010 x
		$styleary[] = $this->ret_option['loadacvlststyle'];// 0b0010
		$styleary[] = $this->ret_option['daypostlink'];// 0b0100
		$styleary[] = $this->ret_option['dplinkstyle'];// 0b1000

		foreach ( $styleary as $key => $val ) {
			$tmp = ( int ) $val << $key;
			$stylebit = $stylebit | $tmp;
		}

		$this->styleflg['daypostlink'] = 0b1100 === ( $stylebit & 0b1100 );// 日付オンマウスで投稿へのリンクを表示で、デフォルトスタイルを書き出す
		$this->styleflg['montharv'] = 0b11 === ( $stylebit & 0b11);// true:月別アーカイブを表示し、スタイルシートをロード

		if ( '1' === $this->ret_option['loadstyle'] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'hldycls_pcldr_style' ) );
		}

		// if ( ( '1' === $this->ret_option['daypostlink'] and '1' === $this->ret_option['dplinkstyle'] ) or ( '1' === $this->ret_option['monthly'] and '0' === $this->ret_option['acvoptorlist'] and '1' === $this->ret_option['loadacvlststyle'] ) ){
		if ( $this->styleflg['daypostlink'] or $this->styleflg['montharv'] ) {
			//↓サイト読み込み時に投稿へのリンクのためのスタイルを書き出すためにwp_headに登録
			add_action( 'wp_head', array( $this, 'hldycls_pcldr_daylink_style' ) );
		}

		if ( $this->ret_option['colorstyle'] ) {
			//↓サイト読み込み時に投稿へのリンクのためのスタイルを書き出すためにwp_headに登録
			add_action( 'wp_head', array( $this, 'hldycls_pcldr_color_style' ) );
			// 管理画面における block 用のスタイルの書き出しの登録
			add_action( 'admin_head-post.php', array( $this, 'hldycls_pcldr_color_style' ) );
			add_action( 'admin_head-widgets.php', array( $this, 'hldycls_pcldr_color_style' ) );
		}

		// if ( ( int ) $this->ret_option['en_cache'] ) {
			add_action( 'publish_post', array( $this, 'save_del_cache' ) );
			add_action( 'wp_ajax_hcpcldrsavecache', array( $this, 'hcpcldr_save_cache' ) );
			add_action( 'wp_ajax_nopriv_hcpcldrsavecache', array( $this, 'hcpcldr_save_cache' ) );
			add_action( 'wp_ajax_hcpcldrdeletecache', array( $this, 'hcpcldr_delete_cache' ) );
		// }

		if ( '1' === $this->ret_option['en_gutenblock'] ) {
			add_action( 'init', array( $this, 'gb_register_block' ) );
		}

		add_action( 'update_option_hldycls_pcldr_options', array( $this, 'save_del_cache' ) );
	}

	public function gb_register_block() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg が有効でない場合は何もしない
			return;
		}

		wp_register_script(
			'hldycls-pcldr-01',
			plugins_url( 'hldycls_gb.js', __FILE__ ),
			array( 'wp-blocks', 'wp-block-editor', 'wp-element', 'wp-components', 'wp-server-side-render' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'hldycls_gb.js' )
		);

		// if ( ! $this->ret_option['colorstyle'] ) { 
			wp_register_style(
				'hldycls-editor-style',
				plugins_url( 'hldycls_pcldr_gb.css', __FILE__ ),
				array( 'wp-edit-blocks' ),
				filemtime( plugin_dir_path( __FILE__ ) . 'hldycls_pcldr_gb.css' )
			);
		// }

		// namespace /block-name , namespace: spcific unique, both only use lowercase alphanumeric characters or dashes
		register_block_type( 'hldycls-pcldr/post-calendar',			
			array(
				'api_version' => 2,
				'editor_script' => 'hldycls-pcldr-01',
				'editor_style' => 'hldycls-editor-style',
				'render_callback' => array( $this, 'hldyclspcldr_render_callback' ),
				'attributes' => array(
					'prtwidth' => array( 'type' => 'string', 'default' => '100%' ),
					'lang' => array( 'type' => 'string' ),
					'wf' => array( 'type' => 'string' ),
					'capt' => array( 'type' => 'string' ),
					'footer' => array( 'type' => 'string' ),
					'closewd' => array( 'type' => 'string' ),
					'closel' => array( 'type' => 'string' ),
					'anniver' => array( 'type' => 'string' ),
					'monthly' => array( 'type' => 'string' ),
					'postype' => array( 'type' => 'string' ),
					'acvheader' => array( 'type' => 'string' ),
					'acvoptorlist' => array( 'type' => 'string' ),
					'daypostlink' => array( 'type' => 'string' ),
					'myholidays' => array( 'type' => 'string' ),
					'adddeldays' => array( 'type' => 'string' ),
					'en_cache' => array( 'type' => 'string' ),
				)
			)
		);
	}
	
	public function hldyclspcldr_render_callback( $attributes ){
		$str = '';
		/*$str = '<span>';
		foreach ( $attributes as $key => $val ) {
			$str .= $key . '=' . $val . '_';
		}
		$str .= '</span>';*/

		$params = $this->ret_option;

		++$this->call_bl_count;
		if ( 1 === $this->call_bl_count ) {
			$idnum = '';
		} else {
			$idnum = '-' . ( string ) $this->call_bl_count;
		}

		foreach ( $attributes as $key => $val ) {
			if ( null !== $val ) {
				$params[ $key ] = $val;
			}
		}

		$prtwidth = '';
		if ( isset( $params['prtwidth'] ) ) {
			$prtwidth = ' style="width:' . esc_attr( $params['prtwidth'] ) . ';"';
		}
		$calendar = $this->holiday_class_post_calendar( $params );
		// mb_strpos が false の場合、mb_substr は元の文字列をそのまま返す
		// $calendar = mb_substr( $calendar, mb_strpos( $calendar, '-->' ) + 3 );
		$ret_cont = '<div id="hldyclspcldr' . $idnum . '"' . $prtwidth . '>' . $calendar . $str . '</div>'; 

		return $ret_cont;
	}
	
	public function ans_option( $tar ) {
		return $this->ret_option[ $tar ];
	}

	/*
	サイト読み込み時にオプション指定のスタイルシートを登録する関数。
	テンプレートに直接記述してスタイルシートを登録することも可。
	*/
	public function hldycls_pcldr_style() {
		if ( '1' === $this->ret_option['en_grid'] ) {
			wp_enqueue_style( 'hldycls_pcldr_style', plugins_url( 'hldycls_pcldr_grid.css', __FILE__ ), false, date( 'YmdHis', filemtime(plugin_dir_path( __FILE__ ) . 'hldycls_pcldr_grid.css' ) ) );
		} else {
			wp_enqueue_style( 'hldycls_pcldr_style', plugins_url( 'hldycls_pcldr.css', __FILE__ ), false, date( 'YmdHis', filemtime(plugin_dir_path( __FILE__ ) . 'hldycls_pcldr.css' ) ) );
		}
	}

	/*
	投稿がある日にオンマウスでの表示で、投稿へのリンクを選択している場合、
	月別アーカイブでリスト表示を選択している場合、
	サイト読み込み時にそのスタイルをヘッダーに書き出す関数。
	*/
	public function hldycls_pcldr_daylink_style() {

		if ( $this->styleflg['daypostlink'] ) {
?>
	<!-- plugin Holiday Class Post Calendar day-post links style -->
	<style type="text/css">
		.daychildren{display:block;position:absolute;width:150px;top:15px;right:-10px;padding:2px;text-align:left;font-size:1.1em;visibility:hidden;opacity:0;background-color:#ffffff;border-radius:3px;z-index:10;}
		.daychildren a{color:#0000ff;border-bottom:dotted 1px red;}
		.daychildren:before{content:"";display:block;position:absolute;top:-20px;left:50%;width:0px;height:0px;border-top: 10px solid transparent;border-right: 0px solid;border-bottom: 10px solid #ffffff;border-left: 20px solid transparent;}
		<?php if ( '1' === $this->ret_option['en_grid'] ) : ?>
		[data-post]{position:relative;}
		[data-post]:hover > .daychildren{animation:openlink 0.4s;animation-delay:0.5s;animation-fill-mode:both;}
		<?php else : ?>
		:is( div, table )[id*="wp-calendar"] :is( tbody td, th ){position:relative;text-align:center;}
		:is( div, table )[id*="wp-calendar"] td:hover > .daychildren{animation:openlink 0.4s;animation-delay:0.5s;animation-fill-mode:both;}
		<?php endif; ?>
		@keyframes openlink{from{ visibility:hidden; opacity: 0;}5% { visibility:visible; opacity: 0; }to{ visibility:visible;opacity:1;}}
		.daychildrenmark:before{content:"♦";}
	</style>
<?php
		}
		if ( $this->styleflg['montharv'] ) {
			
			// if ( '0' === $this->ret_option['acvoptorlist'] ) {
?>
				<!-- plugin Holiday Class Post Calendar archive-list links style -->
				<style type="text/css" id="acvlststyle">
					.mlchildren{display:none;}
					.arcyear > input{display:none;}
					.arcyear:hover{cursor:pointer;color:#fe56aa;}
					input[name*="chlbis-"]:checked ~ .mlchildren{display:block;}
					.mlchildren li a:hover{color:#fe56aa;}
				</style>
<?php
			// }		
		}
	}

	/*
	日曜、土曜、各休日の文字色と背景色のスタイル設定ををヘッダーに書き出す関数。
	*/
	public function hldycls_pcldr_color_style() {
		$colorstyle = array();
		$styleoption = wp_check_invalid_utf8( $this->ret_option['colorstyle'] );
		// $styleoption = str_replace( array( '&', '<', '>', '\'','"', '}', '{' ), '', $styleoption );
		$pattern = '/[&<>\'"}{]/';
		$styleoption = preg_replace( $pattern, '', $styleoption );
		$classname = array( 't'=>'#today', 'd'=>'.doyou', 'n'=>'.nitiyou', 'w'=>'.closewd', 'l'=>'.closedy', 'a'=>'.anniversary', 'v'=>'.lanniversary' );
		$colorstyle = explode ( '_', $styleoption );

		if ( '0' === $this->ret_option['en_grid'] ) {
			$parent = 'table ';
		} else {
			$parent = '.cal-body ';
		}
		$stylestr = array( 't'=>'', 'd'=>'', 'n'=>'', 'w'=>'', 'l'=>'', 'a'=>'', 'v'=>'' );
		$styleel = array( 1=>'color:', 2=>'background-color:', 3=>'border:solid 1px ', 4=>'border-radius:' );
	
		foreach ( $colorstyle as $val ) {
			$eachcolor = explode( '^', $val );
			$tmpstl = array();
			if ( 5 === count( $eachcolor ) ) {
				if ( isset ( $classname[ $eachcolor[0] ] ) ) {

					$ckey = $eachcolor[0];
					$tmpstl[] = $parent . $classname[ $ckey ] . '{';

					$eachcolor[0] = '';

					foreach ( array_filter( $eachcolor ) as $key => $val ) {
						$tmpstl[] = $styleel[ $key ] . $val . ';';
					}
					$stylestr[ $ckey ] = implode( '', $tmpstl ) . '}';
				}
			}
		}

		if ( $stylestr ) {
			echo '<!--plugin Holiday Class Post Calendar holiday style-->', "\n" , '<style type="text/css">' , "\n" , implode ( '', $stylestr ) , '</style>' , "\n";
		}
	}

	//↓ここから管理画面のメニューにオプション設定ページを登録する処理
	public function hldycls_pcldr_add_menu() {
		add_options_page( 'Holiday-class Post Calendar Option', 'Holiday-class Post Calendar Option', 'administrator', 'hldycls_pcldr_plugin_options', array( $this, 'hldycls_pcldr_page_output' ) );
		add_action( 'admin_init', array( $this, 'register_hldycls_pcldr_settings' ) );
	}
	 
	public function register_hldycls_pcldr_settings() {
		register_setting( 'hldycls_pcldr-settings-group', $this->option_name );
	}
	
	public function hldycls_pcldr_page_output() {
?>
	<div class="wrap">
		<h2 style="color:blue;text-shadow:2px 2px 1px white,3px 3px 3px blue;">Holiday-Class Post Calendar v<?php echo $this->version; ?> option</h2>
		<h3>《 Option 設定 》</h3>
		<form method="post" action="options.php">
<?php
				settings_fields( 'hldycls_pcldr-settings-group' );
				do_settings_sections( 'hldycls_pcldr-settings-group' );
				$nonce = wp_create_nonce(__FILE__);

				include_once 'explain.php';
	}
	//↑ここまで

	// Content Policy Security が設定してある場合、nonce が設定してあるなら inline script を作動させるためには、その値が必要
	// headers_list() は応答header のリストで配列になっており key はただの数字
	public function get_cpsnonce() {
		$cps = headers_list();
		$cpsnonce = '';
		foreach ( $cps as $val ) {
			if ( false !== strpos( $val, 'Content-Security-Policy' ) ) {
				$pattern = '/\'nonce-([0-9a-fA-F].*?)\'/';
				if ( preg_match ( $pattern, $val, $matches ) ) {
					$cpsnonce = ' nonce="' . $matches[1] . '"';
				}
				break;
			}
		}
		return $cpsnonce;			
	}

	public function footer_script() {
		if ( $this->call_count > 0 && ! $this->call_fs_flg ) {

			$this->call_fs_flg = true;
			$cpsnonce = $this->get_cpsnonce();
			return PHP_EOL . '<!--plugin Holiday Class Post Calendar monthly archive pulldown script --><script' . $cpsnonce . '>window.addEventListener("load", function() {const doc=document;for(let i=0;i<5;++i){const target ="archive-dropdown"+(i?"-"+i:"");if(null!==doc.getElementById(target)){const arcvdrpdwn=doc.getElementById(target);arcvdrpdwn.onchange=function(){doc.location.href=arcvdrpdwn.options[arcvdrpdwn.selectedIndex].value;}}}})</script>' . PHP_EOL;
		}
	}

	// その月の１日の曜日から指定した曜日のその月においての日にちを配列にして返す関数
	// $wdaynum 1:日曜、2:月、3:火、4:水、5:木、6:金、7:土 求める日にちの曜日を指定
	// $fdwd : その月の1日の曜日 0:日1:月2:火3:水4:木5:金6:土
	// $dnum : その月の日数
	public function ret_daynum ( $wdaynum, $fdwd, $dnum  ) {

		$tmpnum = 0;
		$daynumary = array();
		$limitnum = $dnum - 6;

		if ( $wdaynum ) {

			$firstdaynum = ( ( int ) $wdaynum - $fdwd + 7 ) % 7;

			// １日が月曜の場合(1 === $tmpwday['wday'])に日曜の1が指定してある場合、日曜の初日の日にちが0になってしまうことを避ける。
			$i = 0 === $firstdaynum ? 1 : 0;
			while ( $tmpnum < $limitnum ) {
				$tmpnum = $firstdaynum + 7 * $i;
				$daynumary[] = $tmpnum;
				++$i;
			};
		}
		return $daynumary;
	}

	function holiday_class_post_calendar(){
		global $wpdb, $wp_query, $wp_rewrite;

		$prm = $this->ret_option;
		$args = func_get_args();

		if ( func_num_args() > 0 ) {
			$prm = array_merge( $prm, $args[0] );
			if ( ! isset( $args[0]['postype'] ) ) {
				$q_vars = $wp_query->query_vars;
				if ( isset( $q_vars['post_type'] ) and  $q_vars['post_type'] ) {
					$prm['postype'] = $q_vars['post_type'];
				}
			}
		}

		++$this->call_count;

		$target_Ym = '';
		$tmp_month_no = '';
		$trailslash = '';// パーマリンク設定が基本ではなく、末尾のスラッシュが無い場合の接続文字のスラッシュを設定
		$slashstr = '/';// url 末尾に付けるスラッシュ

		$daylink = $wp_rewrite->get_day_permastruct();

		$timezone = 'Asia/Tokyo';

		$nowdate = new  DateTimeImmutable( '', new DateTimeZone( $timezone ) );
		$basestamp = $nowdate->getTimestamp();

		$elem = array();

		//表示(初期化時は現在)する年月
		$target_Ym = $nowdate->format( 'Ym' );// eq. 202201
		// 現在の年月
		$elem['cmonth'] = $target_Ym;

		// m=で日付テンプレートに日付情報を送信している場合
		if ( is_date() ) {
			if ( $wp_query->get( 'm' ) ) {

				// 日付関係のテンプレートを開く時に付くurlのオプション:m=の値、強制的に年月を指定する時使用
				$tmp_month_no = $wp_query->get( 'm' );

				// 日付まで付いている場合は年月だけにそろえる
				$tmp_month_no = substr( $tmp_month_no, 0, 6 );

			// 数字ベース等デフォルトのm=ではなく、date/2014/12/04/page/2などで日付情報を送信している場合
			} elseif ( $wp_query->get( 'monthnum' ) ) {
				$tmp_month_no = ( string ) $wp_query->get( 'year' ) . sprintf( '%02d', $wp_query->get( 'monthnum' ) );
			}

			if ( $tmp_month_no ) {	

				$tmpym = array( substr( $tmp_month_no, 0, 4 ), substr( $tmp_month_no, 4 ) );

				if ( checkdate( ( int ) $tmpym[1], 1, ( int ) $tmpym[0] ) ) {

					$target_Ym = $tmp_month_no;
					$curfull = $tmpym[0] . '/' . $tmpym[1] . '/01';
					$curnum = $tmpym[0] . $tmpym[1] . '01';
				}
			}
			$prm['en_cache'] = 0;
		} else {
			// 表示する月(初期化時は現在)の1日、曜日を得るのに必要
			$curfull = $nowdate->format( 'Y/m' ) . '/01';
			$curnum = $target_Ym . '01';
		}

		// $tarstampは表示する月のタイムスタンプ
		$tardate = new  DateTimeImmutable( $curfull, new DateTimeZone( $timezone ) );
		$tarstamp = $tardate->getTimestamp();

		// 本日の日にち
		$elem['today'] = $nowdate->format( 'd' );

		// ↓ここからcache file 読み込み処理
		// 日付アーカイブが呼ばれているかの判断
		if ( $target_Ym !== $elem['cmonth'] ) {
			$now_day = '';
			$todayid = 0;
		} else {
			$now_day = $elem['today'];
			$todayid = ( int ) $elem['today'];
		}

		if ( '0' === $prm['en_grid'] ) {
			$torg = 'table';
		} else {
			$torg = 'grid';
		}

		if ( '1' === $prm['acvoptorlist'] ) {
			$arcvdrpdwnscpt = $this->footer_script();
		} else {
			$arcvdrpdwnscpt = '';
		}

		$info_calendar = $target_Ym . $now_day . '_l' . $prm['lang'] . 'w' . $prm['wf'] . 'm' . $prm['monthly'] . '_' . $torg . '_' . $prm['postype'] . '_o' . $prm['acvoptorlist'] . 'd' . $prm['daypostlink'];

		$cache_file = '';
		$en_cache = ( ( int ) $prm['en_cache'] & 0b11111 );// 0b11111 -> 31 にあまり意味はない、31までは有効な数字

		if ( $en_cache ) {
			$cache_file = plugin_dir_path( __FILE__ ) . '/cache/hcpcldr_cache_' . ( string ) $en_cache . '_' . $info_calendar . '.php';
		}

		// 管理画面において server side render における処理では is_admin は決まって false を返す
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$en_cache = 0;
		}

		// $prm['en_cache'] = '0';

		if ( $en_cache ) {

			if ( file_exists( $cache_file ) ) {

				include_once $cache_file;

				if ( $contents ) {
					// $ret_cont = $cache[1];

					if ( $this->call_count > 1 ) {
						$str_callcount = ( string ) $this->call_count;
						$chilbis = '"chlbis-' . $str_callcount . '-';
						$shwchl = '"shwchl-' . $str_callcount . '-';
						$calendarid = 'id="wp-calendar-' . $str_callcount . '"';
						$contents = str_replace( array( '"chlbis-', '"shwchl-', 'id="wp-calendar"' ), array( $chilbis, $shwchl, $calendarid ), $contents );
					}
					
					$contents .= $arcvdrpdwnscpt . '<p style="font-size:0.8em;color:#E1E1A1;padding:0;margin:0;text-align:right;"><span id="cachemark">*</span></p>' . "\n";
					return $contents;
				}	
			}	
		}
		// ↑ここまでcache file 処理

		// パーマリンクが基本ではなくカスタム設定の場合
		if ( ! empty($daylink) ) {

			// パーマリンク設定によるurlに表示される年月の形式
			$dateform = 'Y/m';

			if ( ! $wp_rewrite->use_trailing_slashes ) {
				// パーマリンク設定が基本ではなく、末尾のスラッシュが無い場合、接続文字のスラッシュを設定
				$trailslash = '/';
				// ゆえに末尾のスラッシュは必要なし
				$slashstr = '';
			}
			// 基本となるリンクを置換するための年月表示
			$tmonthform = substr( $target_Ym, 0, 4 ) . '/' . substr( $target_Ym, 4 );
		} else {

			// パーマリンク設定によるurlに表示される年月の形式
			$dateform = 'Ym';
			$slashstr = '';

			$tmonthform = $target_Ym;
		}

		// 表示する年
		$tyear = substr( $target_Ym, 0, 4 );

		// 表示する月
		$tmonth = substr( $target_Ym, 4 );

		// 表示する月の前月
		$lastmonth = new  DateTime( $curfull, new DateTimeZone( $timezone ) );
		$nextmonth = clone $lastmonth;
		$lastmonth->modify( 'first day of last months' );
		$elem['pmonth'] = $lastmonth->format( $dateform );

		// 表示する月の次月
		if ( $target_Ym === $elem['cmonth'] ) {

			// 表示する月が現在の月ならば翌月へのリンクは無。
			$elem['nmonth'] = '';
		} else {

			// 表示する月の翌月
			$nextmonth->modify( 'first day of next months' );
			$elem['nmonth'] = $nextmonth->format( $dateform );	
		}

		// 表示する月の末日
		$elem['lddate'] = ( int ) $tardate->format( 't' );

		// 表示する月の1日の曜日 0:日, 1:月, 2:火, 3:水, 4:木, 5:金, 6:土
		$elem['fdwd'] = ( int ) $tardate->format( 'w' );

		$dateary = array_fill( 1, $elem['lddate'], '' );
		$dateary[1] = 'gettan';

$sql=<<<HERE
SELECT ID,post_date,post_title FROM $wpdb->posts
WHERE post_type = %s
AND post_status = 'publish'
AND DATE_FORMAT(post_date, '%%Y%%m' ) =%s
ORDER BY post_date ASC
HERE;

		$months = $wpdb->get_results( $wpdb->prepare( $sql, $prm['postype'], $target_Ym ) ); // $target_Ym →例：201409

		$ispost = array_fill( 0, $elem['lddate'] + 1, array() );

		foreach ( $months as $val ) {

			$tmpdate = ( int ) substr( $val->post_date, 8, 2 );
			$ispost[ $tmpdate ][] = array( $val->ID, $val->post_title );
		}
		$ispost = array_filter( $ispost );

		$alp_num = array( 'a'=>'01','b'=>'02','c'=>'03','d'=>'04','e'=>'05','f'=>'06','g'=>'07','h'=>'08','i'=>'09','j'=>'10','k'=>'11','l'=>'12','m'=>'13','n'=>'14','o'=>'15','p'=>'16','q'=>'17','r'=>'18','s'=>'19','t'=>'20','u'=>'21','v'=>'22','w'=>'23','x'=>'24','y'=>'25','z'=>'29','!'=>'30' );

		$holidays = array();

		if ( '' === $prm['myholidays'] ) {
			//↓2013～2025までの休日データ、ちなみに最後のykxは251124(2025/11/24のこと)を表す
			$holidays_data = 'maa,man,mbk,mct,mdz,mec,med,mef,mgo,mip,miw,mjn,mkd,mkw,mlw,naa,nam,nbk,ncu,ndz,nec,nee,nef,ngu,nio,niw,njm,nkc,nkx,nlw,oaa,oal,obk,ocu,odz,oed,oee,oef,ogt,oiu,oiv,oiw,ojl,okc,okw,olw,paa,pak,pbk,pcu,pdz,pec,ped,pee,pgr,phk,pis,piv,pjj,pkc,pkw,plw,qab,qai,qbk,qct,qdz,qec,qed,qee,qgq,qhk,qir,qiw,qji,qkc,qkw,qlw,raa,rah,rbl,rcu,rd!,rec,red,ree,rgp,rhk,riq,rix,rjh,rkc,rkw,rlx,saa,san,sbk,scu,sdz,sd!,sea,seb,sec,sed,sef,sgo,shl,sip,siw,sjn,sjv,skd,skw,taa,tam,tbk,tbx,tct,tdz,ted,tee,tef,tgw,tgx,thj,tiu,tiv,tkc,tkw,uaa,uak,ubk,ubw,uct,udz,uec,ued,uee,ugv,ugw,uhi,uit,uiw,ukc,ukw,vaa,vaj,vbk,vbw,vcu,vdz,vec,ved,vee,vgr,vhk,vis,viw,vjj,vkc,vkw,wab,wai,wbk,wbw,wcu,wdz,wec,wed,wee,wgq,whk,wir,wiw,wji,wkc,wkw,xaa,xah,xbl,xbw,xct,xdz,xec,xed,xef,xgo,xhl,xip,xiw,xjn,xkd,xkw,yaa,yam,ybk,ybx,yct,ydz,yec,yee,yef,ygu,yhk,yio,yiw,yjm,ykc,ykx';
			$holidays = explode( ',', $holidays_data );

			$alp_num_flip = array_flip( $alp_num );

			$sameflg = 0;
			if ( $target_Ym ) {
				$alp_year = $alp_num_flip[ substr( $target_Ym, 2, 2 ) ];
				$alp_month = $alp_num_flip[ substr( $target_Ym, 4, 2 ) ];

				foreach ( $holidays as $val ) {
					if ( $alp_year === $val[0] ) {
						if ( $alp_month === $val[1] ) {
							$tmpnum = ( int ) $alp_num[ $val[2] ];

							if ( isset ( $dateary[ $tmpnum ] ) ) {

								$dateary[ $tmpnum ] = 'nitiyou';
							}
							$sameflg = 1;	
						}
					} else {
						if ( 1 === $sameflg ) {
							break;
						}
					}
				}
			}

			$alp_num = null;
			$holidays_data = null;

		} else {
			// 設定されている祝祭日リストを使用しない時の処理。日付指定は1ケタ、2ケタ、3ケタ、4ケタ、6ケタ限定。ex:3,101,1224,200607
			$holidays = explode( ',', $prm['myholidays'] );
			foreach ( $holidays as $val ) {

				if ( ( int ) $val > 0 ) {
					$tmpnum = 0;

					if ( isset ( $val[2] ) ) {
						$tmpval = sprintf( '%04d', $val );
					} else {// 2ケタないし1ケタの場合
						$tmpval = sprintf( '%02d', $val );
					}
					$strlenval = strlen( $tmpval );

					$fulldate = substr( $curnum, 0, ( 8 - $strlenval ) ) . $tmpval;
					$tmpnum = ( int ) substr( $fulldate, 6 );

					if ( $fulldate ) {
						if ( $target_Ym === substr ( $fulldate, 0, 6 ) ) {
							$tmpnum = ( int ) substr( $fulldate, 6, 2 );
							if ( isset( $dateary[ $tmpnum ] ) ) {
								$dateary[ $tmpnum ] = 'nitiyou';
							}
						}
					}
				}
			}
		}

		// 設定されている祝祭日リストに追加、または無視する日付の処理。無視する日付には語尾に'-'を付加する。
		// 3,105,0322-,1108-,20200606
		if ( $prm['adddeldays'] ) {
			$adddel = array();
			if ( is_array( $prm['adddeldays'] ) ) {
				$adddel = $prm['adddeldays'];
			} else {
				$adddel = explode( ',', $prm['adddeldays'] );
			}

			if ( $adddel ) {
				foreach ( $adddel as $val ) {
					$negflg = false;

					if ( '-' === $val[-1] ) {
						$strlenval = strlen( $val );
						$negflg = true;
						$tmpval = substr ( $val, 0, ( $strlenval - 1 ) );
					} else {
						$tmpval = $val;
					}

					if ( isset ( $tmpval[2] ) ) {
						$tmpval = sprintf( '%04d', $tmpval );
					} else {// 2ケタないし1ケタの場合
						$tmpval = sprintf( '%02d', $tmpval );
					}

					$strlenval = strlen( $tmpval );

					$fulldate = substr( $curnum, 0, ( 8 - $strlenval ) ) . $tmpval;

					if ( $fulldate ) {
						if ( $target_Ym === substr ( $fulldate, 0, 6 ) ) {
							$tmpnum = ( int ) substr( $fulldate, 6, 2 );
							if ( isset( $dateary[ $tmpnum ] ) ) {
								if ( $negflg ) {
									if ( $dateary[ $tmpnum ] ) {
										$dateary[ $tmpnum ] = '';
									}
								} else {
									$dateary[ $tmpnum ] = 'nitiyou';
								}
							}
						}
					}
				}
			}
		}

		// その月の日曜日の日にちを求める
		$sunnum = $this->ret_daynum( 1, $elem['fdwd'], $elem['lddate'] );
		foreach ( $sunnum as $val ) {
			$dateary[ $val ] = 'nitiyou';
		}

		// その月の土曜日の日にちを求める
		$satnum = $this->ret_daynum( 7, $elem['fdwd'], $elem['lddate'] );
		foreach ( $satnum as $val ) {
			if ( isset ( $dateary[ $val ] ) ) {
				$dateary[ $val ] .= ' doyou';
			}
		}
		
		// 独自休日を曜日で指定、1:日曜～7:土曜
		if ( $prm['closewd'] ) {
            if ( is_array( $prm['closewd'] ) ) {
				$tmpwd = $prm['closewd'];
			} else {
				$tmpwd = explode( ',', $prm['closewd'] );
			}
			// 指定された各曜日のその月の初めの日にちを、その月の1日の曜日から割り出し、その月の指定されたすべての曜日の日にちに対してクラスを指定する
			foreach( $tmpwd as $val ) {
				$wdnum = $this->ret_daynum( $val, $elem['fdwd'], $elem['lddate'] );
				foreach ( $wdnum as $num ) {
					$dateary[ $num ] .= ' closewd';
				}
			}
		}
	
		// 指定された独自休日。配列でなければ配列にする。毎月同じ休日等の場合は日にちで指定可。
		// 例:array('3','10','202','0928','20140911','20140919')
		$closel = array();
		if ( $prm['closel'] ) {
			if ( is_array( $prm['closel'] ) ) {
				$closel = $prm['closel'];
			} else {
				$closel = explode( ',', $prm['closel'] );
			}

			if ( $closel ) {
				foreach ( $closel as $val ) {
					if ( $val ) {
						if ( isset ( $val[2] ) ) {
							$tmpval = sprintf( '%04d', $val );
						} else {// 2ケタないし1ケタの場合
							$tmpval = sprintf( '%02d', $val );
						}
			
						$tmpnum = 0;
						$strlenval = strlen( $tmpval );

						$fulldate = substr( $curnum, 0, ( 8 - $strlenval ) ) . $tmpval;

						if ( $fulldate ) {
							if ( $target_Ym === substr ( $fulldate, 0, 6 ) ) {
								$tmpnum = ( int ) substr( $fulldate, 6, 2 );

								if ( isset ( $dateary[ $tmpnum ] ) ) {
									$dateary[ $tmpnum ] .= ' closedy';
								}		
							}
						}
					}
				}
			}
		}

		// 独自休日とは別の特別な日を配列にして指定、４ケタか３ケタ または8ケタ、8ケタの場合はclassがlanniversaryになりまた別のスタイル指定が可能。
		//日付の後に:に続けて文字列を指定することで、マウスオン時にtitleに表示させたることも可能、例:array('903','0130:My birth','204','20170829:site open!')
		$anniver = array();
		if ( $prm['anniver'] ) {
			if ( is_array( $prm['anniver'] ) ) {
				$anniver = $prm['anniver'];
			} else {
				$anniver = explode( ',', $prm['anniver'] );
			}

			if ( $anniver ) {
				foreach ( $anniver as $val ) {
					$classname = ' anniversary';
					$tmpnum = 0;
					$annielem = array();
					$fulldate = '';
	
					$annielem = explode( ':', $val );
					if ( isset( $annielem[1] ) and $annielem[1] ) {
						$annielem[1] = esc_html( $annielem[1] );
					} else {
						$annielem[1] = 'anniversary';
					}

					$tmpval = sprintf( '%04d', $annielem[0] );
					$strlenval = strlen( $tmpval );

					if ( 8 === $strlenval ) {
						$fulldate = $annielem[0];
						$classname = ' lanniversary';
					} else{
						$fulldate = substr( $curnum, 0, ( 8 - $strlenval ) ) . $tmpval;		
					}

					if ( $fulldate ) {
						if ( $target_Ym === substr ( $fulldate, 0, 6 ) ) {
							$tmpnum = ( int ) substr( $fulldate, 6, 2 );
							if ( isset ( $dateary[ $tmpnum ] ) ) {
								$dateary[ $tmpnum ] .= $classname . '" title="' . $annielem[1];
							}
						}
					}
				}
			}
		}

		if ( isset ( $dateary[ $todayid ] ) ) { 
			$dateary[ $todayid ] .= ' tdcls" id="today';
		}

		// 基本とする月アーカイブへのリンク　例：localhost/wp/2016/12/ or localhost/wp/?m=201612
		$baselink = get_month_link( $tyear, $tmonth );
		$en_grid = 'table';
		$elem_grid = array(
			'0' => 'table',
			'1' => ' style="display:grid;grid-template-columns:repeat( 7, 1fr );text-align:center;"',
			'2' => '',
		);

		if ( $prm['en_grid'] ) {

			if ( isset ( $elem_grid[ $prm['en_grid'] ] ) ) {
				$en_grid = $elem_grid[ $prm['en_grid'] ];
			} else {
				$en_grid = ' style="' . esc_attr( $prm['en_grid'] ) . '"';
			}
		}

		if ( 'table' === $en_grid ) {
			include 'table_layout.php';
		} else {
			$tmpstrary =array();

			$tmpstrary[] =  "\n<!-- Holiday-class Post Calendar Plugin grid version v" . $this->version . " " . $nowdate->format( 'Y/m/j H:i' ) . " -->\n";
			$parentstyle = '';
			if ( '' !== $prm['parentstyle'] ) {
				$parentstyle = ' style="' . esc_attr( $prm['parentstyle'] ) . '"';
			}
			$tmpstrary[] = '<div id="wp-calendar"' . $parentstyle . '>' . "\n";
			$showwd=array();
			$showsm=array();
			$addcapt = '';
		
			if ( $prm['capt'] ) {
				$addcapt = '<br>' . esc_html( $prm['capt'] );
			}
		
			if ( 'j' === $prm['lang'] ) {
				$heisei = '';
				$nengo = '';		

				$syubundate = new  DateTimeImmutable( '2019-05-01', new DateTimeZone( $timezone ) );
				$syubun = $syubundate->getTimestamp();

				if ( $tarstamp < $syubun ) {

					$heisei = ( string ) ( ( int ) $tardate->format( 'y' ) + 12 );
					$nengo = '平成';
				} else {

					$heisei = ( string ) ( ( int ) $tardate->format( 'y' ) - 18 );
					if ( '1' === $heisei ) {
						$heisei = '元';
					}
					$nengo = '令和';
				}
				$tmpstrary[] = '<p class="cal-ym">' . $tyear . ', ' . $nengo . $heisei . '年' . ( string)( ( int )$tmonth ) . '月' . $addcapt . '</p>' . "\n";

				$showwd = array( [ '日', ' class="nitiyou"' ], [ '月', '' ], [ '火', '' ], [ '水', '' ], [ '木', '' ], [ '金', '' ], [ '土', ' class="doyou"'], [ '日', ' class="nitiyou"' ] );
				$showsm = array( '12月', '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月', '1月' );
			}else{
				$fullengm = array( ' January', ' February', ' March', ' April', ' May', ' June', ' July', ' August', ' September', ' October', ' November', ' December' );
				$tmpstrary[] ='<p class="cal-ym">' . $fullengm[ ( ( int ) $tmonth - 1 ) ] . '&ensp;' . $tyear . $addcapt . '</p>' . "\n";

				$showwd = array( [ 'Su', ' class="nitiyou"' ], [ 'Mo', '' ], [ 'Tu', '' ], [ 'We', '' ], [ 'Th', '' ], [ 'Fr', '' ], [ 'Sa', ' class="doyou"' ], [ 'Su', ' class="nitiyou"'] );
				$showsm = array( 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan' );
			}

			if ( 's' === $prm['wf'] ) {
				unset ( $showwd[7] );
				$rowstart = $elem['fdwd'] + 1;
			} else {
				unset ( $showwd[0] );
				if ( 0 === $elem['fdwd'] ) {
					$rowstart = $elem['fdwd'] + 7;
				} else {
					$rowstart = $elem['fdwd'];
				}
			}

			$tmpstrary[] = '<div class="cal-wd"' . $en_grid . '>';
		
			foreach ( $showwd as $val ) {
				$tmpstrary[] = '<b' . $val[1] . ' title="' . $val[0] . '">' . $val[0] . '</b>';
			}
		
			$tmpstrary[] = '</div>' . "\n";
			$nextmstr = '&nbsp;';
				
			$tmpstrary[] = '<div class="cal-body"' . $en_grid . '>';
		
			$dateary[1] .= '" style="grid-column-start:' . ( string ) $rowstart . ';';
			// $dateary[1] .= ' gettan" style="grid-column-start:' . ( string ) $rowstart . ';';// gettan -> 月旦の意

			foreach ( $dateary as $i => $val ) {
		
				$tmpclnd = '';
				$hdclsstr = '';
		
				if ( $val ) { 
					$hdclsstr = ' class="' . trim( $val ) . '"';
				}
				$tmpstrary[] = '<div' . $hdclsstr;
		
				if ( isset( $ispost[ $i ] ) ) {
					$dayurl = $baselink . $trailslash . sprintf( '%02s',  $i ) . $slashstr;
					if ( 'post' !== $prm['postype'] ) {
						$dayurl = esc_url( add_query_arg( 'post_type', $prm['postype'], $dayurl ) );
					}
					$linkday = $target_Ym . sprintf( '%02d', $i );
		
					$postdatalink = array();
				
					if ( '1' === $prm['daypostlink'] ) {
						foreach ( $ispost[ $i ] as $value ) {

							if ( 'post' !== $prm['postype'] ) {

								$postdatalink[] = '<span class="daychildrenmark"><a href="' . get_post_permalink( $value[0] ) . '">' . $value[1] . '</a></span>';
							} else {
								$postdatalink[] = '<span class="daychildrenmark"><a href="' . get_permalink( $value[0] ) . '">' . $value[1] . '</a></span>';
							}
						}
		
						$tmpstrary[] = ' data-post="y"><a href="' . $dayurl . '">' . $i . '</a><span class="daychildren">' . implode( '<br>', $postdatalink ) . '</span></div>';
					} else {
						foreach ( $ispost[ $i ] as $value ) {

							$postdatalink[] = $value[1];
						}
						$tmpstrary[] = '><a href="' . $dayurl . '" title="' . implode( ',', $postdatalink ) . '">' . $i . '</a></div>';
					}
				} else {
					$tmpstrary[] = '>' . $i . '</div>';
				}
			}
			
			$tmpstrary[] = '</div>' . "\n";
		
			// 下部に追加表示する文字列 :
			// 複数も可。その場合は,（半角カンマ）で区切って指定。それぞれに色等を指定するためのidを付加する場合は:で区切って指定。例：毎週金はお休み,毎月3日は特売日:hclred,営業11時-20時:hclblue
			$footer = '';
		
			if ( $prm['footer'] ) {
				$footerary = explode( ',', $prm['footer'] );
				foreach ( $footerary as $val ) {
					$footerstr = array();
					$footerstr = explode( ':', $val );
					if ( isset( $footerstr[0] ) and $footerstr[0] ) {
						$footerstr[0] = esc_html( $footerstr[0] );
					} else {
						$footerstr[0] = '';
					}
					if ( isset( $footerstr[1] ) and $footerstr[1] ) {
						$footerstr[1] = esc_attr( $footerstr[1] );
					} else {
						$footerstr[1] = '';
					}
					if ( $footerstr[0] ) {
						$tdclass = '';
						if ( $footerstr[1] ) {
							$tdclass = ' id="' . $footerstr[1] . '"';
						}
						$footer .= '<p ' . $tdclass . '>' . $footerstr[0] . '</p>';
					}
				}
				$footer = '<div class="cal-btmstr">' . $footer . '</div>';
			}
		
			$tmpstrary[] = $footer;

			if ( '' !== $elem['nmonth'] ) {
				$monurl = str_replace( $tmonthform, $elem['nmonth'], $baselink );
				if ( 'post' !== $prm['postype'] ) {
					$monurl = esc_url( add_query_arg( 'post_type', $prm['postype'], $monurl ) );
				}
				$nextmstr = '<a href="' . $monurl . '">' . $showsm[ ( ( int ) $tmonth + 1 ) ] . ' &raquo;</a>';
			}
			$monurl = str_replace( $tmonthform, $elem['pmonth'], $baselink );
			if ( 'post' !== $prm['postype'] ) {
				$monurl = esc_url( add_query_arg( 'post_type', $prm['postype'], $monurl ) );
			}
			$tmpstrary[] = '<div class="cal-footer" style="display:grid;grid-template-columns:1fr 1fr;"><div id="lclprevm"><a href="' . $monurl . '">&laquo; ' . $showsm[ ( ( int ) $tmonth - 1 ) ] . '</a></div><div id="hclnextm">' . $nextmstr . '</div></div>' . "\n";

			$tmpstrary[] = '</div>' . "\n";
		}

		if ( '1' === $prm['monthly'] ) {
			$acvstr = 'Monthly Archives';
			$smstr = 'Select Month';
			$nen = '';
			$tuki = '';
			if ( '' !== $prm['acvheader'] ){
				$acvstr = esc_html( $prm['acvheader'] );
			} elseif( 'j' === $prm['lang'] ) {
				$acvstr = '月別アーカイブ';
				$smstr = '月を選択';
				// $nen = '年';
				// $tuki = '月';
			}
			$tmpstrary[] = '<h2 class="widgettitle monacvlst">' . $acvstr . '</h2>' . "\n";
			$post_type = $prm['postype'];

			if ( 1 === ( int ) $prm['acvoptorlist'] ) {

				if ( $this->call_count > 1 ) {
					$curif = '-' . ( string ) $this->call_count;
				} else {
					$curif = '';
				}
				$tmpstrary[] = '<select name="archive-dropdown" id="archive-dropdown' . $curif . '"><option value="">' . $smstr . '</option>' . "\n";

				$monthlisttmp = wp_get_archives( "type=monthly&format=option&show_post_count=1&echo=0&post_type=$post_type" );

				if ( $monthlisttmp ) {// 管理画面において wp_get_archives は値が得られない
					$tmpstrary[] = str_replace( array( '月', '年' ), array( '', '/' ), $monthlisttmp );
				} else {
					$tmpstrary[] = '<option>month-list</option>';
				}
				$tmpstrary[] = '</select>' . "\n";

			} else {
				$monthlisttmp = wp_get_archives( array(
					'type' => 'monthly',
					'format' => 'custom',
					'show_post_count' => true,
					'after' => '!',
					'echo' => false,
					'post_type' => $post_type,
				)  );

				$tarstr = '/20';
				$addnum = 1;

				if ( $monthlisttmp ) {
					$mlary = explode( '!', $monthlisttmp );
					$closeul = '';
					if ( false !== strpos( $mlary[0], '?m=' ) ) {
						$tarstr = 'm=2';
						$addnum = 2;
					}
				} else {
					$mlary = array();
					$closeul = '<li>month-list</li></ul>';
				}

				$prevyear = '1999';
				$curryear = '';
				$tmpstrary[] = '<ul class="monthlylist">' . $closeul;
				$ulflg = 0;
				$chkcnt = 1;

				//<a href='http://localhost/wp/archives/date/2016/11'>2016_11</a>&nbsp;(1)
				//<a href='http://localhost/wp/2022/09'>September 2022</a>&nbsp;(1)
				//<a href='http://localhost/wp/?m=202209'>2022年9月</a>&nbsp;(1)
				foreach ( $mlary as $val ) {
					if ( strlen( $val ) > 10 ) {
						$mltmp = str_replace( array( '年', '月', '\'' ), array( '_', '', '"' ), trim( $val ) );
						$curryear = substr( $mltmp, strrpos( $mltmp, $tarstr ) + $addnum, 4 );

						if ( $curryear !== $prevyear ) {
							if ( 1 === $ulflg ) {
								$tmpstrary[] = '</ul></li>' . "\n";
							}
							$tmpstrary[] = '<li class="arcyear"><label for="chlbis-' . $chkcnt . '" class="chlshwlbl" id="shwchl-' . $chkcnt . '">' . $curryear . $nen . '</label><input type="checkbox" name="chlbis-' . $chkcnt . '" id="chlbis-' . $chkcnt . '"><ul class="mlchildren">' . "\n";
							$ulflg = 1;
							++$chkcnt;
						}
						$tmpstrary[] = '<li>' . str_replace( array( $curryear . '_', ' ' . $curryear . '<' ), array( ' ', ' <' ), $mltmp ) . '</li>' . "\n";

						$prevyear = $curryear;
					}
				}
				if ( $ulflg ) {
					$tmpstrary[] = '</ul></li></ul>';
				}
			}
		}
		$clnd = implode( '', $tmpstrary );
		$ret_cont = $clnd;


		if ( $en_cache ) {
			$clnd = ( string ) $en_cache . '_' . $info_calendar . chr(1) . "\n<!-- cache data -->\n" . $clnd;

			$this->cache_cont = $clnd;
			add_action( 'wp_footer', array( $this, 'footer_write') );	
		}

		if ( $this->call_count > 1 ) {
			$str_callcount = ( string ) $this->call_count;
			$chilbis = '"chlbis-' . $str_callcount . '-';
			$shwchl = '"shwchl-' . $str_callcount . '-';
			$calendarid = 'id="wp-calendar-' . $str_callcount . '"';
			$ret_cont = str_replace( array( '"chlbis-', '"shwchl-', 'id="wp-calendar"' ), array( $chilbis, $shwchl, $calendarid ), $ret_cont );
		}

		return $ret_cont . $arcvdrpdwnscpt;
	}
	
	public function footer_write() {

		if ( $this->cache_cont ) {
			$ajaxurl = admin_url( 'admin-ajax.php' );
			$nonce = wp_create_nonce(__FILE__);

			$cpsnonce = $this->get_cpsnonce();
			$tmpstr = $this->cache_cont;
			$strsend = explode( chr(1), $this->cache_cont );
?>
			<script<?php echo $cpsnonce; ?>>
				( function () {
					window.addEventListener( 'load', function() {
						const url = '<?php echo $ajaxurl; ?>',
							strsend = 'action=hcpcldrsavecache&nonce=<?php echo $nonce; ?>&fileid=<?php echo $strsend[0]; ?>&contents=<?php echo urlencode( $strsend[1] ); ?>',
							req=new XMLHttpRequest();

						req.onreadystatechange = function() {
							if (req.readyState == 4) { // 通信の完了時
								if (req.status == 200) { // 通信の成功時
									console.log( req.responseText );
								}
							}
						}
						req.open( 'POST', url, true );
						req.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
						req.send( strsend );
					});
				})();
			</script>
<?php
		}
	}

	public function hcpcldr_save_cache() {

		//nonceを取得して認証を得る
		/*if ( isset( $_POST['nonce'] ) ) {
			$nonce = $_POST['nonce'];
		} else {
			echo 'error:none nonce data.';
			exit();
		}

		if ( wp_verify_nonce( $nonce, __FILE__) ) {*/
        if ( check_ajax_referer( __FILE__, 'nonce', false ) ) {

			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			if ( WP_Filesystem() ) {
				global $wp_filesystem;

				if ( isset ( $_POST['fileid'] ) ) {
					$fileid = $_POST['fileid'];
				} else {
					$fileid = '';
				}
				if ( isset ( $_POST['contents'] ) ) {
					$contents = str_replace( array( '\"', '\\\'', '\\\\' ), array( '"', '\'', '\\' ), $_POST['contents'] );
					$contents = '<?php' . "\n" . '$contents=<<<HERE' . "\n" . $contents . "\n" . 'HERE;' . "\n" . '?>';
				} else {
					$contents = '';
				}

				if ( $fileid and $contents ) {

					$cache_dir = plugin_dir_path( __FILE__ ) . 'cache/';
					$cache_dir_files = scandir( $cache_dir );
					$cache_dir_files = array_diff( $cache_dir_files, array( '.', '..' ) );
		
					if ( $cache_dir_files ) {

						$file_ids = explode( '_', $fileid );
						if ( isset ( $file_ids[1] ) ) {
							$current_cache_date = $file_ids[1];
			
							foreach ( $cache_dir_files as $file ) {
								if ( false !== strpos( $file, 'hcpcldr_cache_' ) ) {

									if ( false === strpos( $file, $current_cache_date ) ) {

										$filefull = $cache_dir . $file;
										$wp_filesystem->delete ( $filefull, false, 'f' );
									}
								}
							}
						}
					}

					$cache_file = $cache_dir . 'hcpcldr_cache_' . $fileid . '.php';
			
					if ( $wp_filesystem->put_contents( $cache_file, $contents ) ) {
						echo 'Success: hcpcldr cache file saved : id-' . $fileid[0] . '.';
					} else {
						echo 'error:Disable hcpcldr cache file saved : id-' . $fileid[0] . '.';
					}
				} else {
					echo 'error:no data hcpcldr fileid or contents.';
				}
			}
		} else {
			echo 'error:hcpcldr requests are denied.';
		}
	}

	public function save_del_cache( $ope = 0 ) {// publish_post hook
		
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$result = '';

		if ( WP_Filesystem() ) {
			global $wp_filesystem;

			// $target_file = plugin_dir_path( __FILE__ ) . '/cache/hcpcldr_cache_' . ( string ) $i . '.php';
	
			$cache_dir = plugin_dir_path( __FILE__ ) . 'cache/';
			$cache_dir_files = scandir( $cache_dir );
			$cache_dir_files = array_diff( $cache_dir_files, array( '.', '..' ) );


			if ( $cache_dir_files ) {
				$ans = array( 'y' => array(), 'n' => array() );

				foreach ( $cache_dir_files as $file ) {
					if ( false !== strpos( $file, 'hcpcldr_cache_' ) ) {
						$filefull = $cache_dir . $file;
						if ( $wp_filesystem->delete ( $filefull, false, 'f' ) ) {
							$ans['y'][] = $file;
						} else {
							$ans['n'][] = $file;
						}
					}
				}
				if ( count ( $ans['n'] ) ) {
					$result = 'error: Some hcpcldr files could not be deleted : ' . count ( $ans['n'] ) . '!';
				} else {
					$result = 'Success: Deleted all hcpcldr cache file : ' . count ( $ans['y'] ) . '.';
				}
			} else {
				$result = 'error: None hcpcldr file in the target directly.';
			}
		} else {
			$result = 'error: Disabled WP_Filesystem call by hcpcldr.';
		}

		if ( $ope ) {
			return $result;
		}
	}

	public function hcpcldr_delete_cache() {

        if ( ! function_exists( 'wp_get_current_user' ) ) {
            exit;
        }
        // $user_id = get_current_user_id();

        // if ( 1 === $user_id ) {
		if ( current_user_can( 'administrator' ) ) {
            //nonceを取得して認証を得る
            // $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : null;

			$special_string = plugin_dir_path( __FILE__ ) . 'cache/hcpcldr_cache';

            // if ( wp_verify_nonce( $nonce, wp_create_nonce( __FILE__ ) ) ) {
            if ( check_ajax_referer( $special_string, 'nonce', false ) ) {

				$result =  $this->save_del_cache(1);
				echo $result;
			} else{
				echo 'error: Not authorized access - call by hcpcldr.';
			}
		} else {
			echo 'error: Current user has been denied - call by hcpcldr.';
		}
		exit;
	}
}// end class

if ( ! isset ( $hldycls_post_calendar_start ) ) {

	$hldycls_post_calendar_start = new hldycls_post_calendar();

	// ↓ここから管理画面のメニューにオプション設定ページを登録する処理
	if ( is_admin() ) {
		add_action( 'admin_menu', array( $hldycls_post_calendar_start, 'hldycls_pcldr_add_menu' ) );
	}

	// ↓カレンダーを表示するメインとなる関数
	if ( ! function_exists( 'holiday_calendar_echo' ) ) {
		function holiday_calendar_echo() {
			global $hldycls_post_calendar_start;

			$rargs = array();
			$args = func_get_args();
			if ( func_num_args() > 0 ) {
				$rargs = $args[0];
			}
			$ret = $hldycls_post_calendar_start->holiday_class_post_calendar( $rargs );
			echo $ret;
		}
	}

	if ( ! function_exists( 'hldyclspc_getarchives_where' ) ) {
		// 月別アーカイブリストを得るwp_get_archives()においてpost_typeを指定するためのフック
		add_filter( 'getarchives_where', 'hldyclspc_getarchives_where', 10, 2 );

		function hldyclspc_getarchives_where( $where, $r ) {
			if ( isset( $r['post_type'] ) ) {
				$where = str_replace( '\'post\'', '\'' . $r['post_type'] . '\'', $where );
			}
			return $where;
		}
	}
}
} // if class
?>
