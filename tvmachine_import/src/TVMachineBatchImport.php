<?php

namespace Drupal\tvmachine_import;

use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;

use Drupal\tvmachine_blocks\Helper\TVMachineBlocksHelper;
use Drupal\tvmachine_blocks\Helper\DbQueryHelper;

use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Entity\EntityInterface;
use Drupal\file\FileInterface;

/**
 * Class TVMachineBatchImport.
 *
 * @package Drupal\tvmachine_import
 */
class TVMachineBatchImport {

  # Batch operation info
  private $batch;

  # FID of the CSV file.
  private $fid;

  # File object.
  private $file;

  private $skip_first_line;

  private $delimiter;

  private $enclosure;

  /**
   * {@inheritdoc}
   */
  public function __construct($fid, $skip_first_line = FALSE, $delimiter = ';', $enclosure = ',', $batch_name = 'Custom CSV import') {

    $this->fid = $fid;
    $this->file = File::load($fid);
    $this->skip_first_line = $skip_first_line;
    $this->delimiter = $delimiter;
    $this->enclosure = $enclosure;
    $this->temp_path = \Drupal::service('file_system')->realpath(file_default_scheme() . "://ftp_images");

    $this->batch = [
      'title' => $batch_name,
      'finished' => [$this, 'finished'],
      'file' => drupal_get_path('module', 'tvmachine_import') . '/src/TVMachineBatchImport.php',
    ];

    $this->parseCSV();
  }

  /**
   * {@inheritdoc}
   * Prerparing list of operations
   */
  public function parseCSV() {

    $time = time();

    if (($handle = fopen($this->file->getFileUri(), 'r')) !== FALSE) {

      if ($this->skip_first_line) {
        fgetcsv($handle, 0, ',');
      }

      $channels = $this->tvmachine_get_channels();
      $programs = array();

      while (($item = fgetcsv($handle, 0, ',')) !== FALSE) {

        $chanel_name  = trim($item[0]);
        $program_name = trim($item[6]);
 
        if (isset($channels[$chanel_name])) {
          $channel = $channels[$chanel_name];
        } else {
          $channel = false;
          \Drupal::messenger()->addStatus('Can\'t find '.$chanel_name.' channel', false);
        }

        $data_program = array();

        if (isset($channels[$chanel_name])) {
          $data_program['channel']  = $chanel_name;
          $data_program['channel_nid']  = $channel->nid;
        }
        
        $data_program['year']         = $item[1];
        $data_program['month']        = $item[2];
        $data_program['day']          = $item[3];
        $data_program['hour']         = $item[4];
        $data_program['minutes']      = $item[5];
        $data_program['title']        = $program_name;
        $data_program['information']  = $item[7];
        $data_program['category']     = $item[8];
        $data_program['description']  = $item[9];
        $data_program['subtitle']     = $item[10];
        $data_program['image']        = $item[11];
        $data_program['tv_field_1']   = $item[12];
        $data_program['tv_area_1']    = $item[13];
        $data_program['tv_area_2']    = $item[14];
        $data_program['tv_area_3']    = $item[15];
          
        if (isset($channels[$chanel_name])) {
          $programs[] = $data_program;
        } else {
          /*drupal_set_message(t('Could not find channel %chanel_name, so not able to import program %program. Please fix the error and try again.',          array('%chanel_name'=> $chanel_name, '%program'=> $program_name)), 'error');*/
        }
      }

      if (count($programs) > 0) {
        $programs_arr = array_chunk($programs, 50);
        foreach ($programs_arr as $key => $_programs) {
          $this->setOperations($_programs);
        }
      }

      fclose($handle); 
    }

    $time3 = time();
    \Drupal::messenger()->addStatus('Total time is '.($time3 - $time).' sec.');
  }

  /**
   * {@inheritdoc}
   * Set operation item
   */
  public function setOperations($list_of_items = array()) {
    $this->batch['operations'][] = [[$this, 'processItems'], [$list_of_items]];
  }


  /**
  * Get list of channels with names
  */
  public function tvmachine_get_channels() {

    $query = \Drupal::database()->select('node_field_data', 'n');
    $query->fields('n', ['nid', 'title']);
    $query->condition('n.type', 'channels');
    $result = $query->execute()->fetchAll();
    
    $list = array();
    foreach ($result as $key => $value) {
      $list[$value->title] = $value;
    }

    return $list;
  }


