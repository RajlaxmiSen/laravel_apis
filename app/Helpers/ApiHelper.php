<?php

namespace App\Helpers;

use GuzzleClient;
class ApiHelper{

	public $client;
	
	public $headers;
	public $response;
	
	public function __construct(){
		if(session('bearer_token')){
				$headers = [
    			'Authorization' => 'Bearer ' . session('bearer_token'),        
    			'Accept'        => 'application/json',
    			'Content-Type'  => 'application/json'
				];
	
			}else{
				$headers = [
    			'Accept'        => 'application/json',
    			'Content-Type'  => 'application/json'
				];

			}
		
		$this->response=[
						'response'=>1,
						'success'=>0,
						'message'=>"Some Error Occured!",
						'expection'=>"Some Exception Occured!",
						'status_code'=>"",

					];			
		$this->client=new GuzzleClient(['base_uri' => 'http://206.189.143.25:8000/api/' ,'headers'=>$headers]);
		
		
	}

	public function checkLogin($username,$password){
		
		try{
			$response = $this->client->request('POST','user/login/',[
			'form_params'=>[
				'username'=>$username,
				'password'=>$password	
			],
			'http_errors'=>true,

		]);
			if($response->getStatusCode()==200){
				$body=$response->getBody()->getContents();
				if(strlen($body)){
					$data=json_decode($body,true);
					if(isset($data['token']) && strlen($data['token'])){
						session(['bearer_token'=>$data['token']]);
						
						$this->response['token']=$data['token'];
						$this->response['success']=1;
						$this->response['message']='Login success!';

					}else{
						$this->response['message']="unable to verify user!";	
					}
				}else{
					$this->response['message']="Server Not responding!";	
				}
				

			}else{
				$this->response['message']="Unable to Login!";
			}
		}
		catch(\GuzzleHttp\Exception\RequestException $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Request Error!";
			
			if($e->hasResponse()){
				$this->response['status_code']=$e->getResponse()->getStatusCode();
			}

		}
		catch(\GuzzleHttp\Exception\ConnectException  $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Connection Error!";
						if($e->hasResponse()){
				$this->response['status_code']=$e->getResponse()->getStatusCode();
			}

		}
		catch(\GuzzleHttp\Exception\ClientException  $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Client Error!";
						if($e->hasResponse()){
				$this->response['status_code']=$e->getResponse()->getStatusCode();
			}

		}
		catch(\GuzzleHttp\Exception\ServerException  $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Server Error!";
						if($e->hasResponse()){
				$this->response['status_code']=$e->getResponse()->getStatusCode();
			}

		}		
		catch(\Exception $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Some Exception Occured!";

		}
		return $this->response;
		
	}
// /user/account/
	


	public function getUserAccount(){
		
		try{
			$response = $this->client->request('GET','user/account/',[
			'http_errors'=>true,

		]);
			if($response->getStatusCode()==200){
				$body=$response->getBody()->getContents();
				
				if(strlen($body)){
					$data=json_decode($body,true);
					if(is_array($data) && count($data)){
						$this->response['data']=$data;
						$this->response['success']=1;
						$this->response['message']='Data fetch success!';

					}else{
						$this->response['message']="Unable to parse data!";	
					}
				}else{
					$this->response['message']="Server not responding!";	
				}
				

			}else{
				$this->response['message']="Unable to fetch data!";
			}
		}
		catch(\GuzzleHttp\Exception\RequestException $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Request Error!";
			
			if($e->hasResponse()){
				$this->response['status_code']=$e->getResponse()->getStatusCode();
			}

		}
		catch(\GuzzleHttp\Exception\ConnectException  $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Connection Error!";
						if($e->hasResponse()){
				$this->response['status_code']=$e->getResponse()->getStatusCode();
			}

		}
		catch(\GuzzleHttp\Exception\ClientException  $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Client Error!";
						if($e->hasResponse()){
				$this->response['status_code']=$e->getResponse()->getStatusCode();
			}

		}
		catch(\GuzzleHttp\Exception\ServerException  $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Server Error!";
						if($e->hasResponse()){
				$this->response['status_code']=$e->getResponse()->getStatusCode();
			}

		}		
		catch(\Exception $e){
			$this->response['exception']=$e->getMessage();
			$this->response['message']="Some Exception Occured!";

		}
		return $this->response;
		
	}

}