<?php
/**
 * Plugin Name: SWELL Styled Google Map
 * Description: Googleマップをグレースケールやカスタムカラーでオシャレに埋め出すプラグインです。[swell_styled_map src="..." color="#0091ff"]
 * Version: 1.2.2
 * Author: Antigravity
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class SWELL_Styled_Map {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_shortcode( 'swell_styled_map', array( $this, 'render_shortcode' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'settings_init' ) );
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_action_links' ) );
        add_action( 'init', array( $this, 'register_block' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'swell-styled-map-css', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), '1.2.2' );
        wp_enqueue_script( 'swell-styled-map-js', plugin_dir_url( __FILE__ ) . 'assets/js/frontend.js', array(), '1.2.2', true );
    }

    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'swell-styled-map-block-js',
            plugin_dir_url( __FILE__ ) . 'assets/js/block.js',
            array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components' ),
            '1.1.0',
            true
        );
        wp_enqueue_style( 'swell-styled-map-block-css', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', array(), '1.1.0' );
    }

    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) return;
        register_block_type( 'ssm/styled-map', array(
            'render_callback' => array( $this, 'render_block' ),
        ) );
    }

    public function render_block( $attributes ) {
        // ブロックの属性をショートコードの属性形式に変換
        $atts = array(
            'src'        => isset($attributes['src']) ? $attributes['src'] : '',
            'color'      => isset($attributes['color']) ? $attributes['color'] : '',
            'height'     => isset($attributes['height']) ? $attributes['height'] : '',
            'full_width' => (isset($attributes['fullWidth']) && $attributes['fullWidth']) ? 'yes' : 'no',
            'invert'     => (isset($attributes['invert']) && $attributes['invert']) ? 'yes' : 'no',
            'effect'     => (isset($attributes['effect']) && $attributes['effect']) ? 'yes' : 'no',
        );
        return $this->render_shortcode( $atts );
    }

    public function add_action_links( $links ) {
        $settings_link = '<a href="options-general.php?page=swell_styled_map">' . __( '設定' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function add_admin_menu() {
        add_options_page(
            'SWELL Styled Map 設定',
            'SWELL Styled Map',
            'manage_options',
            'swell_styled_map',
            array( $this, 'settings_page' )
        );
    }

    public function settings_init() {
        register_setting( 'swellStyledMap', 'ssm_settings' );

        add_settings_section(
            'ssm_section',
            'デフォルト設定',
            null,
            'swellStyledMap'
        );

        add_settings_field(
            'default_color',
            'デフォルトのオーバーレイ色',
            array( $this, 'color_render' ),
            'swellStyledMap',
            'ssm_section'
        );

        add_settings_field(
            'default_height',
            'デフォルトの高さ (px)',
            array( $this, 'height_render' ),
            'swellStyledMap',
            'ssm_section'
        );

        add_settings_field(
            'default_full_width',
            'デフォルトで全幅にする',
            array( $this, 'full_width_render' ),
            'swellStyledMap',
            'ssm_section'
        );

        add_settings_field(
            'default_invert',
            'デフォルトで黒い地図（反転）にする',
            array( $this, 'invert_render' ),
            'swellStyledMap',
            'ssm_section'
        );

        add_settings_field(
            'default_effect',
            'デフォルトでフェードエフェクトを使う',
            array( $this, 'effect_render' ),
            'swellStyledMap',
            'ssm_section'
        );
    }

    public function color_render() {
        $options = get_option( 'ssm_settings' );
        $value = isset( $options['default_color'] ) ? $options['default_color'] : '#0091ff';
        ?>
        <input type="color" name="ssm_settings[default_color]" value="<?php echo esc_attr( $value ); ?>">
        <?php
    }

    public function height_render() {
        $options = get_option( 'ssm_settings' );
        $value = isset( $options['default_height'] ) ? $options['default_height'] : '450';
        ?>
        <input type="number" name="ssm_settings[default_height]" value="<?php echo esc_attr( $value ); ?>"> px
        <?php
    }

    public function full_width_render() {
        $options = get_option( 'ssm_settings' );
        $value = isset( $options['default_full_width'] ) ? $options['default_full_width'] : 'yes';
        ?>
        <select name="ssm_settings[default_full_width]">
            <option value="yes" <?php selected( $value, 'yes' ); ?>>はい</option>
            <option value="no" <?php selected( $value, 'no' ); ?>>いいえ</option>
        </select>
        <?php
    }

    public function invert_render() {
        $options = get_option( 'ssm_settings' );
        $value = isset( $options['default_invert'] ) ? $options['default_invert'] : 'no';
        ?>
        <select name="ssm_settings[default_invert]">
            <option value="yes" <?php selected( $value, 'yes' ); ?>>はい</option>
            <option value="no" <?php selected( $value, 'no' ); ?>>いいえ</option>
        </select>
        <?php
    }

    public function effect_render() {
        $options = get_option( 'ssm_settings' );
        $value = isset( $options['default_effect'] ) ? $options['default_effect'] : 'yes';
        ?>
        <select name="ssm_settings[default_effect]">
            <option value="yes" <?php selected( $value, 'yes' ); ?>>はい</option>
            <option value="no" <?php selected( $value, 'no' ); ?>>いいえ</option>
        </select>
        <?php
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>SWELL Styled Map 設定</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'swellStyledMap' );
                do_settings_sections( 'swellStyledMap' );
                submit_button();
                ?>
            </form>
            <hr>
            <h2>使い方のヒント</h2>
            <p>1. 投稿画面で「SWELL Styled Map」ブロックを探して追加してください。（推奨）</p>
            <p>2. または、以下のショートコードを記述してください。</p>
            <code>[swell_styled_map src="Googleマップの埋め込みURL"]</code>
            <p>※URLは、Googleマップの「地図を埋め込む」で表示される <code>iframe</code> の <code>src</code> 属性を貼り付けてください。</p>
        </div>
        <?php
    }

    public function render_shortcode( $atts ) {
        $options = get_option( 'ssm_settings' );
        $defaults = array(
            'src'        => '',
            'color'      => isset( $options['default_color'] ) ? $options['default_color'] : '#0091ff',
            'height'     => isset( $options['default_height'] ) ? $options['default_height'] : '450',
            'full_width' => isset( $options['default_full_width'] ) ? $options['default_full_width'] : 'yes',
            'invert'     => isset( $options['default_invert'] ) ? $options['default_invert'] : 'no',
            'effect'     => isset( $options['default_effect'] ) ? $options['default_effect'] : 'yes',
        );
        $a = shortcode_atts( $defaults, $atts );

        if ( empty( $a['src'] ) ) {
            return '<!-- SWELL Styled Map: src is missing -->';
        }

        // iframeタグが丸ごと貼り付けられた場合にURLを抽出する
        if ( strpos( $a['src'], '<iframe' ) !== false ) {
            preg_match( '/src=["\'](.+?)["\']/', $a['src'], $matches );
            if ( ! empty( $matches[1] ) ) {
                $a['src'] = $matches[1];
            }
        }

        $container_class = 'swell-styled-map-container';
        if ( $a['invert'] === 'yes' ) {
            $container_class .= ' -invert';
        }
        if ( $a['full_width'] === 'yes' ) {
            $container_class .= ' -full-width';
        }
        if ( $a['effect'] === 'yes' ) {
            $container_class .= ' -fade-in';
        }

        $overlay_style = ' style="';
        if ( $a['color'] ) {
            $overlay_style .= '--map-overlay-color: ' . esc_attr( $a['color'] ) . '; ';
        }
        
        // 高さの処理（数値のみの場合はpxを付与）
        $height_value = $a['height'];
        if ( is_numeric( $height_value ) ) {
            $height_value .= 'px';
        }
        $overlay_style .= '--map-height: ' . esc_attr( $height_value ) . ';';
        $overlay_style .= '"';

        ob_start();
        ?>
        <div class="<?php echo esc_attr( $container_class ); ?>"<?php echo $overlay_style; ?>>
            <div class="swell-styled-map-inner">
                <iframe 
                    src="<?php echo esc_url( $a['src'] ); ?>" 
                    width="100%" 
                    height="<?php echo esc_attr( $a['height'] ); ?>" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}

new SWELL_Styled_Map();
