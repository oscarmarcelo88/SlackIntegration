<?php
/**
 * Created by PhpStorm.
 * User: oscar_folder
 * Date: 17/08/2018
 * Time: 10:39
 */

//Curl service to fetch the questions from the forum
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://mps-support.jetbrains.com/api/v2/community/posts.json');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);
curl_close($ch);

//Print the data out onto the page.
$data_decode = json_decode($data, true);
//var_dump($data_decode);

date_default_timezone_set('Iceland'); // Set the time of the server of the Forum
$time_between_posts = strtotime("-4 days");


foreach ($data_decode['posts'] as $value)
{
    if (date("Y-m-d\TH:i:s.000\Z", $time_between_posts) < $value['created_at'] && $value['comment_count'] == 0) //Post in the last 2 days and with no comments
    {
        $value['details']= str_replace("'","",$value['details']); //remove the ' from the text
        $messageDataSend = "{
       'text': '*".strip_tags($value['title'])."*\n".strip_tags($value['details'])."',
       'username': 'MPS_Forum',
       'channel': 'C061EG9SL',
       'attachments': [
            {
              'fallback': 'You can answer here: ".strip_tags($value['html_url'])."',
              'actions': [
                {
                  'type': 'button',
                  'text': 'Answer :writing_hand:',
                  'url': '".strip_tags($value['html_url'])."'
                }
              ]
            }
          ]
        }";

        $url = "https://hooks.slack.com/services/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";
        $ch2 = curl_init($url);

        curl_setopt($ch2, CURLOPT_POST, 1);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $messageDataSend);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch2);
        curl_close($ch2);
    }
}