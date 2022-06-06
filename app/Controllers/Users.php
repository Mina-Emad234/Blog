<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\Config\Config;
use org\bovigo\vfs\vfsStreamAbstractContent;

class Users extends BaseController
{
    #helpers & session start
    public function __construct()
    {
        $helpers = ['phpjwt', 'session','form'];
        helper($helpers);
    }
    #check session
    public static function session_check()
    {
        $session = session();
        $session->start();
        if ($session->has('session-id')) {//if session_id exists
            $payload = (string) $session->get('session-id');//convert session value(token) to string
            $sign_verify = (array) verify_jwt($payload);//verify token -->decode to php object
            $expiration = $sign_verify['exp'];
            $not_before = $sign_verify['nbf'];
            if ($expiration <= time() and $not_before <= time()) {//if token not expired
                $session->destroy();//destroy session
                return redirect()->to('/login');//go to index
            }else{
                return $u_id = $sign_verify['id'];
            }
        } else {//if session not exists
            return redirect()->to('/login');//go to index
        }
    }
    #profile page (user's posts)
    public function profile()
    {
        $u_sess = session();//accessing the session instance
        $u_sess->start();//start session
        if ($u_sess->has('session-id') and $_SESSION['session-id'] != NULL) {//if session_id exists
            $payload = (string) $u_sess->get('session-id');//convert session value(token) to string
            $sign_verify = (array) verify_jwt($payload);//verify token -->decode to php object
            $expiration = $sign_verify['exp'];
            $not_before = $sign_verify['nbf'];
            $u_id = $sign_verify['id'];

            if ($expiration >= time() and $not_before <= time()) {//if token not expired
                $post_model = new PostModel();
                list($categories, $posts) = $post_model->getAllPost($u_id);#get all posts or user's posts
                $data['posts'] = $posts;//posts
                $data['categories'] = $categories;//cats
                echo view('templates/header');
                echo view('users/profile', $data);//pass user's posts and cats of posts to view
                echo view('templates/footer');
            } else {//if token expired
                $u_sess->destroy();//destroy session
                return redirect()->to(base_url());//go to index
            }
        } else {//if session not exists
            $u_sess->destroy();//destroy session
            return redirect()->route('');//go to index
        }
    }
    #get posts data(categories) to display in posts view
    public function post()
    {
        $p_sess = session();
        $p_sess->start();
        if ($p_sess->has('session-id')) {//if session_id exists
            $user_model = new UserModel();
            $cat_data = $user_model->getCategoryDetails();#get category data
            if (!empty($cat_data)) {
                $data['cat_options'] = $cat_data;//cat data to pass to view
                echo view('templates/header');
                echo view('users/post', $data);
                echo view('templates/footer');
            }
        } else {
            return redirect()->to(base_url());
        }
    }
    #edit user data to update using AJAX
    public function profileData()
    {
        $pro_sess = session();
        $pro_sess->start();
        Users::session_check();//check session
        if ($this->request->isAJAX()) {//check if request come from ajax
            $jwt_token = (string) $pro_sess->get('session-id'); //$this->request->getGet('sess');
            $jwt_decoded = (array) verify_jwt($jwt_token);//decode(verify) token in session
            $user_rand_id = $jwt_decoded['id'];//get user_rand_id from token decoding
            $usermodel = new UserModel();
            $data = $usermodel->getUserDetails($user_rand_id);//#get user data using rand_id
            return $this->response->setHeader(csrf_header(), csrf_hash())->setBody(json_encode($data));
            //response contains user data (request body) in addition to csrf_header and csrf_hash (request header)
        }
    }
    #update user's data (profile)
    public function update_profile()
    {
        $jwt_decoded = Users::session_check();//check session
        if ($this->request->isAJAX()) {//check if request come from ajax
            $user_rand_id = $jwt_decoded['id'];//get rand_id
            $user_data = (array) json_decode($this->request->getPost('user_data'));//get user data as (array)
            $user_model = new UserModel();
            $user_model->setUserData($user_rand_id, $user_data);//update user data
            return $this->response->setHeader(csrf_header(), csrf_hash())->setBody('{"msg":"success"}');
        }
    }

