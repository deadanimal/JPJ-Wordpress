<?php
/**
 * Inline Script generator
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
$mobile_screen            = ! empty( $param['mobile_screen'] ) ? $param['mobile_screen'] : 768;
$mobile_rules             = ! empty( $param['mobile_rules'] ) ? 'true' : 'false';
$shape                    = ! empty( $param['shape'] ) ? $param['shape'] : 'square';


$arg = array(
	'selector'       => ".float-menu-{$id}",
	'position'       => array( $param['menu'], "center" ),
	'offset'         => array( 0, 0 ),
	'shape'          => $shape,
	'sideSpace'      => filter_var( $param['sideSpace'], FILTER_VALIDATE_BOOLEAN ),
	'buttonSpace'    => filter_var( $param['buttonSpace'], FILTER_VALIDATE_BOOLEAN ),
	'labelSpace'     => filter_var( $param['labelSpace'], FILTER_VALIDATE_BOOLEAN ),
	'labelConnected' => filter_var( $param['labelConnected'], FILTER_VALIDATE_BOOLEAN ),
	'labelEffect'    => 'fade',
	'labelAnim'      => array( (int) $param['labelSpeed'], 'easeOutQuad' ),
	'color'          => 'default',
	'overColor'      => 'default',
	'labelsOn'       => filter_var( $param['labelsOn'], FILTER_VALIDATE_BOOLEAN ),
	'mobileEnable'   => filter_var( $mobile_rules, FILTER_VALIDATE_BOOLEAN ),
	'mobileScreen'   => (int) $mobile_screen,
);

$js = "var FloatMenu_{$id} = " . json_encode( $arg );