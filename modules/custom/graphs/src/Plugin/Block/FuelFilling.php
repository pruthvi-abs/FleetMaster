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
 *   id = "fule_filling",
 *   admin_label = @Translation("Fuel Filling"),
 *   category = @Translation("Custom company graphs block example")
 * )
 */
class FuelFilling extends BlockBase
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



    if ($role[1] == 'company') {
      $userStorage = \Drupal::entityTypeManager()->getStorage('user');
      $query = $userStorage->getQuery();
      $uids = $query->condition('status', '1')->condition('roles', 'data_entry')->execute();
      $users = $userStorage->loadMultiple($uids);
      $keys = array_keys($users);

      array_push($user_ids, $current_user->id());
      $user_id = $current_user->id();
      for ($i = 0; $i < count($users); $i++) {
        $company_id = $users[$keys[$i]]->get('field_operatorcompany')->target_id;
        if ($current_user->id() == $company_id) {
          array_push($user_ids, $users[$keys[$i]]->id());
          $user_id .= ',' . $users[$keys[$i]]->id();
        }
      }
    } elseif ($role[1] == 'data_entry') {
      $user_id = $current_user->id();
      $user = \Drupal\user\Entity\User::load($current_user->id());
      $parent_company_id = $user->field_operatorcompany->getValue()[0]['target_id'];
      array_push($user_ids, $parent_company_id);
      $user_id .= ',' . $parent_company_id;
      //   dump($user_id);
    }

    // dump($month);
    // dump($year);

    $fuel_query = "SELECT title as vehicle_name,field_efficiency_average_value as average from node__field_efficiency_average eavg, node_field_data d where d.nid = eavg.entity_id and d.uid in (" . $user_id . ") order by field_efficiency_average_value DESC limit 5";
    $query = $database->query($fuel_query);
    $months = $query->fetchAll();
    // dump($fuel_query);
    $months = json_decode(json_encode($months), true);
    // dump($months);

    for ($i = 0; $i < count($months); $i++) {
      $data[$i]['x'] = $months[$i]['average'];
      $data[$i]['y'] = $months[$i]['vehicle_name'];
    }

    if (!in_array('fuel_filling', $role)) {
      $data = '';
    }
    // dump($data);

    return array(
      '#type' => 'markup-cust',
      '#temp' => "custom-data-graph",
      '#markup' => json_encode($data),
      '#description' => 'my custom desc'
    );
  }
}
