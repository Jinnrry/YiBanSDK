<?php



/*
基类，一般不需要实例化
*/
class ApplicationInfoClass{
	protected $AppId;         //轻应用ID
	protected $AppSecret;     //轻应用AppSecret
	protected $ReturnUrl;     //轻应用回调地址
	
	
	
	
	/*
	构造函数
	*/
	function __construct($AppId,$AppSecret,$ReturnUrl)              
	{
		$this->AppId=$AppId;
		$this->AppSecret=$AppSecret;
		$this->ReturnUrl=$ReturnUrl;
		if(!isset($_GET['verify_request']))//申请易班授权
		{
			header("Location: https://openapi.yiban.cn/oauth/authorize?client_id=$this->AppId&redirect_uri=$this->ReturnUrl"); 		
		}
	}
	protected function Decipher()//解密授权码
	{
		$postObject = addslashes($_GET["verify_request"]);                                           

                        
  		$postStr = pack("H*", $postObject);
  		$postInfo = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->AppSecret, $postStr, MCRYPT_MODE_CBC, 

$this->AppId);
  		$postInfo = rtrim($postInfo);
		return $postInfo;
	
	}
	
	protected function json_object($postInfo)
	{
		return json_decode($postInfo); 
	}
	
    protected function object_array($array) 
	{           
        if(is_object($array)) 
		{  
        $array = (array)$array;  
        } 
		if(is_array($array)) 
		{  
        	foreach($array as $key=>$value) 
			{  
            	$array[$key] = $this->object_array($value);  
            }  
    }  
    	return $array;  
    }
	
	protected function json_array($json)
	{
		$object=$this->json_object($json);
		$array=$this->object_array($object);
		return $array;
	}
	
	
	/*
	获取用户易班ID
	*/
	public function GetUserId()
	{
		$postInfo=$this->Decipher();
		$InfoArray=$this->json_array($postInfo);
		return $InfoArray[visit_user][userid];
	}
	
	
	/*
	获取用户姓名
	*/
	public function GetUserName()
	{
		$postInfo=$this->Decipher();
		$InfoArray=$this->json_array($postInfo);
		return $InfoArray[visit_user][username];
	}
	
	
	/*
	获取用户昵称
	*/
	public function GetUserNick()
	{
		$postInfo=$this->Decipher();
		$InfoArray=$this->json_array($postInfo);
		return $InfoArray[visit_user][usernick];	
	}
	
	
	/*
	
	获取用户性别
	*/
	public function GetUserSex()
	{
		$postInfo=$this->Decipher();
		$InfoArray=$this->json_array($postInfo);
		return $InfoArray[visit_user][usersex];
	}
	
	
	
	/*
	获取用户授权码
	*/
	public function GetUserToken()
	{
		$postInfo=$this->Decipher();
		$InfoArray=$this->json_array($postInfo);
		return $InfoArray[visit_oauth][access_token];
	}



/*
获取用户授权码过期时间
*/
	public function GetTokenExpires(){
		$postInfo=$this->Decipher();
		$InfoArray=$this->json_array($postInfo);
		return $InfoArray[visit_oauth][token_expires];
	}
}





/*
普通权限应用请实例化该类
*/
class UserBasicInfo extends ApplicationInfoClass{
	private $token;
	private $url;
	
	private function getjson()
	{
		$this->token=$this->GetUserToken();
		$this->url="https://openapi.yiban.cn/user/me?access_token=".$this->token;
		$UserInfo_json=file_get_contents($this->url);
		return $UserInfo_json;
	}
	private function getarray()
	{
		$json=$this->getjson();
		$array=$this->json_array($json);
		return $array;
	}
	
	/*
	获取用户网薪
	*/
	public function GetMoney()
	{
		$array=$this->getarray();
		return $array[info][yb_money];
	}
	
	/*
	获取用户经验
	*/
	public function GetExp()
	{
		$array=$this->getarray();
		return $array[info][yb_exp];
	
	}
	
	/*
	获取用户头像
	*/
	public function GetUserHead()
	{
		$array=$this->getarray();
		return $array[info][yb_userhead];
	}
	
	/*
	获取学校ID
	*/
	public function GetSchoolId()
	{
		$array=$this->getarray();
		return $array[info][yb_schoolid];
	}
	
	
	public function GetRegtime()
	{
		$array=$this->getarray();
		return $array[info][yb_regtime];
	}
	
	/*
	获取学校名称
	*/
	public function GetSchoolName()
	{
		$array=$this->getarray();
		return $array[info][yb_schoolname];
	}

}






/*
校级权限轻应用请实例化这个类
*/
class UserSchoolInfo extends UserBasicInfo{
	private $token;
	private $url;
	private function getjson()
	{
		$this->token=$this->GetUserToken();
		$this->url="https://openapi.yiban.cn/user/verify_me?access_token=".$this->token;
		$UserInfo_json=file_get_contents($this->url);
		return $UserInfo_json;
	}
	private function getarray()
	{
		$json=$this->getjson();
		$array=$this->json_array($json);
		return $array;
	}
	
	/*
	获取学校名称
	*/
	public function GetSchoolName()
	{
		$array=$this->getarray();
		return $array[info][yb_schoolname];
	}
	
	/*
	获取用户真实姓名
	*/
	public function GetUserRealName() 
	{
		$array=$this->getarray();
		return $array[info][yb_realname];
	}    	
	
	
	/*
	获取用户认证信息（学生为学号，老师为教师编号）
	*/
	public function GetStudentId()
	{
		$array=$this->getarray();
		return $array[info][yb_studentid];

	}
	
	
	public function GetExamind()
	{
		$array=$this->getarray();
		return $array[info][yb_examid];

	}
	
	
	public function GetAdmissionid()
	{
		$array=$this->getarray();
		return $array[info][yb_admissionid];

	}	




}
	





