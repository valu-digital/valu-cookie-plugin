<?php

namespace Valu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Valu_Cookieplugin_Dashboard
 */
class Valu_Cookieplugin_Dashboard {

	/**
	 * Capability
	 *
	 * @var string
	 */
	private $capability = 'editor';

	/**
	 * Plugin instance.
	 *
	 * @var Valu_Cookieplugin_Dashboard
	 * @access private
	 */
	private static $instance = null;

	/**
	 * Get plugin instance.
	 *
	 * @return Valu_Cookieplugin_Dashboard
	 * @static
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Cookiebot Dashboard constructor.
	 */
	private function __construct() {

		add_action( 'wp_dashboard_setup', [ $this, 'add_metabox' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'scripts' ] );
	}

	/**
	 * Register a rest route to fetch data from Cookiebot
	 */
	function register_rest_routes() {
		register_rest_route( 'valu-cookiebot-dashboard/v1', 'get', [
			'methods'             => 'GET',
			'callback'            => function ( \WP_REST_Request $request ) {

				$site = intval( $request->get_param( 'site' ) );

				$culture  = get_option( 'valu_cookies_culture' );
				$group_id = get_option( 'valu_cookies_serial' );
				$domain   = get_option( 'valu_cookies_domain' );

				if ( ! $group_id || ! $domain ) {
					return [ 'data' => __( 'Error while fetching information', 'valu-cookie-plugin' ) ];
				}
				if ( strlen( $domain ) < 1 || strlen( $group_id ) < 1 ) {
					return [ 'data' => __( 'Error while fetching information', 'valu-cookie-plugin' ) ];
				}

				$start_date = date( 'Ymd', strtotime( '-30 days' ) );
				$end_date   = date( 'Ymd', strtotime( '-1 days' ) );

				$api_key = get_option( 'valu_cookies_cookiebot-api-key' );
				if ( defined( 'VALU_COOKIEPLUGIN_API_KEY' ) ) {
					if ( is_scalar( VALU_COOKIEPLUGIN_API_KEY ) ) {
						$api_key = VALU_COOKIEPLUGIN_API_KEY;
					} elseif ( is_array( VALU_COOKIEPLUGIN_API_KEY ) && isset ( VALU_COOKIEPLUGIN_API_KEY[ get_current_blog_id() ] ) && VALU_COOKIEPLUGIN_API_KEY[ get_current_blog_id() ] ) {
						$api_key = VALU_COOKIEPLUGIN_API_KEY[ get_current_blog_id() ];
					}
				}
				if ( false === ( $response_body = get_transient( 'valu_cookiebot_' . $site ) ) ) {
					$response      = wp_remote_get( 'https://consent.cookiebot.com/api/v1/' . $api_key . '/json/domaingroup/' . $group_id . '/domain/' . $domain . '/consent/stats?' . 'startdate=' . $start_date . '&enddate=' . $end_date );
					$response_body = wp_remote_retrieve_body( $response );
					set_transient( 'valu_cookiebot_' . $site, $response_body, 120 );
				}

				$result         = json_decode( $response_body, true );
				$parsedConsents = self::parseConsentData( $result );

				$return = '<h3 style="font-weight: 600">' . esc_html( $result['domain'] ) . '</h3>';
				$return .= sprintf( __( '%d days', 'valu-cookie-plugin' ), 7 ) . ' ' . number_format( $parsedConsents[7] * 100.0, 1, ',', ' ' ) . '%';
				$return .= ' (' . $parsedConsents['7_optout'] . ' / ' . $parsedConsents['7_total'] . ')<br>';
				$return .= sprintf( __( '%d days', 'valu-cookie-plugin' ), 14 ) . ' ' . number_format( $parsedConsents[14] * 100.0, 1, ',', ' ' ) . '%';
				$return .= ' (' . $parsedConsents['14_optout'] . ' / ' . $parsedConsents['14_total'] . ')<br>';
				$return .= sprintf( __( '%d days', 'valu-cookie-plugin' ), 30 ) . ' ' . number_format( $parsedConsents[30] * 100.0, 1, ',', ' ' ) . '%';
				$return .= ' (' . $parsedConsents['30_optout'] . ' / ' . $parsedConsents['30_total'] . ')<br>';

				return [ 'data' => $return ];

			},
			'permission_callback' => function () {
				return $this->can_see_dashboard();
			},
		] );
	}

