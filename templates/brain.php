<?php

include "../database/db.php";

//Получаем токен из БД 
function get_token(){
    try{
        $db_token = new db();
        $db_token = $db_token->connect();
        $stmt_token = $db_token->query("SELECT token FROM token");
        $response_token = $stmt_token->fetchAll(PDO::FETCH_OBJ);
        $a = json_encode($response_token);
        $b = json_decode($a, true);
        $db = null;
        return $b[0]['token'];
    } catch(Exception $e){
        echo 'Проблема. раздел: получение токена ',  $e->getMessage(), "\n";
    }
}
//Удаление записей из таблицы "list_project"
function dropTable(){
    $db_id = new db();
    $db_id = $db_id->connect();
    $sql_for_update_list = "SELECT list_project_id FROM list_project";
    $go = $db_id->query($sql_for_update_list);
        while($myrow = $go->fetch(PDO::FETCH_ASSOC)){ 
                $existing_id_all[] = $myrow['list_project_id']; //DATABASE
        }
        if(!empty($existing_id_all)){
            $db_destroys = new db();
            $db_destroys = $db_destroys->connect();
            foreach($existing_id_all as $del_it){
                $t = $del_it;
                $shr = $db_destroys->prepare("DELETE FROM list_project WHERE list_project_id = :t");
                $shr->bindParam(':t', $t, PDO::PARAM_INT);
                $shr->execute();
            }
        }
    $db_id = null;
}
//Подключение к ресурсу через cURL!
function connectToSite($url, $token){
    try{
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Token ' . $token));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $content = curl_exec( $ch );
        curl_close( $ch );
        if ($content){
            return  json_decode($content, true);
        }
    }catch(Exception $e){
        echo 'Проблема. некорректный токен ',  $e->getMessage(), "\n";
    } 
}
function CheckHeaderIn404($url, $token){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_URL, $url );
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Token ' . $token));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    if (!curl_errno($ch)) {
        switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
        case 200: 
            break;
        case 404:  exit("<script>alert('HTTP Header: 404 Not Found');</script>");
            break;
        default:
            echo 'Неожиданный HTTP заголовок: ', $http_code, "\n";
        }
    }
    curl_close( $ch );
}


class TableListProject{
    protected $project_id;
    protected $project;
    protected $yandex_tis;
    protected $ya;
    protected $g;

        public function __construct($project_id, $project, $yandex_tis, $ya, $g, $token){
            $this->project_id = $project_id;
            $this->project = $project;
            $this->yandex_tis = $yandex_tis;
            $this->ya = $ya;
            $this->g = $g;
            $this->token = $token;
            $this->Insert();
        }

        public function __toString(){
            return $this->project_id; 
        }

        //Проверка на повторение записей
        protected function doCheck($sql, $project_id){ 
            $db = new db();
            $db = $db->connect();
            $temper = $db->prepare($sql);
            $temper->bindParam(':project_id', $project_id, PDO::PARAM_INT);
            $temper->execute();
            return $temper->fetch(PDO::FETCH_ASSOC);
        }

        protected function getCountArray($array){
            return count($array);
        }

        //list_project
        protected function Insert(){
            //Ядро
            $count_positions = connectToSite("https://ru.serpseeker.com/api/project/$this->project_id/phrases", $this->token); 
            if(!empty($count_positions)){
                $relult_id = array();
                    foreach($count_positions as $counter_p){
                        $ary[] = $counter_p['phrase'];
                    }
                $kervel = $this->getCountArray($ary); 
                unset($ary);
            }
            //Дата обновления
            $search_systems = connectToSite("https://ru.serpseeker.com/api/project/$this->project_id/engines", $this->token);
            if(!empty($search_systems)){
                $search_systems_id = array();
                    foreach($search_systems as $search_system_id){
                        $search_systems_id[] = $search_system_id['id'];
                    }
                        foreach($search_systems_id as $id_search_system_project){
                            $data_positions = connectToSite("https://ru.serpseeker.com/api/project/$this->project_id/positions/$id_search_system_project/check-dates", $this->token);
                            $data_update_yandex = array_pop($data_positions);
                        }
                unset($search_systems_id);
            }
            //Занесение в таблицу 
            $sql_check = "SELECT list_project_id FROM list_project WHERE list_project_id = :project_id";
            $sql_project = "INSERT INTO list_project (list_project_id, project, yandex_tis, ya, g, kervel, update_time) 
                            VALUES (:list_project_id, :project, :yandex_tis, :ya, :g, :kervel, :update_time)"; 
            $num = $this->doCheck($sql_check, $this->project_id);
            if(!$num){
                $db = new db();
                $db = $db->connect();
                $stmt_project = $db->prepare($sql_project);
                $stmt_project->bindParam(':list_project_id',    $this->project_id);
                $stmt_project->bindParam(':project',            $this->project);
                $stmt_project->bindParam(':yandex_tis',         $this->yandex_tis);
                $stmt_project->bindParam(':ya',                 $this->ya);
                $stmt_project->bindParam(':g',                  $this->g);
                $stmt_project->bindParam(':kervel',             $kervel);
                $stmt_project->bindParam(':update_time',        $data_update_yandex);
                $stmt_project->execute();
                $db = null;
            } 
        }
}






