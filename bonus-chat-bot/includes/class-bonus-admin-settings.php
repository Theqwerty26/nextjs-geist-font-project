<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Bonus_Admin_Settings {

    private $options;

    public function __construct() {
        $this->options = get_option( 'bonus_chat_bot_settings' );

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    public function add_plugin_page() {
        add_menu_page(
            __( 'Bonus Chat Bot', 'bonus-chat-bot' ),
            __( 'Bonus Chat Bot', 'bonus-chat-bot' ),
            'manage_options',
            'bonus-chat-bot',
            array( $this, 'create_admin_page' ),
            'dashicons-format-chat',
            25
        );
    }

    public function create_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Bonus Chat Bot Ayarları', 'bonus-chat-bot' ); ?></h1>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'bonus_chat_bot_option_group' );
                do_settings_sections( 'bonus-chat-bot-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'bonus_chat_bot_option_group',
            'bonus_chat_bot_settings',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'setting_section_id',
            __( 'Genel Ayarlar', 'bonus-chat-bot' ),
            null,
            'bonus-chat-bot-admin'
        );

        add_settings_field(
            'show_on_desktop',
            __( 'Masaüstünde Göster', 'bonus-chat-bot' ),
            array( $this, 'show_on_desktop_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'show_on_tablet',
            __( 'Tablet\'te Göster', 'bonus-chat-bot' ),
            array( $this, 'show_on_tablet_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'show_on_mobile',
            __( 'Mobilde Göster', 'bonus-chat-bot' ),
            array( $this, 'show_on_mobile_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'chatbot_mode',
            __( 'Chatbot Modu', 'bonus-chat-bot' ),
            array( $this, 'chatbot_mode_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'background_color',
            __( 'Arka Plan Rengi', 'bonus-chat-bot' ),
            array( $this, 'background_color_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'highlight_color',
            __( 'Vurgu Rengi', 'bonus-chat-bot' ),
            array( $this, 'highlight_color_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'button_order',
            __( 'Footer Buton Sırası', 'bonus-chat-bot' ),
            array( $this, 'button_order_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'telegram_url',
            __( 'Telegram URL', 'bonus-chat-bot' ),
            array( $this, 'telegram_url_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'whatsapp_url',
            __( 'WhatsApp URL', 'bonus-chat-bot' ),
            array( $this, 'whatsapp_url_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'reklam_ver_url',
            __( 'Reklam Ver URL', 'bonus-chat-bot' ),
            array( $this, 'reklam_ver_url_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'ad_code',
            __( 'Reklam Kodu', 'bonus-chat-bot' ),
            array( $this, 'ad_code_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );

        add_settings_field(
            'source_code_protection',
            __( 'Kaynak Kod Koruması', 'bonus-chat-bot' ),
            array( $this, 'source_code_protection_callback' ),
            'bonus-chat-bot-admin',
            'setting_section_id'
        );
    }

    public function sanitize( $input ) {
        $sanitized = array();

        $sanitized['show_on_desktop'] = isset( $input['show_on_desktop'] ) ? boolval( $input['show_on_desktop'] ) : false;
        $sanitized['show_on_tablet'] = isset( $input['show_on_tablet'] ) ? boolval( $input['show_on_tablet'] ) : false;
        $sanitized['show_on_mobile'] = isset( $input['show_on_mobile'] ) ? boolval( $input['show_on_mobile'] ) : false;

        $sanitized['chatbot_mode'] = in_array( $input['chatbot_mode'], array( 'full-width', 'bubble' ), true ) ? $input['chatbot_mode'] : 'bubble';

        $sanitized['background_color'] = sanitize_hex_color( $input['background_color'] ) ?: '#0b1224';
        $sanitized['highlight_color'] = sanitize_hex_color( $input['highlight_color'] ) ?: '#f7931e';

        $sanitized['button_order'] = sanitize_text_field( $input['button_order'] ?? '' );

        $sanitized['telegram_url'] = esc_url_raw( $input['telegram_url'] ?? '' );
        $sanitized['whatsapp_url'] = esc_url_raw( $input['whatsapp_url'] ?? '' );
        $sanitized['reklam_ver_url'] = esc_url_raw( $input['reklam_ver_url'] ?? '' );

        $sanitized['ad_code'] = wp_kses_post( $input['ad_code'] ?? '' );

        $sanitized['source_code_protection'] = isset( $input['source_code_protection'] ) ? boolval( $input['source_code_protection'] ) : false;

        return $sanitized;
    }

    public function show_on_desktop_callback() {
        printf(
            '<input type="checkbox" id="show_on_desktop" name="bonus_chat_bot_settings[show_on_desktop]" value="1" %s />',
            checked( 1, $this->options['show_on_desktop'] ?? 0, false )
        );
    }

    public function show_on_tablet_callback() {
        printf(
            '<input type="checkbox" id="show_on_tablet" name="bonus_chat_bot_settings[show_on_tablet]" value="1" %s />',
            checked( 1, $this->options['show_on_tablet'] ?? 0, false )
        );
    }

    public function show_on_mobile_callback() {
        printf(
            '<input type="checkbox" id="show_on_mobile" name="bonus_chat_bot_settings[show_on_mobile]" value="1" %s />',
            checked( 1, $this->options['show_on_mobile'] ?? 0, false )
        );
    }

    public function chatbot_mode_callback() {
        $mode = $this->options['chatbot_mode'] ?? 'bubble';
        ?>
        <select id="chatbot_mode" name="bonus_chat_bot_settings[chatbot_mode]">
            <option value="full-width" <?php selected( $mode, 'full-width' ); ?>><?php _e( 'Tam Genişlik', 'bonus-chat-bot' ); ?></option>
            <option value="bubble" <?php selected( $mode, 'bubble' ); ?>><?php _e( 'Baloncuk', 'bonus-chat-bot' ); ?></option>
        </select>
        <?php
    }

    public function background_color_callback() {
        $color = $this->options['background_color'] ?? '#0b1224';
        ?>
        <input type="text" id="background_color" name="bonus_chat_bot_settings[background_color]" value="<?php echo esc_attr( $color ); ?>" class="bonus-color-field" />
        <?php
    }

    public function highlight_color_callback() {
        $color = $this->options['highlight_color'] ?? '#f7931e';
        ?>
        <input type="text" id="highlight_color" name="bonus_chat_bot_settings[highlight_color]" value="<?php echo esc_attr( $color ); ?>" class="bonus-color-field" />
        <?php
    }

    public function button_order_callback() {
        $order = $this->options['button_order'] ?? 'telegram,whatsapp,reklam_ver';
        ?>
        <input type="text" id="button_order" name="bonus_chat_bot_settings[button_order]" value="<?php echo esc_attr( $order ); ?>" />
        <p class="description"><?php _e( 'Buton sırasını virgülle ayırarak belirtin. Örnek: telegram,whatsapp,reklam_ver', 'bonus-chat-bot' ); ?></p>
        <?php
    }

    public function telegram_url_callback() {
        $url = $this->options['telegram_url'] ?? '';
        ?>
        <input type="url" id="telegram_url" name="bonus_chat_bot_settings[telegram_url]" value="<?php echo esc_url( $url ); ?>" />
        <?php
    }

    public function whatsapp_url_callback() {
        $url = $this->options['whatsapp_url'] ?? '';
        ?>
        <input type="url" id="whatsapp_url" name="bonus_chat_bot_settings[whatsapp_url]" value="<?php echo esc_url( $url ); ?>" />
        <?php
    }

    public function reklam_ver_url_callback() {
        $url = $this->options['reklam_ver_url'] ?? '';
        ?>
        <input type="url" id="reklam_ver_url" name="bonus_chat_bot_settings[reklam_ver_url]" value="<?php echo esc_url( $url ); ?>" />
        <?php
    }

    public function ad_code_callback() {
        $code = $this->options['ad_code'] ?? '';
        ?>
        <textarea id="ad_code" name="bonus_chat_bot_settings[ad_code]" rows="5" cols="50"><?php echo esc_textarea( $code ); ?></textarea>
        <p class="description"><?php _e( 'Chatbot ve bonus listesi arasına reklam kodu ekleyin (HTML veya AdSense).', 'bonus-chat-bot' ); ?></p>
        <?php
    }

    public function source_code_protection_callback() {
        $checked = $this->options['source_code_protection'] ?? false;
        ?>
        <input type="checkbox" id="source_code_protection" name="bonus_chat_bot_settings[source_code_protection]" value="1" <?php checked( $checked, true ); ?> />
        <label for="source_code_protection"><?php _e( 'Ctrl + U tuşlarına basıldığında mesaj göster', 'bonus-chat-bot' ); ?></label>
        <?php
    }
}
?>
