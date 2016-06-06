<?php 
require('fpdf181/fpdf.php');

	class Quiz{
		public $title;
		private $font_size;
		public $line_h = 9;
		private $question_num;
		private $page_width;


		private function enum($integer, $char = 1) { 
		    $return = ''; 
			if ($char == 'I' || $char == 'i'){
		    	$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40	, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
		    	while($integer > 0) 
		    	{ 
		    	    foreach($table as $rom=>$arb) 
		    	    { 
		    	        if($integer >= $arb) 
		    	        { 
		    	            $integer -= $arb; 
		    	            $return .= $rom; 
		    	            break; 
		    	        } 
		    	    } 
		    	} 
			
		    	if ($char == 'i') {
		    		$return = strtolower($return);
		    	}

		    } else if($char == 'A' || $char == 'a') {
		    	$table = array_merge(range('A', 'Z'), range('a', 'z'));
		    	$return = $table[$integer-1];

		    	if ($char == 'a') {
		    		$return = strtolower($return);
		    	}

		    } else {
		    	$return = $integer;
		    }
		    
		    return $return; 
		} 

		public function __construct($title){
			$this->file = new FPDF();
			$this->file->addPage();
			$this->set_Font();
			$this->set_Title($title);
			$this->page_width = $this->file->GetPageWidth();
			$this->question_num = 1;
		}
		
		private function addPage(){
			$this->file->addPage();
		}

		private function add_identation($times = 1){
			while($times > 0) {
				$this->file->Cell(5, $this->line_h, " ", 0, 0);
				$times--;
			}
		}

		private function get_writingSpace(){

		}

		public function serialize(){
			$this->file->Output('I', "name");
			$this->file->Close();
		}


		private function set_Title($title){
			$this->file->SetFontSize($this->font_size + 3);
			$this->file->Cell(0, 0, $title, 0, 1, 'C');
			$this->file->Cell(0, 0, " ", 0, 1, 'C');
			$this->file->Ln($this->line_h * 1.4);
			$this->title = $title;
			$this->file->SetFontSize($this->font_size);

		}

		public function set_Font($family = 'Arial', $font_style = "", $font_size = 14){
			$this->file->SetFont($family, $font_style, $font_size);
			$this->font_size = $font_size;

		}


		public function add_shortAnswer($question_text, $if_newLine = true, $numeration = true){
			if($numeration == true){
				$this->file->Write($this->line_h, "\t{$this->question_num}. ".$question_text. "  ");
				$this->question_num++;
			} else {
				$this->file->Write($this->line_h, "\t".$question_text. "  ");
			}

			//if there is not enough space for answering transfer it to new line
			if(($this->page_width /3) > ($this->page_width - $this->file->GetX())){
				$this->file->ln();
			}

			if($if_newLine == true){
				$this->file->ln();
				$this->file->Cell(22, $this->line_h, "\tAnswer:", 0, 0);
			}

			$this->file->Cell(0, $this->line_h, " ", 1, 1);
			$this->file->ln();


		}

		public function add_multipleChoice($question_text, $one_or_more, $answers, $enum_type = 'a', $numeration = true) {

			if($numeration == true){
				$this->file->Multicell(0, $this->line_h, "\t{$this->question_num}. ".$question_text, 0);
				$this->question_num++;
			} else {
				$this->file->Multicell(0, $this->line_h, "\t".$question_text, 0);
			}

			$this->add_identation();
			if ($one_or_more == 'one') {
				$this->file->Cell(0, $this->line_h, "\tSelect one:", 0, 1);
			} elseif ($one_or_more == 'more'){
				$this->file->Cell(0, $this->line_h, "\tSelect one or more:", 0, 1);
			}
			$number = 1;
			foreach ($answers as $answer) {
				$num = $this->enum($number, $enum_type);
				$this->add_identation();
				$this->file->Cell(0, $this->line_h-1, "\t{$num}. {$answer}", 0, 1);
				$number++;
			}

			$this->file->ln();

		} 

		public function add_matching($question_text, $pairs, $numeration = true){
			if($numeration == true){
				$this->file->Multicell(0, $this->line_h, "\t{$this->question_num}. ".$question_text, 0);
				$this->question_num++;
			} else {
				$this->file->Multicell(0, $this->line_h, "\t".$question_text, 0);
			}

			$keys = $pairs[0];
			$values = $pairs[1];

			$showing_together = true;
			foreach ($keys as $key) {
				$width = $this->file->GetStringWidth($key);
				if ($width >= $this->page_width/2.5) {
					$showing_together = false;
					break;
				}
			}
			if ($showing_together != true) {
				foreach ($values as $value) {
					$width = $this->file->GetStringWidth($value);
					if ($width >= $this->page_width/2.5) {
						$showing_together = false;
						break;
					}
				}	
			}


			if ($showing_together == false) { // should be reversed when ready
				$this->file->Cell(0, $this->line_h-1, "Test", 0, 1);
				
			} else {
				$number = 1;
				foreach ($keys as $key) {
					$num = $this->enum($number);
					$this->add_identation();
					$this->file->Cell(0, $this->line_h-1, "\t{$num}. {$key}", 0, 1);
					$number++;
				}
			
				$this->file->ln(2);
	
				$number = 1;
				shuffle($pairs);
				foreach ($values as $value) {
					$num = $this->enum($number, 'a');
					$this->add_identation();
					$this->file->Cell(0, $this->line_h-1, "\t{$num}. {$value}", 0, 1);
					$number++;
				}
			}

			$this->file->ln(3);

		}

		public function add_essay($question_text, $lines_to_answer = 10, $numeration = true){
			if($numeration == true){
				$this->file->Multicell(0, $this->line_h, "\t{$this->question_num}. ".$question_text, 0);
				$this->question_num++;
			} else {
				$this->file->Multicell(0, $this->line_h, "\t".$question_text, 0);
			}

			$this->file->Cell(0, $this->line_h*$lines_to_answer, " ", 1, 1);
			// $this->file->ln();
			$this->file->Cell(0, $this->line_h-($this->line_h/3), " ", 0, 1);

		}

		public function add_embedded($question_text, $answers){
			$questions = preg_split('/\{[0-9]\}+/', $question_text);

			// print_r($questions);

			$this->file->Write($this->line_h, "\t{$this->question_num}. ");
			$this->question_num++;

			 $question_id = 0;
			 foreach ($answers as $key => $value) {
			 	switch ($key) {
			 		case 'shortanswer':
			 		case 'calculated':
			 		case 'numerical':
			 		case 'shortanswer_c':
			 			$this->add_shortAnswer($questions[$question_id], false, false);
			 			break;

			 		case 'multichoice':
			 		case 'mcv':
			 		case 'mch':
			 		case 'calculatedmulti':
			 			$this->add_multipleChoice($questions[$question_id], 'more', $value, 'a', false);
			 			break;

			 		case 'essay':
			 			$this->add_essay($questions[$question_id], 2, false);
			 			break;

			 		case 'match':
			 			$this->add_matching($questions[$question_id], $value, false);
			 			break;	

			 		case "truefalse":
			 			$this->add_multipleChoice($questions[$question_id], "one", array("true", "false"), 'a', false);
			 			break;	

			 		default:
			 			break;
			 	}
			
			 	$question_id++;
			}


		}

	}

 ?>