<?php
define('AJAX_SCRIPT', true);
require('../../config.php');

$method = $_SERVER['REQUEST_METHOD'];
//creates an array based on the requested route 
//example url: data.php/elem1/elem2 -> $request[0]='elem1', $request[1] = 'elem2'
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$context_id = '';

try {
	if($method != 'GET')
		throw new Exception('Unsupported request method. Please use only GET.');
	
	switch (count($request)){
		
		case 1:
			if($request[0]=='contexts'){
				// /contexts
				
				$dbcontexts = $DB->get_records_sql("
						SELECT qc.contextid as id, qc.name, count(1) as numquestions, sum(hidden) as numhidden
						FROM {question} q
						JOIN {question_categories} qc ON q.category = qc.id
						JOIN {context} con ON con.id = qc.contextid
						AND (q.parent = 0 OR q.parent = q.id)
						GROUP BY qc.contextid");
				//Formating data
				$contexts = array();
				foreach ($dbcontexts as $key => $context){
					array_push($contexts, $context);
				}
										
				
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(
						array('contexts'=>$contexts), JSON_PRETTY_PRINT);	
				
			}
			elseif($request[0]=='questions'){
				//code that returns JSON for specific questions
				print_r('Questions JSON');
			}
			break;
		case 2:
			if($request[0] == 'contexts'){
				// /contexts/{id}
				$context_id = $request[1];
				$dbquestions = $DB->get_records_sql("
						SELECT id, name, qtype as type 
						FROM {question}
						WHERE category = ?", array($context_id));
				//Formating data
				$questions = array();
				foreach ($dbquestions as $key => $question){
					array_push($questions, $question);
				}
				
				echo json_encode(array('questions' => $questions), JSON_PRETTY_PRINT);
			}
			break;
		default:
			throw new Exception('There is an error in your request');
			break;
	}



}
   
catch (Exception $e){
    echo $e->getMessage();
}
//$table = 'conceptmap';
//$map_id = $_GET['id'];
//$error = '';
//
//if (!$cm = get_coursemodule_from_id('conceptmap', $map_id)) {
//    $error = 'Course Module ID was incorrect';
//}
//if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
//    $error = 'Course is misconfigured';
//}
//if (!$conceptmap = $DB->get_record($table, array('id'=> $cm->instance))) {
//    $error = 'Course module is incorrect';
//}
//
//
//try {
//    //require_login();
//    //require_sesskey();
//    header('Content-Type: text/plain; charset=utf-8');
//    $id = $conceptmap->id;
//    $title = $conceptmap->name;
//    
//    $request_type = $_SERVER['REQUEST_METHOD'];
//
//    switch($request_type) {
//        case 'GET':
//            $map = from_db_to_json($conceptmap);
//            $map = json_decode($map, true);
//            // echo $map['id'];
//            if($map['id'] != null){
//                deliver_response("Map retrieving successful", $map);
//
//            } else {
//                // deliver_response("Map not found");
//                //--------------------------
//                // SECOND PLUGIN "PDF Export"
//                
//                $tables = $DB->get_tables();
//                $quiz = $DB->get_records("question");
//                print_r($quiz);
//                // super_echo($quiz);
//                // br();
//                // super_echo($tables);
//
//
//                // END OF THE SECOND PLUGIN
//                //--------------------------
//
//            }
//
//            break;
//
//        case 'PUT':
//            $json_string = file_get_contents('php://input');
//            $json_arr = json_decode($json_string, true);
//
//            $conceptmap->mapxml = "";
//
//            if (empty($conceptmap->mapxml)) { // updating empty record with whole map
//                $mapxml = json_arr_to_xml($json_arr);
//                $mapxml = xml_to_string($mapxml);
//            } else { // updating only the differences
//
//                // $mapxml = format_xml($conceptmap->mapxml);
//                // $mapxml = xml_maps_to_json($mapxml);
//                // $mapxml = json_decode($mapxml, true)[0];
//
//                // foreach ($mapxml as $key => $value) {
//                //     if (!empty($json_arr[$key])) {
//                //         $mapxml[$key] = $json_arr[$key];
//                //     }
//                // }
//            }
//            
//            $conceptmap->mapxml = $mapxml;
//
//            $DB->update_record($table, $conceptmap, $bulk=false);
//            // super_echo($conceptmap); // result as XML
//            $map = from_db_to_json($conceptmap);
//            $map = json_decode($map, true);
//            deliver_response("The map has been updated", $map);
//
//            break;
//
//        // case 'DELETE':
//        //     echo "It is a DELETE request";
//        //     $DB->delete_records($table, array('id'=> $id));
//        //     break;
//
//        default:
//            break;
//    }
//
//} catch (Exception $e) {
//    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
//    if (isloggedin()) {
//        header('Content-Type: text/plain; charset=utf-8');
//        echo $e->getMessage();
//    }   
//}
//
//function deliver_response($message=null, $map_as_arr=null){
//
//        $result['message'] = $message;
//        if ($map_as_arr != null) {
//            $result['map'] = $map_as_arr;
//        }
//
//        $map = json_encode($result);
//        echo $map;
//}