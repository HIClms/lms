<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Class ZeptomailService.
 */
class ZeptomailService
{
    public static function sendMailZeptoMail($subject, $message, $email)
    {
        $curl = curl_init();
        $s_key = '';
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.zeptomail.com/v1.1/email",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
                "from": { "address": "noreply@leverpay.io"},
                "to": [{"email_address": {"address": ' . $email . ', "name": "LeverPay"}}],
                "subject":' . $subject . ',
                "htmlbody":"' . preg_replace('/\n/', '', $message) . '",
            }',
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Zoho-enczapikey {$s_key}",
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }

    public static function sendTemplateZeptoMail($templateId, $body, $email)
    {
        $s_key = '';
        $curl = curl_init();
        $info = json_encode($body);
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.zeptomail.com/v1.1/email/template",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => '{
                "merge_info":' . $info . ',
                "from": { "address": "noreply@leverpay.io"},
                "to": [{"email_address": {"address": ' . $email . ', "name": "LeverPay"}}],
                "template_key":' . $templateId . ',
            }',
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Zoho-enczapikey {$s_key}",
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    }
}
