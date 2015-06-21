<?
    class userManager{

        public static function isAuthentificated(){
            return isset($_SESSION['userData']);
        }

        public static function get($name){
            if(self::isAuthentificated()){
                return $_SESSION['userData'][$name];
            } else {
                return null;
            }
        }
        public static function set($name, $value){
            if(self::isAuthentificated()){
                $_SESSION['userData'][$name] = $value;
            }
        }
    }
?>