class TablePositions extends TableListProject
{
    protected $project_id;
    protected $token;

    public function __construct($project_id, $token){
        $this->project_id = $project_id;
        $this->token = $token;
        $this->AddToPositions($project_id, $token);
    }

    protected function getPositionsPhraseSearchSystem($id_project, $search_system, $token, $personal_id){
        if(!empty($search_system)){
            $url = "https://ru.serpseeker.com/api/project/$id_project/positions/$search_system"; 
            $get_records = connectToSite($url, $token);
            //CheckHeaderIn404($url, $token); //Проверка на доступность запроса по позициям фраз, постоянно ночью отрубается сервак сайта!
            if(isset($get_records)){
                if(array_key_exists($personal_id, $get_records)){
                    $array_positins = $get_records[$personal_id]['positions'];
                    foreach($array_positins as $ready_position){
                        $position = $ready_position + 1;
                        return $position;
                    }   
                }
            }
        }
    }

    protected function getInformationAboutProjects($project_id, $token){
        $get_all_info_about_project = connectToSite("https://ru.serpseeker.com/api/project/$project_id/phrases", $token);
        if(!empty($get_all_info_about_project)){
            $array_phrase = [];
                foreach($get_all_info_about_project as $project_property){
                    $phrase = $project_property['phrase'];
                    if($phrase == null){
                        $phrase = 'Данные отсутствуют'; 
                    }
                    $personal_id = $project_property['id'];
                    if($personal_id == null){
                        $personal_id = '-//-'; 
                    }
                    $geo = $project_property['is_geo']; 
                    if($geo == false){
                        $geo = '-';
                    }elseif($geo == true){
                        $geo = '+';
                    } 
                    $array_phrase[$project_id][] = [$project_id, $personal_id, $phrase, $geo];
                }
                return $array_phrase;
            unset($array_phrase);
        }
    }

    protected function getDateUpdateAndSearchSystemsProject($project_id, $token){
        $list_search_systems = connectToSite("https://ru.serpseeker.com/api/project/$project_id/engines", $token);
        if(!empty($list_search_systems)){
            $search_systems = [];
                foreach($list_search_systems as $id_search_system){
                    $search_systems[$project_id][$id_search_system['engine_id']] = $id_search_system['id']; 
                }
                return $search_systems;
            unset($search_systems);
        }
    }




