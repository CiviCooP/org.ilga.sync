<?php
/**
 * Collection of upgrade steps.
 */
class CRM_Sync_Upgrader extends CRM_Sync_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed.
   **/
  public function install() {
    $installOptionGroup = new CRM_Sync_Install_OptionGroup();
    $params = [
      'name'  => 'ilgasync_destination',
      'title' => 'Ilga Sync Destinations',
      'is_active' => 1,
      'is_reserved' => 1,
      'option_values' =>[
        ['name'  => 'headquarters',
          'value' => 'hq',
          'label' => 'Head Quarters',
          'is_active' => 1],
        ['name'  => 'region',
          'value' => 'region',
          'label' => 'Region',
          'is_active' => 1],
      ]
    ];
    $installOptionGroup->create($params);
    $installTag = new CRM_Sync_Install_Tag();
    $installTag->create('Sync');

    $installActivityType = new CRM_Sync_Install_ActivityType();
    $installActivityType->create();
  }

  /**
   * Example: Work with entities usually not available during the install step.
   *
   * This method can be used for any post-install tasks. For example, if a step
   * of your installation depends on accessing an entity that is itself
   * created during the installation (e.g., a setting or a managed entity), do
   * so here to avoid order of operation problems.
   *
  public function postInstall() {
    $customFieldId = civicrm_api3('CustomField', 'getvalue', array(
      'return' => array("id"),
      'name' => "customFieldCreatedViaManagedHook",
    ));
    civicrm_api3('Setting', 'create', array(
      'myWeirdFieldSetting' => array('id' => $customFieldId, 'weirdness' => 1),
    ));
  }

  /**
   * Example: Run an external SQL script when the module is uninstalled.
   * */
  public function uninstall() {
    $installOptionGroup = new CRM_Sync_InstallOptionGroup();
    $installOptionGroup->uninstall(['name'=> 'ilgasync_destination']);
  }




  /**
   * Example: Run an upgrade with a query that touches many (potentially
   * millions) of records by breaking it up into smaller chunks.
   *
   * @return TRUE on success
   * @throws Exception
  */
  public function upgrade_1001() {
    $this->ctx->log->info('Installing tags');
    $installTag = new CRM_Sync_Install_Tag();
    $installTag->create('Sync');

    $installActivityType = new CRM_Sync_Install_ActivityType();
    $installActivityType->create();
    return TRUE;
  } // */

  public function upgrade_1002(){
    $params =  array (
      'name' => 'Call Job.Sync API',
      'description' => 'Call Job.Sync API',
      'run_frequency' => 'Hourly',
      'api_entity' => 'Job',
      'api_action' => 'Sync',
      'parameters' => '',
    );
    civicrm_api3('Job','create',$params);
    return TRUE;
  }

}
