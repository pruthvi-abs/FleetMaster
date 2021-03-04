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
 *   id = "company_status",
 *   admin_label = @Translation("Company Status"),
 *   category = @Translation("Custom company graphs block example")
 * )
 */
class CompanyStatus extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    $database = \Drupal::database();
    $data = array();
    $current_user = \Drupal::currentUser();
    // unassigned
    $query = $database->query("SELECT count(distinct(uid)) as active_company FROM `users_field_data` fd, `user__roles` ur WHERE fd.uid = ur.entity_id AND ur.roles_target_id = 'company' AND `status` = 1");
    $active_company = $query->fetchAll();
    $active_company = json_decode(json_encode($active_company), true);
    // assigned
    $query = $database->query("SELECT count(distinct(uid)) as unactive_company FROM `users_field_data` fd, `user__roles` ur WHERE fd.uid = ur.entity_id AND ur.roles_target_id = 'company' AND `status` = 0");
    $unactive_company = $query->fetchAll();
    $unactive_company = json_decode(json_encode($unactive_company), true);

    if ((int)$active_company[0]['active_company'] != 0 || (int)$unactive_company[0]['unactive_company'] != 0) {
      $data[0]['label'] = 'Active Companies';
      $data[0]['value'] = (int)$active_company[0]['active_company'];
      $data[1]['label'] = 'Unactive Companies';
      $data[1]['value'] = (int)$unactive_company[0]['unactive_company'];
    }else{
      $data = '';
    }

    // dump($unactive_company);
    // dump($active_company);
    // dump($data);

    return array(
      '#type' => 'markup-cust',
      '#temp' => "custom-data-graph",
      '#markup' => json_encode($data),
      '#description' => 'my custom desc'
    );
  }
}
