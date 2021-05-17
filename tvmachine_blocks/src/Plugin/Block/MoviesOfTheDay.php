<?php

namespace Drupal\tvmachine_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\tvmachine_blocks\Helper\DbQueryHelper;

/**
 * Provides a 'Movies of the day' block.
 *
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "tvmachine_moviesoftheday_block",
 *   admin_label = @Translation("Movies of the day")
 * )
 */
class MoviesOfTheDay extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $output = '';

    $day_of_the_week = date("j");

    $query = 'SELECT n.nid, n.type, pm.field_program_month_value, pc.field_program_category_value, pd.field_program_day_value,ph.field_program_hour_value,pmin.field_program_minutes_value,pch.field_program_channel_target_id ' .
       'FROM {node_field_data} AS n ' .
       'JOIN {node__field_program_month} AS pm ' .
       'ON pm.entity_id = n.nid '.
       'JOIN {node__field_program_category} AS pc ' .
       'ON pc.entity_id = n.nid '.
       'JOIN {node__field_program_day} AS pd ' .
       'ON pd.entity_id = n.nid '.
       'JOIN {node__field_program_hour} AS ph ' .
       'ON ph.entity_id = n.nid '.
       'JOIN {node__field_program_minutes} AS pmin ' .
       'ON pmin.entity_id = n.nid '.
       'JOIN {node__field_program_channel} AS pch ' .
       'ON pch.entity_id = n.nid '.
     'where pd.field_program_day_value='.$day_of_the_week.' and pc.field_program_category_value IN (:ids[]) and ph.field_program_hour_value IN (21,22) and pch.field_program_channel_target_id IN (51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139) ORDER BY FIELD(pch.field_program_channel_target_id,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139) limit 15';

    $database = \Drupal::database();
    $query = $database->query($query, [':ids[]' => ["Cine"]]);
    $result = $query->fetchAll();

    $sets = [];
    $programs_nids  = [];

    foreach ($result as $key => $row) {
      
      $channelid = $row->field_program_channel_target_id;
      //$chan_info = node_load($channelid);

      $nid_ch = $row->nid;

      if ($row->field_program_hour_value == 23) {
        if($row->field_program_minutes_value == 0) {
          $sets[$nid_ch] = $channelid;
          $programs_nids[$nid_ch] = $nid_ch;
          $programs_nids[$nid_ch+1] = $nid_ch+1;
        }
      } else {
        $sets[$nid_ch] = $channelid;
        $programs_nids[$nid_ch] = $nid_ch;
        $programs_nids[$nid_ch+1] = $nid_ch+1;
      } 
    }

    $getcount=count($sets);

    if($getcount > 0) {
      $output .=  "<h2>Peliculas de la noche:</h2><br style='clear:both;'>";
    }

    if (count($sets) > 0) {

      $DbQueryHelper = new DbQueryHelper();
      $channels_data = $DbQueryHelper->get_channels(true, $sets, 2);
      $programs_data = $DbQueryHelper->get_programs_by_nids($programs_nids);

      foreach($sets as $val=>$key) {

        /*$chid = node_load($key);
        $channel_data  = _extract_channel_values($chid, 2);

        $ch_image = $channel_data['field_channels_logo'];
        $ch_name = $chid->getTitle();*/

        $ch_image = $channels_data[$key]['logo'];
        $ch_name = $channels_data[$key]['title'];

        $nid_tip = $val;
        //$nid_tip1 = $nid_tip+1;

        /*$node_nid = _extract_program_values(node_load($nid_tip), '53x53');
        $node_nid1 = _extract_program_values(node_load($nid_tip1), '53x53');*/

        $st_date_hr = $programs_data[$nid_tip]['hour'];
        $st_date_min = $programs_data[$nid_tip]['minutes'];
        $st_date_min = sprintf("%02d",$st_date_min);
        $ed_date_hr = $programs_data[$nid_tip+1]['hour'];
        $ed_date_hr = sprintf("%02d",$ed_date_hr);
        $ed_date_min = $programs_data[$nid_tip+1]['minutes'];
        $ed_date_min = sprintf("%02d",$ed_date_min);

        if((isset($ed_date_hr) && $ed_date_hr === "0") || !empty($ed_date_hr)) {
          $ed_date = $ed_date_hr.':'.$ed_date_min;
        } else {
          $ed_date=' ...';
        }

        $movie_logo = $programs_data[$nid_tip]['img_style'];
        if ($programs_data[$nid_tip]['img_style']) {
          $movie_thumb = '<img src="'.$movie_logo.'" alt="'.$programs_data[$nid_tip]['title'].'" title="'.$programs_data[$nid_tip]['title'].'" width="53" height="53" class="thumb">';
        } else {
          $movie_thumb = '';
        }

        $body = $programs_data[$nid_tip]['desc'];
        $url = $programs_data[$nid_tip]['alias'];
        $str = substr($body, 0, 220) . '...';

        if(strlen($str) < 10) {        
          $output .= "";   
        } else  {



          $output .= "<div><div style='height:59px; width: 59px; display: inline; float: left;'>".$movie_thumb."</div>
          
          <div style='width: 180px; display: inline; float: left;'><img src='".$ch_image."' alt='".$channels_data[$key]['title']."' height='25' width='30'><span style='height:25px; display: inline; padding: 0 0 0 6px; vertical-align: 45%;'>".$st_date_hr.":".$st_date_min. " - ".$ed_date."</span><br/><div style='font-weight: bold; padding: 0 8px;'>".$programs_data[$nid_tip]['title']."</div></div>
          
          <div style='width: 500px; display: inline; float: left;'>".$str." <a href='".$url."' style='font-weight: bold; text-decoration: none; color: #ffffff; background-color: #ff0000;'> +Info&nbsp;</a></div></div><br style='clear:both;'><br/>";
        }
      }
    }

    return array(
      '#type' => 'inline_template',
      '#template' => $output,
    );
  }

}
