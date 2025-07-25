<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Bonus_Post_Type {

    public function __construct() {
        add_action( 'init', array( $this, 'register_bonus_post_type' ) );
        add_action( 'init', array( $this, 'register_bonus_taxonomy' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_bonus_meta_boxes' ) );
        add_action( 'save_post_bonus', array( $this, 'save_bonus_meta' ), 10, 2 );
    }

    public function register_bonus_post_type() {
        $labels = array(
            'name'                  => __( 'Bonuslar', 'bonus-chat-bot' ),
            'singular_name'         => __( 'Bonus', 'bonus-chat-bot' ),
            'menu_name'             => __( 'Bonuslar', 'bonus-chat-bot' ),
            'name_admin_bar'        => __( 'Bonus', 'bonus-chat-bot' ),
            'add_new'               => __( 'Yeni Ekle', 'bonus-chat-bot' ),
            'add_new_item'          => __( 'Yeni Bonus Ekle', 'bonus-chat-bot' ),
            'new_item'              => __( 'Yeni Bonus', 'bonus-chat-bot' ),
            'edit_item'             => __( 'Bonusu Düzenle', 'bonus-chat-bot' ),
            'view_item'             => __( 'Bonusu Görüntüle', 'bonus-chat-bot' ),
            'all_items'             => __( 'Tüm Bonuslar', 'bonus-chat-bot' ),
            'search_items'          => __( 'Bonus Ara', 'bonus-chat-bot' ),
            'parent_item_colon'     => __( 'Üst Bonus:', 'bonus-chat-bot' ),
            'not_found'             => __( 'Bonus bulunamadı.', 'bonus-chat-bot' ),
            'not_found_in_trash'    => __( 'Çöpte bonus bulunamadı.', 'bonus-chat-bot' ),
            'featured_image'        => __( 'Logo', 'bonus-chat-bot' ),
            'set_featured_image'    => __( 'Logo Ayarla', 'bonus-chat-bot' ),
            'remove_featured_image' => __( 'Logoyu Kaldır', 'bonus-chat-bot' ),
            'use_featured_image'    => __( 'Logoyu Kullan', 'bonus-chat-bot' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-awards',
            'supports'           => array( 'title', 'thumbnail' ),
            'has_archive'        => true,
            'rewrite'            => array( 'slug' => 'bonus' ),
            'show_in_rest'       => true,
        );

        register_post_type( 'bonus', $args );
    }

    public function register_bonus_taxonomy() {
        $labels = array(
            'name'              => __( 'Bonus Kategorileri', 'bonus-chat-bot' ),
            'singular_name'     => __( 'Bonus Kategorisi', 'bonus-chat-bot' ),
            'search_items'      => __( 'Kategori Ara', 'bonus-chat-bot' ),
            'all_items'         => __( 'Tüm Kategoriler', 'bonus-chat-bot' ),
            'parent_item'       => __( 'Üst Kategori', 'bonus-chat-bot' ),
            'parent_item_colon' => __( 'Üst Kategori:', 'bonus-chat-bot' ),
            'edit_item'         => __( 'Kategoriyi Düzenle', 'bonus-chat-bot' ),
            'update_item'       => __( 'Kategoriyi Güncelle', 'bonus-chat-bot' ),
            'add_new_item'      => __( 'Yeni Kategori Ekle', 'bonus-chat-bot' ),
            'new_item_name'     => __( 'Yeni Kategori Adı', 'bonus-chat-bot' ),
            'menu_name'         => __( 'Bonus Kategorileri', 'bonus-chat-bot' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'bonus-kategori' ),
            'show_in_rest'      => true,
        );

        register_taxonomy( 'bonus_kategori', array( 'bonus' ), $args );

        // Add default terms if not exist
        $default_terms = array( 'Trend', 'Önerilen', 'Tümü' );
        foreach ( $default_terms as $term ) {
            if ( ! term_exists( $term, 'bonus_kategori' ) ) {
                wp_insert_term( $term, 'bonus_kategori' );
            }
        }
    }

    public function add_bonus_meta_boxes() {
        add_meta_box(
            'bonus_details',
            __( 'Bonus Detayları', 'bonus-chat-bot' ),
            array( $this, 'render_bonus_meta_box' ),
            'bonus',
            'normal',
            'default'
        );
    }

    public function render_bonus_meta_box( $post ) {
        wp_nonce_field( 'save_bonus_meta', 'bonus_meta_nonce' );

        $bonus_metni = get_post_meta( $post->ID, '_bonus_metni', true );
        $bonus_linki = get_post_meta( $post->ID, '_bonus_linki', true );
        $sira_numarasi = get_post_meta( $post->ID, '_sira_numarasi', true );
        $baslangic_tarihi = get_post_meta( $post->ID, '_baslangic_tarihi', true );
        $bitis_tarihi = get_post_meta( $post->ID, '_bitis_tarihi', true );
        $aylik_odeme = get_post_meta( $post->ID, '_aylik_odeme', true );

        ?>
        <p>
            <label for="bonus_metni"><?php _e( 'Bonus Metni:', 'bonus-chat-bot' ); ?></label><br />
            <input type="text" id="bonus_metni" name="bonus_metni" value="<?php echo esc_attr( $bonus_metni ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="bonus_linki"><?php _e( 'Bonus Linki:', 'bonus-chat-bot' ); ?></label><br />
            <input type="url" id="bonus_linki" name="bonus_linki" value="<?php echo esc_url( $bonus_linki ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="sira_numarasi"><?php _e( 'Sıra Numarası:', 'bonus-chat-bot' ); ?></label><br />
            <input type="number" id="sira_numarasi" name="sira_numarasi" value="<?php echo esc_attr( $sira_numarasi ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="baslangic_tarihi"><?php _e( 'Başlangıç Tarihi:', 'bonus-chat-bot' ); ?></label><br />
            <input type="date" id="baslangic_tarihi" name="baslangic_tarihi" value="<?php echo esc_attr( $baslangic_tarihi ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="bitis_tarihi"><?php _e( 'Bitiş Tarihi:', 'bonus-chat-bot' ); ?></label><br />
            <input type="date" id="bitis_tarihi" name="bitis_tarihi" value="<?php echo esc_attr( $bitis_tarihi ); ?>" style="width:100%;" />
        </p>
        <p>
            <label for="aylik_odeme"><?php _e( 'Aylık Ödeme Tutarı:', 'bonus-chat-bot' ); ?></label><br />
            <input type="number" id="aylik_odeme" name="aylik_odeme" value="<?php echo esc_attr( $aylik_odeme ); ?>" style="width:100%;" />
            <small><?php _e( 'Bu alan frontend\'de gösterilmeyecektir.', 'bonus-chat-bot' ); ?></small>
        </p>
        <?php
    }

    public function save_bonus_meta( $post_id, $post ) {
        if ( ! isset( $_POST['bonus_meta_nonce'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( $_POST['bonus_meta_nonce'], 'save_bonus_meta' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( $post->post_type != 'bonus' ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['bonus_metni'] ) ) {
            update_post_meta( $post_id, '_bonus_metni', sanitize_text_field( $_POST['bonus_metni'] ) );
        }
        if ( isset( $_POST['bonus_linki'] ) ) {
            update_post_meta( $post_id, '_bonus_linki', esc_url_raw( $_POST['bonus_linki'] ) );
        }
        if ( isset( $_POST['sira_numarasi'] ) ) {
            update_post_meta( $post_id, '_sira_numarasi', intval( $_POST['sira_numarasi'] ) );
        }
        if ( isset( $_POST['baslangic_tarihi'] ) ) {
            update_post_meta( $post_id, '_baslangic_tarihi', sanitize_text_field( $_POST['baslangic_tarihi'] ) );
        }
        if ( isset( $_POST['bitis_tarihi'] ) ) {
            update_post_meta( $post_id, '_bitis_tarihi', sanitize_text_field( $_POST['bitis_tarihi'] ) );
        }
        if ( isset( $_POST['aylik_odeme'] ) ) {
            update_post_meta( $post_id, '_aylik_odeme', floatval( $_POST['aylik_odeme'] ) );
        }
    }
}
?>
