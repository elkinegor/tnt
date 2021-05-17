<?php

namespace Drupal\tvmachine_import\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\tvmachine_import\TVMachineBatchImport;

/**
 * Class ImportForm.
 *
 * @package Drupal\tvmachine_import\Form
 */
class ImportForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['tvmachine_import.import'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    // Extracted FTP links fron the CSV, were stored in the Session
    $tempstore = \Drupal::service('user.private_tempstore')->get('tvmachine_import');

    $config = $this->config('tvmachine_import.import');
    
    $status = $tempstore->get('status');
    
    if ($status == 'step1') {

      $ftp_images = json_decode($tempstore->get('ftp_images'));

      // If we have FTP images
      if ($ftp_images){

        // Prepare data for JS script
        $temp_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://ftp_images");

        $markup = '<span id="ftp_images_list" class="hidden" style="display:none;">';
        $markup_result = '<span id="ftp_result" class="hidden" style="display:none;>';

        foreach ($ftp_images as $key => $value) {

          $data  = array(
            'items' => $value,
            'temp_path' => $temp_path
          );
          $markup .= '<span class="item" data-id="'.($key+1).'">';
          $markup .= json_encode($data);
          $markup .= '</span><br />';

          $markup_result .= '<br /><span class="item-'.($key+1).'">Tread '.($key+1).' In the Proces...</span><br />';
        }

        
        $markup .= '</span>';
        $markup_result .= '</span>';

        $process_state = '<br />Total treads: '.count($ftp_images).'; <span id="process_state">Have done: 0</span><br />';

        // Markup for JS script with data
        $form['value_display'] = array
        (
          '#markup' => $markup .$process_state. $markup_result,
        );

        $tempstore->set('ftp_images', json_encode(array()));
      }

      $form['#attached']['library'][] = 'tvmachine_import/import_form';

    } else {

      $form['file'] = [
        '#title' => $this->t('CSV file'),
        '#type' => 'managed_file',
        '#upload_location' => 'public://imports',
        '#default_value' => NULL,
        '#upload_validators' => array(
          'file_validate_extensions' => array('csv'),
        ),
        //'#required' => TRUE,
      ];

      $form['additional_settings'] = [
        '#type' => 'fieldset',
        '#title' => t('Additional settings'),
      ];

      $form['additional_settings']['skip_first_line'] = [
        '#type' => 'checkbox',
        '#title' => t('Skip first line'),
        '#default_value' => $config->get('skip_first_line'),
        '#description' => t('If file contain titles, this checkbox help to skip first line.'),
      ];

    } 

    $form = parent::buildForm($form, $form_state);
    $form['actions']['submit']['#value'] = $this->t('Save and import');
    $form['actions']['submit']['#attributes'] = array('style' => array('display:none;'));

    if ($status != 'step1') {
      $form['actions']['download_images_ajax'] = [
        '#type' => 'submit',
        '#value' => $this->t('Import'),
        '#submit' => ['::extractFtpImagesFromCSV'],
        '#weight' => 100,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    //parent::submitForm($form, $form_state);

    $file = $form_state->getValue('file');

    if (isset($file[0])) {
      $fid = $file[0];
    } else {
      $tempstore = \Drupal::service('user.private_tempstore')->get('tvmachine_import');
      $fid = json_decode($tempstore->get('fid'));
    }

    if ($fid) {
      $skip_first_line = $form_state->getValue('skip_first_line');

      $config = $this->config('tvmachine_import.import');
      $delimiter = $config->get('delimiter');
      $enclosure = $config->get('enclosure');

      $import = new TVMachineBatchImport($fid, $skip_first_line, $delimiter, $enclosure, 'Custom CSV import');

      $import->setBatch();
    } else {
      \Drupal::messenger()->addStatus('File field is empty');
    }

    $tempstore = \Drupal::service('user.private_tempstore')->get('tvmachine_import');
    $tempstore->set('ftp_images', json_encode(array()));
    $tempstore->set('count_ftp_images', 0);
    $tempstore->set('fid', false);
    $tempstore->set('status', 'finish');
  }

  public function extractFtpImagesFromCSV(array &$form, FormStateInterface $form_state) {

    $fid = $form_state->getValue('file')[0];
    $file = File::load($fid);
    $temp_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://ftp_images");

    $time = time();

    // Get csv content
    $file_content = file_get_contents($file->getFileUri());

    // Extract ftp links
    preg_match_all('/ftp:\/\/([A-Za-z0-9.]+:[A-Za-z0-9]+@)?([\\.A-Za-z0-9]+)\/([\\.\/A-Za-z0-9]+)/', $file_content, $links);
    //dpm($links[0]);
    \Drupal::messenger()->addStatus('Total '.count($links[0]).' FTP links');

    
    //Group the urls
    $ftp_list = array_chunk($links[0], 10);    

    // Save in the session
    $tempstore = \Drupal::service('user.private_tempstore')->get('tvmachine_import');
    $tempstore->set('ftp_images', json_encode($ftp_list));
    $tempstore->set('count_ftp_images', count($links[0]));
    $tempstore->set('fid', $fid);
    $tempstore->set('status', 'step1');
  }
}
