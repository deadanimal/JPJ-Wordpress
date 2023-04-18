<?php if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! empty( $param['menu_1']['item_icon'] ) ):

	$count_i = count( $param['menu_1']['item_icon'] );

	if ( $count_i > 0 ) {
		echo '<div class="floating-menu float-menu-' . absint( $id ) . '">';
		echo '<ul class="fm-bar">';
		for ( $i = 0; $i < $count_i; $i ++ ) {

			echo '<li class="fm-item-' . absint( $id ) . '-' . absint( $i ) . '">';
			$target = ! empty( $param['menu_1']['new_tab'][ $i ] ) ? '_blank' : '_self';
			$link   = $param['menu_1']['item_link'][ $i ];
			echo '<a href="' . esc_attr( $link ) . '" target="' . esc_attr( $target ) . '"';
			if ( ! empty( $param['menu_1']['button_class'][ $i ] ) ) {
				echo ' class="' . esc_attr( $param['menu_1']['button_class'][ $i ] ) . '"';
			}
			if ( ! empty( $param['menu_1']['button_id'][ $i ] ) ) {
				echo ' id="' . esc_attr( $param['menu_1']['button_id'][ $i ] ) . '"';
			}
			if ( ! empty( $param['menu_1']['link_rel'][ $i ] ) ) {
				echo ' rel="' . esc_attr( $param['menu_1']['link_rel'][ $i ] ) . '"';
			}
			echo '>';
			echo '<div class="fm-icon"><i class="' . esc_attr( $param['menu_1']['item_icon'][ $i ] ) . '"></i></div>';
			if(!empty($param['menu_1']['item_tooltip'][ $i ])) {
				echo '<div class="fm-label"><div class="fm-text"> ' . esc_html( $param['menu_1']['item_tooltip'][ $i ] ) . '</div></div>';
			}
			echo '</a>';
			echo '</li>';

		}
		echo '</ul>';

		echo '</div>';
	}
endif;