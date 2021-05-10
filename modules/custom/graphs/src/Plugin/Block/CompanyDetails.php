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
 *   id = "company_details",
 *   admin_label = @Translation("Company Details"),
 *   category = @Translation("All Company Details Block example")
 * )
 */
// SELECT uid FROM user__roles,users_field_data where uid = entity_id AND STATUS = 0 group by uid
// SELECT * FROM user__roles,users_field_data where uid = entity_id AND STATUS = 0 AND roles_target_id = 'company' group by uid
class CompanyDetails extends BlockBase
{
  /**
   * {@inheritdoc}
   */
  public function build()
  {
    global $gcustdata;
    $current_user = \Drupal::currentUser();
    $database = \Drupal::database();

    // $query = "SELECT distinct(fd.uid) as 'user_id', fd.created as 'timestamp', fd.status as 'status', fc.field_company_name_value as 'company_name', fp.field_package_value as 'package' FROM user__field_company_name fc, user__roles ur, users_field_data fd, user__field_package fp where fd.uid = ur.entity_id AND fd.uid = fp.entity_id AND fd.uid = fc.entity_id AND roles_target_id = 'company' order by fd.uid ASC";
    $query = "SELECT distinct(fd.uid) as 'user_id', fd.status as 'status', fc.field_company_name_value as 'company_name', fp.field_package_value as 'package', fpsdate.field_package_start_date_value as 'start_date' FROM user__field_company_name fc, user__roles ur, users_field_data fd, user__field_package fp, user__field_package_start_date fpsdate where fd.uid = ur.entity_id AND fd.uid = fp.entity_id AND fd.uid = fc.entity_id AND fd.uid = fpsdate.entity_id AND roles_target_id = 'company' order by fd.uid ASC";
    $query = $database->query($query);
    $results = $query->fetchAll();
    $results = json_decode(json_encode($results), true);
    
    // dump($query);
    // dump($results);

    for($i = 0;$i<count($results);$i++){
      $data[$i]['user_id'] = $results[$i]['user_id'];
      $data[$i]['company_name'] = $results[$i]['company_name'];
      $data[$i]['package'] = $results[$i]['package'];
      if($results[$i]['status'] == '1'){
        $data[$i]['status'] = 'Active';
      } else {
        $data[$i]['status'] = 'Inactive';
      }
    }
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
