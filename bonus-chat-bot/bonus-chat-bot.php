<?php
/**
 * Plugin Name: Bonus Chat Bot
 * Plugin URI: https://example.com/bonus-chat-bot
 * Description: Bonus Chat Bot WordPress plugin with interactive chatbot and bonus listings in Turkish.
 * Version: 1.0.0
 * Author: Time SEO Agencija
 * Author URI: https://timeseo.agencija
 * Text Domain: bonus-chat-bot
 * Domain Path: /languages
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'BONUS_CHAT_BOT_VERSION', '1.0.0' );
define( 'BONUS_CHAT_BOT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BONUS_CHAT_BOT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files
require_once BONUS_CHAT_BOT_PLUGIN_DIR . 'includes/class-bonus-chat-bot.php';
require_once BONUS_CHAT_BOT_PLUGIN_DIR . 'includes/class-bonus-post-type.php';
require_once BONUS_CHAT_BOT_PLUGIN_DIR . 'includes/class-bonus-admin-settings.php';
require_once BONUS_CHAT_BOT_PLUGIN_DIR . 'includes/class-bonus-shortcode.php';

// Initialize the plugin
function bonus_chat_bot_init() {
    $plugin = new Bonus_Chat_Bot();
    $plugin->init();
}
add_action( 'plugins_loaded', 'bonus_chat_bot_init' );
