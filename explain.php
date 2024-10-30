			<style>
				#opcontable :is(input,select){box-shadow:3px 0px 0px rgba(0,0,255,0.5),0px 3px 0px rgb(79,249,167,0.5);}
				#opcontable button{padding:5px;border-radius:5px;background:linear-gradient(to right,rgb(249, 249, 224),white,rgb(249, 249, 224));}
				#opcontable button:hover{background:linear-gradient(to right,white,rgba(0,0,255,0.5),white);}
				#opcontable button:disabled{background:linear-gradient(to right,rgba(0,0,0,0.3),white,rgba(0,0,0,0.3));}
			</style>
			<table id="opcontable" class="form-table"  style="font-size:1.3em;letter-spacing:0.1em;padding:10px;background:white;border-radius:10px;box-shadow:2px 2px 3px 3px rgba(0,0,0,0.3);">
			<tr>
			<td>表示する言語の選択 : </td>
			<td><select name="hldycls_pcldr_options[lang]">
			<?php
				$selected = array( '', '' );
				if ( 'e' === $this->ret_option['lang'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<option value="j"<?php echo $selected[1]; ?>>Japanese</option>
			<option value="e"<?php echo $selected[0]; ?>>English</option>
			</select></td><td></td>
			</tr>
			<tr>
			<td>週の始めを日曜か月曜かの指定 : </td>
			<td><select name="hldycls_pcldr_options[wf]">
			<?php
				$selected = array( '', '' );
				if ( 'm' === $this->ret_option['wf'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<option value="s"<?php echo $selected[1]; ?>>Sunday</option>
			<option value="m"<?php echo $selected[0]; ?>>Monday</option>
			</select></td><td></td>
			</tr>
			<tr>
			<td>表題に追加表示する文字列 : </td>
			<td><input type="text" name="hldycls_pcldr_options[capt]" value="<?php echo esc_attr( $this->ret_option['capt'] ); ?>"></td>
			<td></td>
			</tr>
			<tr>
			<td>下部に追加表示する文字列 : </td>
			<td><input type="text" name="hldycls_pcldr_options[footer]" value="<?php echo esc_attr( $this->ret_option['footer'] ); ?>"></td>
			<td>複数も可。その場合は ","（半角カンマ）で区切って指定。それぞれに色等を指定するための id を付加する場合は ":"で区切って指定。例：毎週金はお休み,毎月3日は特売日:hclred,営業11時-20時:hclblue</td>
			</tr>
			<tr>
			<td>独自休日を曜日で指定 : class="closewd"</td>
			<td><input type="text" name="hldycls_pcldr_options[closewd]" value="<?php echo esc_attr( $this->ret_option['closewd'] ); ?>"></td>
			<td>毎週水曜日だとか。1が日曜、7が土曜で1～7までの数字で指定。複数ももちろん可。その場合は ","（半角カンマ）で区切って指定。</td>
			</tr>
			<tr>
			<td>独自休日を日にちで指定 : class="closedy"</td>
			<td><input type="text" name="hldycls_pcldr_options[closel]" value="<?php echo esc_attr( $this->ret_option['closel'] ); ?>"></td>
			<td>年月日８ケタ、月日４ケタないし３ケタ、日にち２ケタ以下で指定。日にちだけなら毎月に設定され、月日なら毎年。複数ももちろん可。その場合は ","（半角カンマ）で区切って指定。例：03,929,0927,20140911,20140920</td>
			</tr>
			<tr>
			<td>休日とは別に特別な日を指定 : class="anniversary" or 8ケタで"lanniversary"</td>
			<td><input type="text" name="hldycls_pcldr_options[anniver]" value="<?php echo esc_attr( $this->ret_option['anniver'] ); ?>"></td>
			<td>休日とは別に特別な日を指定。これは毎年の事なら４ケタもしくは３ケタを使用。その年だけなら年月日の8ケタ。複数ももちろん可。マウスオン時に表示させる文字列を指定する場合は、日付の後に ":"で区切り文字列を指定。例：929,0130,20170829:Site Open!</td>
			</tr>
			<tr>
			<td></td>
			<td colspan="2">＊同じ日に複数の休日classを指定することもできます。この場合スタイルシートのより後に書かれている設定が上書きされ適用されます。ただし、todayは id属性なので class属性よりも通常ははるかに優位です。 </td>
			</tr>
			<tr>
			<td>デフォルトで設定してある祝日リストに追加、または無視させる日付</td>
			<td><input type="text" name="hldycls_pcldr_options[adddeldays]" value="<?php echo esc_attr( $this->ret_option['adddeldays'] ); ?>"></td>
			<td>年月日８ケタか６ケタ、月日４ケタないし３ケタ、日にち２ケタ以下で指定。無視させる日付に "-"を付加 ","（半角カンマ）で区切って指定。例：3,929-,0927,20140911-</td>
			</tr>
			<tr>
			<td>カレンダーにリンクを表示させる投稿タイプ : </td>
			<td><select name="hldycls_pcldr_options[postype]">
			<?php
				$args = array(
					'public'   => true,
					'_builtin' => false
				);
				$output = 'names';
				$operator = 'and';

				$post_types = get_post_types( $args, $output, $operator ); 
				//array_unshift( $post_types, 'post', 'page');
				array_unshift( $post_types, 'post' );

				foreach ( $post_types  as $post_type ) {
					$selected = '';
					if ( $this->ret_option['postype'] === $post_type ) {
						$selected = ' selected';
					}
					echo '<option value="' . $post_type . '"' . $selected . '>' . $post_type . '</option>';
				}
			?>
			</select></td>
			<td>カレンダーに表示させる投稿の投稿タイプ、デフォルトは "post"。<br>カスタム投稿専用のテンプレートを使用している場合、そのカスタム投稿の日付アーカイブを表示させる場合は、別途カスタム分類用日付テンプレートが必要になります。</td>
			</tr>
			<tr>
			<td>カレンダー用のデフォルトスタイルシートのロード指定 : </td>
			<td><select name="hldycls_pcldr_options[loadstyle]">
			<?php
				$selected = array( '', '' );
				if ( '0' === $this->ret_option['loadstyle'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<option value="1"<?php echo $selected[1]; ?>>ロードする</option>
			<option value="0"<?php echo $selected[0]; ?>>ロードしない</option>
			</select></td>
			<td>※各ロードされるスタイルシートに関しては、そのスタイルが定まった場合、それぞれのスタイルをテンプレートの主たるスタイルシートに一つにまとめ、ロードするスタイルシートの数を少なくした方がリクエストが減り、ページ読み込みに理想的です。</td>
			</tr>

			<tr>
			<td>親div id="wp-calendar" のスタイル設定 : </td>
			<td><input type="text" name="hldycls_pcldr_options[parentstyle]" value="<?php echo esc_attr( $this->ret_option['parentstyle'] ); ?>" size="60"></td>
			<td>インラインでスタイルを設定する書式と同様。入力したスタイル設定をサニタイズしてそのままインラインに書き出します。<br>例：<span style="font-size:1.1em;color:blue;">font-size:0.75em;color:#003333;width:95%;margin:0 auto;</span></td>
			</tr>

			<tr>
			<td>月別アーカイブリストの表示 : </td>
			<td><select name="hldycls_pcldr_options[monthly]">
			<?php
				$selected = array( '', '' );
				if ( '0' === $this->ret_option['monthly'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<option value="1"<?php echo $selected[1]; ?>>表示する</option>
			<option value="0"<?php echo $selected[0]; ?>>表示しない</option>
			</select></td><td></td>
			</tr>
			<tr>
			<td>月別アーカイブリストのヘッダー文字列 : </td>
			<td><input type="text" name="hldycls_pcldr_options[acvheader]" value="<?php echo esc_attr( $this->ret_option['acvheader'] ); ?>"></td>
			<td></td>
			</tr>
			<tr>
			<td>月別アーカイブリストの表示形式: </td>
			<td><select name="hldycls_pcldr_options[acvoptorlist]">
			<?php
				$selected = array( '', '' );
				if ( '0' === $this->ret_option['acvoptorlist'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<option value="1"<?php echo $selected[1]; ?>>プルダウン</option>
			<option value="0"<?php echo $selected[0]; ?>>年別リスト</option>
			</select></td><td></td>
			</tr>
			<tr>
			<td>月別アーカイブ年別リスト用のデフォルトスタイル設定 : </td>
			<td><select name="hldycls_pcldr_options[loadacvlststyle]">
			<?php
				$selected = array( '', '' );
				if ( '0' === $this->ret_option['loadacvlststyle'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<option value="1"<?php echo $selected[1]; ?>>書き出す</option>
			<option value="0"<?php echo $selected[0]; ?>>書き出さない</option>
			</select></td>
			<td>※上記カレンダー用のデフォルトスタイルシートには、この年別リスト用のデフォルトスタイル設定も含まれているので、ここで書き出す必要はありません。 </td>
			</tr>
			<tr>
			<td>投稿がある日のオンマウスで表示させる対象 : </td>
			<td><select name="hldycls_pcldr_options[daypostlink]">
			<?php
				$selected = array( '', '' );
				if ( '0' === $this->ret_option['daypostlink'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<option value="1"<?php echo $selected[1]; ?>>各投稿へのリンク</option>
			<option value="0"<?php echo $selected[0]; ?>>ツールチップ</option>
			</select>
			</td>
			<td>※各投稿へのリンクに設定した場合、そのリンクのリストの非表示、オンマウス時に表示などを cssにて設定しておかないと全体の表示が著しく乱れてしまいます。その場合は、下のデフォルトスタイルをヘッダーに書き出すことでとりあえずは回避できると思います。</td>
			</tr>
			<tr>
			<td></td><td colspan="2"></td>
			</tr>

			<?php
				$selected = array( '', '' );
				if ( '0' === $this->ret_option['dplinkstyle'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<tr>
			<td>上で各投稿へのリンクを選択した場合のスタイル設定 : </td>
			<td><select name="hldycls_pcldr_options[dplinkstyle]">
			<option value="1"<?php echo $selected[1]; ?>>書き出す</option>
			<option value="0"<?php echo $selected[0]; ?>>書き出さない</option>
			</select></td>
			<td>※この各投稿へのリンク表示のスタイル設定は上記カレンダー用のデフォルトスタイルシートには設定してありません。なお、このスタイル設定の書き出しは、各投稿へのリンクが設定してある場合において有効となります。</td>
			</tr>

			<tr>
			<td>独自のデフォルト祝祭日を指定 : class="nitiyo"</td>
			<td><input type="text" name="hldycls_pcldr_options[myholidays]" value="<?php echo esc_attr( $this->ret_option['myholidays'] ); ?>" size="60"></td>
			<td>デフォルトで設定してある日本の祝祭日ではなく、独自の祝祭日を指定。class="nitiyo"で、日曜日と同じスタイル設定になります。日付は月日が3桁か4桁、年月日は6桁のみ。","（半角カンマ）で区切って指定。例：<span style="color:blue;">909,0317,1211,190924</span><br>このオプションに何かしら文字列が存在する場合、デフォルトの祝祭日は無視されます。</td>
			</tr>

			<tr>
			<td>class color 設定値 : </td><td><input type="text" name="hldycls_pcldr_options[colorstyle]" id="hlclpc_colorstylestr" value="<?php echo esc_attr( $this->ret_option['colorstyle'] ); ?>" size="60"></td><td><button type="button" id="hlclpc_csclear">class color 設定値のクリア</button></td>
			</tr>
			<tr>
			<td></td><td colspan="2">※日曜、土曜、各休日のclassにおいて、文字色、背景色、border色、border角丸、は下方にある各項目において設定することが出来ます。<br>各設定値は、連結されて一つのオプション値として保存されます。上のclass color 設定値がそれになります。<br>
			このオプション値に文字列がある場合は、各スタイル設定がhtmlのヘッダー部に吐き出されます。何も排出したくない場合は、上のボタンによって空欄にして「変更を保存」してください。<br>
			基本パターンは<span style="color:blue;">'曜日^文字色^背景色^border色^角丸'</span>。例、日曜なら'n^white^#ff0000^red^10px'で、各休日は "_"（アンダーバー）で区切ってつなげます。色の指定はcssと全く同様であり、指定された文字列はサニタイズされた後、ほぼそのままを排出します。<br>
			例：<span style="color:blue;">t^#da70d6^#d2b48c^gray^_n^silver^black^^3px_d^yellow^rgba( 0, 0, 255, 0.1 )^red^10px_w^red^yellow^black^50%</span><br>
			<span style="text-decoration:underline dotted red;">注：指定しない項目があっても、一つのclass につき、必ず区切り文字の "^"は4個必要です。</span><br>
			尚、各classの指定文字は、t：today、n：nitiyou、d：doyou、w：closewd、l：closel、a：anniver、v：lanniver　となります。<br>
			下記の表においてそれぞれを設定した後、下にある「class color 設定値の作成」ボタンで設定されるオプション値の文字列が作成されます。各項目は選択状態で未入力の状態において、もう一度クリックすると基本的な色の候補のリストが表示されます。<br>
			このスタイル設定はページのheadタグに書き出されます。別のスタイルシートなどに対象の要素のスタイル設定が存在すれば、当然のことそれらの設定も影響されます。<br>
			尚、このオプションにて設定したスタイル設定は、管理画面における block にも適用されます。</td>
			</tr>
			<tr>
				<?php
					if ( current_user_can( 'administrator' ) ) {
						$disabled = '';
					} else {
						$disabled = ' disabled';
					}
				?>
			<td>キャッシュの設定 : </td><td><input type="number" name="hldycls_pcldr_options[en_cache]" value="<?php echo ( int ) $this->ret_option['en_cache']; ?>" min="0" max="9"></td>
			<td>
				<span><button type="button" id="allcachedelete" <?php echo $disabled; ?>>全キャッシュファイル消去</button></span>
				<span id="rescachedelete"></span>
			</td>
			</tr>
			<tr>
			<td></td>
			<td colspan="2">
				<ul style="list-style-type:square;"><li>"0"でdisable。"1"以上の数字で稼働。ここで指定した数字がキャッシュファイルの識別IDとしてキャッシュファイル名に付加されます。</li>
					<li>テンプレートにおいて、このオプション値と違う数値を引数として指定することで別のキャッシュファイルを設定できます。それによりテンプレートで異なる表示設定にしている場合など、その表示設定ごとにキャッシュファイルを指定できます。これは Gutenberg 用の 各 block においても同様です。テンプレート及び block において指定できる番号には一応制限が設けてあり31です。投稿公開時及びこのページでのオプション設定更新時には全てのキャッシュファイルが削除されます。</li>
					<li>日付、表示言語、週の始まり、月別アーカイブの表示などの設定が同じ場合に、それに該当するキャッシュファイルを読み込みます。当然のこと、日付が変わると新しいキャッシュファイルを作成し、古い日付のものは全て削除されます。</li>
					<li>日付アーカイブページにおいては、キャッシュファイルは生成されず、読み込まれることもありません。</li>
					<li>キャッシュファイルが更新されるタイミングは、該当するキャッシュファイルが存在しない時です。投稿が公開された時やオプション設定値を更新した場合に全キャッシュファイルが消去されます。何かしらの理由でキャッシュを更新したい場合は、上にある全キャッシュファイル消去ボタンを使用してください。キャッシュファイルが無ければ新しく作成します。</li>
					<li>尚、キャッシュデータが使用されている時は、カレンダー右下に小さなグレーのアスタリスクが表示されるので、それにて確認可能です。</li>
					<li>デフォルトは "1"。キャッシュファイルはプラグインフォルダのサブフォルダ /cache/ にあり、 "hcpcldr_cache_1_***.php"。*** 部分はカレンダーの日付＋表示設定パラメータ。</li>
					<li>キャッシュファイルが生成される場合は、ページのロード終了後、非同期のAjax により実行されます。尚、あまりない事だとは思いますが、ページ内に複数のカレンダーを表示させそれぞれにキャッシュファイル設定がされている時、キャッシュファイルが生成されるのはページの読み込みごとに一つづつとなります。</li>
				</ul>
			</td>
			</tr>
			<?php
				$selected = array( '', '' );
				if ( '0' === $this->ret_option['en_gutenblock'] ) {
					$selected[0] = ' selected';
				} else {
					$selected[1] = ' selected';
				}
			?>
			<tr>
			<td>Gutenberg Block Editor 用 custom block の登録 : </td>
			<td><select name="hldycls_pcldr_options[en_gutenblock]">
			<option value="1"<?php echo $selected[1]; ?>>登録</option>
			<option value="0"<?php echo $selected[0]; ?>>登録しない</option>
			</select></td>
			<td>※Classic Editor の場合、全く必要無し。<br>
				管理画面においての block のスタイル設定は、プラグインのデフォルトのスタイルシートを読み込んでいますが、上記の「class color 設定値」においてスタイルが設定されている場合は、その設定にてデフォルト値を上書きします。<br>
				尚、デフォルト・スタイルシートを編集する場合は、プラグインフォルダにある、hldycls_pcldr_gb.css （管理画面専用）です。</td>
			</tr>
			<tr>
			<td>calendar のレイアウト指定　table or grid : </td>
			<td><input type="text" name="hldycls_pcldr_options[en_grid]" value="<?php echo esc_attr( $this->ret_option['en_grid'] ); ?>" size="60"></td>
			<td><ul style="list-style-type:square;"><li>デフォルトは "0" で 旧バージョンのままの table。</li>
				<li>"1" で css grid 用に div でのレイアウトとなり、grid 表示に必要なデフォルトスタイル設定をインラインスタイルに排出：display:grid;grid-template-columns:repeat( 7, 1fr );text-align:center;</li>
				<li>"2" で　"1" と同様に div でレイアウトを生成しますがインラインスタイルは排出しません。（テーマのスタイルシート等で独自のスタイル設定をする場合はこちらで）</li>
				<li>インラインスタイルの設定をここに指定することもできます。生成されるものは "2" と同じもので、その親となる要素のインラインスタイルとしてサニタイズしてそのまま書き出します。grid にはするけれど他の設定もここで一緒にという場合などに。</li>
				<li>※div での出力の場合、いずれにしても1日の曜日分のオフセットを設定する "grid-column-start" の設定は、その1日の要素のインラインスタイルに出力されています。</li>
			</td>
			</tr>
			</table>
			<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="変更を保存"  /></p>
		</form>

		<table style="font-size:1.1em;letter-spacing:0.1em;">
			<?php
				function hlclpc_treatstyle( $targetoption ){
					$eachstyle =  array(
						't' => array( '', '', '', '' ),
						 'n' => array( '', '', '', '' ),
						 'd' => array( '', '', '', '' ),
						 'w' => array( '', '', '', '' ),
						 'l' => array( '', '', '', '' ),
						 'a' => array( '', '', '', '' ),
						 'v' => array( '', '', '', '' )
						 );
					if ( $targetoption ) {
						$styleoption = str_replace( array( '<', '>', '\'','"', 'script', 'eval' ), '', $targetoption );
						$colorstyle = explode ( '_', $styleoption );
						foreach ( $colorstyle as $val ){
							$eachcolor = explode( '^', $val );
							if ( 5 === count( $eachcolor ) ) {
								if ( array_key_exists( $eachcolor[0], $eachstyle ) ) {
									$eachstyle[ $eachcolor[0] ] = array( $eachcolor[1], $eachcolor[2], $eachcolor[3], $eachcolor[4] );
								}
							}
						}
					}

					return $eachstyle;
				}

				$hlclpc_colorstyle = hlclpc_treatstyle( $this->ret_option['colorstyle'] );

				$radius = array( '', '3px', '5px', '10px', '50%');
			?>
			<tr>	<td colspan="3">- - - - - - - - - - - - - - - - - - - -</td></tr>
			<tr>	<td>それぞれのclassの文字色、背景色設定 : </td><td>文字色</td><td>背景色</td><td>border色</td><td>border角丸</td>
			</tr>
			<tr>
			<td>today : </td>
			<td><input type="text" id="hlclpc_tscolor" list="example" value="<?php echo $hlclpc_colorstyle['t'][0]; ?>">
			<datalist id="example">
				<option value="white">白</option>
				<option value="black">黒</option>
				<option value="gray">グレイ</option>
				<option value="silver">銀</option>
				<option value="red">赤</option>
				<option value="blue">青</option>
				<option value="green">緑</option>
				<option value="yellow">黄</option>
				<option value="navy">紺</option>
				<option value="#00ffff">Aqua</option>
				<option value="#7fffd4">Aquamarine</option>
				<option value="#ee82ee">Violet</option>
				<option value="ffc0cb">Pink</option>
				<option value="#eee8aa">PaleGoldenRod</option>
				<option value="#00ff7f">SpringGreen</option>
				<option value="#d2b48c">Tan</option>
				<option value="#ff4500">OrangeRed</option>
				<option value="#da70d6">Orchild</option>
			</datalist>
			</td>
			<td><input type="text" id="hlclpc_tbcolor" list="example" value="<?php echo $hlclpc_colorstyle['t'][1]; ?>"></td>
			<td><input type="text" id="hlclpc_tlcolor" list="example" value="<?php echo $hlclpc_colorstyle['t'][2]; ?>"></td>
			<td>
				<select id="hlclpc_trcolor">
					<?php
						foreach ( $radius as $val ) {
							$selected = $val === $hlclpc_colorstyle['t'][3] ? ' selected' : '';
							echo '<option value="' .$val . '"' . $selected . '>' . $val . '</option>';
						}
					?>
				</select>
			</td>
			</tr>
			<tr>
			<td>nitiyou : </td>
			<td><input type="text" id="hlclpc_nscolor" list="example" value="<?php echo $hlclpc_colorstyle['n'][0]; ?>"></td>
			<td><input type="text" id="hlclpc_nbcolor" list="example" value="<?php echo $hlclpc_colorstyle['n'][1]; ?>"></td>
			<td><input type="text" id="hlclpc_nlcolor" list="example" value="<?php echo $hlclpc_colorstyle['n'][2]; ?>"></td>
			<td>
				<select id="hlclpc_nrcolor">
					<?php
						foreach ( $radius as $val ) {
							$selected = $val === $hlclpc_colorstyle['n'][3] ? ' selected' : '';
							echo '<option value="' .$val . '"' . $selected . '>' . $val . '</option>';
						}
					?>
				</select>
			</td>
			</tr>
			<tr>
			<td>doyou : </td>
			<td><input type="text" id="hlclpc_dscolor" list="example" value="<?php echo $hlclpc_colorstyle['d'][0]; ?>"></td>
			<td><input type="text" id="hlclpc_dbcolor" list="example" value="<?php echo $hlclpc_colorstyle['d'][1]; ?>"></td>
			<td><input type="text" id="hlclpc_dlcolor" list="example" value="<?php echo $hlclpc_colorstyle['d'][2]; ?>"></td>
			<td>
				<select id="hlclpc_drcolor">
					<?php
						foreach ( $radius as $val ) {
							$selected = $val === $hlclpc_colorstyle['d'][3] ? ' selected' : '';
							echo '<option value="' .$val . '"' . $selected . '>' . $val . '</option>';
						}
					?>
				</select>
			</td>
			</tr>
			<tr>
			<td>closewd : </td>
			<td><input type="text" id="hlclpc_wscolor" list="example" value="<?php echo $hlclpc_colorstyle['w'][0]; ?>"></td>
			<td><input type="text" id="hlclpc_wbcolor" list="example" value="<?php echo $hlclpc_colorstyle['w'][1]; ?>"></td>
			<td><input type="text" id="hlclpc_wlcolor" list="example" value="<?php echo $hlclpc_colorstyle['w'][2]; ?>"></td>
			<td>
				<select id="hlclpc_wrcolor">
					<?php
						foreach ( $radius as $val ) {
							$selected = $val === $hlclpc_colorstyle['w'][3] ? ' selected' : '';
							echo '<option value="' .$val . '"' . $selected . '>' . $val . '</option>';
						}
					?>
				</select>
			</td>
			</tr>
			<tr>
			<td>closel : </td>
			<td><input type="text" id="hlclpc_lscolor" list="example" value="<?php echo $hlclpc_colorstyle['l'][0]; ?>"></td>
			<td><input type="text" id="hlclpc_lbcolor" list="example" value="<?php echo $hlclpc_colorstyle['l'][1]; ?>"></td>
			<td><input type="text" id="hlclpc_llcolor" list="example" value="<?php echo $hlclpc_colorstyle['l'][2]; ?>"></td>
			<td>
				<select id="hlclpc_lrcolor">
					<?php
						foreach ( $radius as $val ) {
							$selected = $val === $hlclpc_colorstyle['l'][3] ? ' selected' : '';
							echo '<option value="' .$val . '"' . $selected . '>' . $val . '</option>';
						}
					?>
				</select>
			</td>
			</tr>
			<tr>
			<td>anniversary : </td>
			<td><input type="text" id="hlclpc_ascolor" list="example" value="<?php echo $hlclpc_colorstyle['a'][0]; ?>"></td>
			<td><input type="text" id="hlclpc_abcolor" list="example" value="<?php echo $hlclpc_colorstyle['a'][1]; ?>"></td>
			<td><input type="text" id="hlclpc_alcolor" list="example" value="<?php echo $hlclpc_colorstyle['a'][2]; ?>"></td>
			<td>
				<select id="hlclpc_arcolor">
					<?php
						foreach ( $radius as $val ) {
							$selected = $val === $hlclpc_colorstyle['a'][3] ? ' selected' : '';
							echo '<option value="' .$val . '"' . $selected . '>' . $val . '</option>';
						}
					?>
				</select>
			</td>
			</tr>
			<tr>
			<td>lanniversary : </td>
			<td><input type="text" id="hlclpc_vscolor" list="example" value="<?php echo $hlclpc_colorstyle['v'][0]; ?>"></td>
			<td><input type="text" id="hlclpc_vbcolor" list="example" value="<?php echo $hlclpc_colorstyle['v'][1]; ?>"></td>
			<td><input type="text" id="hlclpc_vlcolor" list="example" value="<?php echo $hlclpc_colorstyle['v'][2]; ?>"></td>
			<td>
				<select id="hlclpc_vrcolor">
					<?php
						foreach ( $radius as $val ) {
							$selected = $val === $hlclpc_colorstyle['v'][3] ? ' selected' : '';
							echo '<option value="' .$val . '"' . $selected . '>' . $val . '</option>';
						}
					?>
				</select>
			</td>
			</tr>
			<tr>
			<td colspan="2"><button type="button" name="hlclpc_makeclassstr" id="hlclpc_makeclassstr" value="ボタン">class color 設定値の作成</button></td><td colspan="2"></td>
			</tr>
		</table>

		<div style="font-size:1.2em;letter-spacing:0.1em;">
			<h3>《 説明書き 》</h3>
			<p>ブロックエディターにおいてブロックを使用するか、カレンダーを表示させたいテンプレートの部分に</p>
			<pre style="font-weight:bold;color:blue;letter-spacing:0.05em;background:white;padding-top:20px;border-radius:10px;">
			&lt;div&gt;
			&lt;?php
				if ( function_exists( 'holiday_calendar_echo' ) ) {
					holiday_calendar_echo();
				}
			?&gt;
			&lt;/div&gt;
			</pre>
			<p>を記述してください。&lt;div&gt;は任意です。<br>上記オプションは、以下のように配列にして、その関数の引数として渡すことも可能です。変更したい値だけを配列にして渡します。</p>
		<pre style="font-weight:bold;color:blue;line-height:1.1em;background:white;padding-top:20px;border-radius:10px;">
		&lt;?php
			if ( function_exists( 'holiday_calendar_echo' ) ) {
				$args=array(
					'lang'=&gt;'j',
					'wf'=&gt;'m',
					'capt' =&gt; 'page post',
					'footer' =&gt; '毎週金はお休み,毎月3日は特売日:hclred',
					'closewd'=&gt; array( '3', '5' ),
					'closel'=&gt; array( '03', '929', '0927', '20140911', '20140920' ),
					'anniver'=&gt; array( '929', '0130', '20171209' ),
					'monthly'=&gt;'0',
					'postype' =&gt; 'post',
					'acvheader' =&gt; '月別アーカイヴ・リスト',
					'acvoptorlist' =&gt; '1',
					'daypostlink' =&gt; '1',
					'myholidays' =&gt; '101,909,0317,1211,190924',
					'adddeldays' =&gt; '3,929-,0927,20140911-',
					'en_cache' =&gt; '1',
				);
				holiday_calendar_echo( $args );
			}
		?&gt;
		</pre> 
			<p>その場合は、保存されているオプション設定値は引数値で上書きされ引数で与えた値が有効になります。これは特定のテンプレートや、条件分岐で表示を変更できるということです。<br>
			条件分岐により、その一つには引数の設定をする必要が無い場合は</p>
		<pre style="font-weight:bold;color:blue;letter-spacing:0.05em;background:white;padding-top:20px;border-radius:10px;">
		&lt;?php
			if ( function_exists( 'holiday_calendar_echo' ) ) {
				if ( is_page() ) {
					$args=array(
						'lang' =&gt; 'j',
						'monthly'=&gt;'0',
						'wf'=&gt;'m',
					);
				} else {
					$args = array();//←これが必要です
				}
				holiday_calendar_echo( $args );
			}
		?&gt;
		</pre>
			<p>と、言う具合に初期化した空の配列を渡してください。これが無いとエラーになります。<br>
			引数の詳細は基本的に上記と同じです。</p>
			<table>
			<tr><td>lang</td><td>日本語か英語表示かの指定　'j' か 'e'、デフォルトは 'e' で英語。</td></tr>
			<tr><td>wf</td><td>週の始めを日曜か月曜かの指定　's' か 'm'、デフォルトは 's' で日曜。</td></tr>
			<tr><td>capt</td><td>キャプションに追加して表示させる文字列。</td></tr>
			<tr><td>footer</td><td>テーブル下部に追加して表示させる文字列。</td></tr>
			<tr><td>closewd</td><td>独自休日を曜日で指定する場合。毎週水曜日だとか。1が日曜、7が土曜で1～7までの数字で指定。複数ももちろん可。その場合は ',' 区切りの文字列でも配列でもOK。上記オプション欄を参照のこと。</td></tr>
			<tr><td>closel</td><td>独自休日を日にちで指定。年月日８ケタ、月日４ケタないし３ケタ、日にち２ケタ以下で指定。日にちだけなら毎月に設定され、月日なら毎年。これも複数可。</td></tr>
			<tr><td>anniver</td><td>それらとは別に特別な日を設定するときは anniver を使用し、これは毎年の事なら４ケタもしくは３ケタを使用。年月日の8ケタも可能で、この場合は class名が 'lanniver' となり区別が可能。複数可。</td></tr>
			<tr><td>monthly</td><td>月別アーカイブのリストも表示させたい場合は '1' を指定。'0' で非表示。デフォルトは '1' で表示。</td></tr>
			<tr><td>postype</td><td>カレンダーにリンクを表示させる投稿の page 以外の投稿タイプ、デフォルトは 'post'。</td></tr>
			<tr><td>acvheader</td><td>月別アーカイブリストのヘッダー文字指定。何も指定しなければ日本語時 '月別アーカイブ'、英語時 'Monthly Archives'。</td></tr>
			<tr><td>acvoptorlist</td><td>月別アーカイブリスト表示がプルダウンかリストかを選択、デフォルトは '1' でプルダウン、'0' でリスト表示。</td></tr>
			<tr><td>daypostlink</td><td>投稿がある日にオンマウスで表示させるのはツールチップかその投稿へのリンクか。デフォルトは '0' でツールチップ、'1' で投稿へのリンク。</td></tr>
			<tr><td>myholidays</td><td>デフォルトで設定してある日本の祝祭日ではなく、独自の祝祭日を指定する。</td></tr>
			<tr><td>adddeldays</td><td>デフォルトで設定されている祝祭日リストに日付を追加する、または削除する。</td></tr>
			<tr><td>en_cache</td><td>キャッシュ指定。'0' で disable。使える番号は'31'まで。が、投稿公開時に消去対象となるのは '5' まで。</td></tr>
			<tr><td>en_grid</td><td>レイアウト指定。'0' で table、'1' で css grid、尚、grid の場合のデフォルトスタイル設定は、display:grid;grid-template-columns:repeat( 7, 1fr );text-align:center;',</td></tr>
			</table>
			<ul style="padding-left:10px;list-style-type:square;">
			<li>スタイルシートの読み込みや各スタイル設定、Block Editor 用 custom block の読み込み等のプラグイン読み込み時に設定されるオプションに関しては、関数の引数において指定しても無意味です。</li>
			<li>尚、プラグインをアンインストールする時は、プラグインページから削除を実行すれば、<br>設定されたオプション値はデータベースから削除されます。</li>
			</ul>
		</div>
	</div>
<?php
	$ajaxurl = admin_url( 'admin-ajax.php' );
	$special_string = plugin_dir_path( __FILE__ ) . 'cache/hcpcldr_cache';

	$nonce = wp_create_nonce( $special_string );
	// $nonce = wp_create_nonce( 'holiday_class_post_calendar' );

	$cpsnonce = $this->get_cpsnonce();
?>
<script<?php echo $cpsnonce; ?>>
	( function () {
		const doc = document;

		function hlclpc_isEmpty( elem ) {
			const str = elem.value;
			if ( str === null || str.length === 0 ) {
				return false;
			} else {
				return true;
			}
		}

		function hlclpc_optionsubmit() {
			let colorstr = [],
				targetstr = [ 't', 'n', 'd', 'w', 'l', 'a', 'v' ],
				scolorname,
				bcolorname,
				scolor,
				bcolor,
				i = 0;

			while ( i < 7 ) {
				scolorname = 'hlclpc_' + targetstr[ i ] + 'scolor';
				bcolorname = 'hlclpc_' + targetstr[ i ] + 'bcolor';
				lcolorname = 'hlclpc_' + targetstr[ i ] + 'lcolor';
				rcolorname = 'hlclpc_' + targetstr[ i ] + 'rcolor';
				scolor = doc.getElementById( scolorname );
				bcolor = doc.getElementById( bcolorname );
				lcolor = doc.getElementById( lcolorname );
				bradius = doc.getElementById( rcolorname );

				if ( hlclpc_isEmpty( scolor ) || hlclpc_isEmpty( bcolor ) ) {
					colorstr.push( targetstr[ i ] + '^' + scolor.value + '^' + bcolor.value + '^' + lcolor.value + '^' + bradius.value );
				}
				++i;
			}

			if ( colorstr.length ) {
				console.log( colorstr.join( ',' ) ); 
				doc.getElementById( 'hlclpc_colorstylestr' ).value = colorstr.join( '_' );
			}
		}

		doc.getElementById( 'hlclpc_csclear' ).onclick = function() {
			document.getElementById( 'hlclpc_colorstylestr' ).value = null;
		}

		doc.getElementById( 'hlclpc_makeclassstr' ).onclick = function() {
			hlclpc_optionsubmit();
		}

		doc.getElementById( 'allcachedelete' ).onclick = function() {
			const url = '<?php echo $ajaxurl; ?>',
				strsend = 'action=hcpcldrdeletecache&nonce=<?php echo $nonce; ?>';

			const req = new XMLHttpRequest();

			req.onreadystatechange = function() {
				let result = '',
					rescolor = 'blue';
		
				if (req.readyState == 4) { // 通信の完了時
					if (req.status == 200) { // 通信の成功時
						result = req.responseText;
	
						const rescd = doc.getElementById( 'rescachedelete' );

						if ( -1 !== result.indexOf( 'error' ) ) {
							rescolor = 'red';
						}
						rescd.innerHTML = '<span style="color:' + rescolor + ';">' + result + '</span>';
					} else {
						console.log( 'error : disabled send' );
					}
				}
			}
			req.open( 'POST', url, true );
			req.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
			req.send( strsend );
		}
	})();
</script>

