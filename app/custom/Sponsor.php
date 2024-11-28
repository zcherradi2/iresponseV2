<?php
declare(strict_types=1);

namespace IR\Custom;


// if (!defined('IR_START')) {
//     exit('<pre>No direct script access allowed</pre>');
// }

class Sponsor
{
    public static function getOffer($id,$apiKey,$type)
    {
        // return ['data'=>'dsa'];
        if($type=="hitpath"){
            $url = "https://partner.traxstax.com/api/campaigns/" . $id;
            $headers = [
                'Accept: application/json',
                'Authorization: Bearer ' . $apiKey,
            ];
        } else if($type=="everflow"){
            $url = "https://api.eflow.team/v1/affiliates/offers/" . $id;
            $headers = [
                "X-Eflow-API-Key: $apiKey",
                "Content-Type: application/json"
            ];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        return json_decode($response,true);
    }
}

// // Usage example
// $apiKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNGNmNzRjYTMzOTlhZDRjODNmZDgwNzEzY2RhNDAyNmJhZjVlMDdkNTM2YTM2ZDljMjcwOWMyYmViMDlkM2RmNThkOWNiNTBlNGE4YjUzY2YiLCJpYXQiOjE3MjMxMzMzNzIuMTQ5MTk3LCJuYmYiOjE3MjMxMzMzNzIuMTQ5MiwiZXhwIjoxNzU0NjY5MzcyLjE0MzEwMSwic3ViIjoiMzUyMTc3Iiwic2NvcGVzIjpbIioiXX0.RDZkK1IDIs95-Qb2LzLHsU-Cr57AMkt4exd4vDRnjBbjNZ7SyarIme6TCfogzZoHbo6KTLbDC_fyW4Qgi_gIlDKUOkkk_4wA2EvBxPrYnNo8tmsXV0lSEWcdeHGi8J-EZfoyJrFYHcqEGOsGor4Ie8ojhl7YdK7Cg64EvZhp8Y31LdLcX2sWFVRFtxn-49UT4X6XHcW2LHeK7uFNzDOt4cpaTdsqG1DzOItv996bTM1bbe5vO1h6SoJwQXsOR0tWAQiSegPztxSwFsVVLalIZJZWr-ySm6hk3qudFbKxQ1gnd7ogMv47cVTFECh_VzgUY1-bmqnFCa8mwjHhY9sh64kAjFcwPMx-IaD0uSM9j8dH8pDSh9200ZHMyPQGav5_yOo6z5Oda-Zh8Svpn19ZjEc9C87b5Ic7YIJM-QGkFE-SNDZgA6QZbrnXnn_7a6bo3NjpRMP45OskJpmQGiIDgU2u3iHEPeCh_yyZAx9Rgg5-2fPOfkIOeBwbHnMe0NcL2N1hYnogKN_KSAS2nCm05LLlYHXHBcgja1JlVgwm3X8K0R-6LjjeZNfVp3kSbp-Bau5zslywZtI1B45byqGplNwXhFrTXTWqq1WshkoV0mPGFXkcTk23m6Z-xGgdNEcDvLQx8WVrmfqDMKDwGGTwMf539CWPrVE2rS7mbwNmjw0';
// $traxstaxClient = new Sponsor();
// $offer = $traxstaxClient->getOffer(768,$apiKey);

// print_r(json_encode($offer));

?>
