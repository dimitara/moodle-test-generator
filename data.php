<?php
define('AJAX_SCRIPT', true);
require('../../config.php');
require_once('generator.php');


$method = $_SERVER['REQUEST_METHOD'];
//creates an array based on the requested route 
//example url: data.php/elem1/elem2 -> $request[0]='elem1', $request[1] = 'elem2'
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$context_id = '';
$input = file_get_contents('php://input');



class Question{
	public $id;
	public $answers;
	public $name;
	public $text;
	public $type;
		
	public function __construct($id, $name, $text, $type){
		$this->id = $id;
		$this->name = $name;
		$this->text = $text;
		$this->type = $type;
		$this->answers = array();
	}
}


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
							
				
 				$input_string = file_get_contents('php://input');
 				$input_array = json_decode($input_string,true);
				$quiz_title = $input_array['title'];
				$quiz_questions = $input_array['questions'];
				$questions = array();
				//extracting standart questions from db
				$dbquestion_answers = $DB->get_records_sql("
						SELECT qa.id as answer_id, qa.question as id, qa.answer, q.name as name, q.qtype as type, q.questiontext as text
						FROM {question_answers} as qa, {question} as q
						WHERE qa.question in (".implode(',',$quiz_questions).")
						AND qa.question = q.id", null);
				
//  			print_r($dbquestion_answers);
			
				//Data formating for standart questions
				foreach($dbquestion_answers as $key => $value){
					if (!array_key_exists($value->id,$questions)){
						$questions[$value->id] = new Question($value->id, $value->name, $value->text, $value->type);		
					}
					array_push($questions[$value->id]->answers, $value->answer);	
				}
				
				// extracting matching questions from db
				$dbquestion_matching_answers = $DB->get_records_sql("
						SELECT qa.id as answer_id, qa.questionid as id, qa.questiontext as first_option,
						qa.answertext as second_option, q.name as name, q.qtype as type, q.questiontext as text
						FROM {qtype_match_subquestions} as qa, {question} as q
						WHERE qa.questionid in (".implode(',',$quiz_questions).")
						AND qa.questionid = q.id", null);
 				
 				
				//print_r($dbquestion_matching_answers);
 				//Data formating for matching questions
				foreach($dbquestion_matching_answers as $key => $value){
					if (!array_key_exists($value->id,$questions)){
						$questions[$value->id] = new Question($value->id, $value->name, $value->text, $value->type);
						array_push($questions[$value->id]->answers,array(),array());
					}
					array_push($questions[$value->id]->answers[0], $value->first_option);
					array_push($questions[$value->id]->answers[1], $value->second_option);
				}
				
				//Final array creation
				$questions = array_values($questions);	
    				//print_r($questions);
				
				//Generating quiz
				generate_Quiz($quiz_title, array('questions'=>$questions));
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

?>