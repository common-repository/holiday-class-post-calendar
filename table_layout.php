<?php
    $tmpstrary = array();

    $parentstyle = '';
    if ( '' !== $prm['parentstyle'] ) {
        $parentstyle = ' style="' . htmlspecialchars( $prm['parentstyle'], ENT_QUOTES, 'UTF-8') . '"';
    }

    $tmpstrary[] =  "\n" . '<!-- Holiday-class Post Calendar Plugin v' . $this->version . ' ' . $nowdate->format( 'Y/m/j H:i' ) . ' -->' . "\n";
    $tmpstrary[] = '<div' . $parentstyle . '><table id="wp-calendar">' . "\n";

    $showwd = array();
    $showsm = array();

    $addcapt = '';
    if ( '' !== $prm['capt'] ) {
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

        $tmpstrary[] = '<caption>' . $tyear . '&ensp;' . $nengo . $heisei . '年' . ( string ) ( ( int ) $tmonth ) . '月' . $addcapt . '</caption>' . "\n";

        $showwd = array( [ '日', ' class="nitiyou"' ], [ '月', '' ], [ '火', '' ], [ '水', '' ], [ '木', '' ], [ '金', '' ], [ '土', ' class="doyou"'], [ '日', ' class="nitiyou"' ] );

        $showsm = array( '12月', '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月', '1月' );
    } else {
        $fullengm = array( ' January', ' February', ' March', ' April', ' May', ' June', ' July', ' August', ' September', ' October', ' November', ' December' );

        $tmpstrary[] = '<caption>' . $fullengm[ ( ( int ) $tmonth - 1 ) ] . '&ensp;' . $tyear . '&ensp;' . $addcapt . '</caption>' . "\n";

        $showwd = array( [ 'Su', ' class="nitiyou"' ], [ 'Mo', '' ], [ 'Tu', '' ], [ 'We', '' ], [ 'Th', '' ], [ 'Fr', '' ], [ 'Sa', ' class="doyou"' ], [ 'Su', ' class="nitiyou"'] );

        $showsm = array( 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan' );
    }

    if ( 's' === $prm['wf'] ) {
        unset ( $showwd[7] );

        $nitinum = 1;
        $donum = 7;
        $daycountadd = 0;
    } else {
        unset ( $showwd[0] );

        // 週初めが日曜でない時は空のtdが一つ少ない
        // 単に --$elem['fdwd'] の値を得たいだけであるが、0のとき-1ではなく6にしたい
        $elem['fdwd'] = ( $elem['fdwd'] + 6 ) % 7;
        // --$elem['fdwd'];
        // if ( $elem['fdwd'] < 0 ) {
        //     $elem['fdwd'] = 6;
        // }
        $nitinum = 7;
        $donum = 6;
        $daycountadd = 1;
    }

    $tmpstrary[] = '<thead><tr>';

    foreach ( $showwd as $val ) {
        $tmpstrary[] = '<th' . $val[1] . ' scope="col" title="' . $val[0] . '">' . $val[0] . '</th>';
    }

    $tmpstrary[] = '</tr></thead>' . "\n";

    $nextmstr = '&nbsp;';
    $footer = '';

    if ( $prm['footer'] ) {
        $footerary = explode( ',', $prm['footer'] );
        if ( $footerary ) {
            foreach ( $footerary as $val ) {
                $footerstr = array();
                if ( false !== strpos( $val, ':' ) ) {
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
                } else {
                    if ( $val ) {
                        $footerstr[0] = esc_html( $val );
                    } else {
                        $footerstr[0] = '';
                    }
                    $footerstr[1] = '';
                }
                if ( $footerstr[0] ) {
                    $tdclass = '';
                    if ( $footerstr[1] ) {
                        $tdclass = ' id="' . $footerstr[1] . '"';
                    }
                    $footer .= '<tr><td colspan="7"' . $tdclass . '>' . $footerstr[0] . '</td></tr>';
                }
            }
        }
    }
    $tmpstrary[] = '<tfoot>' . $footer;

    if ( '' !== $elem['nmonth'] ) {

        // 基本とする月アーカイブへのリンクから文字列置換で次月へのリンクを得る
        $monurl = str_replace( $tmonthform, $elem['nmonth'], $baselink );

        // post_type にカスタム投稿を指定してある場合に
        // そのurlパラメータを付加する
        if ( 'post' !== $prm['postype'] ) {
            $monurl = esc_url( add_query_arg( 'post_type', $prm['postype'], $monurl ) );
        }
        $nextmstr = '<a href="' . $monurl . '">' . $showsm[ ( ( int ) $tmonth + 1 ) ] . ' &raquo;</a>';
    }

    // 基本とする月アーカイブへのリンクから文字列置換で前月へのリンクを得る
    $monurl = str_replace( $tmonthform, $elem['pmonth'], $baselink );

    if ( 'post' !== $prm['postype'] ) {
        $monurl = esc_url( add_query_arg( 'post_type', $prm['postype'], $monurl ) );
    }
    $tmpstrary[] = '<tr><td colspan="3" id="hclprevm"><a href="' . $monurl . '">&laquo; ' . $showsm[ ( ( int ) $tmonth - 1 ) ] . '</a></td><td >&nbsp;</td><td colspan="3" id="hclnextm" >' . $nextmstr . '</td></tr></tfoot>' . "\n<tbody>";

    // trを表示するための曜日のカウント、1から7まで
    $daycount = 1;
    $roopcount = 0;
    $trary = array_fill( 1, 7, array( '', '' ) );
    $trary[1][0] = '<tr>';
    $trary[7][1] = '</tr>' . "\n";

    // 1日の曜日によって空のtdを設定
    if ( $elem['fdwd'] ) {
        $tmpstrary[] = '<tr><td colspan=' . ( string ) $elem['fdwd'] . '>&nbsp;</td>';
        // $daycount += $elem['fdwd'];
        $roopcount += $elem['fdwd'];
    }

    foreach ( $dateary as $i => $val ) {

        $daycount = ( $roopcount % 7 ) + 1;

        $tmpclnd = '';
        $hdclsstr = '';

        if ( $val ) {
            $hdclsstr = ' class="' . trim ( $val ) . '"';
        }
        $tmpstrary[] = $trary[ $daycount ][0] . '<td'. $hdclsstr . '>';

        if ( isset( $ispost[ $i ] ) ) {

            // 基本とする月アーカイブへのリンクに日付データを付加することで日ページへのリンクを作成
            $dayurl = $baselink . $trailslash . sprintf( '%02s',  $i ) . $slashstr;

            // post_type にカスタム投稿を指定してある場合に
			// そのurlパラメータを付加する
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
                $tmpstrary[] = '<a href="' . $dayurl . '">' . $i . '</a><span class="daychildren">' . implode( '<br>', $postdatalink ) . '</span></td>';
            } else {
                foreach ( $ispost[ $i ] as $value ) {

                    $postdatalink[] = $value[1];
                }

                $tmpstrary[] = '<a href="' . $dayurl . '" title="' . implode( ',', $postdatalink ) . '">' . $i . '</a></td>';
            }
        } else {
            $tmpstrary[] = $i . '</td>';
        }

        $tmpstrary[] = $trary[ $daycount ][1];
        ++$roopcount;
    }

    if ( isset ( array_fill( 2, 7, '' )[ $daycount ] ) ) {// if ( $daycount > 1 and $daycount < 8 ) と同じこと
        $tmpstrary[] = '<td colspan=' . ( 7 - $daycount ) . '>&nbsp;</td></tr>' . "\n";
    }

    $tmpstrary[] = '</tbody></table></div>' . "\n";
?>