  /**
   * helper function to insert program node.
  */
  public function tvmachine_add_node_program($data){

    $node = Node::create([
      'type' => 'program',
      'langcode' => 'en',
      'created' => REQUEST_TIME,
      'changed' => REQUEST_TIME,
      'uid' => 1,
      'status' => 1,
      'promote' => 1,
      'sticky' => 0,
      'title' => $data['title'],
    ]);

    $node->field_program_channel = $data['channel_nid']; 
    $node->field_program_year = (int)$data['year'];
    $node->field_program_month = (int)$data['month'];
    $node->field_program_day = (int)$data['day'];
    $node->field_program_hour = (int)$data['hour'];
    $node->field_program_minutes = (int)$data['minutes'];
    $node->field_program_description = $data['description'];
    $node->field_program_subtitle   = $data['subtitle'];
    $node->field_program_information = $data['information'];
    $node->field_program_tv_field_1 = $data['tv_field_1'];
    $node->field_program_tv_area_1  = $data['tv_area_1'];
    $node->field_program_tv_area_2  = $data['tv_area_2'];
    $node->field_program_tv_area_3  = $data['tv_area_3'];
    $node->field_program_category   = $data['category'];

    // Here we remove image value in order not to show on the node view.
    $node->field_program_image  = '';

    if (!empty($data['image'])) {

      $file = $this->tvmachine_image_download($data['image'], $data['title']);

      if ($file) {

        $node->field_program_image_upload[] = array('target_id' => $file->id());
        $fid = $file->id();

      } else {

        \Drupal::messenger()->addStatus('Can\'t upload the image '.$data['image']);
      }     
    }

    $result =  $node->save();

    if ($file) {

      /* Generating image style start */

      if ($data['category'] == 'Cine') {

        $TVMachineBlocksHelper = new TVMachineBlocksHelper; 
        
        /*$file = File::load($fid);
        $file_uri = $file->getFileUri();*/

        $DbQueryHelper = new DbQueryHelper();
        $file_uri = $DbQueryHelper->get_file_uri($fid);

        $TVMachineBlocksHelper->generate_image_style('53x53', $file_uri);
        /*$TVMachineBlocksHelper->generate_image_style('200x000', $file_uri);*/
        /*$TVMachineBlocksHelper->generate_image_style('300x000', $file_uri);*/

      }

      /* Generating image style end */

    }

    return $result;
  }

  /**
   * Download remote image.
  */
  public function tvmachine_image_download($url, $title = false) {
    // Check if the file extension is valid.

    $except = array("jpg", "jpeg", "png", "gif");
    $imp = implode('|', $except);

    $ext = pathinfo($url, PATHINFO_EXTENSION);

    if (!in_array($ext, $except)) {
      \Drupal::messenger()->addStatus('Download Image Error: Bad file extension.');
      return FALSE;
    }  

    if ($title) {

      //$title = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $title);
      //$title = mb_ereg_replace("([\.]{2,})", '', $title);

      $image_name = rtrim($this->tvmachine_filenames_transliteration($title), '-');

    } else {
      $image_name = md5($url);
    }   

    try {

      if(stristr($url, 'ftp://') !== FALSE) {

        $parsed_url = parse_url($url);

        // Validate ftp credentials
        if (isset($parsed_url['scheme']) && $parsed_url['scheme'] == 'ftp' && isset($parsed_url['host']) && isset($parsed_url['user']) && isset($parsed_url['pass']) && isset($parsed_url['path'])) {

          // Get temp file name
          $temp_path = $this->temp_path;
          $pathinfo = pathinfo($parsed_url['path']);
          $temp_file_name = $temp_path .'/'. $pathinfo['basename'];         

          // Read file data
          $handle = fopen($temp_file_name, 'r');

          if ($handle) {
            $image_binary = fread($handle, filesize($temp_file_name));
            fclose($handle);

            // Save file in Drupal
            $file = file_save_data($image_binary, 'public://program/'.$image_name.'.'.$ext, FILE_EXISTS_RENAME);

          } else {
            \Drupal::messenger()->addStatus('Can\'t read file from the ftp_image folder '.$url);
          }

        } else {
          return FALSE; 
        }     

      } else {

        $options = ['http_errors' => FALSE];

        $response = \Drupal::httpClient()->request('GET', $url, $options);

        // Check response code.
        if ($response->getStatusCode() != 200) {
          \Drupal::messenger()->addStatus('Download Image Error: Bad response code '.$response->getStatusCode());
          return FALSE;
        }

        $types = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($response->getHeaderLine('content-type'), $types)) {
          \Drupal::messenger()->addStatus('Download Image Error: Bad Content-Type header '.$response->getHeaderLine('content-type'));
          return FALSE;
        }

        $file = file_save_data($response->getBody()->getContents(), 'public://program/'.$image_name.'.'.$ext, FILE_EXISTS_RENAME);
      }   

      if ($file) {
        //\Drupal::messenger()->addStatus('The image has been uploaded '.$url);
        //\Drupal::messenger()->addStatus('Local image path:'.$file->getFileUri());
        return $file;
      } else {
        return FALSE;
      }
    }
    catch (RequestException $e) {
      \Drupal::messenger()->addStatus('httpClient Exception');

      return FALSE;
    }   