    public function pass_change()
    {
        $pass_sess = session();
        $pass_sess->start();//start session
        $jwt_decoded = Users::session_check();//check session
        $user_rand_id = $jwt_decoded['id'];//get rand_id
        $old_pass = $this->request->getPost('old_pass');//old password
        $new_pass1 = $this->request->getPost('pass1');//new password
        $new_pass2 = $this->request->getPost('pass2');//confirm password

        $validation = \Config\Services::validation();//validation library
        $validation->setRules([
            'pass1' => ['rules' => 'required|min_length[5]'],
            'pass2' => ['rules' => 'required|min_length[5]|matches[pass1]']
        ]);//validation rules

        $user_model = new UserModel();
        $verification = $user_model->verify_old_pass($user_rand_id, $old_pass);//password verification using rand_id
        if ($validation->withRequest($this->request)->run() === FALSE) {//if data not valid
            return redirect()->back()->withInput();
        } else {//if data valid
            if ($verification) {//if password verified
                $hash_pass = password_hash($new_pass2, PASSWORD_BCRYPT);//hash new password
                $affected_rows = $user_model->updatePassword($user_rand_id, $hash_pass);//update password
                $pass_sess->setFlashdata('success', 'Password Updated successfully');//set only next request session data with val
                return redirect()->route('users/profile');//go to profile page
            }
            $pass_sess->setFlashdata('old', 'Not correct old password');//set only next request session data with val
            echo view('templates/header');
            echo view('users/profile');
            echo view('templates/footer');
        }//get profile view without data if password not verified
    }
    #get all categories to display in categories view
    public function categories()
    {
        $u_sess = session();
        $u_sess->start();
        $user_rand_id = Users::session_check();//check session
        if ($u_sess->has('session-id')) {//if session_id exists
            $user_model = new UserModel();//get model
            $cat_model = new CategoryModel();//get model
            $category = $cat_model->join('users','cat_created_by = user_rand_id')->paginate(10);
            $pager = $cat_model->pager;
            $data['cat'] = $category;//assign cat array to variable to pass to view
            $data['uuid'] = $user_rand_id;
            $data['pager'] = $pager;
            echo view('templates/header');
            echo view('users/categories', $data);//pass categories to the view
            echo view('templates/footer');
        }else{
            return redirect()->to(base_url());
        }
    }
    #create category
    public function cat_create()
    {
        $valid = \Config\Services::validation();//validation library
        $user_rand_id = Users::session_check();//check session
        if ($this->request->isAJAX()) {//check if request come from ajax
            if ($user_rand_id != NULL && $user_rand_id != '') {//if session_id exists
                $user_model = new UserModel();//get model
                $un_san_data = json_decode($this->request->getPost('cat_data'), true);//get cat name as associative
                $san_cat_data = esc($un_san_data, 'html');//escape cat data
                $san_cat_id = esc($user_rand_id, 'html');//escape rand_id
                $cat_data = array(
                    'cat_name'  => $san_cat_data['category_name'],
                    'cat_created_by'    => $san_cat_id
                );//prepare data to insert
                if ($valid->run($un_san_data, 'categoryvalid') === FALSE) {//error validation
                    $error = $valid->getErrors();//get errors
                    $error['msg'] = 'error';//error msg
                    return $this->response->setHeader(csrf_header(), csrf_hash())->setBody(json_encode($error));
                    //send token & error
                } else {//if validation is true
                    $aff_rows = $user_model->setCategory($cat_data);//insert new category if not exists
                    if ($aff_rows != 0) {//if category exists return token and success msg
                        return $this->response->setHeader(csrf_header(), csrf_hash())->setBody('{"msg":"success"}');
                    } else {//if category not exists  return token and error msg
                        return $this->response->setHeader(csrf_header(), csrf_hash())->setBody('{"msg":"failed"}');
                    }
                }
            }
        }
    }

    public function deleteCategory($id): \CodeIgniter\HTTP\RedirectResponse
    {
        $user_model = new UserModel();
        $deleted=$user_model->deleteCategory($id);
        if($deleted){
            return redirect()->to('/users/categories')->with('catDeleted','Category deleted successfully!');
        }else{
            return redirect()->back();
        }
    }

    public function updateCategory(){
        if($this->request->getPost('update_cat') == 'update'){
            $data=[
                'cat_id'=>$this->request->getPost('cat_id'),
                'cat_name'=>$this->request->getPost('cat_name')
            ];
            $user_model = new UserModel();
            $updated = $user_model->updateCategory($data);
            if($updated){
                return redirect()->to('/users/categories')->with('catUpdated','Category updated successfully!');
            }
        }else{
            return redirect()->back();
        }
    }
    #logout
    public function logout()
    {
        $sess = session();
        $sess->destroy();//destroy session
        return redirect()->to(base_url());//go to index
    }
}
