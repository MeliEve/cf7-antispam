<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    CF7_AntiSpam
 * @subpackage CF7_AntiSpam/includes
 * @author     Codekraft Studio <info@codekraft.it>
 */

class CF7_AntiSpam_Activator {

	/**
	 * Creating a private static variable called $default_cf7a_options and assigning it an empty array.
	 *
	 * @var $default_cf7a_options
	 */
	private static $default_cf7a_options = array();

	/**
	 * Creating an array of default options for the plugin.
	 *
	 * @var $default_cf7a_options_bootstrap
	 */
	private static $default_cf7a_options_bootstrap = array();

	/**
	 * It sets the default options for the plugin.
	 */
	public static function init_vars() {

		self::$default_cf7a_options = array(
			'cf7a_version'                 => CF7ANTISPAM_VERSION,
			'cf7a_customizations_class'    => CF7ANTISPAM_HONEYPOT_CLASS,
			'cf7a_customizations_prefix'   => CF7ANTISPAM_PREFIX,
			'cf7a_cipher'                  => 'aes-128-cbc',
			'cf7a_score_preset'            => 'weak',
			'cf7a_disable_reload'          => true,
			'check_bot_fingerprint'        => true,
			'check_bot_fingerprint_extras' => true,
			'append_on_submit'             => true,
			'check_time'                   => true,
			'check_time_min'               => 6,
			'check_time_max'               => 3600 * 48,
			'check_bad_ip'                 => true,
			'autostore_bad_ip'             => true,
			'max_attempts'                 => 3,
			'unban_after'                  => 'disabled',
			'check_bad_words'              => true,
			'check_bad_email_strings'      => true,
			'check_bad_user_agent'         => true,
			'check_dnsbl'                  => true,
			'check_refer'                  => true,
			'check_honeypot'               => true,
			'check_honeyform'              => false,
			'check_geoip'                  => false,
			'geoip_dbkey'                  => false,
			'check_language'               => false,
			'check_geo_location'           => false,
			'honeyform_position'           => 'wp_footer',
			'enable_b8'                    => true,
			'b8_threshold'                 => 0.95,
			'enable_advanced_settings'     => 0,
			'bad_words_list'               => array(),
			'bad_ip_list'                  => array(),
			'bad_email_strings_list'       => array(),
			'bad_user_agent_list'          => array(),
			'dnsbl_list'                   => array(),
			'honeypot_input_names'         => array(),
			'languages'                    => array(
				'allowed'    => array(),
				'disallowed' => array(),
			),
			'score'                        => array(
				'_fingerprinting' => 0.1,
				'_time'           => 0.3,
				'_bad_string'     => 0.5,
				'_dnsbl'          => 0.1,
				'_honeypot'       => 0.3,
				'_detection'      => 1,
				'_warn'           => 0.3,
			),
		);

		self::$default_cf7a_options_bootstrap = array(
			'bad_words_list'         => array(
				'viagra',
				'Earn extra cash',
				'MEET SINGLES',
			),
			'bad_email_strings_list' => array(
				wp_parse_url( get_site_url(), PHP_URL_HOST ),
			),
			'bad_user_agent_list'    => array(
				'bot',
				'puppeteer',
				'phantom',
				'User-Agent',
				'Java',
				'PHP',
			),
			'dnsbl_list'             => array(
				/* ipv4 dnsbl */
				'dnsbl-1.uceprotect.net',
				'dnsbl-2.uceprotect.net',
				'dnsbl-3.uceprotect.net',
				'dnsbl.sorbs.net',
				'zen.spamhaus.org',
				'bl.spamcop.net',
				'b.barracudacentral.org',
				'dnsbl.dronebl.org',
				/* ipv6 dnsbl */
				'dnsbl.spfbl.net',
				'bogons.cymru.com',
				'bl.ipv6.spameatingmonkey.net',
			),
			'honeypot_input_names'   => array(
				'name',
				'email',
				'address',
				'zip',
				'town',
				'phone',
				'credit-card',
				'ship-address',
				'billing_company',
				'billing_city',
				'billing_country',
				'email-address',
			),
			'languages'              => array(
				'allowed'    => isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] )
					? cf7a_get_browser_language_array( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) )
					: array(),
				'disallowed' => array(),
			),
		);
	}


	/**
	 * Script that runs when the plugin is installed
	 *
	 * @since    0.1.0
	 */
	public static function install() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		/* create the term database */
		$cf7a_wordlist = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . "cf7a_wordlist (
		  `token` varchar(100) character set utf8 collate utf8_bin NOT NULL,
		  `count_ham` int unsigned default NULL,
		  `count_spam` int unsigned default NULL,
		  PRIMARY KEY (`token`)
		) $charset_collate;";

		$cf7a_wordlist_version = 'INSERT INTO ' . $wpdb->prefix . "cf7a_wordlist (`token`, `count_ham`) VALUES ('b8*dbversion', '3');";
		$cf7a_wordlist_texts   = 'INSERT INTO ' . $wpdb->prefix . "cf7a_wordlist (`token`, `count_ham`, `count_spam`) VALUES ('b8*texts', '0', '0');";

		$cf7a_database = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . "cf7a_blacklist (
			 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			 `ip` varchar(45) NOT NULL,
			 `status` int(10) unsigned DEFAULT NULL,
			 `meta` longtext,
			 PRIMARY KEY (`id`),
             UNIQUE KEY `id` (`ip`)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $cf7a_wordlist );
		dbDelta( $cf7a_wordlist_version );
		dbDelta( $cf7a_wordlist_texts );

		dbDelta( $cf7a_database );
	}

	/**
	 *  Create or Update the CF7 Antispam options
	 *
	 * @param bool $reset_options - whatever to force the reset.
	 */
	public static function update_options( $reset_options = false ) {

		self::init_vars();

		$options = get_option( 'cf7a_options' );

		if ( false !== $options && ! $reset_options ) {

			/* update the plugin options but add the new options automatically */
			if ( isset( $options['cf7a_version'] ) ) {
				unset( $options['cf7a_version'] );
			}

			/* merge previous options with the updated copy keeping the already selected option as default */
			$new_options = array_merge( self::$default_cf7a_options, $options );

			if ( CF7ANTISPAM_DEBUG ) {
				cf7a_log( ' plugin options updated' );}

			update_option( 'cf7a_options', $new_options );
		} else {
			/* if the plugin options are missing Init the plugin with the default option + the default settings */
			$new_options = array_merge( self::$default_cf7a_options, self::$default_cf7a_options_bootstrap );

			add_option( 'cf7a_options', $new_options );
		}

		if ( CF7ANTISPAM_DEBUG ) {
			cf7a_log( $new_options );
		}

		require_once CF7ANTISPAM_PLUGIN_DIR . '/admin/admin-tools.php';

		$notice = new CF7_AntiSpam_Admin_Tools();
		$notice::cf7a_push_notice( esc_html__( 'CF7 AntiSpam updated successful! Please flush cache to refresh hidden form data', 'cf7-antispam' ), 'cf7-antispam' );

	}

	/**
	 *  Activate CF7 Antispam Plugin
	 */
	public static function activate() {

		if ( CF7ANTISPAM_DEBUG ) {
			cf7a_log( ' plugin enabled' );
		}

		if ( ! get_option( 'cf7a_db_version' ) ) {
			self::install();
			update_option( 'cf7a_db_version', '1' );
		}

		/* If the options do not exist then create them*/
		self::update_options();

		set_transient( 'cf7a_activation', true );
	}

}