    protected function AddToPositions($project_id, $token){

        $get_all_info_about_project = connectToSite("https://ru.serpseeker.com/api/project/$project_id/phrases", $token);
        if(!empty($get_all_info_about_project)){
            $array_phrase = [];
                foreach($get_all_info_about_project as $project_property){
                    $phrase = $project_property['phrase'];
                    if($phrase == null){
                        $phrase = 'Данные отсутствуют'; 
                    }
                    $personal_id = $project_property['id'];
                    if($personal_id == null){
                        $personal_id = '-//-'; 
                    }
                    $geo = $project_property['is_geo']; 
                    if($geo == false){
                        $geo = '-';
                    }elseif($geo == true){
                        $geo = '+';
                    } 
                    $array_phrase[$project_id][] = [$project_id, $personal_id, $phrase, $geo];
                }
        }

        $list_search_systems = connectToSite("https://ru.serpseeker.com/api/project/$project_id/engines", $token);
        if(!empty($list_search_systems)){
            $search_systems = [];
                foreach($list_search_systems as $id_search_system){
                    $search_systems[$project_id][$id_search_system['engine_id']] = $id_search_system['id']; 
                }  
        }

        //Готовим массив, содержащий информацию о фразах и подключённых поисковых систем.
        foreach($search_systems as $key => $value){
            if(isset($array_phrase[$key])){
                foreach(array_keys($array_phrase[$key]) as $key2){
                    $array_phrase[$key][$key2][] = $value;  
                }
            }
        }

        //Получаем позиции из поисковых систем, и заносим в БД
        foreach($array_phrase as $all_project_id){
            foreach($all_project_id as $record){ 

                $id_project = $record[0];
                $personal_id = $record[1];
                $phrase = $record[2];
                $is_geo = $record[3];

                $yandex = $record[4][1];
                $google = $record[4][3];
                $google_mobile = $record[4][8];
                $go_Mail = $record[4][7];
    
                $yandex_position = $this->getPositionsPhraseSearchSystem($id_project, $yandex, $token, $personal_id);
                $google_position = $this->getPositionsPhraseSearchSystem($id_project, $google, $token, $personal_id);
                $google_mobile_position = $this->getPositionsPhraseSearchSystem($id_project, $google_mobile, $token, $personal_id);
                $go_mail_position = $this->getPositionsPhraseSearchSystem($id_project, $go_Mail, $token, $personal_id);
                
                $sql_positions_now = "SELECT personal_id FROM Current_postions WHERE personal_id = :project_id";
                $positions_check_now = parent::doCheck($sql_positions_now, $id_project);
                if(!$positions_check_now){
                    $db = new db();
                    $db = $db->connect();                        
                    $sql_current_postions = "INSERT INTO Current_postions (project_id, personal_id, phrase, geo, yandex, google, google_mobile, go_Mail) 
                                             VALUES (:project_id, :personal_id, :phrase, :geo, :yandex, :google, :google_mobile, :go_Mail)";       
                    $stmt_positions = $db->prepare($sql_current_postions);
                    $stmt_positions->bindParam(':project_id',     $id_project);
                    $stmt_positions->bindParam(':personal_id',    $personal_id);
                    $stmt_positions->bindParam(':phrase',         $phrase);
                    $stmt_positions->bindParam(':geo',            $is_geo);
                    $stmt_positions->bindParam(':yandex',         $yandex_position);
                    $stmt_positions->bindParam(':google',         $google_position);
                    $stmt_positions->bindParam(':google_mobile',  $google_mobile_position);
                    $stmt_positions->bindParam(':go_Mail',        $go_mail_position);
                    $stmt_positions->execute();    
                    $db = null;
                }
            }
        } 
        unset($array_phrase);
        unset($search_systems);
    } 
}


$token = get_token();
CheckHeaderIn404("https://ru.serpseeker.com/api/projects", $token); 
if(!$token){
    exit("Ошибка подключения, проверьте существование или корректность токена!");
}else{
    dropTable();
    $url_list = 'https://ru.serpseeker.com/api/projects';
    $content = connectToSite($url_list, $token);
    if($content){
        foreach($content as $values)
        {
            $project_id = $values['id'];   
            $project_name = $values['name'];         
            $yandex_tis = $values['yandex_cy']; 
            $ya = $values['yandex_pages'];      
            $g = $values['google_pages']; 
            //list_project
            $table_list_project = new TableListProject($project_id, $project_name, $yandex_tis, $ya, $g, $token);
            //Current_positions
            $table_current_postions = new TablePositions($project_id, $token);
        }
    }











    $sql_update = "SELECT list_project_id, project, yandex_tis, ya, g, kervel, update_time FROM list_project";
    try{
        $db_get = new db();
        $db_get = $db_get->connect();
        $stmt = $db_get->query($sql_update);
        $dbslim = $stmt->fetchAll(PDO::FETCH_OBJ);
        if(!empty($dbslim)){
            $responseJson = json_encode($dbslim);
            $data = json_decode($responseJson, true);
            $db_get = null;
            echo "<tr>";
                foreach ($data as $values):
                    $hyperkey = $values['list_project_id']; 
                        foreach ($values as $value) :
                            echo"<td><a href='/project/$hyperkey'>$value</a></td>";
                        endforeach; 
            echo "</tr>";
                endforeach;
        $db_get = null; 
        }
    } 
    catch (PDOEception $e) {
        echo '{"Error:": {"text": '.$e->getMessage().'}';
    }    
}


?>