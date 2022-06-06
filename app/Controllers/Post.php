<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Users;
use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class Post extends BaseController
{
    #helpers
    public function __construct()
    {
        $helpers = array('form','phpjwt');
        helper($helpers);
    }
    #create post
    public function create()
    {
        $ses = session();
        $ses->start();
        if (!$ses->has('session-id')) {//if session_id exists
            $ses->destroy();//destroy session
            return redirect()->route('');//go to index
        } else {//if rand_id exists
            $val = \Config\Services::validation();
            $jwt_token = (string) $ses->get('session-id');
            $jwt_token = (array) verify_jwt($jwt_token);
            $user_rand_id = esc($jwt_token["id"]);//rand_id
            $time = (array) Time::now('Africa/cairo', 'en_US');//set time
            $un_san_post_data = array(
                "post_title" => $this->request->getPost("post_title"),
                "post_content"  => $this->request->getPost("post_content"),
                "post_category" => $this->request->getPost("post_category")
            );//posts data
            $san_post_data = esc($un_san_post_data);//escape data
            $val->setRuleGroup('postvalid');//make rule group(multiple validation)
            if ($val->withRequest($this->request)->run() === FALSE) {//if validation false
                return redirect()->back()->withInput();
            } else {//if validation true
                $post_data = array(
                    "blog_title" => $san_post_data["post_title"],
                    "blog_body"  => $san_post_data["post_content"],
                    "user_rand_id" => $user_rand_id,
                    "blog_created_time" => $time["date"],
                    "post_category" => $san_post_data["post_category"]
                );//prepare data to insert
                $post_model = new PostModel();//get post model
                $post_model->insertPost($post_data);//insert data
                return redirect()->to('/users/profile')->with('posted', "Posted Successfully");
                //return to profile page with success message
            }
        }
    }
    #delete post
    public function delete($blog_id)
    {
      $sess = session();
      $sess->start();
      $jwt_token = Users::session_check();
      $user_rand_id = $jwt_token['id'];//rand_id
      if(is_null($blog_id) || empty($blog_id)) {//if post_id not exists
        return redirect()->to(base_url());//go to index
        die;
      } else {//if post_id exists
        $post_model = new PostModel();//get post model
        $deleted = $post_model->verfiyPostUser($blog_id, $user_rand_id);//verify post using post_id and rand_id
        if($deleted == null || $deleted == 0) {//if post not verified
          $sess->destroy();//destroy session
          return redirect()->to(base_url());//go to index
        } else {//if post verified
          $post_model = new PostModel();
          $affected_rows = $post_model->deletePost($blog_id);//delete post and cat_post relation row
          if($affected_rows) {//if post deleted
            return redirect()->to('/users/profile')->with('post_deleted','Deleted Successfully');//success session
          } else {
            return redirect()->to('/users/profile')->with('error','Error Occurred');//error session
          }
        }
      }
    }
    #display post
    public function display($blog_id = 'default')
    {
      $sess = session();
      $sess->start();
      if (!$sess->has('session-id') ){
          return redirect()->to(base_url('/login'));//go to index
      }elseif(empty($blog_id) || $blog_id == 'default'){//check blog_id if empty
        return redirect()->to(base_url());//go to index
      } else {
        $post_model = new PostModel();//get post model
        list($blog, $category) = $post_model->getBlog($blog_id);//get post_data and categories related and assign to vars
        if(is_null($blog) || is_null($category)) {//if post & categories empty
          throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();//error Exception
        } else {//if post & categories not empty
          $comments = $post_model->getAllComments($blog_id);
          $data['comments']=$comments;
          $data['blog'] = $blog;//prepare variable to pass to the view
          $data['category'] = $category;//prepare variable to pass to the view
          echo view('templates/header');
          echo view('posts/post', $data);
          echo view('templates/footer');
        }
      }
    }

}
