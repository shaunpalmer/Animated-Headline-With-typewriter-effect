<?php

/**
 * Plugin Name: Animated Headline With Typewriter Effect
 * Plugin URI: https://projectstudios.nz
 * Description: Simple forward-back-forward typewriter effect for a short animated headline. Use the shortcode [animated_headline] to display the animated headline on any page or post.
 * Version: 1.1.0
 * Author: Shaun Palmer - ProjectStudios.NZ
 * Author URI: https://projectstudios.nz
 * License: GPL2
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

/******************************
 * 1) ENQUEUE COLOR PICKER IN ADMIN
 ******************************/
function animated_headline_admin_scripts($hook_suffix)
{
  // Only load on the General Settings page
  if ($hook_suffix === 'options-general.php') {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
  }
}
add_action('admin_enqueue_scripts', 'animated_headline_admin_scripts');

function animated_headline_enqueue_scripts()
{
  // Enqueue Google Fonts for Montserrat
  wp_enqueue_style(
    'animated-headline-font',
    'https://fonts.googleapis.com/css2?family=Montserrat+Subrayada:wght@400;700&display=swap',
    [],
    null
  );
}
add_action('wp_enqueue_scripts', 'animated_headline_enqueue_scripts');

function animated_headline_shortcode()
{
  $message = get_option('animated_headline_message', 'Default headline message!');
  $headline_color = get_option('animated_headline_font_color', '#000000');
  $headline_size  = get_option('animated_headline_font_size', 38);
  $animation_speed = get_option('animated_headline_animation_speed', 100);
  $background_color = get_option('animated_headline_background_color', '#ffffff');
  $google_font = get_option('animated_headline_google_font', 'Montserrat');

  ob_start();
?>
  <style>
    @keyframes cursorBlink {

      0%,
      100% {
        border-right-color: transparent;
      }

      50% {
        border-right-color: black;
      }
    }

    .animated-text {
      display: inline-block;
      white-space: pre-wrap;
      overflow: hidden;
      border-right: 2px solid black;
      /* Cursor effect */
      animation: cursorBlink 1s step-start infinite;
      font-family: '<?php echo esc_attr($google_font); ?>', sans-serif;
      background-color: <?php echo esc_attr($background_color); ?>;
    }

    .animated-text.final {
      border-right: none;
      /* Remove cursor after final animation */
      animation: none;
      /* Stop blinking */
      color: #000;
    }
  </style>

  <div id="headline" class="animated-text"
    style="color: <?php echo esc_attr($headline_color); ?>; font-size: <?php echo esc_attr($headline_size); ?>px;">
    <?php echo esc_html($message); ?>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const text = <?php echo json_encode($message); ?>;
      const characters = text.split("");
      let currentText = "";
      const headline = document.getElementById("headline");
      headline.textContent = "";
      let i = 0;

      function forward() {
        if (i < characters.length) {
          currentText += characters[i++];
          headline.textContent = currentText;
          setTimeout(forward, <?php echo esc_attr($animation_speed); ?>);
        } else {
          setTimeout(backward, 1000);
        }
      }

      function backward() {
        if (currentText.length > 0) {
          currentText = currentText.slice(0, -1);
          headline.textContent = currentText;
          setTimeout(backward, 50);
        } else {
          setTimeout(final, 1000);
        }
      }

      function final() {
        currentText = "";
        i = 0;

        function finalForward() {
          if (i < characters.length) {
            currentText += characters[i++];
            headline.textContent = currentText;
            setTimeout(finalForward, <?php echo esc_attr($animation_speed); ?>);
          } else {
            headline.classList.add("final");
          }
        }
        finalForward();
      }
      forward();
    });
  </script>
<?php
  return ob_get_clean();
}
add_shortcode('animated_headline', 'animated_headline_shortcode');

