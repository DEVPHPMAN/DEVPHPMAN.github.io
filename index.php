<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once "vendor/autoload.php";
require_once "database/db.php";

$config = ['settings' => ['addContentLengthHeader' => false]];
$app = new \Slim\App($config);

$container = $app->getContainer();
// Register component on container
$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('templates/'); //указываем папку где будут находиться views
};

//Главная страница
$app->get('/', function (Request $request, Response $response) use($app){
$sql = "SELECT list_project_id, project, yandex_tis, ya, g, kervel, update_time FROM list_project";
    try
    {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $dbslim = $stmt->fetchAll(PDO::FETCH_OBJ);
        $responseJson = json_encode($dbslim);
        $arrays = json_decode($responseJson, true);
        $db = null;
        $response = $this->view->render($response, "table.php", ["data" => $arrays]);
        return $response;
    } 
    catch (PDOEception $e) {
        echo '{"Error:": {"text": '.$e->getMessage().'}';
    }    
});
//Отображение информации о проекте по которому был совершен "клик"
$app->get('/project/{project_id}', function (Request $request, Response $response) use($app){
$project_id = $request->getAttribute('project_id');
$sql_show_table = "SELECT phrase, geo, yandex, google, google_mobile, go_Mail FROM Current_postions WHERE project_id  = :project_id";
try
{
    $db = new db();
    $db = $db->connect();
    $table = $db->prepare($sql_show_table);
    $table->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $table->execute();
    $result_table = $table->fetchAll(PDO::FETCH_OBJ);
    $JSON_informations = json_encode($result_table);
    $ready_table = json_decode($JSON_informations, true);
    $response = $this->view->render($response, "positions.php", [
        "ready" => $ready_table
        ]);
    $db = null;
    return $response;
} 
catch(PDOEception $e) {
    echo '{"Error:": {"text": '.$e->getMessage().'}';
}     
});
//Страница токена
$app->get('/token', function($request, $response){
    return $this->view->render($response, 'token.html');
});
//Отладка файла brain 
$app->get('/update', function(Request $request, Response $response){
    return $this->view->render($response, 'brain.php');
});
// Run App
$app->run();
?>
