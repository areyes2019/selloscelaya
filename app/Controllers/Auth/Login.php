<?php
    namespace App\Controllers\Auth;
    use App\Controllers\BaseController;
    use App\Models\UserModel;
    use Config\Auth;
    use Config\Services;

    class Login extends BaseController
    {
        protected $userModel;
        protected $authConfig;
        protected $session;
        protected $validation;

        public function __construct()
        {
            $this->userModel = new UserModel();
            $this->authConfig = config('Auth');
            $this->session = Services::session();
            $this->validation = Services::validation();
        }
        public function index()
        {
            if ($this->request->getMethod() === 'post') {
                return $this->processLogin();
            }
            
            return view('auth/login');
        }
    }

?>

