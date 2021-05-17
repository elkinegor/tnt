<?php

namespace Drupal\tvmachine_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase; 
use Drupal\tvmachine_blocks\Helper\TVMachineBlocksHelper;

/**
 * Provides a 'Listing Poll' block.
 *
 * @Block(
 *   id = "Listing_Poll",
 *   admin_label = @Translation("Listing Poll")
 * )
 */
class ListingPoll extends BlockBase {

  ////public function getCacheMaxAge() {
  //  return 0;
  //}

  /**
   * {@inheritdoc}
   */
  public function build() {

    $temp = 7;

  	$date = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

  	$sets 		= array();
  	$query = \Drupal::entityQuery('node');
    $query->condition('type', 'sets');
    $sets = $query->execute();
    $set = array_shift($sets);

    $tvmachineBlocksHelper = new TVMachineBlocksHelper;
    $block_data = $tvmachineBlocksHelper->templateDataPreparation($temp, $set);

    $path = \Drupal::request()->getPathInfo();

    if ($path == '/mobile/sondeo-noche' || $path == '/mobile/sondeo-noche-tv') {
      $block_data['#show_title'] = 0;   
    }  else {
      $block_data['#show_title'] = 1; 
    }

    $content =  array(
      '#theme' => 'tvmachine_temp_'.$temp,
    );

    return $content + $block_data;

  }
}
