<?php
 
namespace Drupal\tvmachine_pages\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Drupal\tvmachine_blocks\Helper\TVMachineBlocksHelper;
use Drupal\tvmachine_blocks\Helper\DbQueryHelper;

use Symfony\Component\HttpFoundation\Response;

use Drupal\views\Views;

use \Drupal\Core\Language\LanguageDefault;

use Drupal\image\Entity\ImageStyle;

use Drupal\Core\Cache\CacheBackendInterface;

use Drupal\Core\Url;

/**
 * Controller routines for page example routes.
 */
class TVMachinePagesController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'tvmachine_pages';
  }

  public function tvmachine_detail_ajax($temp, $sets, $month, $day, $hour, $minute,$channel_id, $detail_template) {

    $channel_id = intval($channel_id);

    $DbQueryHelper = new DbQueryHelper;        
    $nid = $DbQueryHelper->find_program_by_date($month, $day, $hour, $minute, $channel_id);

    if ($nid) {

      $cache_id  = 'detail_ajax_'.$temp.$sets.$month.$day.$hour.$minute.$channel_id.$detail_template;

      $cache = \Drupal::cache()->get($cache_id);

      if ($cache && is_object($cache)) {
        $result = $cache->data;
      } else {
        $result = false;
      }

      if (!$result) {

        $node_data = $DbQueryHelper->get_prog_title_and_desc($nid);

        $title = $node_data['title'];
        $des = $node_data['desc'];
        $sub = substr($des, 0, 120);
        $CSS_ID = $detail_template;
        if ($detail_template == 1) {
          $CSS_ID = '';
        }

        $viewport = 580;
        if ($detail_template == 3 || $detail_template == 4) {
          $viewport = 362;
        }

        $result = '<!DOCTYPE html>
          <html lang="es-ES" dir="ltr">
          <head>
          <title>'.$title.' - Programación TV - TVguia.es</title>
          <meta charset="utf-8" />
          <meta name="description" lang="es" content="'.$title.' : '.$sub.'..." />
          <meta name="keywords" content="televisión, television, tele, TV, vídeos, programa TV, series, películas, cinema, programación TV" />
          <meta name="robots" content="noindex, nofollow" />
          <meta id="testViewport" name="viewport" content="width = '.$viewport.'" /> 
          <link type=\'text/css\' rel=\'stylesheet\' href=\'/modules/custom/tvmachine_blocks/css/iframe_detail'.$CSS_ID.'.css\' />  
          </head>
          <body>';
        //$result .= $this->tvmachine_detail($month, $day, $hour, $minute, $channel_id, $detail_template);
        $result .= $this->tvmachine_detail_alternative($month, $day, $hour, $minute, $channel_id, $detail_template, $temp);
        $result .= '</body></html>';

        \Drupal::cache()->set($cache_id, $result, REQUEST_TIME + (60*60*1), array('node:'.$nid));

      }

    } else {

      // Or try to use $temp
      switch ($detail_template) {
        case '1':
          $result = "
<!DOCTYPE html>
<html lang='es-ES' dir='ltr'>
<head>
<title>TVguia.es</title>
<meta charset='utf-8' />
<meta name='robots' content='noindex, nofollow' />
<meta id='testViewport' name='viewport' content='width = 580' />
<link type='text/css' rel='stylesheet' href='/modules/custom/tvmachine_blocks/css/iframe_detail.css' />
</head>
<body>
<div id='detail-program-content' class='details-television'>
<div style='float: left; margin: 40px 80px 60px 10px;'>
<img src='/modules/custom/tvmachine_blocks/images/update-grey.png' height='67' width='48'>
</div>
<h1 style='margin: 20px 0 20px 0;'>
<span class='title-details-television'>
Programa en actualización
</span>
</h1>
<span class='element-details-television'>
Este programa no está disponible temporalmente.
<br />
Estará accesible nuevamente <b>en unos minutos</b>...
<br />
</span>
<br />
<span style='font-size: 24px; font-weight: bold;'>:)</span>
</div>
</body>
</html>
";
          break;

        case '2':
          $result = "";
          break;

        case '3':
          $result = "
<!DOCTYPE html>
<html lang='es-ES' dir='ltr'>
<head>
<title>TVguia.es</title>
<meta charset='utf-8' />
<meta name='robots' content='noindex, nofollow' />
<meta id='testViewport' name='viewport' content='width = 362' />
<link type='text/css' rel='stylesheet' href='/modules/custom/tvmachine_blocks/css/iframe_detail3.css' />
</head>
<body>
<div id='header-wrapper-up'>
<div id='tvlogo'>
<a title='Programación TV' href='/mobile/programacion-tv'>
<img src='/themes/tv_mobile/images/programacion-tv.png' alt='Programación TV' width='144' height='31' border='0'>
</a>
</div>
</div>
<br style='clear:both;' />
<div class='details-television'>
<div style='padding:20px 10px 20px 10px; width:300px;'>
<h1>
<div class='title-details-television'>
Programa en actualización
</div>
</h1>
<br />
...nuevamente <b>disponible en unos minutos</b>...
<br />
<span style='font-size: 24px; font-weight: bold;'>:)</span>
<br />
<br style='clear:both;' />
Descubre todos los programas TV <br />de la noche :
<br style='clear:both;' />
<br style='clear:both;' />
<iframe name='mainframe' id='mainframe' src='https://www.tvguia.es/program/content/7/1/0/0/22/45/FFFFFF/FFFFFF/FFFFFF/111111' scrolling='no' width='297' height='1720' frameborder='0' marginwidth='0' marginheight='0'></iframe>
Consulta nuestra guía para acceder a todo la programación TV:
<br style='clear:both;' />
<br style='clear:both;' />
<a title='Programación TV' href='/mobile/programacion-tv'>
<div style='background-color: #ff6e00; color: #ffffff; display: inline; float: left; font-size: 24px; font-weight: bold; margin: 8px 0 0 45px; padding: 2px 14px;'>Programación TV</div>
</a>
<br style='clear:both;' />
<br style='clear:both;' />
Todos los canales en una sola página, para una lectura rápida y sencilla de tu guía TV.
</div>
</div> 
</body>
</html>
          ";
          break;

        case '4':
          $result = "
<!DOCTYPE html>
<html lang='es-ES' dir='ltr'>
<head>
<title>TVguia.es</title>
<meta charset='utf-8' />
<meta name='robots' content='noindex, nofollow' />
<meta id='testViewport' name='viewport' content='width = 362' />
<link type='text/css' rel='stylesheet' href='/modules/custom/tvmachine_blocks/css/iframe_detail4.css' />
</head>
<body>
<br style='clear:both;' />
<div class='details-television'>
<div style='padding:20px 10px 20px 10px; width:300px;'>
<h1>
<div class='title-details-television'>
Programa en actualización
</div>
</h1>
<br />
...nuevamente <b>disponible en unos minutos</b>...
<br />
<span style='font-size: 24px; font-weight: bold;'>:)</span>
<br />
<br style='clear:both;' />
Descubre todos los programas TV <br />de la noche :
<br style='clear:both;' />
<br style='clear:both;' />
<iframe name='mainframe' id='mainframe' src='https://www.tvguia.es/program/content/7/1/0/0/22/45/FFFFFF/FFFFFF/FFFFFF/111111' scrolling='no' width='297' height='1720' frameborder='0' marginwidth='0' marginheight='0'></iframe>
</div>
</div> 
</body>
</html>  
          ";
          break;
        
        default:
          $result = "";
          break;
      }
      
    }  

    $response = new Response();
    $response->setContent($result);

    return $response;

  }

  public function tvmachine_detail_alternative($month, $day, $hour, $minutes, $channel_id, $detail_template, $temp) {

    $display = 'page_'.$detail_template;

    $tvmachineBlocksHelper = new TVMachineBlocksHelper;  
    $DbQueryHelper = new DbQueryHelper;

    $unixtime = $tvmachineBlocksHelper->program_time_to_unix($minutes, $hour, $day, $month);
    $nid = $DbQueryHelper->find_program_by_date_unix($unixtime, $channel_id);

    $time_min = $unixtime - (4* 60 *60);
    $time_max = $unixtime + (4* 60 *60);

    $programs = array(
      'prev' => $DbQueryHelper->get_prev_programs($unixtime, $time_min, $channel_id),
      'current' => $nid,
      'next' => $DbQueryHelper->get_next_programs($unixtime, $time_max, $channel_id),
    );

    $args = array(
      'detail_template' => $detail_template,
      'template' => $temp,
      'sets' => 1,
      'channel'=> $channel_id,
      'minute' => $minutes,
      'hour' => $hour,
      'day' => $day,
      'month' => $month,
      'time' => $unixtime,
    );

    $result = $this->tvmachine_detail_alternative_data_prepare($programs, $display, $args, $channel_id);
    $result['#theme'] = 'program_detail_'.$display;
    $result = \Drupal::service('renderer')->render($result);
    return $result;
  }

  public function tvmachine_detail_alternative_data_prepare($programs, $display, $arg, $channel_id) {

    $detail_template = $arg['detail_template'];
    //$base_url = \Drupal::request()->getSchemeAndHttpHost();
    //$request = explode('/', \Drupal::request()->getPathInfo());

    $result_arr = []; // This var will be passed to the template:
    //$image_path =  "themes/tv_desktop/images"; 

    $date = array(
      'weekday' => date('l',$arg['time']),
      'hour'    => date('H',$arg['time']),
      'minute'  => date('i',$arg['time']),
      'day'     => date('d',$arg['time']),
      'month'   => date('F',$arg['time']),
      'year'    => date('Y')
    );   
    

    if (isset($programs['prev'][0])) {
      $arr['time']['previou']['hour'] = $programs['prev'][0]['hour'];
      $arr['time']['previou']['minute'] = str_pad((int) $programs['prev'][0]['minutes'],2,"0",STR_PAD_LEFT);
      $arr['time']['previou']['title'] = $programs['prev'][0]['title'];
    } 
      
    if ($programs['current']) {

      $program_obj = node_load($programs['current']);
      $channel_obj = node_load($channel_id);

      $node_path = $program_obj->toUrl()->toString();

      if ($display == 'page_3' || $display == 'page_4') {
        $program_values = _extract_program_values($program_obj, '300x000');
        $channel_values = _extract_channel_values($channel_obj);
      } else {
        $program_values = _extract_program_values($program_obj);
        $channel_values = _extract_channel_values($channel_obj, 2);
      } 

      $arr['time']['current']['nid']          = $programs['current'];
      $arr['time']['current']['title']        = $program_values['title'];
      $arr['time']['current']['subtitle']     = $program_values['field_program_subtitle'];
      $arr['time']['current']['weekday']      = $date['weekday'];
      $arr['time']['current']['month']        = $date['month'];
      $arr['time']['current']['month_number']     = $program_values['field_program_month'];
      $arr['time']['current']['day']          = $program_values['field_program_day'];
      $arr['time']['current']['year']         = $date['year'];
      $arr['time']['current']['hour']         = intval($program_values['field_program_hour']);
      $arr['time']['current']['minute']       = str_pad((int) $program_values['field_program_minutes'],2,"0",STR_PAD_LEFT);
      $arr['time']['current']['logo']           = $channel_values['field_channels_logo'];
      $arr['time']['current']['channel_description']    = $channel_values['field_channels_description'];
      $arr['time']['current']['channel_url']        = $channel_values['field_channels_url'];
      $arr['time']['current']['channel_url_live']     = $channel_values['field_channels_url_live'];
      $arr['time']['current']['channel_url_replay']     = $channel_values['field_channels_url_replay'];
      $arr['time']['current']['field_program_image_upload_height']   = $program_values['field_program_image_upload_height'];
      $arr['time']['current']['field_program_image_upload_width']   = $program_values['field_program_image_upload_width'];
      $arr['time']['current']['image']        = $program_values['field_program_image'];
      $arr['time']['current']['description']  = $program_values['field_program_description'];
      $arr['time']['current']['tv_field_1']   = $program_values['field_program_tv_field_1'];
      $arr['time']['current']['clean_title']  = $program_values['clean_title'];
      $arr['time']['current']['urlinfo']= $program_values['URLinfo'];
      $arr['time']['current']['urlvideo']= $program_values['URLvideo'];
      $arr['time']['current']['urlfilmaffinity']= $program_values['URLfilmaffinity'];
      $arr['time']['current']['trailer_url']  = $program_values['trailer_url'];
      $arr['time']['current']['iframe_h']  = $program_values['iframe_h'];
      $arr['time']['current']['iframe_h_prog']  = $program_values['iframe_h_prog'];
      $arr['time']['current']['tv_area_1']    = $program_values['field_program_tv_area_1'];
      $arr['time']['current']['tv_area_2']    = $program_values['field_program_tv_area_2'];
      $arr['time']['current']['tv_area_3']    = $program_values['field_program_tv_area_3'];
      $arr['time']['current']['information']  = $program_values['field_program_information'];
      $arr['time']['current']['category']     = $program_values['field_program_category'];
      $arr['time']['current']['field_program_image_upload']   = $program_values['field_program_image_upload'];
    }

    if (isset($programs['next'][0])) {
      $arr['time']['next']['hour'] = $programs['next'][0]['hour'];
      $arr['time']['next']['minute'] = str_pad((int) $programs['next'][0]['minutes'],2,"0",STR_PAD_LEFT);
      $arr['time']['next']['title'] = $programs['next'][0]['title'];
    } 
      
    if (isset($programs['next'][1])) {
      $arr['time']['after_next']['hour'] = $programs['next'][1]['hour'];
      $arr['time']['after_next']['minute'] = str_pad((int) $programs['next'][1]['minutes'],2,"0",STR_PAD_LEFT);
      $arr['time']['after_next']['title'] = $programs['next'][1]['title'];
    }  
       

    $result_arr['node_path'] = $node_path;

    $time_string = "{$arr['time']['current']['hour']}:{$arr['time']['current']['minute']}";

    if (isset($arr['time']['next'])) {
      $time_string .= " - {$arr['time']['next']['hour']}:{$arr['time']['next']['minute']}";
    }

    // This var will be passed to the template:
    $result_arr['time_string'] = $time_string;
        
    if ($arr['time']['current']['category']):

      $TVMachineBlocksHelper = new TVMachineBlocksHelper(); 

      // This var will be passed to the template:
      $result_arr['text_bg_category'] = $TVMachineBlocksHelper->replace_text_bg_category2($arr['time']['current']['category']);  

    endif;

    if ($arr['time']['current']['tv_area_2']) {
      if ($detail_template == 3 || $detail_template == 4) {
        $arr['time']['current']['tv_area_2'] = _replace_tv_area_2_for_temp_3_and_4($arr['time']['current']['tv_area_2']);
      } else {
        $arr['time']['current']['tv_area_2'] = _replace_tv_area_2($arr['time']['current']['tv_area_2']);
      }
    } 
      
    // This var will be passed to the template:
    $result_arr['time_cur_title_url_urlencode'] = urlencode($arr['time']['current']['title']);

    if ($detail_template == 3 || $detail_template == 4) {

      if (strlen($arr['time']['current']['description']) >= 200 && (stripos($arr['time']['current']['tv_area_1'], 'erótic') === false)&&(stripos($arr['time']['current']['tv_area_1'], 'adultos') === false)&&(stripos($arr['time']['current']['description'], 'sex') === false)&&(stripos($arr['time']['current']['description'], 'erótic') === false)&&(stripos($arr['time']['current']['description'], 'porno') === false)&&(stripos($arr['time']['current']['title'], 'sex') === false)): 
        // This var will be passed to the template:
        $result_arr['description_more_200'] = true;
      endif;

      //--                    

      $month_back_url = (int)$arr['time']['current']['month_number'];
      $day_back_url = (int)$arr['time']['current']['day'];
      $hour_back_url = (int)$arr['time']['current']['hour'];
      if($hour_back_url == 0) {$hour_back_url = 1;}
      else if($hour_back_url == 23) {$hour_back_url = 22;}
      else if($hour_back_url == 21) {$hour_back_url = 20;}
      else if($hour_back_url == 19) {$hour_back_url = 18;}
      else if($hour_back_url == 17) {$hour_back_url = 16;}
      else if($hour_back_url == 15) {$hour_back_url = 14;}
      else if($hour_back_url == 13) {$hour_back_url = 12;}
      else if($hour_back_url == 11) {$hour_back_url = 10;}
      else if($hour_back_url == 9) {$hour_back_url = 8;}
      else if($hour_back_url == 7) {$hour_back_url = 6;}
      else if($hour_back_url == 5) {$hour_back_url = 4;}
      else if($hour_back_url == 3) {$hour_back_url = 2;};

      // Pass the result vars to the template:
      $vars['#month_back_url'] = $month_back_url;
      $vars['#day_back_url'] = $day_back_url;
      $vars['#hour_back_url'] = $hour_back_url;

      //--

      $year_program = date("Y");
      $month_program = $arr['time']['current']['month_number'];
      $day_program = $arr['time']['current']['day'];

      $date_program = strtotime($day_program."-".$month_program."-".$year_program." 21:00:00");

      $prev_date_program = strtotime("-1 day", $date_program);
      $prev_month_program = date('n', $prev_date_program);
      $prev_day_program =  date('j', $prev_date_program);

      $next_date_program = strtotime("+1 day", $date_program);
      $next_month_program = date('n', $next_date_program);
      $next_day_program =  date('j', $next_date_program);

      $hour_morning = array('0', '1', '2', '3', '4', '5');
      $hour_night = array('20', '21', '22', '23');


      if (isset($arr['time']['previou']) && (in_array($arr['time']['current']['hour'], $hour_morning)) && (in_array($arr['time']['previou']['hour'], $hour_night))) {
          $prev_month_url = $prev_month_program;
          $prev_day_url = $prev_day_program;
      } else  {
          $prev_month_url = $month_program;
          $prev_day_url = $day_program;
      };

      if (isset($arr['time']['next']) && (in_array($arr['time']['current']['hour'], $hour_night)) && (in_array($arr['time']['next']['hour'], $hour_morning))) {
          $next_month_url = $next_month_program;
          $next_day_url = $next_day_program;
      } else  {
          $next_month_url = $month_program;
          $next_day_url = $day_program;
      };

      // Pass the result vars to the template:
      $vars['#prev_month_url'] = $prev_month_url;
      $vars['#prev_day_url'] = $prev_day_url;
      $vars['#next_month_url'] = $next_month_url;
      $vars['#next_day_url'] = $next_day_url;
    }    

    $translated_day = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
    $translated_month = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

    $current_title = strip_tags($arr['time']['current']['title']);
    $current_title = str_replace("&#039;", ' ', $current_title);
    $current_title = str_replace("&quot;", ' ', $current_title);

    $current_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $current_url = str_replace("tv-serie-cine4", "tv-serie-cine3", $current_url);

    // This var will be passed to the template:
    if (isset($arr['time']['previou'])) {
      $result_arr['time']['previou'] = $arr['time']['previou'];
    } else {
      $result_arr['time']['previou'] = false;
    }

    $result_arr['time']['current'] = $arr['time']['current'];

    if (isset($arr['time']['next'])) {
      $result_arr['time']['next'] = $arr['time']['next'];
    } else {
      $result_arr['time']['next'] = false;
    }

    if (isset($arr['time']['after_next'])) {
      $result_arr['time']['after_next'] = $arr['time']['after_next'];
    } else {
      $result_arr['time']['after_next'] = false;
    }

    $vars['#arg'] = $arg;

    // Pass the result vars to the template:
    $vars['#result_arr'] = $result_arr;
    if (isset($result)) {
      $vars['tvmachine_pages_result'] = $result;
    }
    $vars['#current_title'] = $current_title;
    $vars['#current_url'] = $current_url;
    $vars['#current'] = $arr['time']['current'];
    $vars['#translated_day'] = $translated_day[date('w', mktime(0, 0, 0, $arr['time']['current']['month_number'], $arr['time']['current']['day'], $arr['time']['current']['year']))];

    $vars['#translated_month'] = $translated_month[date('n', mktime(0, 0, 0, $arr['time']['current']['month_number'], $arr['time']['current']['day'], $arr['time']['current']['year']))-1];

    return $vars;

  }

  public function tvmachine_iframe_setup($id) {

    $sets     = array();
    $query = \Drupal::entityQuery('node');
    $query->condition('type', 'sets');
    $sets = $query->execute();
 
    $titles = [
      1 => $this->t('Parrilla TV por franjas de 2 horas'),
      2 => $this->t('Parrilla TV por franjas de 3 horas'),
      3 => $this->t('Programacion TV de la noche (1 programa)'),
      4 => $this->t('Programacion TV de la noche (2 programas)'),
      5 => $this->t('template 5'),
      6 => $this->t('template 6'),
      7 => $this->t('template 7'),
      8 => $this->t('template 8'),
      9 => $this->t('template 9'),
    ];

    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();

    $check = false;
  
    if (in_array('Webmaster',$roles)) {
      $check = true;
    } 
    
    $today = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

    // immediately set the current date with default time
    $default = array();
    $default['template'] = $id;
    $default['sets'] = 1; //$default['sets'] = $sets[0]->nid;     
    $default['month'] = $today->format('n');      
    $default['day']  = $today->format('j');           
    $default['hour']  = 22;// = $today['hours'];
    // have to change also srcMainFrame_textarea in function executeAction(OPTIONS)
    $default['minute'] = 45;// = $today['minutes'];
    // have to change also srcMainFrame_textarea in function executeAction(OPTIONS)
    $default['color1'] = 'FFFFFF';
    $default['color2'] = 'FFFFFF';
    $default['color3'] = '313131';
    $default['color4'] = '111111';

    $mainframe_width = $this->getMainframeWidth($default['template']);
    $mainframe_height = $this->getMainframeHeight($default['template']);

    $base_url = \Drupal::request()->getSchemeAndHttpHost();

    $module_handler = \Drupal::service('module_handler');
    $tvmachine_path = $module_handler->getModule('tvmachine_blocks')->getPath();
    
    $tvmachine_images_path = '/' . $tvmachine_path . '/images/';
    $path_js =  $tvmachine_path.'/js';

    $default_textarea = $base_url ."/program/content/{$default['template']}/{$default['sets']}/0/0/{$default['hour']}/{$default['minute']}/{$default['color1']}/{$default['color2']}/{$default['color3']}/{$default['color4']}";

    $options_hour = array();
    for($i=0;$i<=23;$i++) {
      $options_hour[] = $i;
    }
    
    $options_minute = array();
    for($i=0;$i<=59;$i++) {
      $minute_value = ($i<=9)? '0'.$i:$i;
      $options_minute[$i] = $minute_value;
    }

    $fieldset = false;

    if ($default['template']==3 || $default['template']==4 || $default['template']==7 || $default['template']==8) {

      if (in_array('Super Admin', $roles)) {
         $check = true;
      }

      if ($check) {
        $fieldset = true;
      }
    }


    $result =  array(
      '#theme' => 'tvmachine_iframe_setup',
      '#sets' => $sets,
      '#title' => $titles[$id],
      '#template' => $id,
      '#default' => $default,
      '#top_description' => $this->getIframeDescription($id),
      '#fieldset' => $fieldset,
      '#options_hour' => $options_hour,
      '#options_minute' => $options_minute,
      '#path_tvmachine_img' => $tvmachine_images_path,
      '#mainframe_width' => $mainframe_width,
      '#mainframe_height' => $mainframe_height,
      '#default_textarea' => $default_textarea,
      '#base_url' => $base_url,
    );

    return $result;
  }

  public function tvmachine_iframe_setup_get_title($id) {

    $titles = [
      1 => $this->t('Parrilla TV por franjas de 2 horas'),
      2 => $this->t('Parrilla TV por franjas de 3 horas'),
      3 => $this->t('Programacion TV de la noche (1 programa)'),
      4 => $this->t('Programacion TV de la noche (2 programas)'),
      5 => $this->t('template 5'),
      6 => $this->t('template 6'),
      7 => $this->t('template 7'),
      8 => $this->t('template 8'),
      9 => $this->t('template 9'),
    ];

    if (isset($titles[$id])) {
      return $titles[$id];
    } else {
      return 'Default title';
    }

  }

  public function list_view($temp, $sets, $month, $day, $hour, $minute) {

    $tvmachineBlocksHelper = new TVMachineBlocksHelper;

    $result = $tvmachineBlocksHelper->getDataByRequest($temp, $sets, $month, $day, $hour, $minute);

    $response = new Response();
    $response->setContent($result);

    return $response;
  }

  public function tvmachine_create_file_cache($temp, $sets, $month, $day, $hour, $minute) {

    $tvmachineBlocksHelper = new TVMachineBlocksHelper;

    $file_cache = $tvmachineBlocksHelper->getFilePathByRequest($temp, $sets, $month, $day, $hour, $minute);
    
    $tvmachineBlocksHelper->createFileByRequest($temp, $sets, $month, $day, $hour, $minute, $file_cache);

    $response = new Response();
    $response->setContent('true');

    return $response;

   }

  public function tvmachine_programs_channel_get_title($channel_id, $tomorrow = false) {

    $DbQueryHelper = new DbQueryHelper();
    $title = $DbQueryHelper->get_channel_title($channel_id);

    return 'Programación '.$title;

  }
 
  public function tvmachine_programs_channel($channel_id, $tomorrow, $mobile) {

    $output = array();

    $DbQueryHelper = new DbQueryHelper();
    $channel_data = $DbQueryHelper->get_channel_data($channel_id);

    if ($channel_data) { 

      $language = \Drupal::languageManager()->getDefaultLanguage()->getId();
    
      $date1 = new \DateTime(NULL, new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));

      if ($tomorrow) {
        $tomorrow_ts = strtotime("+1 days");
        $date1 = new \DateTime("@$tomorrow_ts", new \DateTimeZone(\Drupal::config('system.date')->get('timezone.default')));
      }
      
      if (!$mobile) {
        $date = format_date($date1->format('U'), 'custom', "d", NULL)." de ".format_date($date1->format('U'), 'custom', "F", NULL)." de ".format_date($date1->format('U'), 'custom', "Y", NULL); //"d F Y h:i s"
      } else {
        $date = format_date($date1->format('U'), 'custom', "d", NULL, $language)." de ".format_date($date1->format('U'), 'custom', "F", NULL, $language); //"d F Y h:i s"
      }      

      if($channel_data['logo']) {

        $logo_url = $channel_data['logo'];

      } else {
        $logo_url = NULL;
      }

      $english_month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
      $translated_month = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
      $date = str_replace($english_month, $translated_month, $date);

      $output['#logo_url'] = $logo_url;
      $output['#title'] = $channel_data['title'];
      $output['#date'] = $date;

      $day = $date1->format('d');
      $month = $date1->format('n');

      $DbQueryHelper = new DbQueryHelper();
      $programs = $DbQueryHelper->get_channel_programs($month, $day, $channel_id);

      if (!$mobile) {
        $view_id = 'channel_programs';
      } else {
        $view_id = 'channel_programs_mobile';
      }   

      $programs_data = $this->tvmachine_programs_channel_data_prepare($programs, $view_id, $month, $day, $channel_id);

      $output += $programs_data;


      if (!$mobile) {
        $output['#theme'] = ['tvmachine_programs_channel']; 
      } else {
        $output['#theme'] = ['tvmachine_programs_channel_mobile']; 
      }  

      return $output;

    }
    else {
      return NULL;
    }
  }

  public function tvmachine_programs_channel_data_prepare($programs, $view_id, $month, $day, $channel_id) {

    $tvm_vars = []; // This var will be passed to the template

    $uri = \Drupal::request()->getPathInfo();
    $tvm_vars['#uri'] = $uri;

    if ($view_id == 'channel_programs') {

      if (strpos($uri,'tomorrow') !== false) {
        $tvm_vars['#uri_today'] = $uri;
        $tvm_vars['#uri_tomorow'] = str_ireplace("program-television/","program-television-tomorrow/",$uri);
      }
      if (strpos($uri,'program-television/') !== false) {
        $tvm_vars['#uri_today'] = $uri;
        $tvm_vars['#uri_tomorow'] = str_ireplace("program-television/","program-television-tomorrow/",$uri);
      }
      if (strpos($uri,'program-television-tomorrow/') !== false) {
        $tvm_vars['#uri_today'] = str_ireplace("program-television-tomorrow/","program-television/",$uri);
        $tvm_vars['#uri_tomorow'] = $uri;
      }
      if (strpos($uri,'tv/') !== false) {
        $tvm_vars['#uri_today'] = $uri;
        $tvm_vars['#uri_tomorow'] = str_ireplace("tv/","tv-manana/",$uri);
      }
      if (strpos($uri,'tv-manana/') !== false) {
        $tvm_vars['#uri_today'] = str_ireplace("tv-manana/","tv/",$uri);
        $tvm_vars['#uri_tomorow'] = $uri;
      }

    }

    if ($view_id == 'channel_programs_mobile') {
      
      if (strpos($uri,'tomorrow') !== false) {
        $tvm_vars['#uri_today'] = $uri;
        $tvm_vars['#uri_tomorow'] = str_ireplace("mobile-program-television/","mobile-program-television-tomorrow/",$uri);
      }
      if (strpos($uri,'mobile-program-television/') !== false) {
        $tvm_vars['#uri_today'] = $uri;
        $tvm_vars['#uri_tomorow'] = str_ireplace("mobile-program-television/","mobile-program-television-tomorrow/",$uri);
      }
      if (strpos($uri,'mobile/program-television-tomorrow/') !== false) {
        $tvm_vars['#uri_today'] = str_ireplace("mobile-program-television-tomorrow/","mobile-program-television/",$uri);
        $tvm_vars['#uri_tomorow'] = $uri;
      }
      if (strpos($uri,'mobile/tv/') !== false) {
        $tvm_vars['#uri_today'] = $uri;
        $tvm_vars['#uri_tomorow'] = str_ireplace("mobile/tv/","mobile/tv-manana/",$uri);
      }
      if (strpos($uri,'mobile/tv-manana/') !== false) {
        $tvm_vars['#uri_today'] = str_ireplace("mobile/tv-manana/","mobile/tv/",$uri);
        $tvm_vars['#uri_tomorow'] = $uri;
      }
    }

    $tvm_vars['#strpos_uri_manana'] = strpos($uri,'manana') != false?'':'-active';
    $tvm_vars['#strpos_uri_manana_not'] = strpos($uri,'manana') == false?'':'-active';


    if (isset($tvm_vars['#uri_today'])) {

      $channel_no_pub = $tvm_vars['#uri_today'];
      $channel_no_pub = str_ireplace('/tv/programacion-', '', $channel_no_pub);
      $channel_no_pub = str_ireplace('/tv-manana/programacion-', '', $channel_no_pub);
    } else {
      $channel_no_pub = null;
    }

    if (!in_array($channel_no_pub, array('8-mont-blanc','lcp-public-senat','bfm-tv'), true )) {
      $channel_no_pub = 'ad';
    } else {
      $channel_no_pub = 'no_ad';
    }

    $tvm_vars['#channel_no_pub'] = $channel_no_pub;

    // Process view result:

    $tvm_view_result = array();

    foreach($programs as $id => $program) {


      $tvm_view_result[$id]['bg_category'] = _replace_bg_category($program['category']); 

      $tvm_view_result[$id]['alias'] = $program['alias'];

      if ($view_id == 'channel_programs_mobile') {

        $url = '/television/tv-serie-cine3/5/1/'.$month.'/'.$day.'/'.$program['hour'].'/'.$program['minutes'].'/'.$channel_id;

        $tvm_view_result[$id]['alias'] = $url;        
      }

      $tvm_view_result[$id]['title'] = $program['title'];
      $tvm_view_result[$id]['title_link'] = '<a href="'.$program['alias'].'"><b>'.$program['title'].'</b></a>';
      $tvm_view_result[$id]['hour'] = $program['hour'];
      $tvm_view_result[$id]['minutes'] = $program['minutes'];
      $tvm_view_result[$id]['category'] = $program['category'];
      $tvm_view_result[$id]['descr'] = $program['description'];

      $tvm_view_result[$id]['time_text'] = str_pad((int) $program['hour'],2,"0",STR_PAD_LEFT). ':'. str_pad((int) $program['minutes'],2,"0",STR_PAD_LEFT);

    }

    //AdSense logic start
    foreach ($tvm_view_result as $key => $row) {

      if (isset($tvm_view_result[$key-1]) && $row['hour'] > 12 && $tvm_view_result[$key-1]['hour'] == 12) { 
        $timer1_adsense = 1;
      } else { 
        $timer1_adsense = 0;
      }

      if (isset($tvm_view_result[$key-1]) && $row['hour'] > 19 && $tvm_view_result[$key-1]['hour'] == 19) { 
        $timer2_adsense = 1;
      } else { 
        $timer2_adsense = 0;
      }

      $tvm_view_result[$key]['timer1_adsense'] = $timer1_adsense;
      $tvm_view_result[$key]['timer2_adsense'] = $timer2_adsense;
    }
    //AdSense logic end

    $tvm_vars['#tvm_view_result'] = $tvm_view_result;

    return $tvm_vars;
  }

  /**
   * Return description for the page 
   *
   * @return string
   */
  public function getIframeDescription($template) {

    $desc = '';

    switch ($template) {
      case 1:
        $desc = '&#9658; <strong>Programaci&oacute;n TV completa y actualizada diariamente</strong> por franjas de 2 horas  &nbsp;&nbsp;&#9658; 00h - 02h - 04h ... 16h - 18h - 20h - 22h - 24h<br/>
&#9658; <strong>Rentabiliza tu nueva secci&oacute;n</strong> de programaci&oacute;n TV a&ntilde;adiendo un banner publicitario encima y/o al lado de la parrilla de programas.<br />
&#9658; Parrilla de <strong>570 x 938</strong> pixeles<br/><br />
<strong>Siga los 2 pasos siguientes</strong> para obtener el c&oacute;digo de tu parrilla personalizada :';
        break;
      
      case 2:
        $desc = '&#9658; <strong>Programaci&oacute;n TV completa y actualizada diariamente</strong> por franjas de 3 horas  &nbsp;&nbsp;&#9658; 00h - 03h - 06h ... 15h - 18h - 21h - 24h<br/>
&#9658; <strong>Rentabiliza tu nueva secci&oacute;n</strong> de programaci&oacute;n TV a&ntilde;adiendo un banner publicitario encima y/o al lado de la parrilla de programas.<br />
&#9658; Parrilla de <strong>827 x 938</strong> pixeles<br/><br />
<strong>Siga los 2 pasos siguientes</strong> para obtener el c&oacute;digo de tu parrilla personalizada :';
        break;

      case 3:
        $desc = '&#9658; Lista por canal del <strong>principal programa emitido por la noche.</strong> <br/>
&#9658; Bloque de <strong>297 x 320</strong> pixeles<br/><br/>	
<strong>Siga los 2 pasos siguientes</strong> para obtener el c&oacute;digo de tu parrilla personalizada :';
        break;

      case 4:
        $desc = '&#9658; Lista por canal del <strong>principal programa emitido por la noche +  programa siguiente.</strong> <br/>
&#9658; Bloque de <strong>537 x 320</strong> pixeles<br/><br/>	
<strong>Siga los 2 pasos siguientes</strong> para obtener el c&oacute;digo de tu parrilla personalizada :';
        break;
    }

    return $desc;
  }

  public function getMainframeWidth($template) {

    $mainframe_width = '';

    switch($template) {
     case 1:
       $mainframe_width = 570;
       break;       
     case 5:
     case 9:
       $mainframe_width = 570;
       break;       
     case 2:       
       $mainframe_width = 827;
       break;     
     case 6:
       $mainframe_width = 827;
       break;       
     case 3:
       $mainframe_width = 297;
       break;
     case 7:
       $mainframe_width = 297;
       break;       
     case 4:
     case 8:
       $mainframe_width = 537;
       break;
    }

    return $mainframe_width;
  }

  public function getMainframeHeight($template) {

    $mainframe_height = '';

    switch($template) {
     case 1:
       $mainframe_height = 938;
       break;       
     case 5:
     case 9:
       $mainframe_height = 5860;
       break;       
     case 2:       
       $mainframe_height = 938;
       break;     
     case 6:
       $mainframe_height = 5860;
       break;       
     case 3:
       $mainframe_height = 297;
       break;
     case 7:
       $mainframe_height = 2460;
       break;       
     case 4:
     case 8:
       $mainframe_height = 320;
       break;
    }

    return $mainframe_height;
  }

}
