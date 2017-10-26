<?php
/**
 *
 * <add a short description>
 *
 *  @author Klaas Eikelbooml (CiviCooP) <klaas.eikelboom@civicoop.org>
 *  @date 22-10-17 16:41
 *  @license AGPL-3.0
 *
 */

class CRM_Sync_Utils_Rest {

  public static function call($action,$params){



    $config = CRM_Sync_Config::singleton();
    $url =     $config->get('ilgasync_url');
    $siteKey = $config->get('ilgasync_sitekey');
    $apiKey  = $config->get('ilgasync_apikey');

    $json = urlencode(json_encode($params));

    $callUrl= "{$url}?entity=Sync&action={$action}&api_key={$apiKey}&key={$siteKey}&json={$json}";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_PUT, 1);
    curl_setopt($curl, CURLOPT_URL,$callUrl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $curlresult = curl_exec($curl);

    if (curl_errno($curl)) {
      curl_close ($curl);
      throw new Exception('Local Curl Error:' . curl_error($curl));
    } else {
      curl_close ($curl);
    }

    $result = json_decode($curlresult,TRUE);



    return $result;

  }

}