<?php
require_once "../database/db.php";
function UpdateUpgrade($a){
    if(isset($a)){
        try{
            $db_token_check = new db();
            $db_token_check = $db_token_check->connect();
            $sth_delete = $db_token_check->prepare("SELECT list_project_id FROM list_project");
            $sth_delete->execute();
            $existing_id_all = array();
                while($myrow = $sth_delete->fetch(PDO::FETCH_ASSOC)){ 
                        $existing_id_all[] = $myrow['list_project_id']; 
                }
                foreach($existing_id_all as $del_it){
                    $t = $del_it;
                    $shr = $db_token_check->prepare("DELETE FROM list_project WHERE list_project_id = :t");
                    $shr->bindParam(':t', $t, PDO::PARAM_INT);
                    $shr->execute();
                }
                unset($existing_id_all);
                $sth = $db_token_check->prepare('UPDATE token SET token = :a WHERE  id = 1');
                $sth->bindParam(':a', $a, PDO::PARAM_INT);
                $sth->execute();
                $a = null; 
                $db_token_check = null;
                echo "Токен успешно установлен!"; 
            } 
            catch(Exception $e){
                echo 'Проблема. раздел: устоновка токена ',  $e->getMessage(), "\n";
            }
    }
}
$a = $_POST['a'];
//$token = $a;
$url = 'https://ru.serpseeker.com/api/projects';
$ch = curl_init( $url );
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Token ' . $a));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
if(!curl_errno($ch)) {
    switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case 200:  UpdateUpgrade($a);
            break;
        case 404:  exit("Ошибка: Неправильно указан подключаемый токен!");
            break;
        default:
            echo 'Неожиданный HTTP заголовок: ', $http_code, "\n";
    }
}
curl_close( $ch );
?>