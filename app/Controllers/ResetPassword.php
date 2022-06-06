<?php


namespace App\Controllers;


use App\Models\EmailModel;
use App\Models\UserModel;

class ResetPassword extends BaseController
{
    public function __construct()
    {
        $helper_arrays = ['form', 'session', 'cookie', 'phpjwt'];
        helper($helper_arrays);
    }

    public function getEmail(){
        echo view('templates/header');
        echo view('password/email');
        echo view('templates/footer');
    }



    public function sendEmail(){
        if ($this->request->getMethod() == 'post') {//if request method == post
            $validation =  \Config\Services::validation();//validation library
            $validation->setRules(['email' => ['label' => 'E-mail', 'rules' => 'required|valid_email']]);
            if ($validation->withRequest($this->request)->run() === FALSE) {
                return redirect()->back();
            }else{
                $email = esc($this->request->getPost('email'));
                $user_model = new UserModel();
                $userData = $user_model->getEmail($email);
                if(!$userData){
                    return redirect()->back()->with('wrong_email','wring or <b>Not Exists</b> Email!');
                }else{
                    $verifyCode = rand(100000,999999);
                    $email_model = new EmailModel();
                    $send = $email_model->sendResetPasswordCode($userData['user_mail'],$userData['user_rand_id'],$verifyCode);
                        if($send) {
                            $data['$user_rand_id']=$userData['user_rand_id'];
                            return redirect()->to('/resetpassword/getVerify/'.$userData['user_rand_id']);
                        }else{
                            return redirect()->back()->with('try_again','Email Not sent<b>Try again after some minutes</b>');
                        }
                }
            }
        }else{
            return redirect()->back();
        }
    }
    public function getVerify($user_rand_id){
        $data['user_rand_id']=$user_rand_id;
        echo view('templates/header');
        echo view('password/verify',$data);
        echo view('templates/footer');
    }
    public function sendCode(){
        if ($this->request->getMethod() == 'post') {//if request method == post
            $validation =  \Config\Services::validation();//validation library
            $validation->setRule('verify', 'verify',  'required|exact_length[6]|numeric');
            if ($validation->withRequest($this->request)->run() === FALSE) {
                return redirect()->back()->withInput();
            }else{
                $user_rand_id=esc($this->request->getPost('rand_id'));
                $code=esc($this->request->getPost('verify'));
                $user_model = new UserModel();
                $userData = $user_model->getCode($user_rand_id);
                if(!$userData || $userData['verification_code'] != $code){
                    return redirect()->to('/resetpassword/getVerify/'.$user_rand_id)->with('wrong_verify','<b>Invalid Code!</b>');
                }else{
                    return redirect()->to('/resetpassword/getReset/'.$user_rand_id);
                }
            }
        }else{
            return redirect()->to('/resetpassword/getVerify/'.$user_rand_id)->with('try_again','Something is wrong<b>Try again after some minutes</b>');
        }
    }
    public function getReset($user_rand_id){
        $data['user_rand_id']=$user_rand_id;
        echo view('templates/header');
        echo view('password/reset',$data);
        echo view('templates/footer');
    }
    public function resetPassword(){
        if ($this->request->getMethod() == 'post') {//if request method == post
        $validation =  \Config\Services::validation();//validation library
        $validation->setRules([ 'password' => ['label' => 'Password', 'rules' => 'required|min_length[5]'],
            'confirm_password' => ['label' => 'Confirm Password', 'rules' => 'required|matches[password]']]);
            if ($validation->withRequest($this->request)->run() === FALSE) {
                return redirect()->back()->withInput();
            }else{
                $password = $this->request->getPost('password');
                $confirm_password = $this->request->getPost('confirm_password');
                $user_rand_id=$this->request->getPost('rand_id');
                $user_model = new UserModel();
                $hash_pass = password_hash($password, PASSWORD_BCRYPT);//hash new password
                $affected_rows = $user_model->updatePassword($user_rand_id, $hash_pass);//update password
                if($affected_rows > 0){
                    return redirect()->to(base_url('login'))->with('reset','<b>Password reset successfully</b>');
                }else{
                    return redirect()->to('/resetpassword/getReset/'.$user_rand_id)->with('wrong_data','<b>Invalid data</b>');
                }
            }
        }else{
            return redirect()->to('/resetpassword/getReset/'.$user_rand_id)->with('try_again','Something is wrong<b>Try again after some minutes</b>');
        }
    }

}