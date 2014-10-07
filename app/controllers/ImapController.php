<?php
class ImapController extends BaseController{
	private function validateInputs(){
		$rules = array('to'=>'required|email', 'subject'=>'required','cc'=>'email', 'bcc'=>'email');
		return Validator::make(Input::all(),$rules);
	}
	
	private function sendEmail($composerId=null){	
		$composerId = EmailController::getComposerId();
		$validator = $this->validateInputs();
		if ($validator->fails()){
			Input::flash();
			return View::make('emails.form')->with('composerId',$composerId)->withErrors($validator);
		}
		$email = EmailController::getEmailAddress();
		$password = EmailController::getEmailPassword();
		$to = Input::get('to');
		$cc = Input::get('cc');
		$bcc = Input::get('bcc');
		$subject = Input::get('subject');
		$body = Input::get('body');
		$assetPath = EmailController::getToSendFolder().$composerId;
		$uploadPath = storage_path('/'.$assetPath);
		$files = array();
		if (file_exists($uploadPath)){
			$files = EmailController::getFilesInDir($uploadPath);
		}
	
		$transport = Swift_SmtpTransport::newInstance(EmailController::getEmailProtocol(), EmailController::getEmailPort(), EmailController::getEmailSsl())
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
}
