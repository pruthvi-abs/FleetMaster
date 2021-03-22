<?php

namespace Drupal\graphs\Plugin\Block;

// use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
// use Drupal\Core\Form\FormStateInterface;
// use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "emp_status",
 *   admin_label = @Translation("Employee Status"),
 *   category = @Translation("Custom company graphs block example")
 * )
 */
class EmpStatus extends BlockBase
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
    // unassigned
    // $query = $database->query("SELECT * FROM `node_field_data` WHERE `type` LIKE 'driver' AND `status` = 1 and `uid` = " . $current_user->id());
    $query = $database->query("SELECT count(*) as active_emp FROM `node_field_data` WHERE `type` LIKE 'driver' AND `status` = 0 and `uid` in (" . $user_id . ")");
    $active_emp = $query->fetchAll();
    $active_emp = json_decode(json_encode($active_emp), true);
    // assigned
    // $query = $database->query("SELECT * FROM `node_field_data` WHERE `type` LIKE 'driver' AND `status` = 0 and `uid` = ".$current_user->id());
    $query = $database->query("SELECT count(*) as inactive_emp FROM `node_field_data` WHERE `type` LIKE 'driver' AND `status` = 1 and `uid` in (" . $user_id . ")");
    $unactive_emp = $query->fetchAll();
    $unactive_emp = json_decode(json_encode($unactive_emp), true);

    if ((int)$active_emp[0]['active_emp'] != 0 && (int)$unactive_emp[0]['inactive_emp'] != 0) {
      $data[0]['label'] = 'Active Employees';
      $data[0]['value'] = (int)$active_emp[0]['active_emp'];
      $data[1]['label'] = 'Inactive Employees';
      $data[1]['value'] = (int)$unactive_emp[0]['inactive_emp'];
    }else{
      $data = '';
    }

    if(!in_array('employee',$role)){
      $data = '';
    }

      // dump($unactive_emp);
      // dump($active_emp);
      // dump($data);

    return array(
      '#type' => 'markup-cust',
      '#temp' => "custom-data-graph",
      '#markup' => json_encode($data),
      '#description' => 'my custom desc'
    );
  }
}
