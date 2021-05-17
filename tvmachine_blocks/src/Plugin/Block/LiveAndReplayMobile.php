<?php

namespace Drupal\tvmachine_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

use Drupal\tvmachine_blocks\Helper\DbQueryHelper;

/**
 * Provides a 'Live and replay mobile' block.
 *
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "tvmachine_live_and_replay_mobile_block",
 *   admin_label = @Translation("Live and replay mobile")
 * )
 */
class LiveAndReplayMobile extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    /*$query = "select n.nid,n.type FROM {node} AS n where n.type='channels' and n.nid IN (51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103) ORDER BY FIELD(n.nid,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103) limit 40";
    $result = db_query($query);*/
    $nids = [51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103];

    /*$query = \Drupal::database()->select('node_field_data', 'nfd');
    $query->fields('nfd', ['nid']);
    $query->condition('nfd.type', 'channels');
    $query->condition('nid', $nids, 'IN');
    $query->range(0, 40);
    $result = $query->execute()->fetchAll();*/

    $DbQueryHelper = new DbQueryHelper();
    $nids = $DbQueryHelper->get_channels(true, $nids, 0);

    $output ='';
    $output .= "<h3>TV en Directo y a la carta</h3><br style='clear:both;'>";

    //while($row = db_fetch_array($result)) {
    foreach ($nids as $key => $chnid_load) {

      //$nid = $row['nid'];
      //$chnid_load=node_load($nid);
      $title = $chnid_load['title'];
      $cha_url = $chnid_load['channels_url'];
      $cha_live = $chnid_load['channels_url_live'];
      $cha_replay = $chnid_load['channels_url_replay'];

      if ($chnid_load['logo']) {
        $ch_image = $chnid_load['logo'];
      } else {
        $ch_image = false;
      }
      
      if($cha_url!="") {
        //$webuu= l(t('Web'),$cha_url, array('attributes' => array('target' => '_blank', 'class' => 'inner_web_live_and_replay', 'onclick' => "window.open.focus(this, this.target, 'scrollbars=yes, menubar=yes, height=700, width=1000, resizable=yes, toolbar=yes, location=yes, status=yes'); return false;"))); 
        $webuu = '<a href="'.$cha_url.'" target="_blank" class="inner_web_live_and_replay_mobile" onclick="window.open.focus(this, this.target, \'scrollbars=yes, menubar=yes, height=700, width=1000, resizable=yes, toolbar=yes, location=yes, status=yes\'); return false;">Web</a>';
      }
      else {$webuu="";}
      
      if($cha_live!="") {
        //$live_url= l(t('Live'),$cha_live, array('attributes' => array('target' => '_blank', 'class' => 'inner_live_live_and_replay', 'onclick' => "window.open.focus(this, this.target, 'scrollbars=yes, menubar=yes, height=700, width=1000, resizable=yes, toolbar=yes, location=yes, status=yes'); return false;"))); 
        $live_url= '<a href="'.$cha_live.'" target="_blank" class="inner_live_live_and_replay_mobile" onclick="window.open.focus(this, this.target, \'scrollbars=yes, menubar=yes, height=700, width=1000, resizable=yes, toolbar=yes, location=yes, status=yes\'); return false;">En directo</a>';
      }
      else {$live_url="";}
    
      if($cha_replay!="")  {
        //$replay_cha= l(t('Replay'),$cha_replay, array('attributes' => array('target' => '_blank','class' => 'inner_replay_live_and_replay', 'onclick' => "window.open.focus(this, this.target, 'scrollbars=yes, menubar=yes, height=700, width=1000, resizable=yes, toolbar=yes, location=yes, status=yes'); return false;")));
        $replay_cha = '<a href="'.$cha_replay.'" target="_blank" class="inner_replay_live_and_replay_mobile" onclick="window.open.focus(this, this.target, \'scrollbars=yes, menubar=yes, height=700, width=1000, resizable=yes, toolbar=yes, location=yes, status=yes\'); return false;">A la carta</a>';
      }
      else { $replay_cha=""; }
    
      if(strpos($title,'blocked') !== false) {        
        $output .= "";
      } 
      else {
        $output .= "<div>
        <div class='logo_live_and_replay_mobile' class='tvlogo---".$key."'> <img data-src='".$ch_image."' alt='".$title."' height='50' width='60' class='lazyload' /></div>
        <div class='title_live_and_replay_mobile'>".$title."</div>
        <div class='web_live_and_replay_mobile'>".$webuu."</div>
        <div class='live_live_and_replay_mobile'>".$live_url."</div>
        <div class='replay_live_and_replay_mobile'>".$replay_cha."</div>
        </div><br style='clear:both;'>";
      }
    }

    return array(
      '#type' => 'inline_template',
      '#template' => $output,
      '#attached' => [
        'library' => [
          'lazy/lazysizes',
        ],
      ],
    );
  }

}
