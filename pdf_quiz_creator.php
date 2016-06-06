<?php 
require('tfpdf/tfpdf.php');
define('FPDF_FONTPATH',"tfpdf/font/");


	class Quiz{
		public $title;
		private $font_size;
		public $line_h = 9;
		private $question_num;
		private $page_width;

		public function __construct($title){
			$this->file = new tFPDF();
			$this->file->AddPage();
			$fontName = 'DejaVuSans';
			$this->file->AddFont($fontName,'B', 'DejaVuSans.ttf',true);
			$this->set_Font();
			$this->set_Title($title);
			// $this->page_width = $this->file->GetPageWidth();
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
			$date = new DateTime();
			$pdf_name = "pdf_quizzes/quiz_".$date->getTimestamp().".pdf"; 
			$this->file->Output($pdf_name, 'F', false);
			$this->file->Close();
			return $pdf_name;
			
		}


		private function set_Title($title){
			$this->file->SetFontSize($this->font_size + 3);
			$this->file->Cell(0, 0, $title, 0, 1, 'C');
			$this->file->Cell(0, 0, " ", 0, 1, 'C');
			$this->file->Ln($this->line_h * 1.4);
			$this->title = $title;
			$this->file->SetFontSize($this->font_size);

		}

		public function set_Font($family = 'DejaVuSans', $font_style = "", $font_size = 14){
			$this->file->SetFont($family, $font_style, $font_size);
			$this->font_size = $font_size;

		}

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


		public function add_shortAnswer($question_text, $if_newLine = true, $numeration = true){
			if($numeration == true){
				$this->file->Write($this->line_h, "{$this->question_num}. ".$question_text. "  ");
				$this->question_num++;
			} else {
				$this->file->Write($this->line_h, " ".$question_text. "  ");
			}

			//if there is not enough space for answering transfer it to new line
			if(($this->page_width /3) > ($this->page_width - $this->file->GetX())){
				$this->file->ln();
			}

			if($if_newLine == true){
				$this->file->ln();
				$this->file->Cell(22, $this->line_h, " Answer:", 0, 0);
			}

			$this->file->Cell(0, $this->line_h, " ", 0, 1);
			$this->file->ln();


		}

		public function add_multipleChoice($question_text, $one_or_more, $answers, $enum_type = 'a', $numeration = true) {

			if($numeration == true){
				$this->file->Multicell(0, $this->line_h, " {$this->question_num}. ".$question_text, 0);
				$this->question_num++;
			} else {
				$this->file->Multicell(0, $this->line_h, " ".$question_text, 0);
			}

			$this->add_identation();
			if ($one_or_more == 'one') {
				$this->file->Multicell(0, $this->line_h, " Select one:", 0, 1);
			} elseif ($one_or_more == 'more'){
				$this->file->Multicell(0, $this->line_h, " Select one or more:", 0, 1);
			}
			$number = 1;
			foreach ($answers as $answer) {
				$num = $this->enum($number, $enum_type);
				$this->add_identation();
				$this->file->Multicell(0, $this->line_h-1, " {$num}. {$answer}", 0, 1);
				$number++;
			}

			$this->file->ln();

		} 

		public function add_matching($question_text, $pairs, $numeration = true){
			if($numeration == true){
				$this->file->Multicell(0, $this->line_h, " {$this->question_num}. ".$question_text, 0);
				$this->question_num++;
			} else {
				$this->file->Multicell(0, $this->line_h, " ".$question_text, 0);
			}

			$keys = $pairs[0];
			$values = $pairs[1];

			$showing_together = true;
			foreach ($keys as $key) {
				$width = $this->file->GetStringWidth($key);
				if ($width >= $this->page_width/2.7) {
					$showing_together = false;
					break;
				}
			}
			if ($showing_together == true) {
				foreach ($values as $value) {
					$width = $this->file->GetStringWidth($value);
					if ($width >= $this->page_width/2.5) {
						$showing_together = false;
						break;
					}
				}	
			}


			if ($showing_together == true) {
				foreach ($keys as $key) { 
					$this->file->Cell(0, $this->line_h-1, "     {$key}", 0, 0);
					$this->file->SetX($this->page_width/2.5+ 3);
					shuffle($values);
					$this->file->Cell(0, $this->line_h-1, " {$values[0]}", 0, 0);
					array_shift($values);
					$this->file->Ln();
				}
				
			} else {
				$number = 1;
				foreach ($keys as $key) {
					$num = $this->enum($number);
					$this->add_identation();
					$this->file->Multicell(0, $this->line_h-1, " {$num}. {$key}", 0, 1);
					$number++;
				}
			
				$this->file->ln(2);
	
				$number = 1;
				shuffle($pairs);
				foreach ($values as $value) {
					$num = $this->enum($number, 'a');
					$this->add_identation();
					$this->file->Multicell(0, $this->line_h-1, " {$num}. {$value}", 0, 1);
					$number++;
				}
			}

			$this->file->ln(3);

		}

		public function add_essay($question_text, $lines_to_answer = 10, $numeration = true){
			if($numeration == true){
				$this->file->Multicell(0, $this->line_h, " {$this->question_num}. ".$question_text, 0);
				$this->question_num++;
			} else {
				$this->file->Multicell(0, $this->line_h, " ".$question_text, 0);
			}

			$this->file->Multicell(0, $this->line_h * ($lines_to_answer + 1), " ", 1, 1);			

		}

	}

 ?>