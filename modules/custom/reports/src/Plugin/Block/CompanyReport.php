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
 *   id = "company_report",
 *   admin_label = @Translation("report for companies"),
 *   category = @Translation("Custom Reports Module")
 * )
 */
class CompanyReport extends BlockBase
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
    $user_id = $current_user->id();

    // if($role[1] == 'company'){
    //   $userStorage = \Drupal::entityTypeManager()->getStorage('user');
    //   $query = $userStorage->getQuery();
    //   $uids = $query->condition('status', '1')->condition('roles', 'data_entry')->execute();
    //   $users = $userStorage->loadMultiple($uids);
    //   $keys = array_keys($users);
    
    //   array_push($user_ids,$current_user->id());
    //   $user_id = $current_user->id();
    //   for($i=0;$i<count($users);$i++){
    //     $company_id = $users[$keys[$i]]->get('field_operatorcompany')->target_id;
    //     if($current_user->id() == $company_id){
    //       array_push($user_ids,$users[$keys[$i]]->id());
    //       $user_id .= ',' . $users[$keys[$i]]->id();
    //     }
    //   }
    // }
    // elseif($role[1] == 'data_entry'){
    //   $user_id = $current_user->id();
    //   $user = \Drupal\user\Entity\User::load($current_user->id());
    //   $parent_company_id = $user->field_operatorcompany->getValue()[0]['target_id'];
    //   array_push($user_ids,$parent_company_id);
    //   $user_id .= ',' . $parent_company_id;
    // //   dump($user_id);
    // }
    
    // employee
    // $query = $database->query("SELECT * FROM `node_field_data` WHERE `type` LIKE 'driver' and `uid` in ( " . $user_id . ")");
    $query = $database->query("SELECT * FROM `webform_submission ` ws, `webform_submission_data` wsd  WHERE ws.uri = '/reports' and ws.sid = wsd.sid and ws.uid in ( " . $user_id . ")");
    $report_data = $query->fetchAll();
    $report_data = json_decode(json_encode($report_data), true);

    // $data[0]['total_emp'] = (int)$total_emp[0]['total_emp'];

    dump($report_data);
    // dump($data);

    return array(
      '#type' => 'markup-cust',
      '#temp' => "custom-data-graph",
      '#markup' => json_encode($data),
      '#description' => 'my custom desc'
    );
  }
}
