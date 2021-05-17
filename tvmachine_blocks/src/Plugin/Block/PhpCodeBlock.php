<?php

namespace Drupal\tvmachine_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Php Code Block' block.
 *
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "php_code_block",
 *   admin_label = @Translation("PHP code block")
 * )
 */
class PhpCodeBlock extends BlockBase {

  /*public function getCacheMaxAge() {
    return 0;
  }*/ 

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'tvmachine_blocks_string' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['tvmachine_blocks_string_text'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Block contents'),
      '#description' => $this->t('Enter php html/code'),
      '#default_value' => $this->configuration['tvmachine_blocks_string'],
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['tvmachine_blocks_string']
      = $form_state->getValue('tvmachine_blocks_string_text');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build =  array(
      '#type' => 'inline_template',
      '#template' => $this->php_eval($this->configuration['tvmachine_blocks_string']),
    );
    $build['#cache']['contexts'][] = 'url.query_args:tmonth';
    $build['#cache']['contexts'][] = 'url.query_args:tday';
    $build['#cache']['contexts'][] = 'url.query_args:thour';

    return $build;
  }

  /**
   * Evaluates a string of PHP code.
   *
   * This is a wrapper around PHP's eval(). It uses output buffering to capture
   * both returned and printed text. Unlike eval(), we require code to be
   * surrounded by <?php ?> tags; in other words, we evaluate the code as if it
   * were a stand-alone PHP file.
   *
   * Using this wrapper also ensures that the PHP code which is evaluated can not
   * overwrite any variables in the calling code, unlike a regular eval() call.
   *
   * This function is also used as an implementation of
   * hook_filter_FILTER_process().
   *
   * @param string $code
   *   The code to evaluate.
   *
   * @return string
   *   A string containing the printed output of the code, followed by the
   *   returned output of the code.
   *
   * @ingroup php_wrappers
   *
   * @see php_filter_info()
   */
  public function php_eval($code) {
    /* FIXME global $theme_path, $theme_info;

    // Store current theme path.
    $old_theme_path = $theme_path;

    // Restore theme_path to the theme, as long as php_eval() executes,
    // so code evaluated will not see the caller module as the current theme.
    // If theme info is not initialized get the path from default theme.
    if (!isset($theme_info)) {
      $theme_path = drupal_get_path('theme', Drupal::config('system.theme')->get('default'));
    }
    else {
      $theme_path = dirname($theme_info->filename);
    }*/

    ob_start();
    print eval('?>' . $code);
    $output = ob_get_contents();
    ob_end_clean();

    // Recover original theme path.
    /* $theme_path = $old_theme_path; */

    return $output;
  }

}