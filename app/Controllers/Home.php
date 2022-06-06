<?php

namespace App\Controllers;

use App\Models\EmailModel;
use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\Model;

class Home extends BaseController
{
    #helpers & session start
    public function __construct()
    {
        $helpers = ['phpjwt'];
        helper($helpers);
    }
    #Home page(index)
	public function index()
	{
            $sess = session();//accessing the session instance
            $sess->start();//open session
            if ($sess->has('session-id') and $_SESSION['session-id'] != NULL) {//if session_id exists
                $jwt_token = (string)$sess->get('session-id');
                $jwt_token = (array)verify_jwt($jwt_token);
                $user_rand_id = esc($jwt_token["id"]);//rand_id
                if (is_null($user_rand_id) || empty($user_rand_id)) {//check if rand_id not exists
                    $sess->destroy();//destroy session
                    return redirect()->route('');//go to index
                }
            }
            $post_model = new PostModel();
            //list--> assign variables to array values in order
            list($categories, $posts) = $post_model->getAllPost('default');//get all categories & blogs
            $data['posts'] = $posts;
            $data['categories'] = $categories;
            echo view('templates/header');
            echo view('welcome_message', $data);//display posts and categories in Home page
            echo view('templates/footer');

	}

	//--------------------------------------------------------------------






}
