<?php
 
namespace Drupal\tvmachine_blocks\Helper;

use Drupal\Core\Site\Settings;
use Drupal\Component\Utility\Crypt;


# Class Helper
class DbQueryHelper{

  public function get_channels($with_logo = false, $nids = false, $image_id = 0) {

    if (is_array($nids) && count($nids) == 0) {
      return [];
    }

    if ($with_logo) {
      $host = \Drupal::request()->getSchemeAndHttpHost();
    }

    // Getting channels data
    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('nfd', 'title');
    $query->addField('up', 'field_channels_url_prog_value');
    $query->addField('upm', 'field_channels_url_prog_mobile_value');

    $query->addField('u', 'field_channels_url_value');
    $query->addField('ul', 'field_channels_url_live_value');
    $query->addField('ur', 'field_channels_url_replay_value');

    if ($with_logo) {
      $query->addField('l', 'field_channels_logo_target_id');
      $query->addField('fm', 'uri');
    }

    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');
    $query->leftjoin('node__field_channels_url_prog', 'up', 'up.entity_id = n.nid');
    $query->leftjoin('node__field_channels_url_prog_mobile', 'upm', 'upm.entity_id = n.nid');

    $query->leftjoin('node__field_channels_url', 'u', 'u.entity_id = n.nid');
    $query->leftjoin('node__field_channels_url_live', 'ul', 'ul.entity_id = n.nid');
    $query->leftjoin('node__field_channels_url_replay', 'ur', 'ur.entity_id = n.nid');

    if ($with_logo) {
      $query->leftjoin('node__field_channels_logo', 'l', 'l.entity_id = n.nid');
      $query->leftjoin('file_managed', 'fm', 'fm.fid = l.field_channels_logo_target_id');

      $query->condition('l.delta', $image_id);
    }

    $query->condition('n.type', 'channels');

    if ($nids) {
      $query->condition('n.nid', $nids, 'IN');
    }
    //$query->orderBy('n.nid', 'DESC');
    $channels_data = $query->execute()->fetchAll();

    $channels_data_arr = array();

    foreach ($channels_data  as $key => $value) {
      $channels_data_arr[$value->nid] = array(
        'title' => $value->title,
        'channels_url' => $value->field_channels_url_value,
        'channels_url_live' => $value->field_channels_url_live_value,
        'channels_url_replay' => $value->field_channels_url_replay_value,
        'channels_url_prog' => $value->field_channels_url_prog_value,
        'channels_url_prog_mobile' => $value->field_channels_url_prog_mobile_value,
      );
      if ($with_logo) {

        $channels_data_arr[$value->nid]['logo'] = str_replace("public://", $host."/sites/default/files/", $value->uri);
      }
    }

    return $channels_data_arr;
  }

  public function get_channel_data($channel_id) {

    // Getting channels data
    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('nfd', 'title');
    $query->addField('l', 'field_channels_logo_target_id');
    $query->addField('fm', 'uri');

    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');
    $query->leftjoin('node__field_channels_logo', 'l', 'l.entity_id = n.nid');
    $query->leftjoin('file_managed', 'fm', 'fm.fid = l.field_channels_logo_target_id');

    $query->condition('n.type', 'channels');
    $query->condition('n.nid', $channel_id);
    $query->orderBy('n.nid', 'DESC');
    $result = $query->execute()->fetchAssoc();

    if ($result) {

      $host = \Drupal::request()->getSchemeAndHttpHost();

      return array(
        'title' => $result['title'],
        'logo' => str_replace("public://", $host."/sites/default/files/", $result['uri']),
      );
    } else {
      return array();
    }
  }

  public function get_channel_title($channel_id) {

    // Getting channels data
    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('nfd', 'title');

    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');

    $query->condition('n.type', 'channels');
    $query->condition('n.nid', $channel_id);
    $result = $query->execute()->fetchAssoc();

    if ($result) {

      return $result['title'];
    } else {
      return false;
    }
  }

