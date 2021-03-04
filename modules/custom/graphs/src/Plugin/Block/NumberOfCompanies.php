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
 *   id = "number_of_companies",
 *   admin_label = @Translation("Number Of Companies"),
 *   category = @Translation("Number Of Registered Companies's graphs block example")
 * )
 */
// SELECT uid FROM user__roles,users_field_data where uid = entity_id AND STATUS = 0 group by uid
// SELECT * FROM user__roles,users_field_data where uid = entity_id AND STATUS = 0 AND roles_target_id = 'company' group by uid
class NumberOfCompanies extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    global $gcustdata;
    $current_user = \Drupal::currentUser();
    // dump($current_user);

    $database = \Drupal::database();

    $bronze = "SELECT count(distinct(uid)) AS bronze FROM user__roles ur,users_field_data,user__field_package fp where uid = ur.entity_id AND ur.entity_id = fp.entity_id AND fp.entity_id = uid AND STATUS = 1 AND roles_target_id = 'company' and field_package_value = 'Bronze'";
    $bronze_query = $database->query($bronze);
    $bronze_number = $bronze_query->fetchAll();
    $bronze_number = json_decode(json_encode($bronze_number), true);
    
    $silver = "SELECT count(distinct(uid)) AS silver FROM user__roles ur,users_field_data,user__field_package fp where uid = ur.entity_id AND ur.entity_id = fp.entity_id AND fp.entity_id = uid AND STATUS = 1 AND roles_target_id = 'company' and field_package_value = 'Silver'";
    $silver_query = $database->query($silver);
    $silver_number = $silver_query->fetchAll();
    $silver_number = json_decode(json_encode($silver_number), true);
    
    $gold = "SELECT count(distinct(uid)) AS gold FROM user__roles ur,users_field_data,user__field_package fp where uid = ur.entity_id AND ur.entity_id = fp.entity_id AND fp.entity_id = uid AND STATUS = 1 AND roles_target_id = 'company' and field_package_value = 'Gold'";
    $gold_query = $database->query($gold);
    $gold_number = $gold_query->fetchAll();
    $gold_number = json_decode(json_encode($gold_number), true);
    
    $platinum = "SELECT count(distinct(uid)) AS platinum FROM user__roles ur,users_field_data,user__field_package fp where uid = ur.entity_id AND ur.entity_id = fp.entity_id AND fp.entity_id = uid AND STATUS = 1 AND roles_target_id = 'company' and field_package_value = 'Platinum'";
    $platinum_query = $database->query($platinum);
    $platinum_number = $platinum_query->fetchAll();
    $platinum_number = json_decode(json_encode($platinum_number), true);
    
    // dump($bronze);
    // dump($bronze_number);
    // dump($silver_number);
    // dump($gold_number);
    // dump($platinum_number);
    
    $data[0]['y'] = "Bronze";
    $data[0]['x'] = $bronze_number[0]['bronze'];
    $data[1]['y'] = "Silver";
    $data[1]['x'] = $silver_number[0]['silver'];
    $data[2]['y'] = "Gold";
    $data[2]['x'] = $gold_number[0]['gold'];
    $data[3]['y'] = "Platinum";
    $data[3]['x'] = $platinum_number[0]['platinum'];

    if(empty($data)){
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
