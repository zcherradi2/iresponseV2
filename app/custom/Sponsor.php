<?php
declare(strict_types=1);

namespace IR\Custom;

use Exception;
use IR\App\Models\Affiliate\Suppression as Suppression;
use IR\App\Models\Affiliate\Offer as Offer;
use IR\App\Models\Affiliate\AffiliateNetwork as AffiliateNetwork;
use IR\App\Models\Lists\SuppressionEmail as SuppressionEmail;
use IR\App\Models\Lists\Email as Email;
use IR\App\Models\Lists\DataList as DataList;

include 'CustomSql.php';
// if (!defined('IR_START')) {
//     exit('<pre>No direct script access allowed</pre>');
// }

class Sponsor
{
    static public $supportedSponsorsIds = [5];
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

    public static function getOptismoId($offerId, $apiKey = 'mD1hn1vTBujuUcQmXmz5w') {
        $url = "https://api.eflow.team/v1/affiliates/offers/{$offerId}";
        
        // Set up headers
        $headers = [
            "X-Eflow-API-Key: {$apiKey}",
            "Content-Type: application/json"
        ];
        
        // Initialize cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        try {
            // Check response and parse
            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if (isset($data['relationship']['integrations']['optizmo']['mailer_access_key'])) {
                    $key = $data['relationship']['integrations']['optizmo']['mailer_access_key'];
                    $parts = explode('/', $key);
                    return end($parts); // Get the last part of the string
                }
            }
            // Code that may throw an exception
        } catch (Exception $e) {
            return null;
            // Code to handle the exception
        }

        return null; // Default return if something fails
    }
    public static function getDownloadUrl($campaign, $token = 'xYTcRADff5lk1d6Px0UCcQn62ccT8qKb') {
        // The URL
        $url = "https://mailer-api.optizmo.net/accesskey/download/{$campaign}?token={$token}";
    
        // Initialize cURL
        $ch = curl_init($url);
    
        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        // Check for success
        if ($httpCode === 200) {
            $data = json_decode($response, true); // Decode JSON response
    
            if (isset($data['download_link'])) {
                return $data['download_link'];
            } else {
                return ''; // Key not found
            }
        } else {
            // Handle error
            return '';
        }
    }
    
    public static function downloadZip($zipFileUrl, $downloadPath = '/usr/gm/supDownloads', $timeout = 30) {
        // Set the download path and ensure it exists
        if (!is_dir($downloadPath)) {
            mkdir($downloadPath, 0777, true); // Create the directory if it doesn't exist
        }
        // Initialize cURL session
        $ch = curl_init($zipFileUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
    
        // Execute the request
        $response = curl_exec($ch);
    
        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headers = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);
        curl_close($ch);
    
        if ($httpCode !== 200) {
            echo "Failed to download the file. HTTP Status Code: $httpCode\n";
            return null;
        }
    
        // Parse headers to find the filename
        preg_match('/filename="?([^"]+)"?/', $headers, $matches);
        $filename = $matches[1] ?? 'downloaded.zip';
    
        // Sanitize and set the full file path
        $filename = basename($filename); // Ensure it's a safe filename
        $filePath = $downloadPath . DIRECTORY_SEPARATOR . $filename;
    
        // Save the file
        file_put_contents($filePath, $body);
    
        // echo "File downloaded successfully as $filePath\n";
        return $filePath;
    }
    public static function unzipFile($zipFilePath, $extractToPath) {
        // Ensure the ZIP file exists
        if (!file_exists($zipFilePath)) {
            echo "Error: ZIP file not found: $zipFilePath\n";
            return false;
        }
    
        // Ensure the extraction directory exists
        if (!is_dir($extractToPath)) {
            mkdir($extractToPath, 0777, true);
        }
    
        // Build the unzip command
        $command = 'unzip -o "' . $zipFilePath . '" -d "'. $extractToPath.'"';
    
        // Execute the command
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);
    
        // Check the result
        if ($returnVar === 0) {
            return true;
        } else {
            return false;
        }
    }
    
    static function Executesuppression($emailsMd5,$suppPath){
        // Initialize the result list
        $result = [];
        $emailsMd5 = array_flip($emailsMd5);
        // Check if the suppression file exists
        if (!file_exists($suppPath)) {
            return [];
        }
        // Read the suppression file into an array, each line is one MD5 hash
        $suppMd5List = [];
        $handle = fopen($suppPath, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if (isset($emailsMd5[$line])) {
                    $result[] = $line;
                }
            }
            fclose($handle);
        } else {
            // Handle error opening the file
            return [];
        }

        // Convert the suppression list into a set (array with keys for quick lookup)
        // $suppMd5Set = array_flip($suppMd5List);

        // // Check each email MD5 against the suppression set
        // foreach ($emailsMd5 as $emailMd5) {
        //     if (isset($suppMd5Set[$emailMd5])) {
        //         // Add to result if it exists in the suppression set
        //         $result[] = $emailMd5;
        //     }
        // }

        // Return the result list of matching MD5s
        return $result;
    }
    public static function startSuppression($processId){
        // $process = Suppression::first(Suppression::FETCH_ARRAY,['id = ?',[$processId]],['affiliate_network_id','offer_id','lists_ids']);
        $process = Suppression::first(Suppression::FETCH_OBJECT,['id = ?',[$processId]]);
        // $process->setProgress("100%");
        // $process->setFinishTime(date('Y-m-d H:i:s'));
        // $process->setStatus("Error");
        // $process->update();

        $dbInfo = getDbInfo("clients");
        if(!$dbInfo){
            $process->setProgress("100%");
            $process->setFinishTime(date('Y-m-d H:i:s'));
            $process->setStatus("Error");
            $process->update();
            return 'error reading db config';
        }
        $host = $dbInfo['host'];
        $db = $dbInfo['database'];
        $user = $dbInfo['username'];
        $pass = $dbInfo['password'];
        $port = $dbInfo['port'];



        $offer = Offer::first(Offer::FETCH_ARRAY,['id = ?',[intval($process->getOfferId())]],['production_id',"auto_sup","default_suppression_link","affiliate_network_id"]);
        $offerId = $offer['production_id'];

        $defSupLink = $offer['default_suppression_link'];
        $suplink = 'dasdad';
        if($defSupLink){
            $suplink = $defSupLink;
        }else if(in_array(intval($offer['affiliate_network_id']), Sponsor::$supportedSponsorsIds)){
            $affilate = AffiliateNetwork::first(AffiliateNetwork::FETCH_ARRAY,['id = ?',[intval($offer['affiliate_network_id'])]],['api_key']);
            $apiKey = $affilate['api_key'];
            $opId = Sponsor::getOptismoId($offerId,$apiKey);
            $downloadLink = Sponsor::getDownloadUrl($opId);
            if($downloadLink){
                $suplink = $downloadLink;
            }

        }
        $zipPath = Sponsor::downloadZip($suplink);
        $unzipped = Sponsor::unzipFile($zipPath,"/usr/gm/supDownloads/unzipped");
        if($unzipped){
            $command = 'rm -f "'.$zipPath.'"';
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);
        }else{
            $process->setProgress("100%");
            $process->setFinishTime(date('Y-m-d H:i:s'));
            $process->setStatus("Error");
            $process->update();
            return 'failed unzipping';
        }
        // Find the position of the last .zip
        $lastZipPos = strrpos($zipPath, '.zip');

        // Remove only the last .zip
        if ($lastZipPos !== false) {
            $unzippedPath = substr($zipPath, 0, $lastZipPos).".txt";
        } else {
            $unzippedPath = $zipPath; // If .zip not found, return the original file name
        }
        // Transform the path
        $unzippedPath = str_replace(
            "supDownloads/", // Replace this part
            "supDownloads/unzipped/", // With this part
            $unzippedPath // In this string
        );


        $pdo = getPdo($host,$port,$db,$user,$pass);
        if(!isset($pdo)){
            $process->setProgress("100%");
            $process->setFinishTime(date('Y-m-d H:i:s'));
            $process->setStatus("Error");
            $process->update();
            return "can't open sql";
        }
        $listsId = explode(",",$process->getListsIds());
        $foundCount = 0;
        $error = false;
        foreach($listsId as $listId){
            $listInfo = DataList::first(DataList::FETCH_ARRAY,['id = ?',[intval($listId)]],['table_schema','table_name']);
            $schemaName = $listInfo['table_schema'];
            $tableName = $listInfo['table_name'];
            $query = "
            SELECT email_md5
            FROM {$schemaName}.{$tableName} t
            WHERE (is_seed = 'f' OR is_seed IS NULL)
            AND (is_hard_bounced = 'f' OR is_hard_bounced IS NULL)
            AND (is_blacklisted = 'f' OR is_blacklisted IS NULL)
            ORDER BY email_md5 ASC
            ";
                // Prepare and execute the query
            $stmt = $pdo->prepare($query);
            $stmt->execute();
    
            // Fetch the results as an array
            $emailMd5Array = $stmt->fetchAll($pdo::FETCH_COLUMN);
            // $mails = Email::first(Email::FETCH_OBJECT,['id = ?',[2]]);
            $foundList = Sponsor::Executesuppression($emailMd5Array,$unzippedPath);
            if(count($foundList)){
                // clients->suppressions->
                $schema = "suppressions";
                $supTableName = "sup_list_".$offer['affiliate_network_id']."_".$offerId."_".$listId;
                createSupTableIfNotExists($pdo,$schema,$supTableName);
                if(clearTable($pdo,$schema,$supTableName)){
                    $sql = "INSERT INTO $schema.$supTableName (email_md5) VALUES (:email_md5)";
                    $stmt = $pdo->prepare($sql);
                    foreach ($foundList as $email_md5) {
                        // Bind the email_md5 value
                        $stmt->execute([':email_md5' => $email_md5]);
                    }
                    $foundCount += count($foundList);
                }else{
                    $error = true;
                }
            }

        }
        if($foundCount == 0 && $error){
            $process->setStatus("Error");
        }else{
            $process->setStatus("Completed");
        }
        $process->setEmailsFound("$foundCount");
        $process->setProgress("100%");
        $process->setFinishTime(date('Y-m-d H:i:s'));
        $process->update();

        $domainTxtPath = str_replace("suppression_","domains_",$unzippedPath);
        $domainTxtPath = str_replace("-MD5.txt",".txt",$domainTxtPath);

        $command = 'rm -f "'.$unzippedPath.'";rm -f "'.$domainTxtPath.'"';
        $output = [];
        $returnVar = 0;
        exec($command, $output, $returnVar);
        
        return "done";
    }
}

