<?php
use Illuminate\Support\Facades\Input;
class EmailController extends BaseController{
	private $emailProtocol = 'smtp.gmail.com';
	private $emailPort = 465;
	private $emailSsl = "ssl";
	private $emailAddress = '';
	private $emailPassword = '';
	private $toSendFolder = 'uploads/tosend/f';
	
	//inicio
	public function start(){
		return Redirect::to(URL::to('/').'/email');
	}
	
	public function loadForm(){
		$composerId = $this->getNewComposerId();
		return View::make('emails.form')->with('composerId',$composerId);
	}
	
	//Carga archivos al servidor
	public function process($composerId=null){
		if (Input::get('sendEmailButton')){
			$composerId = $this->getComposerId();
			$this->sendEmail($composerId);
		}else{
			return $this->upload($composerId);
		}
	}
	
	//Carga archivos al servidor
	private function upload($composerId=null){
		// Grab our files input
		switch ($_SERVER['REQUEST_METHOD']){
			case 'GET':
				return $this->get($composerId);
			case 'POST':
				return $this->post();
			case 'DELETE':
				return $this->delete($composerId);
		}
	}
	
	
	private function get($composerId=null){
		$result = array();
		if ($composerId!=null){
			$assetPath = $this->getToSendFolder().$composerId;
			$uploadPath = storage_path('/'.$assetPath);
			if (file_exists($uploadPath)){
				$files = $this->getFilesInDir($uploadPath);
				if ($files){
					$fileNames = array();
					$sessionFiles = null;
					if (Session::has($composerId)){
						$sessionFiles = Session::get($composerId);
					}
					foreach($files as $file){
						$fileNames[$file] = $file;
						if ($sessionFiles!=null && array_key_exists($file, $sessionFiles)){
							$fileNames[$file] = $sessionFiles[$file];
						}
					}
						
					foreach ($files as $file) {
						$newName = $fileNames[$file];
						$newPath = $uploadPath.'/'.$newName;
						$success = new stdClass();
						$success->name = $newName;
						$success->size = filesize($newPath);
						$success->url = $newPath;
						$success->thumbnailUrl = $newPath;
						$success->deleteUrl = 'email/1?files='.$newName;
						$success->deleteType = 'DELETE';
						$result[] = $success;
					}
					Session::put($composerId, $fileNames);
				}
			}
		}
		return Response::json(array('files'=> $result), 200);
	}
	
	private function post(){
		$files = Input::file('files');
		// We will store our uploads in public/uploads/basic
		$composerId = $this->getComposerId();
		$assetPath = $this->getToSendFolder().$composerId;
		$uploadPath = storage_path('/'.$assetPath);
		// 	We need an empty arry for us to put the files back into
		$results = array();
		$sessionResult = null;
		if (Session::has($composerId)){
			$sessionResult = Session::get($composerId);
		} else{
			$sessionResult = array();
		}
		if ($files){
			foreach ($files as $file) {
				$newPath = $this->recursiveIncrementFilename($uploadPath, $file->getClientOriginalName());
				// store our uploaded file in our uploads folder
				$newName = str_replace($uploadPath.'/', '', $newPath);
				$file->move($uploadPath, $newName);
				// set our results to have our asset path
				$success = new stdClass();
				$success->name = $file->getClientOriginalName();
				$success->size = $file->getClientSize();
				$success->url = $newPath;
				$success->thumbnailUrl = $newPath;
				$success->deleteUrl = 'email/'.$composerId.'?files='.$newName;
				$success->deleteType = 'DELETE';
				$results[] = $success;
				$sessionResult[$newName] = $file->getClientOriginalName();
			}
		}
		Session::put($composerId,$sessionResult);
		return Response::json(array( 'files'=> $results), 200);
		//return View::make('emails.upload')->with('files',$results);
	}
	
	
	private function delete($composerId){
		$files = Input::get('files');
		// We will store our uploads in public/uploads/basic
		$assetPath = $this->getToSendFolder().$composerId;
		$uploadPath = storage_path('/'.$assetPath);
		// 	We need an empty arry for us to put the files back into
		$results = array();
	
		if ($files){
			if (!is_array($files)){
				$files = array($files);
			}
			$response = array();
			foreach($files as $file){
				$encondedFile = $file;
				$newName =$file;
				$success = is_file($uploadPath.'/'.$encondedFile) && unlink($uploadPath.'/'.$encondedFile);
				$response[$newName] = $success;
			}
			return Response::json($response, 200);
		}else{
			return Response::json('Error', 400);
		}
	}
	
