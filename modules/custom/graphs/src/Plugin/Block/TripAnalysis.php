<?php

namespace Drupal\graphs\Plugin\Block;

// use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Symfony\Component\Validator\Constraints\Length;

// use Drupal\Core\Form\FormStateInterface;
// use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "trip_analysis",
 *   admin_label = @Translation("Trip Analysis"),
 *   category = @Translation("Custom company graphs block example")
 * )
 */
class TripAnalysis extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $database = \Drupal::database();
    $data = array();
    $current_user = \Drupal::currentUser();
    $role = $current_user->getRoles();
    // dump($role);
    $user_ids = array();
    $user_id = '';

 

    if($role[1] == 'company'){
      $userStorage = \Drupal::entityTypeManager()->getStorage('user');
      $query = $userStorage->getQuery();
      $uids = $query->condition('status', '1')->condition('roles', 'data_entry')->execute();
      $users = $userStorage->loadMultiple($uids);
      $keys = array_keys($users);
    
      array_push($user_ids,$current_user->id());
      $user_id = $current_user->id();
      for($i=0;$i<count($users);$i++){
        $company_id = $users[$keys[$i]]->get('field_operatorcompany')->target_id;
        if($current_user->id() == $company_id){
          array_push($user_ids,$users[$keys[$i]]->id());
          $user_id .= ',' . $users[$keys[$i]]->id();
        }
      }
    }
    elseif($role[1] == 'data_entry'){
        $user_id = $current_user->id();
        $user = \Drupal\user\Entity\User::load($current_user->id());
        $parent_company_id = $user->field_operatorcompany->getValue()[0]['target_id'];
        array_push($user_ids,$parent_company_id);
        $user_id .= ',' . $parent_company_id;
    //   dump($user_id);
    }

    $current_date =  date("Y-m-d");
    $month =  date("m", strtotime($current_date . ' - 6 months'));
    $year =  date("Y", strtotime($current_date . ' - 6 months'));
    $start_date = date("Y", strtotime($current_date . ' - 6 months')) . "-" . date("m", strtotime($current_date . ' - 6 months')) . "-01";
    $end_date = date("Y", strtotime($current_date)) . "-" . date("m", strtotime($current_date . ' - 1 months')) . "-" . cal_days_in_month(CAL_GREGORIAN, $month, $year);

    // dump($month);
    // dump($year);

    $months_query = "SELECT count(*) as months,month(field_end_date_time_value) as months_name,YEAR(field_end_date_time_value) as year_name from node__field_end_date_time dt, node_field_data d where d.nid = dt.entity_id and field_end_date_time_value between '$start_date' And '$end_date' and d.uid in (" . $user_id . ") group by month(field_end_date_time_value),year(field_end_date_time_value)";
    $query = $database->query($months_query);
    $months = $query->fetchAll();
    // dump($months_query);
    $months = json_decode(json_encode($months), true);
    // dump($months);

    $j = (int)$month;
    $p = $year;
    for ($i = 0; $i < 6; $i++) {
      if ($j >= 13) {
        $j = 1;
        $p++;
      }
      for ($k = 0; $k < count($months); $k++) {
        // if (!is_null($months[$k]['months_name'])) {
        if ($j == (int)$months[$k]['months_name']) {
          // dump($j);
          // dump($i);
          $data[$i]['x'] = $months[$k]['months'];
          if (!empty($months[$k]['months_name'])) {
            $data[$i]['y'] = date("F", mktime(null, null, null, $months[$k]['months_name'], 1)) . '-' . $months[$k]['year_name'];
          }
          break;
          // $k++;
        } else {
          $data[$i]['x'] = "0";
          $data[$i]['y'] = date("F", mktime(null, null, null, $j, 1)) . '-' . $p;
        }
      }
      // }
      $j++;
    }

    if(!in_array('trip_logging',$role)){
      $data = '';
    }
    // dump($data);

    // if ((int)$data[0]['x'] == 0 && (int)$data[1]['x'] == 0 && (int)$data[2]['x'] == 0 && (int)$data[3]['x'] == 0 && (int)$data[4]['x'] == 0 && (int)$data[5]['x'] == 0) {
    //   $data = '';
    // }

    return array(
      '#type' => 'markup-cust',
      '#temp' => "custom-data-graph",
      '#markup' => json_encode($data),
      '#description' => 'my custom desc'
    );
  }
}
