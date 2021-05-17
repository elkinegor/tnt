<?php

namespace Drupal\tvmachine_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\tvmachine_blocks\Helper\DbQueryHelper;

/**
 * Provides a 'Ld+json' block.
 *
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "tvmachine_ld_json_block",
 *   admin_label = @Translation("Ld+json")
 * )
 */
class LdJSON extends BlockBase {

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
     'where pd.field_program_day_value='.$day_of_the_week.' and pc.field_program_category_value IN (:ids[]) and ph.field_program_hour_value IN (21,22) and pch.field_program_channel_target_id IN (51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77) ORDER BY FIELD(pch.field_program_channel_target_id,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77) limit 5';

    $database = \Drupal::database();
    $query = $database->query($query, [':ids[]' => ["Cine"]]);
    $result = $query->fetchAll();

    $sets = [];
    $programs_nids  = [];

    foreach ($result as $key => $row) {
      
      $channelid = $row->field_program_channel_target_id;
      $chan_info = node_load($channelid);

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
      $output .=  "<br style='clear:both;'>";
    }

    $DbQueryHelper = new DbQueryHelper();
    $channels_data = $DbQueryHelper->get_channels(true, $sets, 2);
    $programs_data = $DbQueryHelper->get_programs_by_nids($programs_nids);

    if (count($sets) > 0) {

      foreach($sets as $val=>$key) {

        $ch_image = $channels_data[$key]['logo'];
        $ch_name = $channels_data[$key]['title'];

        $nid_tip = $val;
        $nid_tip1 = $nid_tip+1;

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
        $movie_thumb = '<img src="'.$movie_logo.'" alt="'.$programs_data[$nid_tip]['title'].'" title="'.$programs_data[$nid_tip]['title'].'" width="53" height="53" class="thumb">';

        $movie_image = $programs_data[$nid_tip]['img_origin'];
        $body = $programs_data[$nid_tip]['desc'];
        $url = $programs_data[$nid_tip]['alias'];
        $str = substr($body, 0, 220) . '...';
        $day_start_for_json = date("d");
        $month_start_for_json = date("m");
        $year_start_for_json = date("Y");

        if($ed_date_hr == 00 || $ed_date_hr == 01 || $ed_date_hr == 02 || $ed_date_hr == 03) {

          $day_end_for_json = date("d",strtotime("now + 1 day"));
          $month_end_for_json = date("m",strtotime("now + 1 day"));
          $year_end_for_json = date("Y",strtotime("now + 1 day"));

        }else{

          $day_end_for_json = date("d");
          $month_end_for_json = date("m");
          $year_end_for_json = date("Y");

        }

        if(strlen($str) < 10) {        
          $output .= "";
        } else  {
          $output .=  '
          <script type="application/ld+json">
          {
          "@context":"https://schema.org",
          "@type":"Event",
          "name":"'.$st_date_hr.':'.$st_date_min.' - '.$programs_data[$nid_tip]['title'].'",
          "startDate":"'.$year_start_for_json.'-'.$month_start_for_json.'-'.$day_start_for_json.'T'.$st_date_hr.':'.$st_date_min.'+01:00",
          "endDate":"'.$year_end_for_json.'-'.$month_end_for_json.'-'.$day_end_for_json.'T'.$ed_date_hr.':'.$ed_date_min.'+01:00",
          "url":"https://www.tvguia.es'.$url.'",
          "description": "'.$str.'",
          "image":"'.$movie_image.'",
          "location":{
          "@type":"Place",
          "name":"'.$ch_name.'",
          "address":"'.$ch_name.'"}
          }
          </script>';
        }
      }
    }

    return array(
      '#type' => 'inline_template',
      '#template' => $output,
    );
  }

}
