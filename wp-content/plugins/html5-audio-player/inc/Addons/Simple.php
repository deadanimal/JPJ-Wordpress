<?php
namespace H5AP\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Elementor Hello World
 *
 * Elementor widget for hello world.
 *
 * @since 1.0.0
 */
class Simple extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'SimpleAudioPlayer';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Simple Audio Player', 'h5ap' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-play';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'basic' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return ['h5ap-public'];
	}

	/**
	 * Style
	 */
	public function get_style_depends() {
		return ['bplugins-plyrio'];
	}


	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Simple Audio Player', 'h5ap' ),
				'tab' 	=> Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'baudio_url',
			[
				'label' 		=> esc_html__( 'Upload or paste audio file', 'h5ap' ),
				'type' 			=> 'b-select-file',
				'separator' 	=> 'before',
			]
		);
		
		$this->add_control(
			'baudio_muted',
			[
				'label' => __( 'Muted', 'h5ap' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'h5ap' ),
				'label_off' => __( 'No', 'h5ap' ),
				'return_value' => true,
				'default' => '',
				'separator' 	=> 'before',
			]
		);

		$this->add_control(
			'baudio_repeat',
			[
				'label' => __( 'Repeat', 'h5ap' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'h5ap' ),
				'label_off' => __( 'No', 'h5ap' ),
				'return_value' => true,
				'default' => '',
				'separator' 	=> 'before',
			]
		);
		$this->add_control(
			'baudio_autoplay',
			[
				'label' => __( 'Autoplay', 'h5ap' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'h5ap' ),
				'label_off' => __( 'No', 'h5ap' ),
				'return_value' => true,
				'default' => '',
				'separator' 	=> 'before',
			]
		);

		$this->end_controls_section();

		
		// Style Tab Start
		$this->start_controls_section(
			'style_tab',
			[
				'label' => esc_html__( 'Style', 'html5-audio-player' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);
			

		$this->add_control(
			'baudio_width',
			[
				'label' 		=> __( 'Width', 'h5ap' ),
				'type'			=> Controls_Manager::SLIDER,
				'size_units' 	=> [ 'px', '%', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'selectors' => [
					'{{WRAPPER}} .skin_simple' => 'width:{{SIZE}}{{UNIT}};margin:0 auto;max-width:100%;',
				],
			]
		);
		
		$this->add_control(
			'seekTime',
			[
				'label' 		=> esc_html__( 'Seek Time', 'h5ap' ),
				'type' 			=> Controls_Manager::NUMBER,
				'placeholder'	=> esc_attr__('Input seek time here','h5ap'),
				'default'		=> 10,
				'label_block'	=> false,
			]
		);

		$this->add_control(
			'primary',
			[
				'label' 		=> esc_html__( 'Primary Color', 'h5ap' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'label_block'	=> false,
				'selectors' => [
					'{{WRAPPER}} #skin_default' => '--plyr-color-main:{{VALUE}};--plyr-range-fill-background: {{VALUE}};--plyr-audio-control-color:{{VALUE}};--plyr-audio-control-color-hover:#ffffff',
					'{{WRAPPER}}.elementor-widget-SimpleAudioPlayer #skin_default .plyr__control:hover' => 'color: var(--plyr-audio-control-color-hover,#fff)',
				],
				'default' => '#4f5b5f'
			]
		);

		$this->add_control(
			'background',
			[
				'label' 		=> esc_html__( 'Background Color', 'h5ap' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => \Elementor\Core\Schemes\Color::get_type(),
					'value' => \Elementor\Core\Schemes\Color::COLOR_1,
				],
				'label_block'	=> false,
				'selectors' => [
					'{{WRAPPER}} .skin_simple .plyr--audio .plyr__controls' => 'background:{{VALUE}};',
				],
				'default' => '#ffffff'
			]
		);


		$this->end_controls_section();

	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$arm = '';
		$arm .= $settings['baudio_repeat'] === 'true' ? ' loop' : '';
		$arm .= $settings['baudio_autoplay'] === 'true' ? ' autoplay' : '';
		$arm .= $settings['baudio_muted'] == 'true' ? ' muted' : '';

		$data = [
			'autoplay' =>$settings['baudio_autoplay'] === 'true' ? true : false,
			'muted' => $settings['baudio_muted'] === 'true' ? true : false,
			'loop' => ['active' => $settings['baudio_repeat'] === 'true' ? true : false],
			'seekTime' => $settings['seekTime']
		];

		$color = $settings['primary'];

		?>

		<div id="skin_default" class="skin_simple h5ap_elementor" data-options="<?php echo esc_attr(wp_json_encode($data)); ?>">
            <audio id="bplayer_id" <?php echo esc_attr($arm); ?>>
				<source src="<?php echo esc_url($settings['baudio_url'].'?download=false'); ?>" type="audio/mp3">
			</audio> 
        </div>
		
		<?php
	}

}