    return FALSE;
  }


  /**
  * Implements callback for hook_file_validate().
  */
  public function tvmachine_filenames_transliteration($filename) {
    $filename = \Drupal::transliteration()->transliterate($filename);
    // Replace whitespace.
    $filename = str_replace(' ', '-', $filename);
    // Remove remaining unsafe characters.
    $filename = preg_replace('![^0-9A-Za-z_.-]!', '', $filename);
    // Remove multiple consecutive non-alphabetical characters.
    $filename = preg_replace('/(_)_+|(\.)\.+|(-)-+/', '\\1\\2\\3', $filename);
    // Force lowercase to prevent issues on case-insensitive file systems.
    $filename = strtolower($filename);

    // For empty.
    $name = explode('.', $filename);
    $name = reset($name);
    $extension = explode(".", $filename);
    $extension = end($extension);

    // Is empty.
    if (!$name) {
      $filename = md5(rand()) . '.' . $extension;
    }

    return $filename;
  }

  /**
   * {@inheritdoc}
   *
   */
  public function processItems($programs, &$context) {

    foreach ($programs as $key => $data) {

      if (count($data) < 5) {
        \Drupal::messenger()->addStatus('Data count less then 5');
      } else {

        $DbQueryHelper = new DbQueryHelper;
          
        if ($nid = $DbQueryHelper->find_program_by_date($data['month'], $data['day'], $data['hour'], $data['minutes'], $data['channel_nid'])) {

          //$data['@url'] = 'node/'. $nid;
          //\Drupal::messenger()->addStatus('Could not import program '.$data['month'].'/'.$data['day'].'/'.$data['hour'].'/'.$data['minutes'].'/'.$data['channel'].'/'.$data['month'].', program already exist: '.$data['@url']);
          //$context['message'] = t('Could not import program '.$data['month'].'/'.$data['day'].'/'.$data['hour'].'/'.$data['minutes'].'/'.$data['channel'].'/'.$data['month'].', program already exist: '.$data['@url']);

          $context['results']['exists'][] = 1;
        } 
        else {
          $nid = $this->tvmachine_add_node_program($data);
          $context['results']['created'][] = 1;
        } 
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setBatch() {
    batch_set($this->batch);
  }

  /**
   * {@inheritdoc}
   *
   * Metod if we call batch from the form submit
   */
  public function processBatch() {
    batch_process();
  }

  /**
   * {@inheritdoc}
   *
   * Batch Result info
   */
  public function finished($success, $results, $operations) {
    if ($success) {
      if (isset($results['created'])) {
        $message = \Drupal::translation()
          ->formatPlural(count($results['created']), 'One post was created.', '@count posts were created.');
      } else {
        $message = t('Zero posts were created');
      }

    }
    else {
      $message = t('Finished with an error.');
    }
    drupal_set_message($message);

    if (isset($results['exists'])) {

      $message = \Drupal::translation()
          ->formatPlural(count($results['exists']), 'One post already exist.', '@count posts already exists.');

      drupal_set_message($message);
    }

    $temp_path = $this->temp_path;

    if (file_exists($temp_path.'/')) {

      $files = glob($temp_path.'/*');
      \Drupal::messenger()->addStatus('Folder contains '.count($files).' files');
      
      foreach (glob($temp_path.'/*') as $file) {
        unlink($file);
      }

    }

    $TVMachineBlocksHelper = new TVMachineBlocksHelper();
    $TVMachineBlocksHelper->remove_orphaned_images(true);
  }
}