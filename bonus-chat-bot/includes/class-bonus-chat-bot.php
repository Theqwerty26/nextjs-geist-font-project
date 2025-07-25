<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Bonus_Chat_Bot {

    public function init() {
        // Load plugin text domain for translations
        add_action( 'init', array( $this, 'load_textdomain' ) );

        // Initialize custom post type
        $this->bonus_post_type = new Bonus_Post_Type();

        // Initialize admin settings
        $this->admin_settings = new Bonus_Admin_Settings();

        // Initialize shortcode
        $this->shortcode = new Bonus_Shortcode();

        // Enqueue frontend assets
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // Enqueue admin assets
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

        // Add source code protection script
        add_action( 'wp_footer', array( $this, 'source_code_protection_script' ) );
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'bonus-chat-bot', false, dirname( plugin_basename( __FILE__ ) ) . '/../languages' );
    }

    public function enqueue_assets() {
        // Enqueue CSS and JS for chatbot frontend
        wp_enqueue_style( 'bonus-chatbot-style', BONUS_CHAT_BOT_PLUGIN_URL . 'assets/css/chatbot.css', array(), BONUS_CHAT_BOT_VERSION );
        wp_enqueue_script( 'bonus-chatbot-script', BONUS_CHAT_BOT_PLUGIN_URL . 'assets/js/chatbot.js', array( 'jquery' ), BONUS_CHAT_BOT_VERSION, true );

        // Localize script with AJAX URL and nonce
        wp_localize_script( 'bonus-chatbot-script', 'BonusChatBot', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'bonus_chat_bot_nonce' ),
        ) );
    }

    public function enqueue_admin_assets( $hook ) {
        // Load admin CSS and JS only on plugin admin pages
        if ( strpos( $hook, 'bonus-chat-bot' ) !== false ) {
            wp_enqueue_style( 'bonus-chatbot-admin-style', BONUS_CHAT_BOT_PLUGIN_URL . 'assets/css/admin.css', array(), BONUS_CHAT_BOT_VERSION );
            wp_enqueue_script( 'bonus-chatbot-admin-script', BONUS_CHAT_BOT_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), BONUS_CHAT_BOT_VERSION, true );
        }
    }

    public function source_code_protection_script() {
        ?>
        <script type="text/javascript">
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key.toLowerCase() === 'u') {
                alert('site admininin selamı var');
                e.preventDefault();
            }
        });
        </script>
        <?php
    }
}
?>
