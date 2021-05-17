<?php

namespace Drupal\tvmachine_blocks\Helper;

use Drupal\Core\Messenger\MessengerInterface;

//use Drupal\Core\Url;

use Drupal\Core\Site\Settings;
use Drupal\Component\Utility\Crypt;

use Drupal\tvmachine_blocks\Helper\DbQueryHelper;
use Drupal\image\Entity\ImageStyle;

# Class Helper
class TVMachineBlocksHelper{

  public function dpm($input, $name = NULL, $type = MessengerInterface::TYPE_STATUS) {
    //\Drupal::service('devel.dumper')->message($input, $name, $type);
    if (is_string($input)) {
      //\Drupal::logger('TVMachineBlocksHelper')->info($input);
    }    
  }

  public function w($input) {
    if (is_string($input)) {
      \Drupal::logger('TVMachineBlocksHelper')->info($input);
    } else {
      //\Drupal::logger('TVMachineBlocksHelper')->info('Input not a string');
    }
  }

  /**
   * {@inheritdoc}
   *
   * Preparing the  data for tvmachine_temp_*.html.twig template. 
   */
  public function templateDataPreparation($temp = NULL, $set = NULL, $month = NULL, $day = NULL, $hour = NULL, $minute = NULL) {

    $block_vars = array();

    // Data vars
    $english_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    $translated_month = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    $english_day = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $translated_day = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
    $short_translated_day = array('Lu.', 'Ma.', 'Mi.', 'Ju.', 'Vi.', 'Sá.', 'Do.');

    // Lib name
    $libraries = 'temp_'.$temp;

    $day_vars = [];

    // Path vars

    if(($temp == 5) || ($temp == 6)) {    

      $day1 = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

      $day2 = clone $day1;
      $day2->setDate($day1->format('Y'), $day1->format('n'), $day1->format('j')+1);
      $day3 = clone $day1;
      $day3->setDate($day1->format('Y'), $day1->format('n'), $day1->format('j')+2);

      $day01 = clone $day1;
      $day01->setDate($day1->format('Y'), $day1->format('n'), $day1->format('j')-1);

      $day02 = clone $day1;
      $day02->setDate($day1->format('Y'), $day1->format('n'), $day1->format('j')-2);

      $day1->format('j');

      $day_vars['day1_j'] = $day1->format('j');
      $day_vars['day2_j'] = $day2->format('j');
      $day_vars['day3_j'] = $day3->format('j');

      $day_vars['day01_j'] = $day01->format('j');
      $day_vars['day02_j'] = $day02->format('j');

      $day_vars['day1_n'] = $day1->format('n');
      $day_vars['day2_n'] = $day2->format('n');
      $day_vars['day3_n'] = $day3->format('n');

      $day_vars['day01_n'] = $day01->format('n');
      $day_vars['day02_n'] = $day02->format('n');

      $day_vars['day1_l'] = $day1->format('l');
      $day_vars['day2_l'] = $day2->format('l');
      $day_vars['day3_l'] = $day3->format('l');

      $day_vars['day01_l'] = $day01->format('l');
      $day_vars['day02_l'] = $day02->format('l');

      $day_vars['day1_l_replace'] = str_replace($english_day, $translated_day, t($day1->format('l')));
      $day_vars['day2_l_replace'] = str_replace($english_day, $translated_day, t($day2->format('l')));
      $day_vars['day3_l_replace'] = str_replace($english_day, $translated_day, t($day3->format('l')));

      $day_vars['day01_l_replace'] = str_replace($english_day, $short_translated_day, t($day01->format('l')));
      $day_vars['day02_l_replace'] = str_replace($english_day, $short_translated_day, t($day02->format('l')));

      $day_vars['dateYmdHis'] = date('Y-m-d H:i:s');
    }

    $js_vars = [];   

    if(($temp == 6)) {

      $module_handler = \Drupal::service('module_handler');
      $tvmachine_path = $module_handler->getModule('tvmachine_blocks')->getPath();
      $base_url = \Drupal::request()->getSchemeAndHttpHost();
      $tvmachine_images_path = '/' . $tvmachine_path . '/images/';

      $js_vars = [
        'base_url' => $base_url,
        'tvmachine_path' => $tvmachine_path,
        'tvmachine_images_path' => $tvmachine_images_path,
        'temp' => $temp,
        'set' => $set,
        'month' => $month,
        'day' => $day,
        'hour' => $hour,
        'minute' => $minute,
      ];
    }

    $args = [];

    if(($temp == 5)) {

      // Vars for twig html

      //start copy from tvmachine_template_grid_static.tpl.php
      date_default_timezone_set("Europe/Paris");
      
      $date02 = getdate(time() - (48 * 60 * 60));
      $date01 = getdate(time() - (24 * 60 * 60));
      $date0 = getdate(time());
      $date1 = getdate(time() + (24 * 60 * 60));
      $date2 = getdate(time() + (48 * 60 * 60));
      //end copy from tvmachine_template_grid_static.tpl.php

      $day_vars['date02'] = $date02;
      $day_vars['date01'] = $date01;
      $day_vars['date0'] = $date0;
      $day_vars['date1'] = $date1;
      $day_vars['date2'] = $date2;


      // Vars for js code and twig html
      $data_vars = $this->temp5JSVarsPreparation();

      $js_vars['month'] = $data_vars['month'];
      $js_vars['day'] = $data_vars['day'];
      $js_vars['hour'] = $data_vars['hour'];
      $js_vars['minute'] = $data_vars['minute'];
      $js_vars['temp'] = $temp;

      $path = \Drupal::request()->getpathInfo();
      $path_args  = explode('/',$path);

      $args = [];

      if (isset($path_args[8])) {$args['color1'] = $path_args[8];}
      if (isset($path_args[9])) {$args['color2'] = $path_args[9];}
      if (isset($path_args[10])) {$args['color3'] = $path_args[10];}
      if (isset($path_args[11])) {$args['color4'] = $path_args[11];}
    }

    $block_vars = ['#js_vars' => $js_vars, '#args' => $args];

    $block_vars['#english_month'] = $english_month;
    $block_vars['#translated_month'] = $translated_month;
    $block_vars['#english_day'] = $english_day;
    $block_vars['#translated_day'] = $translated_day;
    $block_vars['#day_vars'] = $day_vars;

    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    
    if (in_array('super_admin',$roles) || in_array('editor',$roles)) {
      $block_vars['#is_admin'] = 1;
    } else {
      $block_vars['#is_admin'] = 0;
    }

    if(($temp == 7)) {
      $hour = 22;
      $minute = 45;
    }

    $block_vars['#body'] = $this->getDataByRequest($temp, $set, $month, $day, $hour, $minute);

    return $block_vars;

  }