function animated_headline_register_settings()
{
  // Register the setting (the option name)
  register_setting('general', 'animated_headline_message', [
    'type' => 'string',
    'sanitize_callback' => 'sanitize_text_field',
    'default' => 'Default headline message!'
  ]);
  register_setting('general', 'animated_headline_font_color', [
    'type'              => 'string',
    'sanitize_callback' => 'sanitize_hex_color',
    'default'           => '#000000'
  ]);
  register_setting('general', 'animated_headline_font_size', [
    'type'              => 'integer',
    'sanitize_callback' => 'absint',
    'default'           => 38
  ]);
  register_setting('general', 'animated_headline_animation_speed', [
    'type'              => 'integer',
    'sanitize_callback' => 'absint',
    'default'           => 100
  ]);
  register_setting('general', 'animated_headline_background_color', [
    'type'              => 'string',
    'sanitize_callback' => 'sanitize_hex_color',
    'default'           => '#ffffff'
  ]);
  register_setting('general', 'animated_headline_google_font', [
    'type'              => 'string',
    'sanitize_callback' => 'sanitize_text_field',
    'default'           => 'Montserrat'
  ]);

  // Add a new section to General Settings
  add_settings_section(
    'animated_headline_settings_section',
    __('Animated Headline Settings', 'animated-headline'),
    'animated_headline_settings_section_description',
    'general'
  );

  // Add a field for the headline message (uses the built-in form)
  add_settings_field(
    'animated_headline_message_field',
    __('Headline Message', 'animated-headline'),
    'animated_headline_message_field_callback',
    'general',
    'animated_headline_settings_section'
  );
  add_settings_field(
    'animated_headline_font_color_field',
    __('Headline Font Color', 'animated-headline'),
    'animated_headline_font_color_callback',
    'general',
    'animated_headline_settings_section'
  );
  add_settings_field(
    'animated_headline_font_size_field',
    __('Headline Font Size (px)', 'animated-headline'),
    'animated_headline_font_size_callback',
    'general',
    'animated_headline_settings_section'
  );
  add_settings_field(
    'animated_headline_animation_speed_field',
    __('Animation Speed (ms)', 'animated-headline'),
    'animated_headline_animation_speed_callback',
    'general',
    'animated_headline_settings_section'
  );
  add_settings_field(
    'animated_headline_background_color_field',
    __('Background Color', 'animated-headline'),
    'animated_headline_background_color_callback',
    'general',
    'animated_headline_settings_section'
  );
  add_settings_field(
    'animated_headline_google_font_field',
    __('Google Font', 'animated-headline'),
    'animated_headline_google_font_callback',
    'general',
    'animated_headline_settings_section'
  );
}
add_action('admin_init', 'animated_headline_register_settings');

function animated_headline_settings_section_description()
{
  echo '<p><strong>Customize your animated headline here. The changes will be saved when you click the "Save Changes" button on the General Settings page.</strong></p>';
  echo '<p><strong>Single Use Front Page Big Bold Headline with typewriter effect</strong></p>';
}

function animated_headline_message_field_callback()
{
  $saved_message = get_option('animated_headline_message', 'Default headline message!');
?>
  <input type="text" id="animated_headline_message" name="animated_headline_message"
    value="<?php echo esc_attr($saved_message); ?>" style="width:100%;">
  <br><br>
<?php
}

function animated_headline_font_color_callback()
{
  $color = get_option('animated_headline_font_color', '#000000');
?>
  <input type="text" id="animated_headline_font_color" name="animated_headline_font_color"
    value="<?php echo esc_attr($color); ?>" class="my-color-field" data-default-color="#000000" />
  <script>
    (function($) {
      $(function() {
        $('.my-color-field').wpColorPicker();
      });
    })(jQuery);
  </script>
<?php
}

function animated_headline_font_size_callback()
{
  $size = get_option('animated_headline_font_size', 38);
?>
  <input type="number" min="10" max="100" step="1" id="animated_headline_font_size" name="animated_headline_font_size"
    value="<?php echo esc_attr($size); ?>" /> px
<?php
}

function animated_headline_animation_speed_callback()
{
  $speed = get_option('animated_headline_animation_speed', 100);
?>
  <input type="number" min="50" max="500" step="10" id="animated_headline_animation_speed" name="animated_headline_animation_speed"
    value="<?php echo esc_attr($speed); ?>" /> ms
<?php
}

function animated_headline_background_color_callback()
{
  $color = get_option('animated_headline_background_color', '#ffffff');
?>
  <input type="text" id="animated_headline_background_color" name="animated_headline_background_color"
    value="<?php echo esc_attr($color); ?>" class="my-color-field" data-default-color="#ffffff" />
  <script>
    (function($) {
      $(function() {
        $('.my-color-field').wpColorPicker();
      });
    })(jQuery);
  </script>
<?php
}

function animated_headline_google_font_callback()
{
  $font = get_option('animated_headline_google_font', 'Montserrat');
  $fonts = ['Montserrat', 'Roboto', 'Open Sans', 'Lato', 'Oswald'];
?>
  <select id="animated_headline_google_font" name="animated_headline_google_font">
    <?php foreach ($fonts as $f) : ?>
      <option value="<?php echo esc_attr($f); ?>" <?php selected($font, $f); ?>><?php echo esc_html($f); ?></option>
    <?php endforeach; ?>
  </select>
<?php
}
