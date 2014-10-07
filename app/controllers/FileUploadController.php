<?php
use Illuminate\Http\Request;
class FileUploadController extends BaseController{
	
	//Carga archivos al servidor
	public function upload($composerId=null){
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
	
	
	public function get($composerId=null){
		$result = array();
		if ($composerId!=null){
			$assetPath = EmailController::getToSendFolder().$composerId;
			$uploadPath = storage_path('/'.$assetPath);
			if (file_exists($uploadPath)){
				$files = EmailController::getFilesInDir($uploadPath);
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
	
	public function post(){
		$files = Input::file('files');
		// We will store our uploads in public/uploads/basic
		$composerId = EmailController::getComposerId();
		$assetPath = EmailController::getToSendFolder().$composerId;
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
	
	
	public function delete($composerId){
		$files = Input::get('files');
		// We will store our uploads in public/uploads/basic
		$assetPath = EmailController::getToSendFolder().$composerId;
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
	
	public function recursiveIncrementFilename ($path, $filename){
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

}
