<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Bonus_Shortcode {

    public function __construct() {
        add_shortcode( 'bonus_list', array( $this, 'render_bonus_list' ) );
    }

    public function render_bonus_list( $atts ) {
        $atts = shortcode_atts( array(
            'template' => 'vertical', // vertical, slider, accordion
        ), $atts, 'bonus_list' );

        $template = sanitize_text_field( $atts['template'] );

        // Query bonuses with date visibility and order
        $today = date( 'Y-m-d' );
        $args = array(
            'post_type'      => 'bonus',
            'posts_per_page' => -1,
            'meta_query'     => array(
                'relation' => 'AND',
                array(
                    'key'     => '_baslangic_tarihi',
                    'value'   => $today,
                    'compare' => '<=',
                    'type'    => 'DATE',
                ),
                array(
                    'key'     => '_bitis_tarihi',
                    'value'   => $today,
                    'compare' => '>=',
                    'type'    => 'DATE',
                ),
            ),
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_sira_numarasi',
            'order'          => 'ASC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'bonus_kategori',
                    'field'    => 'slug',
                    'terms'    => array( 'tumu', 'trend', 'onerilen' ),
                    'operator' => 'IN',
                ),
            ),
        );

        $query = new WP_Query( $args );

        if ( ! $query->have_posts() ) {
            return '<p>' . esc_html__( 'Bonus bulunamadı.', 'bonus-chat-bot' ) . '</p>';
        }

        ob_start();

        // Load admin settings for colors and fonts
        $settings = get_option( 'bonus_chat_bot_settings' );
        $background_color = isset( $settings['background_color'] ) ? esc_attr( $settings['background_color'] ) : '#0b1224';
        $highlight_color = isset( $settings['highlight_color'] ) ? esc_attr( $settings['highlight_color'] ) : '#f7931e';

        // Template rendering
        switch ( $template ) {
            case 'slider':
                $this->render_slider( $query->posts, $background_color, $highlight_color );
                break;
            case 'accordion':
                $this->render_accordion( $query->posts, $background_color, $highlight_color );
                break;
            case 'vertical':
            default:
                $this->render_vertical_list( $query->posts, $background_color, $highlight_color );
                break;
        }

        wp_reset_postdata();

        return ob_get_clean();
    }

    private function render_vertical_list( $posts, $bg_color, $highlight_color ) {
        ?>
        <div class="bonus-list vertical-list" style="background-color: <?php echo $bg_color; ?>; color: #fff; padding: 1rem;">
            <?php foreach ( $posts as $post ) : 
                $bonus_metni = get_post_meta( $post->ID, '_bonus_metni', true );
                $bonus_linki = get_post_meta( $post->ID, '_bonus_linki', true );
                $logo_id = get_post_thumbnail_id( $post->ID );
                $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
                ?>
                <div class="bonus-item" style="border-bottom: 1px solid <?php echo $highlight_color; ?>; padding: 0.5rem 0; display: flex; align-items: center;">
                    <?php if ( $logo_url ) : ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $post->post_title ); ?>" style="width: 50px; height: 50px; object-fit: contain; margin-right: 1rem;" />
                    <?php endif; ?>
                    <div class="bonus-content">
                        <a href="<?php echo esc_url( $bonus_linki ); ?>" target="_blank" rel="nofollow" style="color: <?php echo $highlight_color; ?>; font-weight: bold; text-decoration: none;">
                            <?php echo esc_html( $post->post_title ); ?>
                        </a>
                        <p style="margin: 0;"><?php echo esc_html( $bonus_metni ); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    private function render_slider( $posts, $bg_color, $highlight_color ) {
        ?>
        <div class="bonus-list slider" style="background-color: <?php echo $bg_color; ?>; color: #fff; padding: 1rem; overflow-x: auto; white-space: nowrap;">
            <?php foreach ( $posts as $post ) : 
                $bonus_metni = get_post_meta( $post->ID, '_bonus_metni', true );
                $bonus_linki = get_post_meta( $post->ID, '_bonus_linki', true );
                $logo_id = get_post_thumbnail_id( $post->ID );
                $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
                ?>
                <div class="bonus-item" style="display: inline-block; width: 200px; margin-right: 1rem; vertical-align: top; border: 1px solid <?php echo $highlight_color; ?>; border-radius: 8px; padding: 1rem;">
                    <?php if ( $logo_url ) : ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $post->post_title ); ?>" style="width: 100%; height: 100px; object-fit: contain; margin-bottom: 0.5rem;" />
                    <?php endif; ?>
                    <a href="<?php echo esc_url( $bonus_linki ); ?>" target="_blank" rel="nofollow" style="color: <?php echo $highlight_color; ?>; font-weight: bold; text-decoration: none;">
                        <?php echo esc_html( $post->post_title ); ?>
                    </a>
                    <p style="margin: 0;"><?php echo esc_html( $bonus_metni ); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    private function render_accordion( $posts, $bg_color, $highlight_color ) {
        ?>
        <div class="bonus-list accordion" style="background-color: <?php echo $bg_color; ?>; color: #fff; padding: 1rem;">
            <?php foreach ( $posts as $post ) : 
                $bonus_metni = get_post_meta( $post->ID, '_bonus_metni', true );
                $bonus_linki = get_post_meta( $post->ID, '_bonus_linki', true );
                $logo_id = get_post_thumbnail_id( $post->ID );
                $logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';
                $item_id = 'bonus-accordion-' . $post->ID;
                ?>
                <div class="bonus-item" style="border-bottom: 1px solid <?php echo $highlight_color; ?>; margin-bottom: 0.5rem;">
                    <button type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $item_id ); ?>" style="width: 100%; background: none; border: none; color: <?php echo $highlight_color; ?>; font-weight: bold; text-align: left; padding: 0.5rem 0; cursor: pointer;">
                        <?php if ( $logo_url ) : ?>
                            <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $post->post_title ); ?>" style="width: 40px; height: 40px; object-fit: contain; vertical-align: middle; margin-right: 0.5rem;" />
                        <?php endif; ?>
                        <?php echo esc_html( $post->post_title ); ?>
                    </button>
                    <div id="<?php echo esc_attr( $item_id ); ?>" hidden style="padding: 0.5rem 0 1rem 0;">
                        <a href="<?php echo esc_url( $bonus_linki ); ?>" target="_blank" rel="nofollow" style="color: <?php echo $highlight_color; ?>; font-weight: normal; text-decoration: none;">
                            <?php echo esc_html( $bonus_metni ); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.bonus-list.accordion button');
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    const expanded = button.getAttribute('aria-expanded') === 'true';
                    button.setAttribute('aria-expanded', !expanded);
                    const content = document.getElementById(button.getAttribute('aria-controls'));
                    if (content) {
                        if (expanded) {
                            content.hidden = true;
                        } else {
                            content.hidden = false;
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }
}
?>
