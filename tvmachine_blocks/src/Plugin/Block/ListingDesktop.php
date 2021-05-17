<?php

namespace Drupal\tvmachine_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase; 
use Drupal\tvmachine_blocks\Helper\TVMachineBlocksHelper;

/**
 * Provides a 'Listing Desktop' block.
 *
 * @Block(
 *   id = "Listing_Desktop",
 *   admin_label = @Translation("Listing Desktop")
 * )
 */
class ListingDesktop extends BlockBase {

  //public function getCacheMaxAge() {
  //  return 0;
  //}

  /**
   * {@inheritdoc}
   */
  public function build() {

    $temp = 6;

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
    $hour = 21;
    $minute = 0;

    $tvmachineBlocksHelper = new TVMachineBlocksHelper;
    $block_data = $tvmachineBlocksHelper->templateDataPreparation($temp, $set, $month, $day, $hour, $minute);

    $content =  array(
      '#theme' => 'tvmachine_temp_'.$temp,
    );

    return $content + $block_data;

  }
}
