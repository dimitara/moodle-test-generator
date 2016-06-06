<?php 
require('pdf_quiz_creator.php');

	function generate_Quiz($title, $arr){
		
		$file = new Quiz($title);
		foreach ($arr as $questions) { 
			
			foreach ($questions as $question) {

				$question_title = trim_outer_tags($question->text);

				switch ($question->type) {
					case 'shortanswer':
					case 'calculated':
					case 'numerical':
						$file->add_shortAnswer($question_title, true);
						break;

					case 'multichoice':
					case 'calculatedmulti':
						$trimmed_answers = array();
						foreach ($question->answers as $option) {
							$option = trim_outer_tags($option); 
							array_push($trimmed_answers, $option);
						}
						$file->add_multipleChoice($question_title, 'more', $trimmed_answers, 'a');
						break;

					case 'essay':
						$file->add_essay($question_title);
						break;

					case 'match':
						$trimmed_answers = array();
						foreach ($question->answers as $options_set) {
							$trimmed_set = array();
							foreach ($options_set as $option) {
								$option = trim_outer_tags($option);
								array_push($trimmed_set, $option);
							}
							array_push($trimmed_answers, $trimmed_set);
						}					
						$file->add_matching($question_title, $trimmed_answers);
						break;	

					case "truefalse":
						$file->add_multipleChoice($question_title, "one", array("true", "false"), 'a');
						break;	

					default:
						break;
				}
			}
		}

		$file_name = $file->serialize();
		return $file_name;
	}


	function trim_outer_tags($value){
		$arr = array();
		preg_match('/">(.*)<\/s/', $value, $arr);
		
		if (array_key_exists(1, $arr)){
			$result = $arr[1];

		} else {
			$arr = array();
			preg_match('/>(.*)</', $value, $arr);
			if(array_key_exists(1, $arr)){
				if(endsWith($arr[1], "<br>")){
					$temp = $arr[1];
					$result = preg_replace('/<br>(?!.*br>)/', " ", $temp);
					return $result;
				}
				$result = $arr[1];	
			} else{
				$result = $value;
			}	
		}
	
		return $result;
	}

	function endsWith($haystack, $needle)
	{
	    $length = strlen($needle);
	    if ($length == 0) {
	        return true;
	    }

	    return (substr($haystack, -$length) === $needle);
	}

?>