<?php
/**
 * WooCommerce Settings Exporter.
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  WPFactory
 */

namespace WPF_Settings_Exporter;

use Timber\Timber;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WC_Settings_Exporter' ) ) {

	class WC_Settings_Exporter {
		protected $settings_id;
		protected $settings_sections;
		protected $settings;
		protected $template_file;
		protected $templates_path = array();
		protected $output_path;
		protected $output_filename_template;
		protected $output_filename;
		protected $exporter_name;
		protected $custom_wc_settings_attribute;
		protected $filesystem_path;
		protected $last_smart_option = array();

		static function test(){
			//error_log('asdasds');
		}

		function set_last_smart_option( $type, $value ) {
			if ( ! empty( $value ) ) {
				$this->last_smart_option[ $type ] = $value;
			}
		}

		function get_last_smart_option( $type, $value ) {
			if ( isset( $this->last_smart_option[ $type ] ) && ! empty( $last_smart_option_value = $this->last_smart_option[ $type ] ) ) {
				return $last_smart_option_value;
			}
			return $value;
		}

		public function __construct() {
			$this->set_template_file( "wc-settings-html.twig" );
			$this->set_output_filename( "html.html" );
			$this->set_exporter_name( "WC-Settings" );
			$this->add_templates_path( plugin_dir_path( __FILE__ ) . '../templates' );
			$this->set_output_filename_template( '{{exporter_name|lower}}-{{output_filename}}' );
			$this->set_custom_wc_settings_attribute('wpfse_data');
		}

		function get_option_attr( $option, ...$attributes ) {
			$value = '';
			foreach ( $attributes as $attr ) {
				if (
					isset( $option[ $this->get_custom_wc_settings_attribute() ] ) &&
					isset( $option[ $this->get_custom_wc_settings_attribute() ][ $attr ] )
				) {
					$context = Timber::context();
					$context = array_merge( $context, $option );
					$value   = Timber::compile_string( $option[ $this->get_custom_wc_settings_attribute() ][ $attr ], $context );
				}
				if ( empty( $value ) && isset( $option[ $attr ] ) ) {
					$value = $option[ $attr ];
				}
				if ( ! empty( $value ) ) {
					break;
				}
			}
			return $value;
		}

		function get_smart_option( $option, $attr ) {
			$value = $this->get_option_attr( $option, $attr );
			if ( ! empty( $value ) ) {
				$this->set_last_smart_option( $attr, $value );
			}
			if ( empty( $value ) ) {
				$value = $this->get_last_smart_option( $attr, $value );
			}
			return $value;
		}

		function setup() {
			if ( empty( $this->get_output_path() ) ) {
				$this->set_output_path( plugin_dir_path( $this->get_filesystem_path() ) );
			}
			$this->add_templates_path( plugin_dir_path( $this->get_filesystem_path() ) );
		}

		function init() {
			$this->setup();
			add_action( 'woocommerce_sections_' . $this->get_settings_id(), array( $this, 'set_settings' ), PHP_INT_MAX );
			add_filter( 'woocommerce_get_sections_' . $this->get_settings_id(), array( $this, 'set_sections' ), PHP_INT_MAX );
			add_filter( 'woocommerce_sections_' . $this->get_settings_id(), array( $this, 'output' ), PHP_INT_MAX );
			add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );




			//$curl = new \WP_Http_Curl();
			//$test = $curl->request( 'http://localhost/wpdev/wp-admin/admin.php?page=wc-settings&tab=alg_wc_cost_of_goods&section' );
			//error_log(print_r($test,true));

			//error_log('asd');
			/*add_action('init',function(){
				//error_log('----');

				$pages = \WC_Admin_Settings::get_settings_pages();

				foreach ($pages as $page){
					//error_log($page->get_id());
					if($this->get_settings_id()===$page->get_id()){
						error_log(print_r($page->get_id(),true));
					}
				}
				//error_log(print_r($test,true));
				//$test = apply_filters( 'woocommerce_get_settings_pages', array() );
			},999);*/

		}

		function add_to_twig( $twig ) {
			$twig->addFunction( new \Timber\Twig_Function( 'wpfse_section_label', array( $this, 'get_section_label_by_id' ) ) );
			$twig->addFunction( new \Timber\Twig_Function( 'wpfse_option_attr', array( $this, 'get_option_attr' ) ) );
			$twig->addFunction( new \Timber\Twig_Function( 'wpfse_smart_opt', array( $this, 'get_smart_option' ) ) );
			$twig->addFunction( new \Timber\Twig_Function( 'wpfse_option_icon', array( $this, 'get_option_icon' ) ) );
			$twig->addFilter( new \Timber\Twig_Filter( 'wpfse_remove_local_links', array( $this, 'remove_local_links' ) ) );
			$twig->addFilter(new \Twig\TwigFilter('ucfirst', 'ucfirst'));
			$twig->addFilter(new \Twig\TwigFilter('wpfse_final_dot', array( $this, 'final_dot' )));
			return $twig;
		}

		function final_dot( $string ) {
			$last_char = substr( $string, - 1 );
			return '.' !== $last_char && ! empty( $last_char ) ? $string . '.' : $string;
		}

		function get_option_icon( $option ) {
			$icon = '';
			switch ( $option['type'] ) {
				case 'checkbox':
					if ( filter_var( $option['default'], FILTER_VALIDATE_BOOLEAN ) ) {
						$icon = '<i class="fa-regular fa-square-check"></i>';
					} else {
						$icon = '<i class="fa-regular fa-square-check"></i>';
						//$icon = '<i class="fa-solid fa-square"></i>';
					}
					//$icon = '<i class="material-symbols-outlined">check_box</i>';
					break;
				case 'text':
					$icon = '<i class="fa-regular fa-input-text"></i>';
					//$icon = '<i class="fa-solid fa-input-text"></i>';
					//$icon = '<i class="material-symbols-outlined">text_fields</i>';
					break;
				case 'number':
					$icon = '<i class="fa-regular fa-input-numeric"></i>';
					//$icon = '<i class="fa-solid fa-input-text"></i>';
					//$icon = '<i class="material-symbols-outlined">text_fields</i>';
					break;
				case 'select':
					$icon = '<i class="fa-regular fa-square-caret-down"></i>';
					//$icon = '<i class="fa-solid fa-input-text"></i>';
					//$icon = '<i class="material-symbols-outlined">text_fields</i>';
					break;
				case 'multiselect':
					$icon = '<i class="fa-regular fa-list-dropdown"></i>';
					//$icon = '<i class="fa-solid fa-input-text"></i>';
					//$icon = '<i class="material-symbols-outlined">text_fields</i>';
					break;
				case 'radio':
					$icon = '<i class="fa-regular fa-circle-dot"></i>';
					break;
				default:
					//$icon='<i class="fa-solid fa-check"></i>';
					//$icon = '<i class="material-symbols-outlined">radio_button_checked</i>';
			}
			//error_log(print_r($option,true));

			return $icon;
		}

		function get_section_label_by_id( $section_id ) {
			$sections = $this->get_sections();
			if ( isset( $sections[ $section_id ] ) ) {
				return $sections[ $section_id ];
			}
			return $section_id;
		}

		function set_sections( $sections ) {
			$this->settings_sections = $sections;
			return $sections;
		}

		function get_sections() {
			return $this->settings_sections;
		}

		function set_settings() {
			$settings = array();
			foreach ( $this->get_sections() as $section_id => $section_title ) {
				$settings[ $section_id ] = apply_filters( 'woocommerce_get_settings_' . $this->get_settings_id() . '_' . $section_id, array() );
			}
			$this->settings = $settings;
		}

		function generate_output_filename() {
			$context = Timber::context();
			$context = array_merge( $context, array(
				'exporter_name'   => $this->get_exporter_name(),
				'output_filename' => $this->get_output_filename()
			) );
			return Timber::compile_string( $this->get_output_filename_template(), $context );
		}

		function output() {
			$context                  = Timber::context();
			$context['full_settings'] = $this->get_settings();
			Timber::$locations        = $this->get_templates_path();
			$result                   = Timber::compile( $this->get_template_file(), $context );
			file_put_contents( $this->output_path . '/' . $this->generate_output_filename(), $result );
		}

		function remove_local_links( $value ) {
			if ( strpos( $value, get_site_url() ) !== false ) {
				$allowed_html = wp_kses_allowed_html( 'post' );
				unset( $allowed_html['a'] );
				$value = wp_kses( $value, $allowed_html );
			}
			return $value;
		}

		function add_settings_id( $settings_id ) {
			$this->settings_id = $settings_id;
		}

		function set_output_path( $path ) {
			$this->output_path = $path;
		}

		function get_output_path(){
			return $this->output_path;
		}

		function get_settings_id() {
			return $this->settings_id;
		}

		/**
		 * @return mixed
		 */
		public function get_output_filename_template() {
			return $this->output_filename_template;
		}

		/**
		 * @param mixed $output_file_name
		 */
		public function set_output_filename_template( $output_filename_template ) {
			$this->output_filename_template = $output_filename_template;
		}

		/**
		 * @return mixed
		 */
		public function get_settings() {
			return $this->settings;
		}

		/**
		 * @return mixed
		 */
		public function get_templates_path() {
			return $this->templates_path;
		}

		/**
		 * @param mixed $templates_path
		 */
		public function add_templates_path( $templates_path ) {
			$path = $this->get_templates_path();
			array_unshift( $path, $templates_path );
			$this->templates_path = $path;
		}

		/**
		 * @return string
		 */
		public function get_exporter_name() {
			return $this->exporter_name;
		}

		/**
		 * @param string $exporter_name
		 */
		public function set_exporter_name( $exporter_name ) {
			$this->exporter_name = $exporter_name;
		}

		/**
		 * @return string
		 */
		public function get_template_file() {
			return $this->template_file;
		}

		/**
		 * @param $template_file
		 */
		public function set_template_file( $template_file ) {
			$this->template_file = $template_file;
		}

		/**
		 * @return mixed
		 */
		public function get_output_filename() {
			return $this->output_filename;
		}

		/**
		 * @param mixed $output_filename
		 */
		public function set_output_filename( $output_filename ) {
			$this->output_filename = $output_filename;
		}

		/**
		 * @return mixed
		 */
		public function get_custom_wc_settings_attribute() {
			return $this->custom_wc_settings_attribute;
		}

		/**
		 * @param mixed $custom_wc_settings_attribute
		 */
		public function set_custom_wc_settings_attribute( $custom_wc_settings_attribute ) {
			$this->custom_wc_settings_attribute = $custom_wc_settings_attribute;
		}

		/**
		 * @return mixed
		 */
		public function get_filesystem_path() {
			return $this->filesystem_path;
		}

		/**
		 * @param mixed $filesystem_path
		 */
		public function set_filesystem_path( $filesystem_path ) {
			$this->filesystem_path = $filesystem_path;
		}





	}
}