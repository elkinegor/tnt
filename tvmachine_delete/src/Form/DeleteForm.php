<?php

namespace Drupal\tvmachine_delete\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\tvmachine_blocks\Helper\TVMachineBlocksHelper;

/**
 * Class ImportForm.
 *
 * @package Drupal\tvmachine_delete\Form
 */
class DeleteForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['tvmachine_delete.delete'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'delete_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('tvmachine_delete.delete');

    $hours_array = array(
      0 => '0',
      1 => '1',
      2 => '2',
      3 => '3',
      4 => '4',
      5 => '5',
      6 => '6',
      7 => '7',
      8 => '8',
      9 => '9',
      10 => '10',
      11 => '11',
      12 => '12',
      13 => '13',
      14 => '14',
      15 => '15',
      16 => '16',
      17 => '17',
      18 => '18',
      19 => '19',
      20 => '20',
      21 => '21',
      22 => '22',
      23 => '23'
    );

    $form['1'] = array(
      '#type' => 'fieldset', 
      '#weight' => 1, 
      '#collapsible' => FALSE, 
      '#collapsed' => FALSE,
      '#attributes' => array('style' => 'padding-left:100px'),
    );
    $form['1']['templates'] = array(
      '#type' => 'select',
      '#title' => t('Templates'),
      '#default_value' => 'all templates',
      '#options' => array(
        '00' => 'all template',
        '01' => 'template 1',
        '02' => 'template 2',
        '03' => 'template 3',
        '04' => 'template 4',
        '05' => 'template 5',
        '06' => 'template 6',
        '07' => 'template 7',
        '08' => 'template 8',
        '09' => 'template 9'
      ),
    );
    $month = array();
    for($i=1;$i<=12;$i++){
      $monthName = date("F", mktime(0, 0, 0, $i, 10)); 
      $month[$i] = $monthName;
    }
    $form['1']['month'] = array(
      '#type' => 'select',
      '#title' => t('Month'),
      '#default_value' => 'all month',
      '#options' => $month,
    );
    $day=array();
    for($j=1;$j<=31;$j++){
      if($j<10){
        $day[$j]='0'.$j;
      }
      else{
        $day[$j]= $j;
      }
    } 
    $form['1']['day'] = array(
      '#type' => 'select',
      '#title' => t('Day'),
      '#default_value' => 'all day',
      '#options' => $day,
    );

    $form['1']['hour'] = array(
      '#type' => 'select',
      '#title' => t('Hour'),
      '#default_value' => '0',
      '#options' => $hours_array,
    );

    $form['1']['item'] = array(
      '#type' => 'item',
      '#value' =>'to',
    );    
    $form['1']['tmonth'] = array(
      '#type' => 'select',
      '#title' => t('Month'),
      '#default_value' => 'all tmonth',
      '#options' => $month,
    );
    $form['1']['tday'] = array(
      '#type' => 'select',
      '#title' => t('Day'),
      '#default_value' => 'all tday',
      '#options' => $day,
    );
    $form['1']['thour'] = array(
      '#type' => 'select',
      '#title' => t('Hour'),
      '#default_value' => '23',
      '#options' => $hours_array,
    );

    $form = parent::buildForm($form, $form_state);
    $form['actions']['submit']['#value'] = $this->t('Delete page');
    $form['actions']['submit']['#weight'] = 98;

    $form['actions']['delete_all_pages'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete all pages'),
      '#submit' => ['::deleteAll'],
      '#weight' => 100,
    ];
/**
    $form['actions']['warm_day_02'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day -2'),
      '#weight' => 100,
    ];
    $form['actions']['warm_day_02']['#attributes']['class'][] = 'warm_day_02';

    $form['actions']['warm_day_01'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day -1'),
      '#weight' => 100,
    ];
    $form['actions']['warm_day_01']['#attributes']['class'][] = 'warm_day_01';

    $form['actions']['warm_day_0'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day 0'),
      '#weight' => 100,
    ];
    $form['actions']['warm_day_0']['#attributes']['class'][] = 'warm_day_0';

    $form['actions']['warm_day_1'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day +1'),
      '#weight' => 100,
      '#attributes' => array('style' => 'background-image: -webkit-linear-gradient(top,#daff69,#d9fc6d)'),
    ];
    $form['actions']['warm_day_1']['#attributes']['class'][] = 'warm_day_1';

    $form['actions']['warm_day_2'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day +2'),
      '#weight' => 100,
      '#attributes' => array('style' => 'background-image: -webkit-linear-gradient(top,#daff69,#d9fc6d)'),
    ];
    $form['actions']['warm_day_2']['#attributes']['class'][] = 'warm_day_2';

    $form['actions']['warm_day_3'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day +3'),
      '#weight' => 100,
    ];
    $form['actions']['warm_day_3']['#attributes']['class'][] = 'warm_day_3';

    $form['actions']['warm_day_4'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day +4'),
      '#weight' => 100,
    ];
    $form['actions']['warm_day_4']['#attributes']['class'][] = 'warm_day_4';

    $form['actions']['warm_day_5'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day +5'),
      '#weight' => 100,
    ];
    $form['actions']['warm_day_5']['#attributes']['class'][] = 'warm_day_5';

    $form['actions']['warm_day_6'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day +6'),
      '#weight' => 100,
    ];
    $form['actions']['warm_day_6']['#attributes']['class'][] = 'warm_day_6';

    $form['actions']['warm_day_7'] = [
      '#type' => 'submit',
      '#value' => $this->t('Warm day +7'),
      '#weight' => 100,
    ];
    $form['actions']['warm_day_7']['#attributes']['class'][] = 'warm_day_7';
*/
    $form['actions']['delete_cache_tags'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete cache tags'),
      '#submit' => ['::deleteCacheTags'],
      '#weight' => 100,
      '#attributes' => array('style' => 'background-image: -webkit-linear-gradient(top,#ffdc54,#fcdf6d)'),
    ];
    $form['actions']['delete_cache_tags']['#attributes']['class'][] = 'delete_cache_tags';

    $form['actions']['delete_cache_tags_mobile'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete cache tags mobile'),
      '#submit' => ['::deleteCacheTagsMobile'],
      '#weight' => 100,
    ];
    $form['actions']['delete_cache_tags_desktop'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete cache tags desktop'),
      '#submit' => ['::deleteCacheTagsDesktop'],
      '#weight' => 100,
    ];
    $form['actions']['delete_orphaned_images'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete orphaned images'),
      '#submit' => ['::deleteOrphanedImages'],
      '#weight' => 100,
    ];

    $form['#attached']['library'][] = 'tvmachine_delete/delete_form';


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

    parent::submitForm($form, $form_state);

    $tems = $form_state->getValue('templates');
    $sets = '01'; // $sets = $form_state['values']['sets'];
    $month = $form_state->getValue('month');
    $day = $form_state->getValue('day');
    $hour = $form_state->getValue('hour');
    $tmonth = $form_state->getValue('tmonth');
    $tday = $form_state->getValue('tday');
    $thour = $form_state->getValue('thour');
    $count = 0;
    if($tems!='00')
    {
      $this->tvmachine_delete_program($tems,$sets,$month,$day,$hour,$tmonth,$tday,$thour);
    }
    else
    {
      for($t=1;$t<=9;$t++) {
        $temp_idx = '0'.$t;
        $this->tvmachine_delete_program($temp_idx,$sets,$month,$day,$hour,$tmonth,$tday,$thour);
      }
    }

  }

  /**
   * {@inheritdoc}
   *
   */
  public function deleteAll(array &$form, FormStateInterface $form_state) {

    $file_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

    $directory = $file_path .'/templates/tv';
    if($this->tvmachine_delete_directory($directory)) {
      drupal_set_message($directory . ' directory removed.');
    } else {
      drupal_set_message('Occus a error when delete drectory '.$directory.'. Because this directory is not exists','error');
    }
  }

  public function deleteCacheTags(array &$form, FormStateInterface $form_state) {

    \Drupal\Core\Cache\Cache::invalidateTags(['config:block.block.listingdesktop','config:block.block.php_adhomeside','config:block.block.php_footer_listing_desktop','config:block.block.listingnowdesktop','config:block.block.listingpolldesktop','config:block.block.listingmobile','config:block.block.php_adhomemobile','config:block.block.listingpollmobile']);

    drupal_set_message('Cache tags were invalidated');
  }

  public function deleteCacheTagsMobile(array &$form, FormStateInterface $form_state) {

    \Drupal\Core\Cache\Cache::invalidateTags(['config:block.block.listingmobile','config:block.block.php_adhomemobile','config:block.block.listingpollmobile']);

    drupal_set_message('Cache tags were invalidated');
  }

  public function deleteCacheTagsDesktop(array &$form, FormStateInterface $form_state) {

    \Drupal\Core\Cache\Cache::invalidateTags(['config:block.block.listingdesktop','config:block.block.php_adhomeside','config:block.block.php_footer_listing_desktop','config:block.block.listingnowdesktop','config:block.block.listingpolldesktop']);

    drupal_set_message('Cache tags were invalidated');
  }

  public function deleteOrphanedImages(array &$form, FormStateInterface $form_state) {

    $TVMachineBlocksHelper = new TVMachineBlocksHelper();
    $TVMachineBlocksHelper->remove_orphaned_images();

    drupal_set_message('Orphaned images have been removed');
  }

  public function tvmachine_delete_directory($dirname) {
    if (is_dir($dirname)) $dir_handle = opendir($dirname);
    if (!isset($dir_handle) || !$dir_handle) return false;
    while($file = readdir($dir_handle)) {
      if ($file != "." && $file != "..") {
        if (!is_dir($dirname."/".$file)) unlink($dirname."/".$file);
        else $this->tvmachine_delete_directory($dirname.'/'.$file); 
      }
    }
    closedir($dir_handle);
    rmdir($dirname);
    return true;
  }

  public function tvmachine_delete_program($tems,$sets,$months,$days,$hours,$montht,$dayt,$hourt)
  {
    $months = (int)$months;
    $days = (int)$days; 
    $hours = (int)$hours;
    $montht = (int)$montht;
    $dayt = (int)$dayt;
    $hourt = (int)$hourt;

    //dpm('$months '.$months.' $days '.$days.' $hours '.$hours);
    //dpm('$montht '.$montht.' $dayt '.$dayt.' $hourt '.$hourt);

    $file_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");

    // Process months
    for($i=$months; $i<=$montht; $i++) {
      $m=($i<=9)?'0'.$i:$i;

      $start_day = ($i===$months)?$days:1;
      $end_day = ($i===$montht)?$dayt:31;

      // Process days
      for($j=$start_day;$j<=$end_day;$j++) {

        //dpm('month '.$i.' day '.$j.' temp '.$tems);

        $d = ($j<=9)?'0'.$j:$j;

        $dirname= $file_path .'/templates/tv/t'.$tems.'s'.$sets.'m'.$m.'d'.$d;

        $start_hour = 0;
        $end_hour = 23;

        //dpm('$hours = '.$hours);
        //dpm('$hourt = '.$hourt);

        // Is it first day of the first month and the start time not default
        if ($i===$months && $j===$days && $hours !== 0){
          //dpm('Set up start hour');
          $start_hour = $hours;
        }
        // Is it last day of the last month and the end time not default
        if ($i===$montht && $j===$dayt && $hourt !== 23){
          //dpm('Set up end hour');
          $end_hour = $hourt;
        }

        // Process hours when we need it
        if ($start_hour !== 0 || $end_hour !== 23) {

          //dpm('Process this day by hours day: '.$j);

          for($k=$start_hour; $k<=$end_hour; $k++){ 

            //dpm('month '.$i.' day '.$j.' hour '.$k);      
            $h = ($k<=9)?'0'.$k:$k;
            $filename = $dirname.'/'.'tv_'.$m.$d.$h.'t'.$tems.'s'.$sets.'.php';
            $this->rmfile($filename);
            drupal_set_message('Remove file '.$filename);
          }

        } else {
          // Day not last or first or time is default - remove all day
          //dpm('Remove all day at once: '.$j);
          $this->rmdirr($dirname);
          drupal_set_message('Erased directory '.$dirname);
        }    
      }
    }
  }
   

  public function rmdirr($dirname){
    if (!file_exists($dirname)) {
      return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
      return unlink($dirname);
    }
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
      if ($entry == '.' || $entry == '..') {
        continue;
      }
    $this->rmdirr($dirname .'/'. $entry);
    }
    $dir->close();
    return rmdir($dirname);
  }

  public function rmfile($filename){
    if (!file_exists($filename)) {
      return false;
    }
    if (is_file($filename) || is_link($filename)) {
      return unlink($filename);
    }
    return false;
  }

}