	private function recursiveIncrementFilename ($path, $filename){
		$test = "{$path}/{$filename}";
		if (!is_file($test)) return $test;
	
		$file_info = pathinfo($filename);
		$part_filename = $file_info['filename'];
	
		if (preg_match ('/(.*)_(\d+)$/', $part_filename, $matches)){
			$num = (int)$matches[2] +1;
			$part_filename = $matches[1];
		}
		else{
			$num = 1;
		}
		$filename = $part_filename.'_'.$num;
	
		if (array_key_exists('extension', $file_info)){
			$filename .= '.'.$file_info['extension'];
		}
	
		return $this->recursiveIncrementFilename($path, $filename);
	}
	
	private static function getFilesInDir($dirName){
		$files = array();
		$dhandle = opendir($dirName);
		if ($dhandle) {
			// loop through all of the files
			while (false !== ($fname = readdir($dhandle))) {
				// if the file is not this file, and does not start with a '.' or '..',
				// then store it for later display
				if (($fname != '.') && ($fname != '..') &&
						($fname != basename($_SERVER['PHP_SELF']))) {
							// store the filename
							$files[] = (is_dir( "./$fname" )) ? "(Dir) {$fname}" : $fname;
						}
			}
			// close the directory
			closedir($dhandle);
		}
		return $files;
	}
	
	
	//Envio de email
	private function validateInputs(){
		$rules = array('to'=>'required|email', 'subject'=>'required','cc'=>'email', 'bcc'=>'email');
		return Validator::make(Input::all(),$rules);
	}
	
	private function sendEmail($composerId=null){
		$composerId = $this->getComposerId();
		$validator = $this->validateInputs();
		if ($validator->fails()){
			Input::flash();
			return View::make('emails.form')->with('composerId',$composerId)->withErrors($validator);
		}
		$email = $this->getEmailAddress();
		$password = $this->getEmailPassword();
		$to = Input::get('to');
		$cc = Input::get('cc');
		$bcc = Input::get('bcc');
		$subject = Input::get('subject');
		$body = Input::get('body');
		$assetPath = $this->getToSendFolder().$composerId;
		$uploadPath = storage_path('/'.$assetPath);
		$files = array();
		if (file_exists($uploadPath)){
			$files = $this->getFilesInDir($uploadPath);
		}
	
		$transport = Swift_SmtpTransport::newInstance($this->getEmailProtocol(), $this->getEmailPort(), $this->getEmailSsl())
		->setUsername($email)
		->setPassword($password);
		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance($subject)
		->setFrom(array($email => ''))
		->setTo(array($to => ''))
		->setBody($body);
		if (isset($cc)&&!empty($cc)){
			$message->setCc(array($cc => ''));
		}
	
		if (isset($bcc)&&!empty($bcc)){
			$message->setBcc(array($bcc => ''));
		}
		foreach($files as $file){
			$fileName = $file;
			$originalFileName = $file;
			$attachment = Swift_Attachment::newInstance(file_get_contents($uploadPath.'/'.$fileName), $originalFileName);
			$message->attach($attachment);
		}
		$numSent = $mailer->send($message);
		return 'Correo enviado, eso creo';
	}
	
		
	//getters
	private function getNewComposerId(){
		return 'composer'.rand(1000, 9999);
	}
	
	private function getComposerId(){
		return Input::get('composerId')?Input::get('composerId'):$this->getNewComposerId();
	}
	
	private function getEmailAddress(){
		return $this->emailAddress;
	}
	
	private function getEmailPassword(){
		return $this->emailPassword;
	}
	
	private function getEmailProtocol(){
		return $this->emailProtocol;
	}
	
	private function getEmailPort(){
		return $this->emailPort;
	}
	
	private function getEmailSsl(){
		return $this->emailSsl;
	}
	
	private function getToSendFolder(){
		return $this->toSendFolder;
	}
}