  // Compact mode for the list template
  public function get_programs($time_limit_min, $time_limit_max, $compact = false) {
    // Getting programs data
    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('st', 'field_program_start_time_value');
    $query->addField('ch', 'field_program_channel_target_id');
    $query->addField('nfd', 'title');
    if (!$compact) {
      $query->addField('c', 'field_program_category_value');
      $query->addField('i', 'field_program_image_upload_target_id');
      $query->addField('fm', 'uri');
    } 

    $query->join('node__field_program_start_time', 'st', 'st.entity_id = n.nid');
    $query->join('node__field_program_channel', 'ch', 'ch.entity_id = n.nid');
    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');

    if (!$compact) {
      $query->leftjoin('node__field_program_category', 'c', 'c.entity_id = n.nid');
      $query->leftjoin('node__field_program_image_upload', 'i', 'i.entity_id = n.nid');
      $query->leftjoin('file_managed', 'fm', 'fm.fid = i.field_program_image_upload_target_id');
    }

    $query->condition('st.field_program_start_time_value', [$time_limit_min,$time_limit_max], 'BETWEEN');
    $query->condition('n.type', 'program');
    $query->orderBy('st.field_program_start_time_value', 'ASC');
    $output = $query->execute()->fetchAll();

    return $output;
  }

  public function get_programs_by_nids($nids) {

    if (is_array($nids) && count($nids) == 0) {
      return [];
    }

    $host = \Drupal::request()->getSchemeAndHttpHost();
    $private_key = \Drupal::service('private_key')->get();
    $salt = Settings::getHashSalt();

    // Getting programs data
    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('h', 'field_program_hour_value');
    $query->addField('m', 'field_program_minutes_value');
    $query->addField('nfd', 'title');
    $query->addField('d', 'field_program_description_value');
    $query->addField('i', 'field_program_image_upload_target_id');
    $query->addField('fm', 'uri');

    $query->join('node__field_program_hour', 'h', 'h.entity_id = n.nid');
    $query->join('node__field_program_minutes', 'm', 'm.entity_id = n.nid');
    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');

    $query->leftjoin('node__field_program_description', 'd', 'd.entity_id = n.nid');
    $query->leftjoin('node__field_program_image_upload', 'i', 'i.entity_id = n.nid');
    $query->leftjoin('file_managed', 'fm', 'fm.fid = i.field_program_image_upload_target_id');


    $query->condition('n.nid', $nids, 'IN');
    $query->condition('n.type', 'program');

    $programs_data = $query->execute()->fetchAll();

    $programs_data_arr = array();
    $aliases_arr = $this->get_aliases_by_nids($nids);

    foreach ($programs_data  as $key => $value) {
      $programs_data_arr[$value->nid] = array(
        'title' => $value->title,
        'hour' => $value->field_program_hour_value,
        'minutes' => $value->field_program_minutes_value,
        'desc' => $value->field_program_description_value,
        'alias' => $aliases_arr['/node/'.$value->nid],
      );
      if ($value->uri) {
        $programs_data_arr[$value->nid]['img_origin'] = str_replace("public://", $host."/sites/default/files/", $value->uri);

        $token = substr(Crypt::hmacBase64('53x53:'.$value->uri, $private_key . $salt), 0, 8);
        $programs_data_arr[$value->nid]['img_style'] = str_replace("public://", $host."/sites/default/files/styles/53x53/public/", $value->uri.'?itok='.$token);
      } else {
        $programs_data_arr[$value->nid]['img_origin'] = false;
        $programs_data_arr[$value->nid]['img_style'] = false;
      }
    }

    return $programs_data_arr;
  }

  public function get_prog_title_and_desc($program_id) {

    // Getting channels data
    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('nfd', 'title');
    $query->addField('d', 'field_program_description_value');

    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');
    $query->leftjoin('node__field_program_description', 'd', 'd.entity_id = n.nid');


    $query->condition('n.type', 'program');
    $query->condition('n.nid', $program_id);
    $result = $query->execute()->fetchAssoc();

    if ($result) {

      return array(
        'title' => $result['title'],
        'desc' => $result['field_program_description_value'],
      );
    } else {
      return array();
    }
  }

