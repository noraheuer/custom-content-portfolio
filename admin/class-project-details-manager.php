<?php

/**
 * Handles building the project details manager.
 *
 * @since  1.0.0
 * @access public
 */
final class CCP_Project_Details_Manager {

	/**
	 * The post object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object
	 */
	public $post;

	/**
	 * The post ID.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    int
	 */
	public $post_id = 0;

	/**
	 * Array of sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $sections = array();

	/**
	 * Array of controls.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $controls = array();

	/**
	 * Array of settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $settings = array();

	/**
	 * Sets up the cap tabs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $role
	 * @param  array   $has_caps
	 * @return void
	 */
	public function __construct( $post ) {

		$this->post    = $post;
		$this->post_id = $post->ID;

		require_once( ccp_plugin()->dir_path . 'admin/class-project-details-section.php' );
		require_once( ccp_plugin()->dir_path . 'admin/class-project-details-control.php' );
		require_once( ccp_plugin()->dir_path . 'admin/class-project-details-setting.php' );

		require_once( ccp_plugin()->dir_path . 'admin/class-project-details-control-date.php' );
		require_once( ccp_plugin()->dir_path . 'admin/class-project-details-control-excerpt.php' );
		require_once( ccp_plugin()->dir_path . 'admin/class-project-details-setting-date.php' );

		// Add sections and controls.
		$this->register();
	}

	/**
	 * Registers the sections (and each section's controls) that will be used for
	 * the tab content.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function register() {

		// Hook before registering.
		do_action( 'ccp_project_details_manager_register', $this );
	}

	/**
	 * Register a section.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function register_section( $name, $args = array() ) {

		$defaults = array(
			'label'   => '',
			'post'    => $this->post,
			'icon'    => 'dashicons-admin-generic',
			'object'  => 'CCP_Project_Details_Section'
		);

		$args = wp_parse_args( $args, $defaults );

		$section_object = $args['object'];

		$this->sections[ $name ] = new $section_object( $this, $name, $args );
	}

	/**
	 * Register a control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function register_control( $name, $args = array() ) {

		$defaults = array(
			'label'       => '',
			'description' => '',
			'section'     => '',
			'setting'     => $name,
			'post'        => $this->post,
			'object'      => 'CCP_Project_Details_Control'
		);

		$args = wp_parse_args( $args, $defaults );

		$control_object = $args['object'];

		$this->controls[ $name ] = new $control_object( $this, $name, $args );
	}

	/**
	 * Register a setting.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function register_setting( $name, $args = array() ) {

		$defaults = array(
			'sanitize_callback' => '',
			'post'              => $this->post,
			'object'            => 'CCP_Project_Details_Setting'
		);

		$args = wp_parse_args( $args, $defaults );

		$setting_object = $args['object'];

		$this->settings[ $name ] = new $setting_object( $this, $name, $args );
	}

	public function get_setting( $name ) {

		return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : false;
	}

	/**
	 * Outputs the manager HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function display() { ?>

		<div id="ccp-project-tabs" class="postbox">

			<h3><?php printf( esc_html__( 'Project Details: %s', 'members' ), '<span class="ccp-which-tab"></span>' ); ?></h3>

			<div class="inside">

				<div class="ccp-project-manager">
					<?php $this->nav(); ?>
					<?php $this->content(); ?>
				</div><!-- .ccp-project-manager -->

			</div><!-- .inside -->

		</div><!-- .postbox -->
	<?php }

	/**
	 * Outputs the nav.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function nav() { ?>

		<ul class="ccp-project-sections-nav">

		<?php foreach ( $this->sections as $section ) : ?>

			<?php $icon = preg_match( '/dashicons-/', $section->icon ) ? sprintf( 'dashicons %s', sanitize_html_class( $section->icon ) ) : esc_attr( $section->icon ); ?>

			<li>
				<a href="<?php echo esc_attr( "#ccp-project-section-{$section->name}" ); ?>"><i class="<?php echo $icon; ?>"></i> <span class="label"><?php echo esc_html( $section->label ); ?></span></a>
			</li>

		<?php endforeach; ?>

		</ul><!-- .ccp-project-sections-nav -->
	<?php }

	/**
	 * Outputs the content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function content() { ?>

		<div class="ccp-project-sections">

		<?php foreach ( $this->sections as $section ) : ?>

			<div id="<?php echo esc_attr( "ccp-project-section-{$section->name}" ); ?>" class="ccp-project-section <?php echo esc_attr( "ccp-project-section-{$section->name}" ); ?>">

				<?php foreach ( $this->controls as $control ) : ?>

					<?php if ( $section->name === $control->section ) : ?>

						<div id="<?php echo esc_attr( "ccp-project-control-{$control->name}" ); ?>" class="ccp-project-control <?php echo esc_attr( "ccp-project-control-{$control->name}" ); ?>">
							<?php $control->content_template(); ?>
						</div><!-- .ccp-project-control -->

					<?php endif; ?>

				<?php endforeach; ?>

			</div><!-- .ccp-project-section -->

		<?php endforeach; ?>

		</div><!-- .ccp-project-sections -->
	<?php }

	/**
	 * Saves the settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function update() {

		foreach ( $this->settings as $setting )
			$setting->save();
	}
}