    /**
   * {@inheritdoc}
   *
   * Preparing js vars for the tvmachine_temp_5.html.twig template. 
   */
  public function temp5JSVarsPreparation() {

    //start copy from block iframe 
    
    if (isset($_GET['tmonth']) && !empty($_GET['tmonth'])) {
      $month = preg_replace('/[^a-zA-Z0-9]+/', '', $_GET['tmonth']);
    } else {
      $month = date('n');
    }

    if (isset($_GET['tday']) && !empty($_GET['tday'])) {
      $day = preg_replace('/[^a-zA-Z0-9]+/', '', $_GET['tday']);
    } else {
      $day = date('j');
    }
    
    //prevent to pick day out of scope of 3 next day
    $requested_daynumber = date("z", mktime(0,0,0,$month,$day,date('y')))+1;
    $daynumber = date("z")+1;
    $diff_daynumber = $requested_daynumber - $daynumber;
    if (($diff_daynumber != -2) && ($diff_daynumber != -1) && ($diff_daynumber != 0) && ($diff_daynumber != 1) && ($diff_daynumber != 2) && ($diff_daynumber != -365) && ($diff_daynumber != -364) && ($diff_daynumber != -363) && ($diff_daynumber != 363) && ($diff_daynumber != 364) && ($diff_daynumber != 365)) {
      $month = date('n');
      $day = date('j');
    }
    
    if (isset($_GET['thour']) && !empty($_GET['thour'])) {
      $hour = preg_replace('/[^a-zA-Z0-9]+/', '', $_GET['thour']);
    } else {
      $hour = 22;
    }

    if($hour==1) {$hour = 1;}
    else if($hour=="0") {$hour = 1;}
    else if($hour=="2") {$hour = 2;}
    else if($hour=="4") {$hour = 4;}
    else if($hour=="6") {$hour = 6;}
    else if($hour=="8") {$hour = 8;}
    else if($hour=="10") {$hour = 10;}
    else if($hour=="12") {$hour = 12;}
    else if($hour=="14") {$hour = 14;}
    else if($hour=="16") {$hour = 16;}
    else if($hour=="18") {$hour = 18;}
    else if($hour=="20") {$hour = 20;}
    else if($hour=="22") {$hour = 22;}
    else if(empty($hour)) {$hour = 22;}
    else if($hour=="now"){
      date_default_timezone_set('Europe/Paris');
      $now_hour = date('H');
      if($now_hour==0||$now_hour==1) { $hour = 1;}
      else if($now_hour==2||$now_hour==3) { $hour = 2;}
      else if($now_hour==4||$now_hour==5) { $hour = 4;}
      else if($now_hour==6||$now_hour==7) { $hour = 6;}
      else if($now_hour==8||$now_hour==9) { $hour = 8;}
      else if($now_hour==10||$now_hour==11) { $hour = 10;}
      else if($now_hour==12||$now_hour==13) { $hour = 12;}
      else if($now_hour==14||$now_hour==15) { $hour = 14;}
      else if($now_hour==16||$now_hour==17) { $hour = 16;}
      else if($now_hour==18||$now_hour==19) { $hour = 18;}
      else if($now_hour==20||$now_hour==21) { $hour = 20;}
      else if($now_hour==22||$now_hour==23) { $hour = 22;}
      else if($now_hour==24) { $hour = 1;}
    }
    else {
      if($hour==0||$hour==1) { $hour = 1;}
      else if($hour==2||$hour==3) { $hour = 2;}
      else if($hour==4||$hour==5) { $hour = 4;}
      else if($hour==6||$hour==7) { $hour = 6;}
      else if($hour==8||$hour==9) { $hour = 8;}
      else if($hour==10||$hour==11) { $hour = 10;}
      else if($hour==12||$hour==13) { $hour = 12;}
      else if($hour==14||$hour==15) { $hour = 14;}
      else if($hour==16||$hour==17) { $hour = 16;}
      else if($hour==18||$hour==19) { $hour = 18;}
      else if($hour==20||$hour==21) { $hour = 20;}
      else if($hour==22||$hour==23) { $hour = 22;}
      else if($hour==24) { $hour = 1;}
      else {
        $hour = 22;
      }
    }
    //end copy from block iframe

    //start copy from block iframe now
    $current_url = $_SERVER['REQUEST_URI'];

    if ($current_url == '/mobile/programacion-tv-ahora'){
      $now_hour = date('H');
      $tminute = (int)date('i');
      $lapse = array("46", "47", "48", "49", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59");
      
      if($now_hour==0) { $hour = 1;}
      else if(($now_hour==1)&&(in_array($tminute, $lapse))) { $hour = 2;}
      else if($now_hour==1) { $hour = 1;}
      
      else if($now_hour==2) { $hour = 2;}
      else if(($now_hour==3)&&(in_array($tminute, $lapse))) { $hour = 4;}
      else if($now_hour==3) { $hour = 2;}
      
      else if($now_hour==4) { $hour = 4;}
      else if(($now_hour==5)&&(in_array($tminute, $lapse))) { $hour = 6;}
      else if($now_hour==5) { $hour = 4;}
      
      else if($now_hour==6) { $hour = 6;}
      else if(($now_hour==7)&&(in_array($tminute, $lapse))) { $hour = 8;}
      else if($now_hour==7) { $hour = 6;}
      
      else if($now_hour==8) { $hour = 8;}
      else if(($now_hour==9)&&(in_array($tminute, $lapse))) { $hour = 10;}
      else if($now_hour==9) { $hour = 8;}
      
      else if($now_hour==10) { $hour = 10;}
      else if(($now_hour==11)&&(in_array($tminute, $lapse))) { $hour = 12;}
      else if($now_hour==11) { $hour = 10;}
      
      else if($now_hour==12) { $hour = 12;}
      else if(($now_hour==13)&&(in_array($tminute, $lapse))) { $hour = 14;}
      else if($now_hour==13) { $hour = 12;}
      
      else if($now_hour==14) { $hour = 14;}
      else if(($now_hour==15)&&(in_array($tminute, $lapse))) { $hour = 16;}
      else if($now_hour==15) { $hour = 14;}
      
      else if($now_hour==16) { $hour = 16;}
      else if(($now_hour==17)&&(in_array($tminute, $lapse))) { $hour = 18;}
      else if($now_hour==17) { $hour = 16;}
      
      else if($now_hour==18) { $hour = 18;}
      else if(($now_hour==19)&&(in_array($tminute, $lapse))) { $hour = 20;}
      else if($now_hour==19) { $hour = 18;}
      
      else if($now_hour==20) { $hour = 20;}
      else if(($now_hour==21)&&(in_array($tminute, $lapse))) { $hour = 22;}
      else if($now_hour==21) { $hour = 20;}
      
      else if($now_hour==22) { $hour = 22;}
      else if($now_hour==23) { $hour = 22;}
      else if($now_hour==24) { $hour = 1;}
      else {$hour = 22;} 
    } 
    //end copy from block iframe now

    return [
      'month' => $month,
      'day' => $day,
      'hour' => $hour,
      'minute' => 0,
    ];
  }

  /**
  * Prepare body for the template
  */

  public function getDataByRequest($temp, $set = 1, $month = false, $day = false, $hour, $minute) {

    $date = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

    if(!$month) {
      $month = $date->format('n');
    }

    if(!$day) {
      $day = $date->format('j');      
    }

    if($temp == 1 || $temp == 5 || $temp == 9) {
      $hour = intval($hour / 2) * 2;
    }
    else if($temp == 2 || $temp == 6) {
      $hour = intval($hour / 3) * 3;
    }
      

    $parent_set = $this->parentSet($set);  

    $file_cache = $this->getFilePathByRequest($temp, $parent_set, $month, $day, $hour, $minute);

    if (!file_exists($file_cache)) {
      $this->createFileByRequest($temp, $parent_set, $month, $day, $hour, $minute, $file_cache);  
    } 

    $output = file_get_contents($file_cache);
    ob_start();
    require($file_cache);                
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
  }

  /**
  * We would like to have 1 file in cache for all the channel sets for the given month/day/hour/minute
  */

  public function  parentSet($set) {
     switch($set) {
        case 1: 
           return 1;
           
        default: 
           return 1;
     }
  }

  public function  getFilePathByRequest($temp, $set, $month, $day, $hour, $minute) {

    $template = (strlen($temp)<2) ? '0'.$temp:$temp;
    $set      = ($set < 10) ? '0'.$set : $set;
    $month    = (strlen($month)<2) ? '0'.$month:$month;
    $day      = (strlen($day)<2) ? '0'.$day:$day;
    $hour     = (strlen($hour)<2) ? '0'.$hour:$hour;
    $minute   = (strlen($minute)<2) ? '0'.$minute:$minute;
    
    $file_directory_path = 'sites/default/files';     // don't use file_directory_path() for optimize iframe.php cache
    
    $directory = $file_directory_path . '/templates/tv/t' . $template . 's' . $set . 'm' . $month . 'd' . $day;
    switch (intval($temp)) {
      case 1:
      case 2:
      case 5:
      case 6:
      case 9:
        $file_path = $directory . '/tv_'. $month . $day . $hour .'t'. $template .'s'. $set.'.php';
        break;
        
      case 3:
      case 4:
      case 7:
      case 8:
      $file_path = $directory . '/tv_'. $month . $day . $hour . $minute .'t'. $template .'s'. $set.'.php';
        break;
    }
    
    return $file_path;
  }

  public function createFileByRequest($temp, $sets, $month, $day, $hour, $minute=0, $file_cache) {

    $directory = dirname($file_cache);
    
    // check directory exists if not exists directory then create directories
    $files_directory = \Drupal::service('file_system')->realpath(file_build_uri(''));

    if (!is_dir($files_directory . '/templates')) {
      mkdir($files_directory . '/templates',0777);
    }

    if (!is_dir($files_directory . '/templates/tv')) {
      mkdir($files_directory . '/templates/tv',0777);
    }

    if (!is_dir($directory)) {
       if(!mkdir($directory)) {
          drupal_set_message("Could not create directory ($directory)", 'error', FALSE);
          exit;
       }
    }
    
    // Turn off cache for testing
    if (!file_exists($file_cache)) {

      $data = $this->getDataForTemplates($temp, $sets,  $month, $day, $hour, $minute);

      if (!$handle = fopen($file_cache, 'w')) {
         drupal_set_message("Could not open file ($file_cache)", 'error', FALSE);
         exit;
      }

      // write content to file $filename 
      if (fwrite($handle, $data) === FALSE) {
          drupal_set_message("Could not write to file ($file_cache)", 'error', FALSE);
          exit;
      } 

      fclose($handle);
    }
  }

  public function tvmachine_get_image_style_uri($file_id, $style = '53x53') {

    $host = \Drupal::request()->getSchemeAndHttpHost();
    $private_key = \Drupal::service('private_key')->get();
    $salt = Settings::getHashSalt();

    $DbQueryHelper = new DbQueryHelper();
    $uri = $DbQueryHelper->get_file_uri($file_id);

    if ($uri) {

      $token = substr(Crypt::hmacBase64($style.':'.$uri, $private_key . $salt), 0, 8);
      $style_uri = str_replace("public://", $host."/sites/default/files/styles/".$style."/public/", $uri.'?itok='.$token);
      $origin_uri = str_replace("public://", $host."/sites/default/files/", $uri);

      if ($style != '53x53') {

        $system_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://");
        $local_path = str_replace("public://", $system_path."/styles/".$style."/public/", $uri);

        $file_info = @getimagesize($local_path);

        if (!$file_info) { 

          $this->generate_image_style($style, $uri);
          $file_info = @getimagesize($local_path);
        } 

        $image_height = $file_info[1];
        $image_width = $file_info[0];
      } else {
        $image_height = 53;
        $image_width = 53;
      }

      return array(
        'style_uri' => $style_uri,
        'origin_uri' => $origin_uri,
        'height' => $image_height,
        'width' => $image_width,
      );
      
    } else {
      return false;
    }
  }

  public function program_time_to_unix($minute, $hour, $day, $month) {

    //we use UTC timezone for filter, because field_program_start_time is stored in UTC timezone
    $date = new \DateTime(NULL, new \DateTimeZone('UTC')); 

    $current_date = new \DateTime(NULL, new \DateTimeZone('UTC'));
    $current_month = $current_date->format('n');
    if ($current_month == 12 && $month == 1) {
      $date->setDate($date->format('Y')+1, $month, $day);
    } else if ($current_month == 1 && $month == 12) {
      $date->setDate($date->format('Y')-1, $month, $day);
    } else {
      $date->setDate($date->format('Y'), $month, $day);
    }

    $date->setTime($hour,$minute);   
    $unixtime = $date->format('U');

    return $unixtime;
  }

  public function tvmachine_get_cat_arrays() {

	$cat_peliculas = array("Cine", "Cortometrajes");
	$cat_series = array("Miniserie", "Serie", "Telenovela");
	$cat_deportes = array("Deportivo");
	$cat_noticias = array("Actualidad cinematográfica", "Actualidad cultural", "Debate", "Economía", "Informativo", "Meteorológico", "Política", "Actualidad");
	$cat_infantil = array("Animación", "Infantil", "Juvenil");
	$cat_entretenimiento = array("Concierto", "Entretenimiento", "Gastronómico", "Humor", "Late show", "Magia", "Misterio", "Moda", "Musical", "Sketches", "Talk show", "Zapping");
	$cat_documental = array("Agronomía", "Aventura", "Divulgativo", "Documental", "Docu-reality", "Magacín", "Magacín de actualidad", "Magazine", "Medio ambiente", "Naturaleza", "Reportajes", "Salud", "Environnement", "Serie documental", "Turismo", "Cultural", "Educativo", "Bricolaje", "Literatura");
	$cat_concursos = array("Concurso", "Talent Show");
	$cat_corazon = array("Actualidad social", "Actualidad televisiva");
	$cat_reality = array("reality");

    return array(
      'peliculas' => $cat_peliculas,
      'series' => $cat_series, 
      'deportes' => $cat_deportes, 
      'noticias' => $cat_noticias, 
      'infantil' => $cat_infantil, 
      'entretenimiento' => $cat_entretenimiento, 
      'documental' => $cat_documental,
      'concursos' => $cat_concursos,
      'corazon' => $cat_corazon,
      'reality' => $cat_reality,
    );
  }

  public function replace_text_bg_category($category) {

    $cat = $this->tvmachine_get_cat_arrays();

    if (in_array($category, $cat['peliculas']))
    {
      $text_bg_category = 'tvpeliculas peliculas_highlight';
    }
    else if (in_array($category, $cat['series']))
    {
      $text_bg_category = 'tvseries series_highlight';
    }
    else if (in_array($category, $cat['noticias']))
    {
      $text_bg_category = 'tvnoticias';
    }
    else if (in_array($category, $cat['infantil']))
    {
      $text_bg_category = 'tvinfantil infantil_highlight';
    }
    else if (in_array($category, $cat['entretenimiento']))
    {
      $text_bg_category = 'tventretenimiento entretenimiento_highlight';
    }
    else if (in_array($category, $cat['concursos']))
    {
      $text_bg_category = 'tvconcursos';
    }
    else if (in_array($category, $cat['corazon']))
    {
      $text_bg_category = 'tvcorazon';
    }
    else if (in_array($category, $cat['reality']))
    {
      $text_bg_category = 'tvreality';
    }
    else if (in_array($category, $cat['documental']))
    {
      $text_bg_category = 'tvdocumental documental_highlight';
    }
    else if (in_array($category, $cat['deportes']))
    {
      $text_bg_category = 'tvdeportes deportes_highlight';
    }
    else
    {
      $text_bg_category = 'tvprog';
    }

    return $text_bg_category;
  }

  public function replace_text_bg_category2($category) {

    $cat = $this->tvmachine_get_cat_arrays();

    if (in_array($category, $cat['peliculas']))
    {
      $text_bg_category = 'text_bg_peliculas';
    }
    else if (in_array($category, $cat['series']))
    {
      $text_bg_category = 'text_bg_series';
    }
    else if (in_array($category, $cat['noticias']))
    {
      $text_bg_category = 'text_bg_noticias';
    }
    else if (in_array($category, $cat['infantil']))
    {
      $text_bg_category = 'text_bg_infantil';
    }
    else if (in_array($category, $cat['entretenimiento']))
    {
      $text_bg_category = 'text_bg_entretenimiento';
    }
    else if (in_array($category, $cat['concursos']))
    {
      $text_bg_category = 'text_bg_concursos';
    }
    else if (in_array($category, $cat['corazon']))
    {
      $text_bg_category = 'text_bg_corazon';
    }
    else if (in_array($category, $cat['reality']))
    {
      $text_bg_category = 'text_bg_reality';
    }
    else if (in_array($category, $cat['documental']))
    {
      $text_bg_category = 'text_bg_documental';
    }
    else if (in_array($category, $cat['deportes']))
    {
      $text_bg_category = 'text_bg_deportes';
    }
    else
    {
      $text_bg_category = 'text_bg_prog';
    } 

    return $text_bg_category;
  }

  public function next_prev_buttons($tmonth, $tday, $thour) {

    $days_list = array(
      -3 => array(
        'month' => date("n", strtotime("-3 days")),
        'day' => date("j", strtotime("-3 days")),
      ),
      -2 => array(
        'month' => date("n", strtotime("-2 days")),
        'day' => date("j", strtotime("-2 days")),
      ),
      -1 => array(
        'month' => date("n", strtotime("-1 day")),
        'day' => date("j", strtotime("-1 day")),
      ),
      0 => array(
        'month' => date("n"),
        'day' => date("j"),
      ),
      1 => array(
        'month' => date("n", strtotime("+1 day")),
        'day' => date("j", strtotime("+1 day")),
      ),
      2 => array(
        'month' => date("n", strtotime("+2 days")),
        'day' => date("j", strtotime("+2 days")),
      ),
      3 => array(
        'month' => date("n", strtotime("+3 days")),
        'day' => date("j", strtotime("+3 days")),
      ),
    );

    $hours  = array(1,2,4,6,8,10,12,14,16,18,20,22);

    foreach ($hours as $hour_key => $hour) {
      foreach ($days_list as $day_key => $day) {
        if (($tmonth == $day['month']) && ($tday == $day['day']) && ($thour == $hour)) {

          $prev_hour_str = ($hour-2) < 10 ? '0'.($hour-2) : ($hour-2);
          $hour_str = $hour < 10 ? '0'.$hour : $hour;
          $next_hour_str = ($hour+2) < 10 ? '0'.($hour+2) : ($hour+2);
          $after_next_hour_str = ($hour+4) < 10 ? '0'.($hour+4) : ($hour+4);

          $tag_prev = $prev_hour_str."-".$hour_str; 
          $month_prev = $day['month']; 
          $day_prev = $day['day']; 
          $hour_prev = $hour - 2; 

          $tag_next = $next_hour_str."-".$after_next_hour_str; 
          $month_next = $day['month'];
          $day_next = $day['day']; 
          $hour_next = $hour + 2;

          if ($hour == 1) {

            if ($day_key == -2) {  
              // First button day
              $tag_prev = "empty"; 
              $month_prev = null;  
              $day_prev = null; 
              $hour_prev="empty";
            } else {
              $tag_prev = "22-24"; 
              $month_prev = $days_list[$day_key-1]['month']; 
              $day_prev = $days_list[$day_key-1]['day']; 
              $hour_prev="22";
            }

            $tag_next = "02-04"; 
            $month_next = $day['month']; 
            $day_next = $day['day']; 
            $hour_next = 2;

          } else if ($hour == 2) {

            $tag_prev = "00-02"; 
            $month_prev = $days_list[$day_key-1]['month']; 
            $day_prev = $days_list[$day_key-1]['day']; 
            $hour_prev="1";

          } else if ($hour == 22) {

            if ($day_key == 2) {
              $tag_next = "empty";
              $month_next = null;
              $day_next = null; 
              $hour_next = "empty";
            } else {
              $tag_next = "00-02"; 
              $month_next = $days_list[$day_key+1]['month']; 
              $day_next = $days_list[$day_key+1]['day']; 
              $hour_next = 1;
            }

          }         
        }
      }
    }

    return array(
      'tag_prev' => $tag_prev,
      'month_prev' => $month_prev,
      'day_prev' => $day_prev,
      'hour_prev' => $hour_prev,
      'tag_next' => $tag_next,
      'month_next' => $month_next,
      'day_next' => $day_next,
      'hour_next' => $hour_next,
    );
  }

  public function getDataForTemplates($temp, $set,  $month, $day, $hour, $minute) {

    $DbQueryHelper = new DbQueryHelper;

    if($temp == 1 || $temp == 2 || $temp == 5 || $temp == 6 || $temp == 9) {$template_display = 'grid';}
    if($temp == 3 || $temp == 4 || $temp == 7 || $temp == 8) {$template_display = 'list';}

    // Getting channels data
    $channels_data_arr = $DbQueryHelper->get_channels();

    // Getting channels data end

    if ($template_display == 'grid') {

      $host = \Drupal::request()->getSchemeAndHttpHost();
      $private_key = \Drupal::service('private_key')->get();
      $salt = Settings::getHashSalt();
    
      $sets_node = \Drupal\node\Entity\Node::load($set);
      $field_sets_channels = $sets_node->get('field_sets_channels')->getValue(); 

      // assign preoder
      $set_channels = array();
      $start_time_arr = array();

      foreach ($field_sets_channels as $channel) {
        $set_channels[$channel['target_id']] = array();
        $start_time_arr[$channel['target_id']] = array();
      }

      // Calculate time limits
      $time_limits = $this->get_time_limits($month, $day, $hour, $minute, $temp );
      $time_limit_min = $time_limits['min'];
      $time_limit_max = $time_limits['max'];

      // Getting programs data
      $output = $DbQueryHelper->get_programs($time_limit_min, $time_limit_max);

      // If we have the programs
      if ($output) {

        $programs_paths = array();

        foreach ($output as $key => $value) {
          
          $program_nid  = $value->nid;
          $channel_nid  = $value->field_program_channel_target_id;
          $start_time = $value->field_program_start_time_value;
          $title = $value->title;
          $category = $value->field_program_category_value;

          //  Сollect paths of nodes in order to get aliases
          $programs_paths[] = '/node/'.$program_nid;

          // New way to get image path
          if (isset($value->field_program_image_upload_target_id)) {
            $image_id = $value->field_program_image_upload_target_id;
            $image_uri = $value->uri;
            $token = substr(Crypt::hmacBase64('53x53:'.$image_uri, $private_key . $salt), 0, 8);
            $style_uri = str_replace("public://", $host."/sites/default/files/styles/53x53/public/", $image_uri.'?itok='.$token);
            
          } else {
            $image_id = false;
            $image_uri = false;
            $style_uri = false;
          }
          // New way to get image path end       
          
          // Old way to get image path
          /*if ($image_id) {

            $file = \Drupal\file\Entity\File::load($image_id);
            $image = \Drupal::service('image.factory')->get($file->getFileUri());

            if ($image->isValid()) {
              $style_uri = ImageStyle::load('53x53')->buildUrl($file->getFileUri());

            } else {
              $style_uri = false;
            }
          } else {
            $style_uri = false;
          }*/
          // Old way to get image path end

          $date = new \DateTime(null, new \DateTimeZone('UTC'));
          $date->setTimestamp($start_time);

          $program_year = $date->format('Y');
          $program_month = $date->format('n');
          $program_day = $date->format('j');
          $program_hour = $date->format('G');
          $program_minutes = intval($date->format('i'));

          // if there is already program for that channel and time then skip it, to avoid duplicates
          if (isset($start_time_arr[$channel_nid][$start_time])) {
            continue;
          }
          $start_time_arr[$channel_nid][$start_time] = 1;

          // Here we using date in order to get number of the current day of the year (from 1 to 366). We using this value in order to display programs in the table
          $date = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));
          $date->setDate($date->format('Y'), $program_month, $program_day);
          $date->setTime(0,0); 

          $offset_day  = $date->format('z') + 1;
          $offset_hour = $offset_day * 24 + $program_hour;
          $offset_time = $offset_hour * 60 + $program_minutes;

          // New year fix start
          $current_date = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default'))); 

          $current_month = $current_date->format('n');
          if (($current_month == 12 && $program_month == 1 && $month == 12) || ($current_month == 1 && $program_month == 1 && $month == 12)) {
            continue;
          } 
          // New year fix end

          $set_channels[$channel_nid][] = array(
            'nid' => $program_nid,
            'nid_1' => $channel_nid, 
            'time' => $offset_time, 
            'dayY' => $offset_day,
            'field_program_month' => $program_month, 
            'field_program_day' => $program_day,
            'field_program_hour' => $program_hour, 
            'field_program_minutes' => $program_minutes, 
            'field_program_start_time' => $start_time,
            'field_program_image_upload' => $style_uri,
            'title' => $title,
            'field_program_category' => $category,
          );
        }

        // getting aliases of nodes
        $aliases_arr = $DbQueryHelper->get_aliases_by_paths($programs_paths);
        // getting aliases of nodes end

        $date =  new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

        // Here we using date in order to filter programs ($arg['hourstart'] using as filter, see $start_time as well). In order to display programs that included in the curent time frame or very close to this time frame
        $date->setDate($date->format('Y'), intval($month), intval($day));
        $date->setTime(0,0); 

        $arg = array();
        $arg['month']  = intval($month);
        $arg['dayM'] = intval($day);
        $arg['template']   = $temp;
        $arg['sets']       = $set;
        $arg['year'] = $date->format('Y');
        $arg['dayY'] = $date->format('z') + 1;    
        $arg['hour']       = $hour;    
        $arg['hourstart']  = intval($arg['dayY']) * 24 + $hour;
        $arg['minute']     = $minute;


        $start_time = intval($arg['hourstart']) * 60; // convert into minutes

        if($temp == '1' || $temp == '5' || $temp == '9') {
         $end_time = $start_time + 120;
        }
        else if($temp == '2' || $temp == '6') {
         $end_time = $start_time + 180;
        }

        foreach ($set_channels as $channel_nid => $channel)  // placement of programs and channels in order of time
        {

          foreach ($channel as $i => $row)
          {   

            $start = $row['time'];
            $arrow = '';
            
            if ($row['time'] < $start_time) // compare to start_time
            {
              if (isset($channel[$i + 1])) {
                $row_next = $channel[$i + 1];
                if (isset($row_next['time']) && (intval($row_next['time']) <= $start_time)) {
                   continue;
                }
                $arrow = 'left';
              } else {
                $row_next = NULL;
              }
            }
            else if ($row['time'] >= $start_time && $row['time'] < $end_time) // compare to start_time - end_time
            {
              if (isset($channel[$i + 1])) {
                $row_next = $channel[$i + 1];
              } else {
                $row_next = NULL;
              }        
            }
            else 
            {
              continue;
            }

            if (!$row_next) {

              $row_next['time'] = $end_time; //24 * 60; // maximum time
              $arrow = 'right';

            } else {

              if ($row_next['time'] > $end_time) {
                $row_next['time'] = $end_time;
                $arrow = 'right';
              } else {
                if ($row_next['time'] - $row['time']< 15) {
                  $arrow = 'plus';
                }
              }
            }

            // all cases
            $c[$channel_nid][$i] = $row + array(
              'start' => $start,
              'end' => $row_next['time'],
              'arrow' => $arrow,
              'space' =>  ($row_next['time'] - ($start_time > $start ? $start_time : $start)),
            );
          }
        }

        if(!isset($c)){
          $vars['custom_rows'] = [];
        } else {
          $vars['custom_rows'] = $this->prepare_templates_grid($c, $arg, $start_time, $end_time, $channels_data_arr, $aliases_arr);
        }
      } else {
        // If we haven't any programs
        $vars['custom_rows'] = [];
      }
    }