	/**
	 * Calculate percentages of opt-out visitors
	 *
	 * @param $data Parsed JSON data as an array
	 *
	 * @return int[] Array of containing calculated data
	 */
	static function parseConsentData( $data ) {
		$tempData    = [
			'optin_30'  => 0,
			'optout_30' => 0,
			'optin_14'  => 0,
			'optout_14' => 0,
			'optout_7'  => 0,
			'optin_7'   => 0,
		];
		$returnArray = [ 7 => 0, 14 => 0, 30 => 0 ];
		$now         = new \DateTime();
		foreach ( $data['consentstat']['consentday'] as $dateData ) {
			$date     = \DateTime::createFromFormat( 'Y-m-d\TH:i:s', $dateData['Date'] );
			$abs_diff = $now->diff( $date )->format( "%a" );
			if ( $abs_diff <= 30 ) {
				$tempData['optin_30']  += $dateData['OptIn'];
				$tempData['optout_30'] += $dateData['OptOut'];
			}
			if ( $abs_diff <= 14 ) {
				$tempData['optin_14']  += $dateData['OptIn'];
				$tempData['optout_14'] += $dateData['OptOut'];
			}
			if ( $abs_diff <= 7 ) {
				$tempData['optin_7']  += $dateData['OptIn'];
				$tempData['optout_7'] += $dateData['OptOut'];
			}
		}
		if ( $tempData['optin_30'] + $tempData['optout_30'] > 0 ) {
			$returnArray[30]          = round( $tempData['optout_30'] / ( $tempData['optin_30'] + $tempData['optout_30'] ), 3 );
			$returnArray['30_total']  = ( $tempData['optin_30'] + $tempData['optout_30'] );
			$returnArray['30_optin']  = $tempData['optin_30'];
			$returnArray['30_optout'] = $tempData['optout_30'];
		}
		if ( $tempData['optin_14'] + $tempData['optout_14'] > 0 ) {
			$returnArray[14]          = round( $tempData['optout_14'] / ( $tempData['optin_14'] + $tempData['optout_14'] ), 3 );
			$returnArray['14_total']  = ( $tempData['optin_14'] + $tempData['optout_14'] );
			$returnArray['14_optin']  = $tempData['optin_14'];
			$returnArray['14_optout'] = $tempData['optout_14'];
		}
		if ( $tempData['optin_7'] + $tempData['optout_7'] > 0 ) {
			$returnArray[7]          = round( $tempData['optout_7'] / ( $tempData['optin_7'] + $tempData['optout_7'] ), 3 );
			$returnArray['7_total']  = ( $tempData['optin_7'] + $tempData['optout_7'] );
			$returnArray['7_optin']  = $tempData['optin_7'];
			$returnArray['7_optout'] = $tempData['optout_7'];
		}

		return $returnArray;
	}

	function add_metabox() {

		if ( $this->can_see_dashboard() ) {
			add_meta_box( 'valu-cookiebot-dashboard', 'Cookiebot', [ $this, 'dashboard' ], 'dashboard', 'normal' );
		}
	}

	/**
	 * @return bool
	 */
	function can_see_dashboard(): bool {
		return current_user_can( $this->capability );
	}

	/**
	 * Valu Cookiebot Dashboard form
	 */
	function dashboard() {

		if ( ! $this->can_see_dashboard() ) {
			return;
		}


		$group_id = get_option( 'valu_cookies_serial' );
		$domain   = get_option( 'valu_cookies_domain' );
		if ( $group_id && $domain ) {
			?>

			<form id="valu-cookiebot-dashboard-form" method="post">
				<div class="input-text-wrap valu-cookiebot-dashboard-input-wrap">
					<?php esc_html_e( "How many has selected only necessary cookies?", 'valu-cookie-plugin' ); ?>
				</div>

				<input class="valu-cookiebot-dashboard-submit button button-primary valu-cookiebot-dashboard-input-wrap"
				       type="submit" value="<?php esc_attr_e( "Get", 'valu-cookie-plugin' ); ?>">
			</form>
			<div id="valu-cookiebot-response"></div>


			<script>
                jQuery('document').ready(function ($) {

                    $('#valu-cookiebot-dashboard-form').on('submit', function (e) {

                        e.preventDefault();

                        var cookiebotFormData = $("#valu-cookiebot-dashboard-form").serializeArray();

                        $.ajax({
                            url: wpApiSettings.root + 'valu-cookiebot-dashboard/v1/get',
                            method: 'GET',
                            beforeSend: function (xhr) {
                                xhr.setRequestHeader('X-WP-Nonce', '<?php echo esc_html( wp_create_nonce( 'wp_rest' ) ); ?>');
                            },
                            data: cookiebotFormData,
                        }).done(function (response) {
                            $('#valu-cookiebot-response').html(response.data);
                        });
                    });
                });
			</script>

			<style>
                .valu-cookiebot-dashboard-input-wrap {
                    margin-bottom: 12px;
                }

                #valu-cookiebot-response {
                    margin-top: 12px;
                }
			</style>

			<?php
		}
	}

	/**
	 * Scripts
	 */
	function scripts( $hook ) {
		if ( 'index.php' === $hook ) {
			wp_enqueue_script( 'wp-api' );
		}
	}


}

