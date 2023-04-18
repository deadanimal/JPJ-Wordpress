<?php
/**
 * Inline Style generator
 *
 * @package     Wow_Plugin
 * @author      Dmytro Lobov <d@dayes.dev>
 * @copyright   2019 Wow-Company
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$zindex = ! empty( $param['zindex'] ) ? $param['zindex'] : 9999;


$css = ".float-menu-{$id} {
		--fm-icon-size: 24px;
        --fm-label-size: 15px;
        --fm-border-radius: 50%;
        --fm-color: #ffffff;
        --fm-background: #128be0;
		--fm-z-index: {$zindex};
}";


$count_items = ( ! empty( $param['menu_1']['item_type'] ) ) ? count( $param['menu_1']['item_type'] ) : '0';

if ( $count_items > 0 ) {
	for ( $i = 0; $i < $count_items; $i ++ ) {

		$hcolor = ! empty( $param['menu_1']['hcolor'][ $i ] ) ? $param['menu_1']['hcolor'][ $i ] : $param['menu_1']['color'][ $i ];
		$hbcolor = ! empty( $param['menu_1']['hbcolor'][ $i ] ) ? $param['menu_1']['hbcolor'][ $i ] : $param['menu_1']['bcolor'][ $i ];

		$css .= ".fm-item-{$id}-{$i} {
			--fm-color: {$param['menu_1']['color'][ $i ]};
			--fm-background: {$param['menu_1']['bcolor'][ $i ]};
		}";

		$css .= ".fm-item-{$id}-{$i}:hover {
			--fm-hover-color: $hcolor;
			--fm-hover-background: $hbcolor;
		}";

	}
}

if ( ! empty( $param['include_mobile'] ) ) {
	$screen = ! empty( $param['screen'] ) ? $param['screen'] : 480;
	$css    .= "
		@media only screen and (max-width: {$screen}px){
			.float-menu-{$id} {
				display:none;
			}
		}";
}
if ( ! empty( $param['include_more_screen'] ) ) {
	$screen_more = ! empty( $param['screen_more'] ) ? $param['screen_more'] : 1200;
	$css         .= "
		@media only screen and (min-width: {$screen_more}px){
			.float-menu-{$id} {
				display:none;
			}
		}";
}

$css = trim( preg_replace( '~\s+~s', ' ', $css ) );