    if ($template_display == 'list') {

      $base_url = \Drupal::request()->getSchemeAndHttpHost();
    
      $sets_node = \Drupal\node\Entity\Node::load($set);
      $field_sets_channels = $sets_node->get('field_sets_channels')->getValue();

      // assign preoder
      $set_channels = array();

      foreach ($field_sets_channels as $channel) {
        $set_channels[$channel['target_id']] = array();
      }

      // Calculate time limits
      $time_limits = $this->get_time_limits($month, $day, $hour, $minute, $temp);
      $time_limit_min = $time_limits['min'];
      $time_limit_max = $time_limits['max'];

      $output = $DbQueryHelper->get_programs($time_limit_min, $time_limit_max, true);

      if ($output) {

        $programs_paths = array();

        foreach ($output as $key => $value) {
          
          $program_nid  = $value->nid;
          $channel_nid  = $value->field_program_channel_target_id;
          $start_time = $value->field_program_start_time_value;
          $title = $value->title;

          //  Сollect paths of nodes in order to get aliases
          $programs_paths[] = '/node/'.$program_nid;

          $date = new \DateTime(null, new \DateTimeZone('UTC'));
          $date->setTimestamp($start_time);

          $program_year = $date->format('Y');
          $program_month = $date->format('n');
          $program_day = $date->format('j');
          $program_hour = $date->format('G');
          $program_minutes = intval($date->format('i'));

          // Here we using date in order to get number of the current day of the year (from 1 to 366). We using this value in order to dusplay programs in the table
          $date = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));
          $date->setDate($date->format('Y'), $program_month, $program_day);
          $date->setTime($program_hour,$program_minutes); 

          $set_channels[$channel_nid][] = array(
            'nid' => $program_nid,
            'nid_1' => $channel_nid, 
            'timestart' => $date->format('U'),
            'title' => $title,
            'field_program_month' => $program_month, 
            'field_program_day' => $program_day,
            'field_program_hour' => $program_hour, 
            'field_program_minutes' => $program_minutes, 
          );
        }

