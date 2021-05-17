<?php

namespace Drupal\tvmachine_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase; 
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\tvmachine_blocks\Helper\TVMachineBlocksHelper;


/**
 * Provides a 'Listing Mobile' block.
 *
 * @Block(
 *   id = "Listing_Mobile",
 *   admin_label = @Translation("Listing Mobile")
 * )
 */

class ListingMobile extends BlockBase {

  //public function getCacheMaxAge() {
  //  return 0;
  //}

  /**
   * {@inheritdoc}
   */
  public function build() {

    $temp = 5;

  	$date = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

 	$sets 		= array();
  	$query = \Drupal::entityQuery('node');
    $query->condition('type', 'sets');
    $sets = $query->execute();

		if(isset($_GET['set'])) {
       $set = intval($_GET['set']);
    }
    else {
      $set = array_shift($sets);
    }

    $month = $date->format('m');
    $day = $date->format('d');
    $hour = 22;
    $minute = 0;

    if(isset($_GET['thour']) && !empty($_GET['thour'])) {
       $hour = intval($_GET['thour']);
    }
    if(isset($_GET['tday']) && !empty($_GET['tday'])) {
       $day = intval($_GET['tday']);
    }
    if(isset($_GET['tmonth']) && !empty($_GET['tmonth'])) {
       $month = intval($_GET['tmonth']);
    }

    /* Code from Mobile iframe start */

    date_default_timezone_set("Europe/Paris");

    if (isset($_GET['tmonth']) && !empty($_GET['tmonth'])) {
      $tmonth = $_GET['tmonth'];
      $tmonth = preg_replace('/[^a-zA-Z0-9]+/', '', $tmonth);
    } else {
      $tmonth = date('n');
    }

    if (isset($_GET['tday']) && !empty($_GET['tday'])) {
      $tday = $_GET['tday'];
      $tday = preg_replace('/[^a-zA-Z0-9]+/', '', $tday);
    } else { $tday = date('j');}

    $requested_daynumber = date("z", mktime(0,0,0,$tmonth,$tday,date('y')))+1;
    $daynumber = date("z")+1;
    $diff_daynumber = $requested_daynumber - $daynumber;

    if (($diff_daynumber != -2) && ($diff_daynumber != -1) && ($diff_daynumber != 0) && ($diff_daynumber != 1) && ($diff_daynumber != 2) && ($diff_daynumber != -365) && ($diff_daynumber != -364) && ($diff_daynumber != -363) && ($diff_daynumber != 363) && ($diff_daynumber != 364) && ($diff_daynumber != 365)) {
      $month = date('n');
      $day = date('j');
    }

    /* Code from Mobile iframe end */

    $tvmachineBlocksHelper = new TVMachineBlocksHelper;
    $block_data = $tvmachineBlocksHelper->templateDataPreparation($temp, $set, $month, $day, $hour, $minute);
    $content =  array(

      '#theme' => 'tvmachine_temp_'.$temp,
    );

    $content['#cache']['contexts'][] = 'url.path';
    $content['#cache']['contexts'][] = 'url.query_args:tmonth';
    $content['#cache']['contexts'][] = 'url.query_args:tday';
    $content['#cache']['contexts'][] = 'url.query_args:thour';

    return $content + $block_data;
  }
}