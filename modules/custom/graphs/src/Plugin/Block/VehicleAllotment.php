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
 *   id = "vehicle_allotment",
 *   admin_label = @Translation("Vehicle Allotment"),
 *   category = @Translation("Custom company graphs block example")
 * )
 */
class VehicleAllotment extends BlockBase
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
    // $query = $database->query("SELECT * FROM `node_field_data` WHERE `type` LIKE 'vehicle' AND `status` = 1 and `uid` in (" . $user_id . ")");
    $query = $database->query("SELECT count(*) as unassigned_vehicle FROM `node_field_data` WHERE `type` LIKE 'vehicle' AND `status` = 1 and `uid` in (" . $user_id . ")");
    $unassigned = $query->fetchAll();
    $unassigned = json_decode(json_encode($unassigned), true);
    // dump($query);
    // assigned
    // $query = $database->query("SELECT * FROM `node_field_data` WHERE `type` LIKE 'vehicle' AND `status` = 0 and `uid` in (" . $user_id . ")");
    $query = $database->query("SELECT count(*) as assigned_vehicle FROM `node_field_data` WHERE `type` LIKE 'vehicle' AND `status` = 0 and `uid` in (" . $user_id . ")");
    $assigned = $query->fetchAll();
    $assigned = json_decode(json_encode($assigned), true);
    // dump($query);

    if((int)$unassigned[0]['unassigned_vehicle'] != 0 && (int)$assigned[0]['assigned_vehicle'] != 0){ 
      $data[0]['label'] = 'Assigned Vehicle';
      $data[0]['value'] = (int)$assigned[0]['assigned_vehicle'];
      $data[1]['label'] = 'Unassigned Vehicle';
      $data[1]['value'] = (int)$unassigned[0]['unassigned_vehicle'];
    } else{
      $data = '';
    }

    // dump($unassigned);
    // dump($assigned);
    // dump($data);


    return array(
      '#type' => 'markup-cust',
      '#temp' => "custom-data-graph",
      '#markup' => json_encode($data),
      '#description' => 'my custom desc'
    );
  }
}