        // getting aliases of nodes
        $aliases_arr = $DbQueryHelper->get_aliases_by_paths($programs_paths);

        $date =  new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

        // Here we using date in order to filter programs ($arg['hourstart'] using as filter, see $start_time as well). In order to display programs that included in the curent time frame or very close to this time frame
        $date->setDate($date->format('Y'), intval($month), intval($day));
        $date->setTime(0,0); 

        $arg = array();
        $arg['template']   = $temp;
        $arg['sets']       = $set;
        $arg['month']  = intval($month);
        $arg['dayM'] = intval($day);
        $arg['year'] = $date->format('Y');
        $arg['dayY'] = $date->format('z') + 1;    
        $arg['hour']       = $hour;    
        $arg['hourstart']  = intval($arg['dayY']) * 24 + $hour;
        $arg['minute']     = $minute;
        /*if (isset($view->args[6])) {$arg['color1'] = $view->args[6];}
        if (isset($view->args[7])) {$arg['color2'] = $view->args[7];}
        if (isset($view->args[8])) {$arg['color3'] = $view->args[8];}
        if (isset($view->args[9])) {$arg['color4'] = $view->args[9];}*/

        foreach ($set_channels as $cid => $channel)  // placement of programs and channels in order of time
        {
          $count = count($channel);
          foreach ($channel as $key => $program)
          {

            if ($key+1 == $count) {
              $next_program = NULL;
            }
            else {
              $next_program = $channel[$key+1];
            }

            $date = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

            if (!$next_program) {
              $date->setDate($date->format('Y'), $program['field_program_month'], $program['field_program_day']);
              $date->setTime(strtotime($program['field_program_hour']),120); 

              $set_channels[$cid][$key]['timeend'] = null;
            } 
            else {           
              $date->setDate($date->format('Y'), $next_program['field_program_month'], $next_program['field_program_day']);
              $date->setTime((int)$next_program['field_program_hour'], (int)$next_program['field_program_minutes']); 

              $set_channels[$cid][$key]['timeend'] = $date->format('U');
            }
          }
        }

