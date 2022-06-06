<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmailModel;
use CodeIgniter\Controller;
use CodeIgniter\Model;
use DateTime;
use org\bovigo\vfs\vfsStreamAbstractContent;

class Login extends BaseController
{
    #helpers
    public function __construct()
    {
        $helper_arrays = ['form', 'session', 'cookie', 'phpjwt'];
        helper($helper_arrays);
    }
    #login page
    public function index()
    {
        $sess = session();//accessing the session instance
        $sess->start();
        if (isset($_SESSION['session-id']) and $_SESSION['session-id'] != NULL) {
            return redirect()->to(base_url());
        } else {
            echo view('templates/header');
            echo view('login');
            echo view('templates/footer');
        }
    }
    #login/auth
    public function auth()
    {
        if ($this->request->getMethod() == 'post') {//if request method == post
            $session = session();
            $user_model = new UserModel();
            $auth_data = array(
                'user_name' => $this->request->getPost('uname'),
                'user_pass' => $this->request->getPost('password')
            );//get auth data
            $val = $user_model->verify($auth_data);//verify data
            if ($val === 1) {//if data verified
                $session->start();//open session
                $session->setFlashdata('auth', '1');//set only next request session data with val
                $user_id_result = $user_model->getUserId($this->request->getPost('uname'));//get rand_id using username
                $user_id = $user_id_result['user_rand_id'];
                $admin = $user_id_result['admin'];
                if(is_null($user_id_result['email_verified_at']) && is_null($user_id_result['verify_send_time'])) {
                    $email_model = new EmailModel();
                    $email_model->sendVerificatinEmail($user_id_result['user_mail'], $user_id);
                    $session->setFlashdata('require_verify', "Email Verification required <b>through 24 hours</b>");//set only next request session data with val
                    return redirect()->to(base_url('login'))->withInput();//go to this url(home page)
                }elseif(is_null($user_id_result['email_verified_at']) && !is_null($user_id_result['verify_send_time'])) {
                    $session->setFlashdata('require_verify', "Email Verification required");//set only next request session data with val
                    return redirect()->to(base_url('login'))->withInput();//go to this url(home page)
                }else{
                if ($admin == 1) {
                    $session->set('admin', 1);
                }
                $cookie = create_jwt($user_id);//get token using rand_id
                $session->set('session-id', $cookie);//set token as session_id
                return redirect()->to(base_url()); //->setCookie('sess',$cookie,time()+3600);
            }
            } else {//if data not verified
                $session->start();//open session
                return redirect()->to(base_url('login'))->withInput();//go to this url(home page)
            }
        } else {
            return redirect()->to(base_url('login'))->withInput();//go to this url(home page)
        }
    }

    public function verify($user_rand_id){
        $session=session();
        $session->start();
        if (!is_null($user_rand_id) || !empty($user_rand_id)) {//check if rand_id not exists
            $user_model = new UserModel();
            $noRecords = $user_model->verifyEmailAddress($user_rand_id);
            if ($noRecords > 0) {
                $session->setFlashdata('success_verify', "Email Verified Successfully!");
            }
            return redirect()->to(base_url('login'))->withInput();//go to this url(home page)
        }
    }
}
