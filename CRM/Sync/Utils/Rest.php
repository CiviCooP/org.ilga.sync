<?php
/**
 *  Utility class to to the execute a rest call. Includes the option
 *  to do a mock call where the results of the rest call are not
 *  sent but stored in an array (usable for testing purposes)
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 22-10-17 16:41
 *  @license AGPL-3.0
 *
 */
class CRM_Sync_Utils_Rest {

  private static $_mock;

  private static function pushMock($action,$params,$url){
    if(!isset(self::$_mock)){
      self::$_mock = array();
    }
    self::$_mock[] = array(
      'action' => $action,
      'params'  => $params,
      'url'    => $url,
    );
  }

  public static function getMock(){
    if(!isset(self::$_mock)){
      self::$_mock = array();
    }
    return self::$_mock;
  }

  public static function forget(){
    self::$_mock = array();
  }

  public static function call($action,$params){

    /* find the paramaters for the rest call - these are configured in the ilga
       sync settings screen
    */
    $config = CRM_Sync_Config::singleton();
    $url =     $config->get('ilgasync_url');
    $siteKey = $config->get('ilgasync_sitekey');
    $apiKey  = $config->get('ilgasync_apikey');

    $json = urlencode(json_encode($params));

    $callUrl= "{$url}?entity=Sync&action={$action}&api_key={$apiKey}&key={$siteKey}&json={$json}";

    if(isset($params['mock'])&&$params['mock']){
      self::pushMock($action,$params,$callUrl);
      return 'mocked';
    } else {

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_PUT, 1);
      curl_setopt($curl, CURLOPT_URL, $callUrl);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $curlresult = curl_exec($curl);

      if (curl_errno($curl)) {
        curl_close($curl);
        throw new Exception('Call failed locally with the following Curl error number :' . curl_errno($curl) . 'and message ' . curl_error($curl));
      }
      else {
        curl_close($curl);
      }
      $result = json_decode($curlresult, TRUE);
      return $result;
    }
  }
}
