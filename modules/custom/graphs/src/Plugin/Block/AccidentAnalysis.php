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
 *   id = "accident_analysis",
 *   admin_label = @Translation("Accident Analysis"),
 *   category = @Translation("Custom company graphs block example")
 * )
 */
class AccidentAnalysis extends BlockBase
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
    $end_year = date("Y", strtotime($current_date . ' - 5 years'));
    $start_year = date("Y", strtotime($current_date));

    // dump($start_year);
    // dump($end_year);

    $years_query = "SELECT count(*) as counts,YEAR(field_accident_date_value) as year_name from node__field_accident_date adt, node_field_data d where d.nid = adt.entity_id and year(field_accident_date_value) between '$end_year' And '$start_year' and d.uid in (" . $user_id . ") group by year(field_accident_date_value)";
    $query = $database->query($years_query);
    $years = $query->fetchAll();
    // dump($years_query);
    $years = json_decode(json_encode($years), true);
    // dump($years);

    $k = (int)$end_year;
    for($i=0;$i<6;$i++){
      for($j=0;$j<count($years);$j++){
        if($k == $years[$j]['year_name']){
          $data[$i]['x'] = $years[$j]['counts'];
          $data[$i]['y'] = (string)$k;
          break;
        }else{
          $data[$i]['x'] = "0";
          $data[$i]['y'] = (string)$k;
        }
      }
      $k++;
    }

    if(!in_array('accident_monitering',$role)){
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