        $date = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

        $date->setDate($date->format('Y'), $arg['month'], $arg['dayM']);
        $date->setTime($arg['hour'], $arg['minute']); 

        $timeURL = $date->format('U');

        if(count($set_channels) == 0) {
          $vars['custom_rows'] = [];
        } else {
          $vars['custom_rows'] = $this->prepare_templates_list($set_channels, $arg, $timeURL, $base_url, $channels_data_arr, $aliases_arr);
        }
      } else {
        // No results
        $vars['custom_rows'] = [];
      }
    }

    $result = '';

    foreach ($vars['custom_rows'] as $key => $value) {
      if ($template_display == 'grid') {$result .= $value['wrapper_before'];}
      $result .= $value['body'];
      if ($template_display == 'grid') {$result .= $value['wrapper_after'];}
    }

    return $result;
  }

  public function prepare_templates_grid(&$c, $arg, $start_time, $end_time,$channels_data_arr, $aliases_arr) {

    $host = \Drupal::request()->getSchemeAndHttpHost();

    $target = "_blank";
    if ($arg['template'] == 6 || $arg['template'] == 5) {
      $target = "_top";
    }

    $count = 0;
    $rows = [];

    $current_path = \Drupal::service('path.current')->getPath();
    $path_args = explode('/', $current_path);

    foreach($c as $channel_nid => $channel) {

      $rows[$channel_nid]['body'] = '';

      $rows[$channel_nid]['wrapper_before'] = "<div id='channel-{$channel_nid}'>\n";

      // get channel path

      $channel_path = $channels_data_arr[$channel_nid]['channels_url_prog'];
      if ($arg['template'] == 5) {
        $channel_path = $channels_data_arr[$channel_nid]['channels_url_prog_mobile'];
      }

      $channel_title = $channels_data_arr[$channel_nid]['title'];
      // get channel logo
      $wrapper_before = '<div class="tvlogo"><a href="/'.$channel_path.'" target="'.$target.'" alt="'. $channel_title .'"><div class="tvlogo'.$channel_nid.'"> </div></a></div>';

      if ($arg['template'] == 9) {
        $wrapper_before = '<div class="tvlogo"><div class="tvlogo'.$channel_nid.'"> </div></div>';
      }

      $rows[$channel_nid]['wrapper_before'] .= $wrapper_before;

      foreach ($channel as $key => $program) {
        
        if($arg['template'] == '1') {
          $width_b = ((intval($program['time']) - intval($start_time)) * 4) - 3;
          $width = ((intval($program['space']) * 4) - 3);
        }   
        else if($arg['template'] == '5') {
          $width_b = ((intval($program['time']) - intval($start_time)) * 4) - 3; // size big 6) -3;
          $width = ((intval($program['space']) * 4) - 3); // size big 6) -3;
        }
        else if($arg['template'] == '9') {
          $width_b = ((intval($program['time']) - intval($start_time)) * 4) - 3; // size big 6) -3;
          $width = ((intval($program['space']) * 4) - 3); // size big 6) -3;
        }   
        else if($arg['template'] == '2') {
          $width_b = ((intval($program['time']) - intval($start_time)) * 4) - 3;
          $width = ((intval($program['space']) * 4) - 3);
        }
        else if($arg['template'] == '6') {
          $width_b = ((intval($program['time']) - intval($start_time)) * 4) - 3;
          $width = ((intval($program['space']) * 4) - 3);
        }
          
        //ADD THUMBNAILS 

      if (($arg['template'] == '6' || $arg['template'] == '5') && $program['field_program_image_upload'] && ($program['field_program_category'] == 'Cine'))
        {

          $_thumbnail = '<img data-src="'.$program['field_program_image_upload'].'" alt="" title="'.$program['title'].'" width="53" height="53" class="lazyload progr_thumb" />';
       
        }
        else {
          $_thumbnail = "";
        }
        
        $thumbnail = '';
        if($width >= 195) {
          $thumbnail = $_thumbnail;
        }

        $category  = $this->replace_text_bg_category($program['field_program_category']);

        $hour = intval($program['field_program_hour']);
        $minute = intval($program['field_program_minutes']);

        $program_time = $hour*60 + $minute;
        $day = $program['field_program_day'];
        $month = $program['field_program_month'];
        $channelid = $program['nid_1'];
        
        $color2 = "MyIframe['color2']";
        $color4 = "MyIframe['color4']";
        $paramStr = "{$arg['template']},{$arg['sets']},$month,$day,$hour,$minute,$channelid,$color2,$color4";
           
           
        // if request url is not 'program/content/' then create "back to listing" url
        
        /*if(!($path_args['1'] == 'program' && $path_args['2'] == 'content')) {

           $listing_url = Url::fromUserInput('/programacion-tv/programacion-tv-ahora', array(
                 'absolute' => TRUE,
                 'query' => array(
                    'set' => $arg['sets'],
                    'm' => $arg['month'],
                    'd' => $arg['dayM'],
                    'h' => $arg['hour'],
                    'i' => $arg['minute'],
                 )
              )
           )->toString();

        }*/
            
        // Comment this to disable target="_blank" for templates 1,2
        
        if($arg['template'] == '1' || $arg['template'] == '2') {

           //$href = Url::fromUserInput('/node/'. $program['nid'], array('absolute' => TRUE))->toString();
           $href = $host.$aliases_arr['/node/'. $program['nid']];
        }
        else if($arg['template'] == '5'){
           $href = "/television/tv-serie-cine3/{$arg['template']}/{$arg['sets']}/$month/$day/$hour/$minute/$channelid";
           if(!empty($arg['color2'])) {
           $href .= "/". $arg['color2'];
           if(!empty($arg['color4'])) {
           $href .= "/". $arg['color4'];
              }
           }

           //$href = Url::fromUserInput($url, array('absolute' => FALSE))->toString();
        }
        else if($arg['template'] == '9'){
           $href = "/television/tv-serie-cine4/{$arg['template']}/{$arg['sets']}/$month/$day/$hour/$minute/$channelid";
           if(!empty($arg['color2'])) {
           $href .= "/". $arg['color2'];
           if(!empty($arg['color4'])) {
           $href .= "/". $arg['color4'];
              }
           }

           //$href = Url::fromUserInput($url, array('absolute' => TRUE))->toString();
        }
        else {
           $href = "/television/tv-serie-cine/{$arg['template']}/{$arg['sets']}/$month/$day/$hour/$minute/$channelid";
           if(!empty($arg['color2'])) {
           $href .= "/". $arg['color2'];
           if(!empty($arg['color4'])) {
           $href .= "/". $arg['color4'];
              }
           }

           //$href = Url::fromUserInput($url, array('absolute' => FALSE))->toString();
        }
        
        if($program['field_program_minutes']<=9) $minute = str_pad((int) $minute,2,"0",STR_PAD_LEFT);
        if($program['start'] < $start_time)
        {

          if($program['space'] < 15) {    
             if($arg['template'] == '5' || $arg['template'] == '9') {
               $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'"><div class="'.$category.'" style="width: '.$width.'px"><span class="tvarrow"></span><span class="tvplus">+</span></div></a>';

            }
            else if($arg['template'] == '6') {
               $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width: '.$width.'px"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#0000a0\', BORDERCOLOR, \'#0000a0\');" ><span class="tvarrow">&#9668;</span><span class="tvplus">+</span></a></div>';
            }                  
            // Comment this to disable target="_blank" for templates 1,2
            else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {
              $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width: '.$width.'px"><a target="_blank" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');" ><span class="tvarrow">&#9668;</span><span class="tvplus">+</span></a></div>';
            }
            else {
               $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width: '.$width.'px"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');" ><span class="tvarrow">&#9668;</span><span class="tvplus">+</span></a></div>';
            }
          }
          else {
             if($arg['template'] == '5' || $arg['template'] == '9') {
              $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'"><div class="'.$category.'" style="width:'.$width.'px;">'.$thumbnail.'<span class="tvarrow"></span><span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </div></a>';
            }
            else if($arg['template'] == '6') {

              $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvarrow">&#9668;</span><span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';

            }
            // Comment this to disable target="_blank" for templates 1,2
            else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {
               $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a target="_blank" href="'.$href.'">'.$thumbnail.'<span class="tvarrow">&#9668;</span><span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
            }
            
            else {
               $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvarrow">&#9668;</span><span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
            }
          }
        }
        else if($program['start'] > $start_time)
        {
          if($program['start'] >= $end_time)
          {
            $rows[$channel_nid]['body'] .= '<div class="tvprog" style="width:595px;"></div>';
          }
          else
          {          
            if($program['end'] < $end_time) {
            
              if($program['space'] < 15) {

                if(!isset($channel[$key-1])) {

                   if($width_b > 0) {

                     if($arg['template'] == '5' || $arg['template'] == '9') {
                        $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'" ><div class="tvprog" style="width:'.$width_b.'px">&nbsp;</div><div class="'.$category.'" style="width:'.$width.'px;"><span class="tvplus">+</span></div></a>';
                     }
                     else if($arg['template'] == '6') {

                      $rows[$channel_nid]['body'] .= '<div class="tvprog" style="width:'.$width_b.'px">&nbsp;</div><div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#0000a0\', BORDERCOLOR, \'#0000a0\');" ><span class="tvplus">+</span></a></div>';
                   }  
                   // Comment this to disable target="_blank" for templates 1,2
                   else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {  

                           $rows[$channel_nid]['body'] .= '<div class="tvprog" style="width:'.$width_b.'px">&nbsp;</div><div class="'.$category.'" style="width:'.$width.'px;"><a target="_blank"  href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');" ><span class="tvplus">+</span></a></div>';
                     }
                     
                      else {    

                           $rows[$channel_nid]['body'] .= '<div class="tvprog" style="width:'.$width_b.'px">&nbsp;</div><div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');" ><span class="tvplus">+</span></a></div>';
                     }
                     
                  } 
                     
                }
                else
                {   

                    if($arg['template'] == '5' || $arg['template'] == '9') {
                        $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'" ><div class="'.$category.'" style="width:'.$width.'px;"><span class="tvplus">+</span></div></a>';
                    }
                    else if($arg['template'] == '6') {
                       $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#0000a0\', BORDERCOLOR, \'#0000a0\');" ><span class="tvplus">+</span></a></div>';
                 }
                   // Comment this to disable target="_blank" for templates 1,2
                   else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {
                      $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a target="_blank" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');" ><span class="tvplus">+</span></a></div>';      
                 
                   }
                    else {
                     $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');" ><span class="tvplus">+</span></a></div>';        }
                }
              }
              else
              {
                if(!isset($channel[$key-1]))
                {

                  $rows[$channel_nid]['body'] .= '<div class="tvprog" style="width:'.$width_b.'px;"></div>';
                  
                  if($arg['template'] == '5' || $arg['template'] == '9') {
                       $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'"><div class="'.$category.'" style="width:'.$width.'px;">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </div></a>';
                  } 
                  else if($arg['template'] == '6') {
                       $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
                  } 
                  // Comment this to disable target="_blank" for templates 1,2
                  else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {
                     $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a target="_blank" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
                  }
                  
                  else {      
                     $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
                  }
                }
                else
                {

                   if($arg['template'] == '5' || $arg['template'] == '9') {     
                    $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'"><div class="'.$category.'" style="width:'.$width.'px;">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </div></a>';
                    }
                   else if($arg['template'] == '6') {  
    
                    $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
                    }
                   // Comment this to disable target="_blank" for templates 1,2
                    else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {
                     $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a target="_blank" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';    
                 
                   }
                  else {    
                     $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
                  }
                }
              }
            }
            else // $program['end'] >= $end_time
            {
              if(!isset($channel[$key-1])) {
                if(($width_b > 0)||($program['start'] - $start_time == 1)) {
                  // Egor fix
                  $rows[$channel_nid]['body'] .= '<div class="tvprog" style="width:'.$width_b.'px;"></div>';
                }
              }

              if(intval($end_time) - (intval($program['start'])) < 15) {
     
                 if($arg['template'] == '5' || $arg['template'] == '9') {
                   $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'" ><div class="'.$category.'" style="width:'.$width.'px"><span class="tvplus">+</span> <span class="tvarrow"></span></div></a>';
                 }
                 else if($arg['template'] == '6') {
                    $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#0000a0\', BORDERCOLOR, \'#0000a0\');" ><span class="tvplus">+</span> <span class="tvarrow">&#9658;</span></a></div>';
                 }
                 // Comment this to disable target="_blank" for templates 1,2
                 else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {
                   $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px"><a target="_blank" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');"><span class="tvplus">+</span> <span class="tvarrow">&#9658;</span></a></div>';

                 }
                 
                 else {
                   $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');"><span class="tvplus">+</span> <span class="tvarrow">&#9658;</span></a></div>';

                 }
              }
              else {
                if($arg['template'] == '5' || $arg['template'] == '9') {  
                   $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'"><div class="'.$category.'" style="width:'.$width.'px;">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span><span class="tvarrow"></span> '.$program['title'].' </div></a>';

                }
                else if($arg['template'] == '6') {  
                   $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span><span class="tvarrow">&#9658;</span> '.$program['title'].' </a></div>';

                }
                // Comment this to disable target="_blank" for templates 1,2
                else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {
                   $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a target="_blank" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span><span class="tvarrow">&#9658;</span> '.$program['title'].' </a></div>';

                }
               
                else {    
                  $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span><span class="tvarrow">&#9658;</span> '.$program['title'].' </a></div>';

                }
                
              }
            }
          }
        }
        else// if($program['start'] = $start_time)
        {

          if($program['space'] < 15) {      
            if($arg['template'] == '5' || $arg['template'] == '9') {
               $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'" ><div class="'.$category.'" style="width:'.$width.'px;"><span class="tvarrow"></span><span class="tvplus">+</span></div></a>';

             }
             else if($arg['template'] == '6') {
                 $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#0000a0\', BORDERCOLOR, \'#0000a0\');" ><span class="tvarrow">&#9668;</span><span class="tvplus">+</span></a></div>';

             }
             // Comment this to disable target="_blank" for templates 1,2
             else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {              
                $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a target="_blank" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');"><span class="tvarrow">&#9668;</span><span class="tvplus">+</span></a></div>';

             }
             
             else {
               $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'" onmouseout="UnTip()" onmouseover="Tip(\'&lt;strong&gt;'.$hour.':'.$minute.'&lt;/strong&gt;&nbsp;'.str_replace("'", "\'", $program['title']).'\', WIDTH, 300, BGCOLOR, \'#ffffff\', FONTCOLOR, \'#111111\', BORDERCOLOR, \'#111111\');"><span class="tvarrow">&#9668;</span><span class="tvplus">+</span></a></div>';

            }
          }
          else {

            if($arg['template'] == '5' || $arg['template'] == '9') {  
               $rows[$channel_nid]['body'] .= '<a target="_top" href="'.$href.'"><div class="'.$category.'" style="width:'.$width.'px;">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </div></a>';

            }
            else if($arg['template'] == '6') {  
               $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';

            }
            // Comment this to disable target="_blank" for templates 1,2
            else if($arg['template'] == '500' || $arg['template'] == '1' || $arg['template'] == '2') {
               $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a target="_blank" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
            }
            
            else {
               $rows[$channel_nid]['body'] .= '<div class="'.$category.'" style="width:'.$width.'px;"><a class="a-details" ch="'.$channel_nid.'" p="'. $program['nid'] .'" href="'.$href.'">'.$thumbnail.'<span class="tvhour">'.$hour.':'.$minute.'</span> '.$program['title'].' </a></div>';
            }
          }
        }
      }
      
      $rows[$channel_nid]['wrapper_after'] = '<br style="clear:both;" />';
      $rows[$channel_nid]['wrapper_after'] .= "</div>\n"; // of id='channel-$channel_nid'

    if(($channel_nid == '56') && ($arg['template'] == '5') && ($arg['dayM'] == '21' ||$arg['dayM'] == '22' || $arg['dayM'] == '23' || $arg['dayM'] == '24' || $arg['dayM'] == '25')) { 

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Bloque de 336 x 280 pixeles -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-6412530744130865"
             data-ad-slot="1966106009"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';}

    else if (($channel_nid == '56') && ($arg['template'] == '5') && ($arg['dayM'] == '26' || $arg['dayM'] == '27')) {  

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- OneData 336x280 -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-8975649173389036"
             data-ad-slot="8148047069"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';}

    else if (($channel_nid == '56') && ($arg['template'] == '5')) {  

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- TV es mobile in 336x280 -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-8975649173389036"
             data-ad-slot="6018864129"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';
    }

    if(($channel_nid == '68') && ($arg['template'] == '5') && ($arg['dayM'] == '21' ||$arg['dayM'] == '22' || $arg['dayM'] == '23' || $arg['dayM'] == '24' || $arg['dayM'] == '25')) {  

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Bloque de 336 x 280 pixeles -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-6412530744130865"
             data-ad-slot="1966106009"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';}

    else if (($channel_nid == '68') && ($arg['template'] == '5') && ($arg['dayM'] == '26' || $arg['dayM'] == '27')) {  

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- OneData 336x280 -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-8975649173389036"
             data-ad-slot="8148047069"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';}

    else if (($channel_nid == '68') && ($arg['template'] == '5')) {  

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- TV es mobile in 336x280 -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-8975649173389036"
             data-ad-slot="6018864129"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';}

    if(($channel_nid == '80') && ($arg['template'] == '5') && ($arg['dayM'] == '21' ||$arg['dayM'] == '22' || $arg['dayM'] == '23' || $arg['dayM'] == '24' || $arg['dayM'] == '25')) {  

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- Bloque de 336 x 280 pixeles -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-6412530744130865"
             data-ad-slot="1966106009"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';}

    else if (($channel_nid == '80') && ($arg['template'] == '5') && ($arg['dayM'] == '26' || $arg['dayM'] == '27')) {  

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- OneData 336x280 -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-8975649173389036"
             data-ad-slot="8148047069"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';}

    else if (($channel_nid == '80') && ($arg['template'] == '5')) {  

      $rows[$channel_nid]['wrapper_after'] .= '<div class="tvadvertising">
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <!-- TV es mobile in 2 336x280 -->
        <ins class="adsbygoogle"
             style="display:inline-block;width:336px;height:280px"
             data-ad-client="ca-pub-8975649173389036"
             data-ad-slot="5750238539"></ins>
        <script>
             (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
      </div><br style="clear:both;" />';
    }

    $count++;
  }

    // Sorting channels like on the Set
    $node = \Drupal\node\Entity\Node::load(1);
    $channels_weights = $node->get('field_sets_channels')->getValue();

    $sorted_channels = array();

    foreach ($channels_weights as $key => $value) {
      $channel_id = $value['target_id'];

      if (isset($rows[$channel_id])) {

        $sorted_rows[] = $rows[$channel_id];
      }
    }

    return $sorted_rows;
  }

  public function prepare_templates_list(&$a, $arg, $timeURL, $base_url, $channels_data_arr, $aliases_arr) {

    $target = "_blank";
    if ($arg['template'] == 6 || $arg['template'] == 5) {
      $target = "_top";
    }

    $rows = [];
    $channels_and_programs = []; // prevent duplicates

    foreach($a as $channel_nid => $channel)
    {

      $rows[$channel_nid]['body'] = '';

      foreach($channel as $count => $program)
      {
         
        if (!isset($channels_and_programs[$channel_nid])) {
         
			$check = is_null($program['timeend']) || ($program['timeend'] > $timeURL) && ($program['timestart'] <= $timeURL);

			if($check) {

			  $channels_and_programs[$channel_nid] = 1;

			  $node_pathauto = $aliases_arr['/node/'. $program['nid']];
			  
			  $template = $arg['template'];
			  $sets       = $arg['sets'];
			  $month    = $program['field_program_month'];
			  $day      = $program['field_program_day'];
			  $title    = $program['title'];
			  $hour     = $program['field_program_hour'];
			  $minute   = $program['field_program_minutes'];
			  $channelid  = $program['nid_1'];
			  
			  if($arg['template'] == 4 || $arg['template'] == 8) {

				 if(isset($channel[$count+1])) {

				   $node_pathauto_2 = $aliases_arr['/node/'. $channel[$count+1]['nid']];

				   $hour_1 = str_pad((int) $channel[$count+1]['field_program_hour'],2,"0",STR_PAD_LEFT);
				   $minute_1 = str_pad((int) $channel[$count+1]['field_program_minutes'],2,"0",STR_PAD_LEFT);
				   $title_1 = $channel[$count+1]['title'];
				 }
			  }
			  
			  if($arg['template'] == 3 || $arg['template'] == 4 || $arg['template'] == 8) {            
			  
				$rows[$channel_nid]['body'] .= '<div id="channel-'.$channelid.'" style="height:30px; background-color:#FFFFFF;">';

			  } else if($arg['template'] == 7) { 
			  
				$rows[$channel_nid]['body'] .= '<div id="channel-'.$channelid.'" style="background-color:#FFFFFF;" class="channel-poll">';
			  } 
				 
			  $channel_path = $channels_data_arr[$channel_nid]['channels_url_prog'];

			  $rows[$channel_nid]['body'] .= '<div class="tvlogo">
				<a href="/'.$channel_path.'" target="'.$target.'"><div class="tvlogo'.$channelid.'">
				</div></a>
			  </div>';        
		
			  if($arg['template'] == 3 || $arg['template'] == 4) { 

				$rows[$channel_nid]['body'] .= '<div class="tvprog">
				  <a href="'.$node_pathauto.'" target="_top"><!-- target="_blank" onclick="top.location.href=this.href;return false;" -->
					<strong>'.$hour.':'.str_pad((int) $minute,2,"0",STR_PAD_LEFT).'</strong>&nbsp;'.$title.'
				  </a>
				</div>';
				
			  } else if($arg['template'] == 7) {

			  $rows[$channel_nid]['body'] .= '<div class="tvprog">
					<strong>'.$hour.':'. str_pad((int) $minute,2,"0",STR_PAD_LEFT).'</strong>&nbsp;'.$title.'
				</div>';
						
			  } else if($arg['template'] == 8) {

				$rows[$channel_nid]['body'] .= '<div class="tvprog">
				  <a href="'.$node_pathauto.'" target="_top"><!-- target="_blank" onclick="top.location.href=this.href;return false;" -->
					<strong>'.$hour.':'. str_pad((int) $minute,2,"0",STR_PAD_LEFT).'</strong>&nbsp;'.$title.'
				  </a>
				</div>';         
			  }

			  if($arg['template'] == 4) { 

				$rows[$channel_nid]['body'] .= '<div class="tvprog">';

				  if(isset($channel[$count+1])):

					$rows[$channel_nid]['body'] .= '<a href = "'.$node_pathauto_2.'" target="_top"><!-- target="_blank" onclick="top.location.href=this.href;return false;" -->
					<strong>'.$hour_1.':'.$minute_1.'</strong>&nbsp;'.$title_1.'
				  </a>';
				  endif;
				$rows[$channel_nid]['body'] .= '</div>';

			  } else if($arg['template'] == 8) {

				$rows[$channel_nid]['body'] .= '<div class="tvprog">';

				  if(isset($channel[$count+1])):

					$rows[$channel_nid]['body'] .= '<a href = "'.$node_pathauto_2.'" target="_top"><!-- target="_blank" onclick="top.location.href=this.href;return false;" -->
					  <strong>'.$hour_1.':'.$minute_1.'</strong>&nbsp;'.$title_1.'
					</a>';
				  endif;
				$rows[$channel_nid]['body'] .= '</div>';
			  } 

			  $rows[$channel_nid]['body'] .= '<br style="clear:both;" />            
			  </div>';
			}
        }
      }
    }

    // Sorting channels like on the Set
    $node = \Drupal\node\Entity\Node::load(1);
    $channels_weights = $node->get('field_sets_channels')->getValue();

    $sorted_channels = array();

    foreach ($channels_weights as $key => $value) {
      $channel_id = $value['target_id'];

      if (isset($rows[$channel_id])) {
        $sorted_rows[] = $rows[$channel_id];
      }
    }

    return $sorted_rows;
  }

  public function get_time_limits($month, $day, $hour, $minute, $temp) {

    $date = new \DateTime(NULL, new \DateTimeZone('UTC'));

    // Start code for the year logic:
    $current_date = new \DateTime(NULL, new \DateTimeZone('UTC'));
    $current_month = $current_date->format('n');

    if ($current_month == 12 && $month == 1) {
      $date->setDate($date->format('Y')+1, $month, $day);
    } else if ($current_month == 1 && $month == 12) {
      $date->setDate($date->format('Y')-1, $month, $day);
    } else {
      // Defaul logic
      $date->setDate($date->format('Y'), $month, $day);
      // Defaul logic end
    }
    // End code for the year logic

    $date->setTime($hour,$minute);   
    $unixtime = $date->format('U');

    if ($temp == 4 || $temp == 5 || $temp == 7 || $temp == 8) {
      $minus_hours = 4; $plus_hours = 6;
    } else {
      $minus_hours = 4; $plus_hours = 3;
    }

    $filter_min = $unixtime - ($minus_hours * 60 *60);
    $filter_max = $unixtime + ($plus_hours * 60 *60);

    return array('min' => $filter_min, 'max' => $filter_max);
  }

  public function generate_image_style($image_style, $file_uri) {

    $style = ImageStyle::load($image_style);
    $style_uri = $style->buildUri($file_uri);
    $style->createDerivative($file_uri, $style_uri);

  }

  public function remove_orphaned_images($remove_csv = false) {

    $age = \Drupal::config('system.file')->get('temporary_maximum_age');
    $file_storage = \Drupal::entityManager()->getStorage('file');

    // Only delete temporary files if older than $age. Note that automatic cleanup
    // is disabled if $age set to 0.
    if ($age) {

      $fids = \Drupal::entityQuery('file');

      if ($remove_csv) {
        $fids = $fids->condition('uri', '.csv', 'CONTAINS');
      } else {
        $fids = $fids->condition('uri', 'program', 'CONTAINS');
      }
      
      $fids = $fids->execute();

      if ($fids) {

        $files = $file_storage->loadMultiple($fids);

        $file_usage = \Drupal::service('file.usage');

        foreach ($files as $file) {

          $references = \Drupal::service('file.usage')->listUsage($file);
          if (empty($references)) {
            if (file_exists($file->getFileUri())) {

              $file->delete();
            }
            else {
              \Drupal::logger('file system')->error('Could not delete temporary file "%path" during garbage collection', array('%path' => $file->getFileUri()));
            }
          }
          else {
            \Drupal::logger('file system')->info('Did not delete temporary file "%path" during garbage collection because it is in use by the following modules: %modules.', array('%path' => $file->getFileUri(), '%modules' => implode(', ', array_keys($references))));
          }
        }
      }
    }
  }

}

?>
