<?
	class homeAction{

		public function login(){
            $this->title = 'Authentification';
		}
		public function index(){  
            $this->title = 'Quizz musical';
            if(!userManager::isAuthentificated()){  
                route::$action = 'login';             
                route::$action = 'login';
            }
		}

	}
?>