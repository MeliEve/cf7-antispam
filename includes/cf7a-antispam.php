<?php

class CF7_AntiSpam_filters {

	protected $b8;

	public function __construct() {
		$this->b8 = $this->cf7a_b8_init();
	}

	private function cf7a_b8_init() {
		// the database
		global $wpdb;

		// B8 config
		$mysql = new mysqli( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

		$config_b8      = array( 'storage' => 'mysql' );
		$config_storage = array(
			'resource' => $mysql,
			'table'    => $wpdb->prefix . 'cf7_antispam_wordlist'
		);

		// We use the default lexer settings
		$config_lexer = [];

		// We use the default degenerator configuration
		$config_degenerator = [];

		// Include the b8 code
		require_once CF7ANTISPAM_PLUGIN_DIR . '/vendor/b8/b8.php';

		# Create a new b8 instance
		try {
			return new b8\b8( $config_b8, $config_storage, $config_lexer, $config_degenerator );
		} catch ( Exception $e ) {
			error_log( 'CF7 Antispam error message: ' . $e->getMessage() );
			exit();
		}
	}

	public function cf7a_b8_classify($message, $verbose = false) {
		$time_elapsed = microtimeFloat();

		$rating = $this->b8->classify( $message );

		if ($verbose) {
			error_log( "CF7 Antispam - message:\r\n$message"  );
			error_log( 'CF7 Antispam - Classification: ' . $rating );

			$mem_used      = round( memory_get_usage() / 1048576, 5 );
			$peak_mem_used = round( memory_get_peak_usage() / 1048576, 5 );
			$time_taken    = round( microtimeFloat() - $time_elapsed, 5 );

			error_log( "CF7 Antispam stats : Memory: $mem_used - Peak memory: $peak_mem_used - Time Elapsed: $time_taken" );
		}

		return $rating;
	}

	public function cf7a_b8_learn_spam($message) {
		$this->b8->learn( $message, b8\b8::SPAM );
	}

	public function cf7a_b8_unlearn_spam($message) {
		$this->b8->unlearn( $message, b8\b8::SPAM );
	}

	public function cf7a_b8_learn_ham($message) {
		$this->b8->learn( $message, b8\b8::HAM );
	}

	public function cf7a_b8_unlearn_ham($message) {
		$this->b8->unlearn( $message, b8\b8::HAM );
	}

	public function cf7a_d8_flamingo_message() {
		echo sprintf(
			'<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>',
			esc_html( sprintf( __( "I learned this was spam - score before/after: %s/%s", 'cf7-antispam'), "100", "200") )
		);
	}

	public function cf7a_d8_flamingo_classify() {

		if ( !isset($_REQUEST['action'] ) || $_REQUEST['action'] !== 'spam' && $_REQUEST['action'] !== 'unspam' && $_REQUEST['action'] !== 'save' ) {
			return;
		}

		if ( $_REQUEST['action'] === 'save' && $_REQUEST['save'] === 'Update' ) {
			$action = $_REQUEST['inbound']['status'] == 'spam' ? 'spam' : 'ham'; // spam / ham
		} else if ($_REQUEST['action'] === 'spam' ){
			$action = 'spam';
		} else if ($_REQUEST['action'] === 'unspam' ){
			$action = 'ham';
		}

		foreach ( (array) $_REQUEST['post'] as $post ) {

			$flamingo_post = new Flamingo_Inbound_Message( $post );

			//TODO there is 4 sure a better way to get the additional settings, at @Takayuki

			// get the form tax using the slug we find in the flamingo message
			$form = get_term_by('slug', $flamingo_post->channel,'flamingo_inbound_channel');

			// get the post where are stored the form data
			$additional_f = get_page_by_path($form->slug, '', 'wpcf7_contact_form');

			// get the additional setting of the form
			$form_additional_settings = get_post_meta( $additional_f->ID, '_additional_settings', true) ;

			if ($form_additional_settings !== '') {
				$lines = explode( "\n", $form_additional_settings); // TODO: best practice is to explode using EOL (End Of Line).

				// extract the flamingo_key = value;
				foreach ($lines as $line) {
					$matches = array();
					preg_match('/flamingo_(.*)(?=:): "\[(.*)]"/', $line , $matches);
					$additional_settings[$matches[1]] = $matches[2];
				}
			}

			if ( isset($additional_settings) && isset( $flamingo_post->fields[$additional_settings['message']] ) ) {

				$text = stripslashes($flamingo_post->fields[$additional_settings['message']]);
				$rating = $text != '' ? $this->cf7a_b8_classify($text) : "none" ;

				if ( $action == 'spam' ) {

					$this->cf7a_b8_unlearn_ham($text);
					$this->cf7a_b8_learn_spam($text);

				} else if ( $action == 'ham' ) {

					$this->cf7a_b8_unlearn_spam($text);
					$this->cf7a_b8_learn_ham($text);

				} else {
					return;
				}

				$rating_after = $text != '' ? $this->cf7a_b8_classify($text) : "none" ;

				update_post_meta( $flamingo_post->id(), '_cf7a_b8_classification', $rating_after );

				error_log( sprintf( __( "I learned {$flamingo_post->id()} {$flamingo_post->from_email} was $action - score before/after: %f/%f", 'cf7-antispam'), $rating, $rating_after) );
			}
		}
	}

	public function flamingo_columns($columns) {
		return array_merge( $columns, array(
			'd8' => __( 'd8 classification', 'cf7-antispam' )
		));
	}

	public function flamingo_d8_column( $column, $post_id ) {
		$classification = get_post_meta($post_id, '_cf7a_b8_classification', true);
		if ( 'd8' === $column ) {
			echo $classification ? round(floatval($classification)  * 100) . '%' : 'none';
		}
	}

	public function cf7a_spam_filter( $spam ) {
		// the database
		global $wpdb;

		// Time Counter
		$time_elapsed = null;

		// Get the submitted data
		$submission = WPCF7_Submission::get_instance();

		// this plugin options
		$options = get_option( 'cf7a_options', array() );

		// check the timestamp
		$timestamp                       = isset($_POST['_wpcf7a_form_creation_timestamp']) ? intval( cf7a_decrypt( $_POST['_wpcf7a_form_creation_timestamp'] ) ) : 0;
		$timestamp_submitted             = $submission->get_meta( 'timestamp' );
		$submission_minimum_time_elapsed = 3;
		$submission_maximum_time_elapsed = 3600;

		// Checks if the mail contains bad words
		$bad_words = $options['bad_words_list'];

		// Checks if the mail contains bad user agent
		$bad_user_agent_list = $options['bad_user_agent_list'];
		$user_agent = $submission->get_meta( 'user_agent' );

		// Check sender mail has prohibited string
		$bad_email_strings = $options['bad_email_strings_list'];

		// Get the contact form additional data
		$contact_form = $submission->get_contact_form();

		$email   = $contact_form->pref( 'flamingo_email' );
		$subject = $contact_form->pref( 'flamingo_subject' );
		$message = $contact_form->pref( 'flamingo_message' );

		$message_compressed = str_replace( " ", "", strtolower( $message ) );

		// check the remote ip
		$remote_ip = $submission->get_meta( 'remote_ip' );

		$real_remote_ip = cf7a_decrypt( $_POST['_wpcf7a_real_sender_ip'] );
		$real_remote_ip = filter_var( $remote_ip, FILTER_VALIDATE_IP ) ? $remote_ip : '';

		// TESTING
		// $remote_ip = "93.57.247.109"; //test ipv4
		// $remote_ip = "185.153.110.243"; //test ipv4
		// $remote_ip = "0000:0000:0000:0000:0000:ffff:7f00:0001"; //test ipv6 spam
		// $remote_ip = "2a00:23c6:f508:7b00:41aa:8900:7785:3824"; //test ipv6 ok

		// b8 threshold
		$b8_threshold = floatval( $options['b8_threshold'] );
		$b8_threshold = ( $b8_threshold > 0 && $b8_threshold < 1 ) ? $b8_threshold : 1;

		/**
		 * Check if the time to submit the email il lower than expected
		 */
		if ( $options['check_time'] ) {
			if ($timestamp == 0) {
				$spam = true;

				error_log( "_wpcf7a_timestamp field is missing, probable form hacking attempt" );

				$submission->add_spam_log( array(
					'agent'  => 'timestamp_issue',
					'reason' => "_wpcf7a_timestamp field is missing, probable form hacking attempt",
				) );
			}

			if ( $timestamp_submitted <= ( $timestamp + $submission_minimum_time_elapsed ) ) {

				$spam = true;

				$time_elapsed = $timestamp_submitted - $timestamp;

				error_log( "It took too little time to fill in the form - ($time_elapsed)" );

				$submission->add_spam_log( array(
					'agent'  => 'timestamp_issue',
					'reason' => "Sender send the email in $time_elapsed. Too little to complete this form!",
				) );
			}

			/**
			 * Check if the time to submit the email il higher than expected
			 */
			if ( $timestamp_submitted >= ( $timestamp + $submission_maximum_time_elapsed ) ) {

				$spam = true;

				$time_elapsed = $timestamp_submitted - $timestamp;

				error_log( "It took too much time to fill in the form - ($time_elapsed)" );

				$submission->add_spam_log( array(
					'agent'  => 'timestamp_issue',
					'reason' => "Sender send the email in $time_elapsed. Too much time to complete this form or the timestamp was hacked!",
				) );
			}
		}

		/**
		 * Checks if the emails contains prohibited words
		 * for example it check if the sender mail is the same than the website domain because it is an attempt to bypass controls,
		 * because emails client can't blacklists the email itself, we must prevent it
		 */
		if ( $options['check_bad_email_strings'] && $email ) {

			foreach ( $bad_email_strings as $bad_email_string ) {

				if ( false !== stripos( strtolower( $email ), strtolower( $bad_email_string ) ) ) {

					$spam = true;

					error_log( "The sender mail domain is the same of the website - {$email} contains $bad_email_string" );

					$submission->add_spam_log( array(
						'agent'  => 'same_domain',
						'reason' => "Hijack the sender mail",
					) );
				}
			}
		}

		/**
		 * Checks if the emails user agent is denied
		 */
		if ( $options['check_bad_user_agent'] && $user_agent ) {

			foreach ( $bad_user_agent_list as $bad_user_agent ) {

				if ( false !== stripos( strtolower( $user_agent ), strtolower( $bad_user_agent ) ) ) {

					$spam = true;

					error_log( "The email user agent is listed into bad user agent list - $user_agent contains $bad_user_agent" );

					$submission->add_spam_log( array(
						'agent'  => 'bad_user_agent',
						'reason' => "The email user agent is listed into bad user agent list",
					) );
				}
			}
		}

		/**
		 * Search for prohibited words
		 */
		if ( $options['check_bad_words'] && $message_compressed != '' ) {
			foreach ( $bad_words as $bad_word ) {
				if ( false !== stripos( $message_compressed, str_replace( " ", "", strtolower( $bad_word ) ) ) ) {

					error_log( "Detected a bad word ($bad_word)" );

					$spam = true;

					$submission->add_spam_log( array(
						'agent'  => 'bad_words',
						'reason' => "Detected a bad word ($bad_word)",
					) );
				}
			}
		}


		/**
		 * Check the remote ip if is listed into Domain Name System Blacklists
		 * DNS blacklist are spam blocking DNS like lists that allow to block messages from specific systems that have a history of sending spam
		 */
		if ( $options['check_dnsbl'] && $remote_ip ) {
			// dsnbl check - inspiration taken from https://gist.github.com/tbreuss/74da96ff5f976ce770e6628badbd7dfc

			if ( filter_var( $remote_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {

				$reverse_ip = cf7a_reverse_ipv4( $remote_ip );

				if ( false !== ( $dnsbl = cf7a_check_dnsbl( $reverse_ip, 'ipv4' ) ) ) {

					error_log( "The $remote_ip has tried to send an email but is listed in the $dnsbl IPv4 Domain Name System Blacklists." );

					$spam = true;

					$submission->add_spam_log( array(
						'agent'  => 'dnsbl_listed',
						'reason' => "$remote_ip listed in the dnsbl IPv4 $dnsbl",
					) );

				}

			} else if ( filter_var( $remote_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {

				$reverse_ip = cf7a_reverse_ipv6( $remote_ip );

				if ( false !== ( $dnsbl = cf7a_check_dnsbl( $reverse_ip, 'ipv6' ) ) ) {

					error_log( "The $remote_ip has tried to send an email but is listed in the $dnsbl IPv6 Domain Name System Blacklists." );

					$spam = true;

					$submission->add_spam_log( array(
						'agent'  => 'dnsbl_listed',
						'reason' => "$remote_ip listed in the dnsbl IPv6 $dnsbl",
					) );

				}
			}
		}

		/**
		 * B8 is a statistical "Bayesian" spam filter
		 * https://nasauber.de/opensource/b8/
		 */
		if ( $options['enable_b8'] && $message ) {

			$text   = stripslashes( $message );
			$rating = $this->cf7a_b8_classify($text);
			error_log( 'CF7 Antispam - Classification before learning: ' . $rating );

			if ( $spam || $rating > $b8_threshold ) {

				$this->cf7a_b8_learn_spam($text);

				if ($rating > $b8_threshold) {
					error_log( "CF7 Antispam - D8 detect spamminess of $rating while the minimum is > $b8_threshold so this mail will be marked as spam" );

					$spam = true;

					$submission->add_spam_log( array(
						'agent'  => 'd8_spam_detected',
						'reason' => "d8 spam detected",
					) );
				}

			} else if ( $rating < ( $b8_threshold * .5 ) ) {

				// the mail was classified as ham so we let learn to d8 what is considered (a probable) ham
				$this->cf7a_b8_learn_ham($text);

				error_log( "CF7 Antispam - D8 detect spamminess of $rating (below the half of the threshold of $b8_threshold) so this mail will be marked as ham" );
			}
		}

		return $spam; // case closed
	}

}