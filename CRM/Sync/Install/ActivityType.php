<?php

/**
 * If a record is changed this is installed in a seperate activity
 *
 * @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 * @date 25-10-17 15:57
 * @license AGPL-3.0
 *
 */
class CRM_Sync_Install_ActivityType {

  public function create() {

    $params = [];
    $params['name'] = 'Synchronised';
    $params['label'] = 'Synchronised';
    $params['option_group_id'] = "activity_type";
    $params['is_active'] = 1;

    try {
      $optionValueId = civicrm_api3('OptionValue', 'getvalue', [
        'return' => "id",
        'option_group_id' => "activity_type",
        'name' => "Synchronised",
      ]);
      $params['id'] = $optionValueId;
    } catch (Exception $ex) {

    }

    $result = civicrm_api3('OptionValue', 'create', $params);
    if ($result['is_error']) {
      throw new Exception($result['error_message']);
    }

  }

}