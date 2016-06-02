<?php
define('AJAX_SCRIPT', true);
require('../../config.php');

$method = $_SERVER['REQUEST_METHOD'];
//creates an array based on the requested route 
//example url: data.php/elem1/elem2 -> $request[0]='elem1', $request[1] = 'elem2'
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$context_id = '';
$input = file_get_contents('php://input');

try {
// 	if($method != 'GET')
// 		throw new Exception('Unsupported request method. Please use only GET.');
	
	switch (count($request)){
		
		case 1:
			//Endpoint: /contexts
			if($request[0]=='contexts'){
				if($method != 'GET')
					throw new Exception('Unsupported request method. Please use GET.');
				
				$dbcontexts = $DB->get_records_sql("
						SELECT qc.id as id, qc.name, count(1) as numquestions, sum(hidden) as numhidden
						FROM {question} q
						JOIN {question_categories} qc ON q.category = qc.id
						JOIN {context} con ON con.id = qc.contextid
						AND (q.parent = 0 OR q.parent = q.id)
						GROUP BY qc.id");
				//Formating data
				$contexts = array();
				foreach ($dbcontexts as $key => $context){
					array_push($contexts, $context);
				}
										
				
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode(
						array('contexts'=>$contexts), JSON_PRETTY_PRINT);	
				
			}
			elseif($request[0]=='pdfgen'){
				
				//Endpoint: /pdfgen 
				
				if($method != 'POST')
					throw new Exception('Unsupported request method. Please use POST.');
				
				//code that runs pdfgenerator module
				echo json_encode(json_decode($input),true);
//  				$input = json_decode(file_get_contents('php://input'),true);
				
//  				echo json_encode($input);
// 				print_r($input);
					
				
				
			}
			break;
		case 2:
			if($request[0] == 'contexts'){
				if($method != 'GET')
					throw new Exception('Unsupported request method. Please use GET.');
				
				//Endpoint: /contexts/{id}
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
				
				header('Content-Type: application/json; charset=utf-8');
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
