<?php 
require('pdf_quiz_creator.php');




// 	$text = fopen("questions.json", "r") or die("Unable to open file");
// 	$string = fread($text, filesize("questions.json"));
// 	fclose($text);

// 	$arr = json_decode($string, false);

// 	generate_Quiz("Title", $arr);

// // 	// super_echo($arr);


// // 	function super_echo($arr){
// // 		echo "<pre>";
// // 		print_r($arr);
// // 		echo "</pre>";
// // 		echo "<hr>";

// // 	}

	function generate_Quiz($title, $arr){
		
		foreach ($arr as $questions) { 
	
			$file = new Quiz($title);
			foreach ($questions as $question) {

				switch ($question->type) {
					case 'shortanswer':
					case 'calculated':
					case 'numerical':
						$file->add_shortAnswer($question->text, true);
						break;

					case 'multichoice':
					case 'calculatedmulti':
						$file->add_multipleChoice($question->text, 'more', $question->answers, 'a');
						break;

					case 'essay':
						$file->add_essay($question->text, 2);
						break;

					case 'match':
						$file->add_matching($question->text, $question->answers);
						break;	

					case "truefalse":
						$file->add_multipleChoice($question->text, "one", array("true", "false"), 'a');
						break;	

					case "multianswer":
						$file->add_embedded($question->text, $question->answers);


					default:
						break;
				}
			}
		}

		return $file->serialize();
	}

?>