  public function get_prev_programs($unix, $time_min, $channel_id) {

    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('st', 'field_program_start_time_value');
    $query->addField('ch', 'field_program_channel_target_id');
    $query->addField('nfd', 'title');
    $query->addField('h', 'field_program_hour_value');
    $query->addField('m', 'field_program_minutes_value');

    $query->join('node__field_program_start_time', 'st', 'st.entity_id = n.nid');
    $query->join('node__field_program_channel', 'ch', 'ch.entity_id = n.nid');
    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');
    $query->join('node__field_program_hour', 'h', 'h.entity_id = n.nid');
    $query->join('node__field_program_minutes', 'm', 'm.entity_id = n.nid');

    $query->condition('st.field_program_start_time_value', [$time_min, $unix-1], 'BETWEEN');
    $query->condition('n.type', 'program');
    $query->condition('ch.field_program_channel_target_id', $channel_id);
    $query->orderBy('st.field_program_start_time_value', 'DESC');
    $query->range(0, 3);
    $output = $query->execute()->fetchAll();

    $nids = array();
    foreach ($output as $key => $value) {
      $nids[] =  array(
        'unix' => $value->field_program_start_time_value,
        'hour' => $value->field_program_hour_value,
        'minutes' => $value->field_program_minutes_value,
        'title' => $value->title,
        'nid' => $value->nid
      );

    }

    return $nids;
  }

  public function get_next_programs($unix, $time_max, $channel_id) {

    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('st', 'field_program_start_time_value');
    $query->addField('ch', 'field_program_channel_target_id');
    $query->addField('nfd', 'title');
    $query->addField('h', 'field_program_hour_value');
    $query->addField('m', 'field_program_minutes_value');

    $query->join('node__field_program_start_time', 'st', 'st.entity_id = n.nid');
    $query->join('node__field_program_channel', 'ch', 'ch.entity_id = n.nid');
    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');
    $query->join('node__field_program_hour', 'h', 'h.entity_id = n.nid');
    $query->join('node__field_program_minutes', 'm', 'm.entity_id = n.nid');


    $query->condition('st.field_program_start_time_value', [$unix+1, $time_max], 'BETWEEN');
    $query->condition('n.type', 'program');
    $query->condition('ch.field_program_channel_target_id', $channel_id);
    $query->orderBy('st.field_program_start_time_value', 'ASC');
    $query->range(0, 3);
    $output = $query->execute()->fetchAll();

    $nids = array();
    foreach ($output as $key => $value) {
      $nids[] =  array(
        'unix' => $value->field_program_start_time_value,
        'hour' => $value->field_program_hour_value,
        'minutes' => $value->field_program_minutes_value,
        'title' => $value->title,
        'nid' => $value->nid
      );
    }

    return $nids;
  }

