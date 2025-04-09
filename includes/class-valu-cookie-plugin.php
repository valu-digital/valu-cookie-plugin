<?php
/**
 * Main plugin class file.
 *
 * @package WordPress Plugin Template/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class Valu_Cookie_Plugin {

	/**
	 * The single instance of Valu_Cookie_Plugin.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.0
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version; //phpcs:ignore

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token; //phpcs:ignore

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * Capability
	 *
	 * @var string
	 */
	private $capability = 'editor';

	/**
	 * Constructor funtion.
	 *
	 * @param string $file File constructor.
	 * @param string $version Plugin version.
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'Valu_Cookie_Plugin';

		// Load plugin environment variables.
		$this->file = $file;

		// Add menu page
		add_action( 'admin_menu', [ $this, 'add_menu' ] );

		// Register rest route
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ], 0 );

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action( 'init', [ $this, 'load_localisation' ], 0 );
	} // End __construct ()

	/**
	 * Return Cookiebot supported cultures which have WordPress translation
	 *
	 * @return array
	 */
	public static function getCookiebotCultures() {
		$cultures = [
			//    "aa" => __('Afar'),
			"af"  => __( 'Afrikaans' ),
//            "agq" => __('Aghem'),
//            "ak" => __('Akan'),
			"sq"  => __( 'Albanian' ),
//            "am" => __('Amharic'),
			"ar"  => __( 'Arabic' ),
//            "hy" => __('Armenian'),
//            "as" => __('Assamese'),
//            "ast" => __('Asturian'),
//            "asa" => __('Asu'),
//            "az" => __('Azerbaijani'),
//            "ksf" => __('Bafia'),
//            "bm" => __('Bambara'),
//            "bas" => __('Basaa'),
//            "ba" => __('Bashkir'),
//            "eu" => __('Basque'),
			"be"  => __( 'Belarusian' ),
//            "bem" => __('Bemba'),
//            "bez" => __('Bena'),
//            "bn" => __('Bengali'),
//            "bin" => __('Bini'),
//            "byn" => __('Blin'),
//            "brx" => __('Bodo'),
//            "bs" => __('Bosnian'),
//            "br" => __('Breton'),
			"bg"  => __( 'Bulgarian' ),
//            "my" => __('Burmese'),
			"ca"  => __( 'Catalan' ),
//            "tzm" => __('Central Atlas Tamazight'),
//            "ce" => __('Chechen'),
//            "chr" => __('Cherokee'),
//            "cgg" => __('Chiga'),
			"zh"  => __( 'Chinese' ),
//            "cu" => __('Church Slavic'),
//            "ksh" => __('Colognian'),
//            "kw" => __('Cornish'),
//            "co" => __('Corsican'),
			"hr"  => __( 'Croatian' ),
			"cs"  => __( 'Czech' ),
			"da"  => __( 'Danish' ),
//            "dv" => __('Divehi'),
//            "dua" => __('Duala'),
			"nl"  => __( 'Dutch' ),
//            "dz" => __('Dzongkha'),
//            "ebu" => __('Embu'),
			"en"  => __( 'English' ),
//            "eo" => __('Esperanto'),
			"et"  => __( 'Estonian' ),
//            "ee" => __('Ewe'),
//            "ewo" => __('Ewondo'),
//            "fo" => __('Faroese'),
			"fil" => __( 'Filipino' ),
			"fi"  => __( 'Finnish' ),
			"fr"  => __( 'French' ),
//            "fur" => __('Friulian'),
//            "ff" => __('Fulah'),
			"gl"  => __( 'Galician' ),
//            "lg" => __('Ganda'),
//            "ka" => __('Georgian'),
			"de"  => __( 'German' ),
			"el"  => __( 'Greek' ),
//            "gn" => __('Guarani'),
//            "gu" => __('Gujarati'),
//            "guz" => __('Gusii'),
//            "ha" => __('Hausa'),
//            "haw" => __('Hawaiian'),
			"he"  => __( 'Hebrew' ),
			"hi"  => __( 'Hindi' ),
			"hu"  => __( 'Hungarian' ),
//            "ibb" => __('Ibibio'),
			"is"  => __( 'Icelandic' ),
//            "ig" => __('Igbo'),
//            "smn" => __('Inari Sami'),
			"id"  => __( 'Indonesian' ),
//            "ia" => __('Interlingua'),
//            "iu" => __('Inuktitut'),
			"ga"  => __( 'Irish' ),
			"it"  => __( 'Italian' ),
			"ja"  => __( 'Japanese' ),
//            "jv" => __('Javanese'),
//            "dyo" => __('Jola-Fonyi'),
//            "kea" => __('Kabuverdianu'),
//            "kab" => __('Kabyle'),
//            "kkj" => __('Kako'),
//            "kl" => __('Kalaallisut'),
//            "kln" => __('Kalenjin'),
//            "kam" => __('Kamba'),
//            "kn" => __('Kannada'),
//            "kr" => __('Kanuri'),
//            "ks" => __('Kashmiri'),
//            "kk" => __('Kazakh'),
//            "km" => __('Khmer'),
//            "quc" => __('Kʼicheʼ'),
//            "ki" => __('Kikuyu'),
//            "rw" => __('Kinyarwanda'),
//            "kok" => __('Konkani'),
			"ko"  => __( 'Korean' ),
//            "khq" => __('Koyra Chiini'),
//            "ses" => __('Koyraboro Senni'),
//            "ku" => __('Kurdish'),
//            "nmg" => __('Kwasio'),
//            "ky" => __('Kyrgyz'),
//            "lkt" => __('Lakota'),
//            "lag" => __('Langi'),
//            "lo" => __('Lao'),
//            "la" => __('Latin'),
			"lv"  => __( 'Latvian' ),
//            "ln" => __('Lingala'),
			"lt"  => __( 'Lithuanian' ),
//            "nds" => __('Low German'),
//            "dsb" => __('Lower Sorbian'),
//            "lu" => __('Luba-Katanga'),
//            "smj" => __('Lule Sami'),
//            "luo" => __('Luo'),
//            "lb" => __('Luxembourgish'),
//            "luy" => __('Luyia'),
			"mk"  => __( 'Macedonian' ),
//            "jmc" => __('Machame'),
//            "mgh" => __('Makhuwa-Meetto'),
//            "kde" => __('Makonde'),
//            "mg" => __('Malagasy'),
//            "ms" => __('Malay'),
//            "ml" => __('Malayalam'),
			"mt"  => __( 'Maltese' ),
//            "mni" => __('Manipuri'),
//            "gv" => __('Manx'),
//            "mi" => __('Maori'),
//            "arn" => __('Mapuche'),
//            "mr" => __('Marathi'),
//            "mas" => __('Masai'),
//            "mzn" => __('Mazanderani'),
//            "mer" => __('Meru'),
//            "mgo" => __('Metaʼ'),
//            "moh" => __('Mohawk'),
//            "mn" => __('Mongolian'),
//            "mfe" => __('Morisyen'),
//            "mua" => __('Mundang'),
//            "naq" => __('Nama'),
//            "ne" => __('Nepali'),
//            "nnh" => __('Ngiemboon'),
//            "jgo" => __('Ngomba'),
//            "nqo" => __('NʼKo'),
//            "nd" => __('North Ndebele'),
//            "lrc" => __('Northern Luri'),
//            "se" => __('Northern Sami'),
//            "nso" => __('Northern Sotho'),
//            "nb" => __('Norwegian Bokmål'),
//            "nn" => __('Norwegian Nynorsk'),
//            "nus" => __('Nuer'),
//            "nyn" => __('Nyankole'),
//            "oc" => __('Occitan'),
//            "or" => __('Oriya'),
//            "om" => __('Oromo'),
//            "os" => __('Ossetic'),
//            "pap" => __('Papiamento'),
//            "ps" => __('Pashto'),
			"fa"  => __( 'Persian' ),
			"pl"  => __( 'Polish' ),
			"pt"  => __( 'Portuguese' ),
//            "prg" => __('Prussian'),
//            "pa" => __('Punjabi'),
			"ro"  => __( 'Romanian' ),
//            "rm" => __('Romansh'),
//            "rof" => __('Rombo'),
//            "rn" => __('Rundi'),
			"ru"  => __( 'Russian' ),
//            "rwk" => __('Rwa'),
//            "ssy" => __('Saho'),
//            "sah" => __('Sakha'),
//            "saq" => __('Samburu'),
//            "sg" => __('Sango'),
//            "sbp" => __('Sangu'),
//            "sa" => __('Sanskrit'),
//            "gd" => __('Scottish Gaelic'),
//            "seh" => __('Sena'),
			"sr"  => __( 'Serbian' ),
//            "ksb" => __('Shambala'),
//            "sn" => __('Shona'),
//            "ii" => __('Sichuan Yi'),
//            "sd" => __('Sindhi'),
//            "si" => __('Sinhala'),
//            "sms" => __('Skolt Sami'),
			"sk"  => __( 'Slovak' ),
			"sl"  => __( 'Slovenian' ),
//            "xog" => __('Soga'),
//            "so" => __('Somali'),
//            "nr" => __('South Ndebele'),
//            "sma" => __('Southern Sami'),
//            "st" => __('Southern Sotho'),
			"es"  => __( 'Spanish' ),
//            "zgh" => __('Standard Moroccan Tamazight'),
			"sw"  => __( 'Swahili' ),
//            "ss" => __('Swati'),
			"sv"  => __( 'Swedish' ),
//            "gsw" => __('Swiss German'),
//            "syr" => __('Syriac'),
//            "shi" => __('Tachelhit'),
//            "dav" => __('Taita'),
//            "tg" => __('Tajik'),
//            "ta" => __('Tamil'),
//            "twq" => __('Tasawaq'),
//            "tt" => __('Tatar'),
//            "te" => __('Telugu'),
//            "teo" => __('Teso'),
			"th"  => __( 'Thai' ),
//            "bo" => __('Tibetan'),
//            "tig" => __('Tigre'),
//            "ti" => __('Tigrinya'),
//            "to" => __('Tongan'),
//            "ts" => __('Tsonga'),
//            "tn" => __('Tswana'),
			"tr"  => __( 'Turkish' ),
//            "tk" => __('Turkmen'),
			"uk"  => __( 'Ukrainian' ),
//            "hsb" => __('Upper Sorbian'),
//            "ur" => __('Urdu'),
//            "ug" => __('Uyghur'),
//            "uz" => __('Uzbek'),
//            "vai" => __('Vai'),
//            "ve" => __('Venda'),
			"vi"  => __( 'Vietnamese' ),
//            "vo" => __('Volapük'),
//            "vun" => __('Vunjo'),
//            "wae" => __('Walser'),
			"cy"  => __( 'Welsh' ),
//            "fy" => __('Western Frisian'),
//            "wal" => __('Wolaytta'),
//            "wo" => __('Wolof'),
//            "xh" => __('Xhosa'),
//            "yav" => __('Yangben'),
			"yi"  => __( 'Yiddish' ),
//            "yo" => __('Yoruba'),
//            "dje" => __('Zarma'),
//            "zu" => __('Zulu')
		];
		asort( $cultures );
		reset( $cultures );

		return $cultures;
	}

	/**
	 * Rest routes
	 */
	function register_rest_routes() {
		register_rest_route( 'valu-cookie-plugin/v1', 'get', [
			'methods'             => 'GET',
			'callback'            => function ( \WP_REST_Request $request ) {

				$site    = sanitize_text_field( $request->get_param( 'site' ) );
				$culture = sanitize_text_field( $request->get_param( 'culture' ) );

				return $this->fetchCookiebotInformation( $culture );

			},
			'permission_callback' => [ $this, 'can_use_valu_cookie_plugin' ],
		] );
	}

	/**
	 *
	 * @return bool
	 */
	function can_use_valu_cookie_plugin(): bool {

		return current_user_can( $this->capability );
	}

	/**
	 * Add menu page for Valu Cookie Base
	 */
	function add_menu() {

		if ( $this->can_use_valu_cookie_plugin() ) {
			add_menu_page( __( 'Cookies', 'valu-cookie-plugin' ), __( 'Cookies', 'valu-cookie-plugin' ), $this->capability, 'valu-cookie-plugin', [
				$this,
				'list_cookies'
			], 'dashicons-privacy', 800 );
		}
	}

	function list_cookies() {
		if ( ! $this->can_use_valu_cookie_plugin() ) {
			wp_die( esc_html( __( 'You do not have rights to observe cookies', 'valu-cookie-plugin' ) ) );
		}
		$defaultCulture   = get_option( 'valu_cookies_culture' );
		$selectedCultures = get_option( 'valu_cookies_cultures' );
		$allCultures      = self::getCookiebotCultures();
		$cultures         = [];
		foreach ( $selectedCultures as $code ) {
			$cultures[ $code ] = $allCultures[ $code ];
		}
		if ( $_GET["culture"] ) {
			$selectedCulture = sanitize_text_field( $_GET["culture"] );
		} else {
			$selectedCulture = $defaultCulture;
		}

		?>

		<div class="wrap valu-cookie-plugin-listing">

			<h2 class="title"><?php esc_html_e( 'Cookies', 'valu-cookie-plugin' ); ?></h2>

			<form>
				<input type="hidden" name="page" value="valu-cookie-plugin">
				<label for="culture"><?php esc_html_e( "Language", 'valu-cookie-plugin' ); ?></label>
				<select name="culture">
					<?php
					foreach ( $cultures as $code => $name ) {
						echo "<option value=\"" . esc_attr( $code ) . "\"" . ( $code === $selectedCulture ? " selected=\"selected\"" : "" ) . "\">" . esc_html( $name ) . "</option>\n";
					}
					?>
				</select>
				<input type="submit" value="<?php esc_html_e( 'Get', 'valu-cookie-plugin' ); ?>">
			</form>

			<div id="valu-cookie-plugin-list"><?php esc_html_e( 'Fetching…', 'valu-cookie-plugin' ); ?></div>

			<script>

                const listing = document.querySelector("#valu-cookie-plugin-list");

                jQuery('document').ready(function ($) {
                    $.ajax({
                        method: 'GET',
                        url: '/wp-json/valu-cookie-plugin/v1/get?culture=<?php echo esc_attr( $selectedCulture )?>',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader('X-WP-Nonce', '<?php echo wp_create_nonce( 'wp_rest' )?>');
                        },
                        dataType: 'json',
                    }).done(function (response) {

                        const listing = document.querySelector("#valu-cookie-plugin-list");
                        var data = JSON.parse(response.data);
                        let items = data['cookies'];
                        let last_scan = data['utcscandate'];
                        let last_scan_date = new Date(last_scan).toLocaleString('fi-FI');
                        listing.innerHTML = '<div class="valu-cookie-plugin__last-scan"><?php esc_html_e( 'Cookies scanned last', 'valu-cookie-plugin' )?>: ' + last_scan_date + '</div>';

                        items.map(item => {

                            const list = document.getElementById("valu-cookie-plugin-list");

                            if (item.PurposeDescription.length > 0) {
                                var desc_class = true;
                            } else {
                                var desc_class = false;
                            }

                            let categoryName = "";
                            switch (item.Category) {
                                case "1":
                                    categoryName = "<?php echo __( 'Necessary', 'valu-cookie-plugin' );?>";
                                    break;
                                case "2":
                                    categoryName = "<?php echo __( 'Preferences', 'valu-cookie-plugin' );?>";
                                    break;
                                case "3":
                                    categoryName = "<?php echo __( 'Statistics', 'valu-cookie-plugin' );?>";
                                    break;
                                case "4":
                                    categoryName = "<?php echo __( 'Marketing', 'valu-cookie-plugin' );?>";
                                    break;
                                case "5":
                                    categoryName = "<?php echo __( 'Unclassified', 'valu-cookie-plugin' );?>";
                                    break;
                            }

                            let dom = "";
                            dom += '<div class="valu-cookie-plugin-list-item card" data-valu-cookie-category="' + item.Category + '" data-valu-cookie-description="' + desc_class + '">';
                            dom += '<div class="valu-cookie-plugin-list-item__name"><h2>' + item.Name + '</h2></div>';
                            dom += '<div class="valu-cookie-plugin-list-item__description"><label><?php esc_html_e( 'Cookie Description', 'valu-cookie-plugin' )?>: </label><p>' + item.PurposeDescription + '</p></div>';
                            dom += '<div class="valu-cookie-plugin-list-item__category"><label><?php esc_html_e( 'Cookie Category', 'valu-cookie-plugin' )?>: </label><p>' + categoryName + '</p></div>';
                            dom += '<div class="valu-cookie-plugin-list-item__provider"><label><?php esc_html_e( 'Provider', 'valu-cookie-plugin' )?>: </label><p>' + item.Provider + '</p></div>';
                            dom += '<div class="valu-cookie-plugin-list-item__first-url"><label><?php esc_html_e( 'First found URL', 'valu-cookie-plugin' )?>: </label><a href="' + item.FirstURL + '"><p>' + item.FirstURL + '</p></a></div>';
                            if ("5" === item.Category || false === desc_class) {
                                dom += '<div class="valu-cookie-plugin-list-item__error"><h3><?php esc_html_e( ' Attention! Cookie has missing description or it is unclassified', 'valu-cookie-plugin' )?></h3></div>';
                            }
                            dom += '</div>';
                            list.innerHTML += dom;
                        });
                    });
                });
			</script>

			<style>

                .valu-cookie-plugin-list-item {
                    max-width: 700px !important;
                }

                .valu-cookie-plugin__last-scan {
                    padding: 15px 0;
                    font-weight: bold;
                }

                .valu-cookie-plugin-list-item[data-valu-cookie-category="5"],
                .valu-cookie-plugin-list-item[data-valu-cookie-description="false"] {
                    border: 3px solid #d63638;
                    margin-left: 30px;
                }

                .valu-cookie-plugin-list-item__provider,
                .valu-cookie-plugin-list-item__first-url,
                .valu-cookie-plugin-list-item__description,
                .valu-cookie-plugin-list-item__category {
                    display: flex;
                    align-items: center;
                }

                .valu-cookie-plugin-list-item__description {
                    display: flex;
                    align-items: flex-start;
                }

                .valu-cookie-plugin-list-item__description p {
                    margin-top: 0;
                }

                .valu-cookie-plugin-list-item label {
                    margin-right: 7px;
                    font-weight: bold;
                }
			</style>

		</div>

		<?php
	}

	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'valu-cookie-plugin', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = 'valu-cookie-plugin';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	}

	/**
	 * Main Valu_Cookie_Plugin Instance
	 *
	 * Ensures only one instance of Valu_Cookie_Plugin is loaded or can be loaded.
	 *
	 * @param string $file File instance.
	 * @param string $version Version parameter.
	 *
	 * @return Object Valu_Cookie_Plugin instance
	 * @see Valu_Cookie_Plugin()
	 * @since 1.0.0
	 * @static
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of Valu_Cookie_Plugin is forbidden' ), 'valu-cookie-plugin' ), esc_attr( $this->_version ) );

	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of Valu_Cookie_Plugin is forbidden' ), 'valu-cookie-plugin' ), esc_attr( $this->_version ) );
	} // End __wakeup ()

	/**
	 * @param $culture
	 *
	 * @return array
	 */
	public function fetchCookiebotInformation( $culture ): array {
		$api_key = get_option( 'valu_cookies_cookiebot-api-key' );

		// API Key can be set at wp-config
		if ( defined( 'VALU_COOKIEPLUGIN_API_KEY' ) ) {
			if ( is_scalar( VALU_COOKIEPLUGIN_API_KEY ) ) {
				$api_key = VALU_COOKIEPLUGIN_API_KEY;
			} elseif ( is_array( VALU_COOKIEPLUGIN_API_KEY ) && isset ( VALU_COOKIEPLUGIN_API_KEY[ get_current_blog_id() ] ) && VALU_COOKIEPLUGIN_API_KEY[ get_current_blog_id() ] ) {
				$api_key = VALU_COOKIEPLUGIN_API_KEY[ get_current_blog_id() ];
			}
		}
		$group_id = get_option( 'valu_cookies_serial' );
		$domain   = get_option( 'valu_cookies_domain' );
		if ( strlen( $group_id ) < 1 || strlen( $api_key ) < 1 || strlen( $domain ) < 1 ) {
			return [ 'data' => __( 'Error while fetching information', 'valu-cookie-plugin' ) ];
		}


		if ( false === ( $responseBody = get_transient( 'valu_cookiebot_' . $domain . '_' . $culture ) ) ) {
			$response     = wp_remote_get( 'https://consent.cookiebot.com/api/v1/' . $api_key . '/json/domaingroup/' . $group_id . '/' . $culture . '/domain/' . $domain . '/cookies' );
			$responseBody = wp_remote_retrieve_body( $response );
			set_transient( 'valu_cookiebot_' . $domain . '_' . $culture, $responseBody, 120 );
		}

		$result = $responseBody;

		return [ 'data' => $result ];
	}

	/**
	 * List all unclassified cookies and cookies lacking purpose description
	 * @return void
	 */
	public static function cliCommand() {
		$instance = self::instance();

		if ( is_null( $instance->settings ) ) {
			$instance->settings = Valu_Cookie_Plugin_Settings::instance( $instance );
		}

		$data        = $instance->fetchCookiebotInformation( get_option( 'valu_cookies_culture' ) );
		$decodedData = json_decode( $data['data'], true );
		foreach ( $decodedData['cookies'] as $cookie ) {
			if ( ! $cookie['PurposeDescription'] ) {
				WP_CLI::warning( sprintf( "Missing Description: %s ( %s )", $cookie['Name'], $cookie['Provider'] ) );
			}
			if ( $cookie['Category'] != 5 ) {
				WP_CLI::warning( sprintf( "Unclassified: %s ( %s )", $cookie['Name'], $cookie['Provider'] ) );
			}

		}
	}
}

