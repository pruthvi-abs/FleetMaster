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
 *   id = "employee_count",
 *   admin_label = @Translation("Number of Employee"),
 *   category = @Translation("Total Number of employees block example")
 * )
 */
class EmployeeCount extends BlockBase
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
    // dump($role[1]);
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
    }
      // dump($user_id);
    
    // employee
    // $query = $database->query("SELECT * FROM `node_field_data` WHERE `type` LIKE 'driver' and `uid` in ( " . $user_id . ")");
    $query = $database->query("SELECT count(*) as total_emp FROM `node_field_data` WHERE `type` LIKE 'driver' and `uid` in ( " . $user_id . ")");
    $total_emp = $query->fetchAll();
    $total_emp = json_decode(json_encode($total_emp), true);

    $data[0]['total_emp'] = (int)$total_emp[0]['total_emp'];

    if(!in_array('employee',$role)){
      $data = '';
    }

    // dump($total_emp);
    // dump($data);

    return array(
      '#type' => 'markup-cust',
      '#temp' => "custom-data-graph",
      '#markup' => json_encode($data),
      '#description' => 'my custom desc'
    );
  }
}