  public function get_channel_programs($month, $day, $channel_id) { 

    // Getting programs data
    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('st', 'field_program_start_time_value');
    $query->addField('ch', 'field_program_channel_target_id');
    $query->addField('nfd', 'title');
    $query->addField('c', 'field_program_category_value');
    $query->addField('h', 'field_program_hour_value');
    $query->addField('mi', 'field_program_minutes_value');
    $query->addField('mo', 'field_program_month_value');
    $query->addField('da', 'field_program_day_value');
    $query->addField('de', 'field_program_description_value');


    $query->join('node__field_program_start_time', 'st', 'st.entity_id = n.nid'); 
    $query->join('node__field_program_channel', 'ch', 'ch.entity_id = n.nid');
    $query->join('node_field_data', 'nfd', 'nfd.nid = n.nid');
    $query->leftjoin('node__field_program_category', 'c', 'c.entity_id = n.nid');
    $query->join('node__field_program_hour', 'h', 'h.entity_id = n.nid');
    $query->join('node__field_program_minutes', 'mi', 'mi.entity_id = n.nid');
    $query->join('node__field_program_month', 'mo', 'mo.entity_id = n.nid');
    $query->join('node__field_program_day', 'da', 'da.entity_id = n.nid');
    $query->leftjoin('node__field_program_description', 'de', 'de.entity_id = n.nid');

    $query->condition('n.type', 'program');
    $query->condition('ch.field_program_channel_target_id', $channel_id);
    $query->condition('mo.field_program_month_value', $month);
    $query->condition('da.field_program_day_value', $day);
    
    $query->orderBy('st.field_program_start_time_value', 'ASC');
    $output = $query->execute()->fetchAll();

    $paths = array();
    foreach ($output as $key => $value) {
      $paths[] = '/node/'.$value->nid;
    }

    $aliases = $this->get_aliases_by_paths($paths);

    $programs = array();
    foreach ($output as $key => $value) {
      $programs[] = array(
        'nid' => $value->nid, 
        'title' => $value->title, 
        'alias' => $aliases['/node/'.$value->nid], 
        'month' => $value->field_program_month_value, 
        'day' => $value->field_program_day_value, 
        'hour' => $value->field_program_hour_value, 
        'minutes' => $value->field_program_minutes_value, 
        'description' => $value->field_program_description_value,
        'category' => $value->field_program_category_value, 
      );
    }

    return $programs;
  }

  public function find_program_by_date($month, $day, $hour, $minute, $channel_id) { 

    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'program');
    $query->condition('field_program_day', (int)$day);
    $query->condition('field_program_month', (int)$month);
    $query->condition('field_program_hour', (int)$hour);
    $query->condition('field_program_minutes', (int)$minute);
    $query->condition('field_program_channel', $channel_id);
    $query->range(0, 1);
    $result = $query->execute();
    $nid = reset($result);

    if ($nid) {
      return $nid;
    }

    return NULL;
  }

  public function find_program_by_date_unix($unix, $channel_id) { 

    $query = \Drupal::database()->select('node', 'n');
    $query->fields('n', array('nid'));
    $query->addField('st', 'field_program_start_time_value');
    $query->addField('ch', 'field_program_channel_target_id');
    $query->join('node__field_program_start_time', 'st', 'st.entity_id = n.nid');
    $query->join('node__field_program_channel', 'ch', 'ch.entity_id = n.nid');
    $query->condition('st.field_program_start_time_value', $unix);
    $query->condition('ch.field_program_channel_target_id', $channel_id);
    $query->condition('n.type', 'program');

    $result = $query->execute()->fetchAssoc();

    if ($result) {
      return $result['nid'];
    }

    return NULL;
  }

  public function get_aliases_by_paths($paths) {

    if ($paths) {

      $query = \Drupal::database()->select('path_alias', 'a');
      $query->fields('a', array('path', 'alias'));
      $query->condition('a.path', $paths, 'IN');
      $aliases = $query->execute()->fetchAll();

      $aliases_arr = array();

      foreach ($aliases  as $key => $value) {
        $aliases_arr[$value->path] = $value->alias;
      }

      return $aliases_arr;

    } else {
      return array();
    }
  }

  public function get_aliases_by_nids($nids) {

    if (is_array($nids) && count($nids) == 0) {
      return [];
    }

    $paths = [];
    foreach ($nids as $key => $nid) {
      $paths[] = '/node/'.$nid;
    }

    return $this->get_aliases_by_paths($paths);
  }

  public function get_alias_by_nid($nid) {

    $query = \Drupal::database()->select('path_alias', 'a');
    $query->fields('a', array('path', 'alias'));
    $query->condition('a.path', '/node/'.$nid);
    $result = $query->execute()->fetchAssoc();

    return $result['alias'];
  }

  public function get_file_uri($file_id) {

    $query = \Drupal::database()->select('file_managed', 'f');
    $query->fields('f', array('uri'));
    $query->condition('f.fid', $file_id);
    $result = $query->execute()->fetchAssoc();

    return $result['uri'];

  }

}