// // Usage example
// $apiKey = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNGNmNzRjYTMzOTlhZDRjODNmZDgwNzEzY2RhNDAyNmJhZjVlMDdkNTM2YTM2ZDljMjcwOWMyYmViMDlkM2RmNThkOWNiNTBlNGE4YjUzY2YiLCJpYXQiOjE3MjMxMzMzNzIuMTQ5MTk3LCJuYmYiOjE3MjMxMzMzNzIuMTQ5MiwiZXhwIjoxNzU0NjY5MzcyLjE0MzEwMSwic3ViIjoiMzUyMTc3Iiwic2NvcGVzIjpbIioiXX0.RDZkK1IDIs95-Qb2LzLHsU-Cr57AMkt4exd4vDRnjBbjNZ7SyarIme6TCfogzZoHbo6KTLbDC_fyW4Qgi_gIlDKUOkkk_4wA2EvBxPrYnNo8tmsXV0lSEWcdeHGi8J-EZfoyJrFYHcqEGOsGor4Ie8ojhl7YdK7Cg64EvZhp8Y31LdLcX2sWFVRFtxn-49UT4X6XHcW2LHeK7uFNzDOt4cpaTdsqG1DzOItv996bTM1bbe5vO1h6SoJwQXsOR0tWAQiSegPztxSwFsVVLalIZJZWr-ySm6hk3qudFbKxQ1gnd7ogMv47cVTFECh_VzgUY1-bmqnFCa8mwjHhY9sh64kAjFcwPMx-IaD0uSM9j8dH8pDSh9200ZHMyPQGav5_yOo6z5Oda-Zh8Svpn19ZjEc9C87b5Ic7YIJM-QGkFE-SNDZgA6QZbrnXnn_7a6bo3NjpRMP45OskJpmQGiIDgU2u3iHEPeCh_yyZAx9Rgg5-2fPOfkIOeBwbHnMe0NcL2N1hYnogKN_KSAS2nCm05LLlYHXHBcgja1JlVgwm3X8K0R-6LjjeZNfVp3kSbp-Bau5zslywZtI1B45byqGplNwXhFrTXTWqq1WshkoV0mPGFXkcTk23m6Z-xGgdNEcDvLQx8WVrmfqDMKDwGGTwMf539CWPrVE2rS7mbwNmjw0';
// $traxstaxClient = new Sponsor();
// $offer = $traxstaxClient->getOffer(768,$apiKey);

// print_r(json_encode($offer));

?>
