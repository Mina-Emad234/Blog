<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Config\Config;
use CodeIgniter\Controller;

class Signup extends Controller
{
    /*
    * Making the helpers available to all methods
    */
    #helpers
    public function __construct()
    {
        $helper_arrays = ['form', 'session', 'url', 'text'];
        helper($helper_arrays);
    }

    /*
    *   Inflating signup page
    */
    public function index()
    {
        echo view('templates/header');
        echo view('signup');
        echo view('templates/footer');
    }

    /*
    *   Initializing the validation and session library;
    *   Validating and showing errors
    *   user registration
    */

    public function register()
    {
        $validation =  \Config\Services::validation();//validation library
        $validation->setRules([//validation rules
            'e_mail' => ['label' => 'E-mail', 'rules' => 'required|valid_email'],
            'mobile' => ['label' => 'Mobile', 'rules' => 'required|exact_length[11]|is_unique[users.mobile,id]|mobileValidation[mobile]'],
            'fname' => ['label' => 'FirstName', 'rules' => 'required|alpha'],
            'lname' => ['label' => 'LastName', 'rules' => 'required|alpha'],
            'uname' => ['label' => 'Username', 'rules' => 'required|alpha_numeric'],
            'passwd' => ['label' => 'Password', 'rules' => 'required|min_length[5]'],
            'confirm_pass' => ['label' => 'Confirm Password', 'rules' => 'required|matches[passwd]'],
            'image' => [
                'label' => 'Confirm Password',
                'rules' => 'uploaded[image]'.
                '|is_image[image]'.
                '|mime_in[image, image/png, image/jpg,image/jpeg, image/gif]'.
                '|max_size[image, 4096]'
            ],
        ],[
            'mobile' => [
                'mobileValidation' => 'Invalid Mobile Number',
            ],
        ]);
        $session = \Config\Services::session();//session library
        $session->start();//session open
        if ($this->request->getMethod() == 'post') {//if method == post
            $u_model = new UserModel();
            $username_val = $u_model->getUniqueUsername($this->request->getPost('uname'));//check if user is unique
            // echo var_dump($username_val);die;
            if ($validation->withRequest($this->request)->run() === FALSE || $username_val != NULL) {//check if validation is false
                if ($username_val != NULL) {//check if username isn't unique
                    $session->setFlashdata('username', 'Username already taken');
                }
                return redirect()->back()->withInput();//go to url(sign-up page)
            } else {//if validation is true
                $hash_pass = password_hash($this->request->getPost('passwd'), PASSWORD_BCRYPT);//hash password
                $random_id = random_string('alnum', 32);//Generates a random string
                 $imageFile = $this->request->getFile('image');
                 $newName = $imageFile->getRandomName();
                $imageFile->move(  ROOTPATH . '/public/uploads',$newName);
                $san_data = array(
                        'user_name' => esc($this->request->getPost('uname')),
                        'user_fname' => esc($this->request->getPost('fname')),
                        'user_lname' => esc($this->request->getPost('lname')),
                        'user_mail' => esc($this->request->getPost('e_mail')),
                        'mobile' => esc($this->request->getPost('mobile')),
                        'user_pass' => $hash_pass,
                        'image'=>$newName,
                        'user_rand_id' => $random_id
                );
                $u_model->insertSignupData($san_data);//insert user registration data
                $session->setFlashdata('Success', 'Successfully registered');//next request success value session
                return redirect('login');//go to login page
            }
        } else {//if method not post
            return redirect()->back();//go to url(sign-up page)
        }
    }
}
