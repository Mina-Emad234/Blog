<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\Users;
use App\Models\PostModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;

class Comment extends BaseController
{
    #helpers
    public function __construct()
    {
        $helpers = array('form','session','phpjwt');
        helper($helpers);
    }

    public function create(){
        $user_rand_id =  Users::session_check();//check session
        if ($this->request->isAJAX()) {//check if request come from ajax
            if ($user_rand_id != NULL && $user_rand_id != '') {//if session_id exists
                $post_model = new PostModel();//get model
                $comment_data = json_decode($this->request->getPost('comment_data'),true);//get cat name as associative
                $comment_data = esc($comment_data, 'html');//escape cat data
                $rand_id = esc($user_rand_id, 'html');//escape rand_id
                $comment = array(
                    'comment_text'  => $this->request->getPost('comment'),
                    'user_rand_id'    => $rand_id,
                    'post_id'=> $this->request->getPost('post_id')
                );//prepare data to insert
                $affectedRows = $post_model->insertComment($comment);
                if ($affectedRows > 0){
                    return $this->response->setHeader(csrf_header(), csrf_hash())->setBody('{"msg":"success"}');
                }
            }
        }
    }

    public function delete(){
        $user_rand_id =  Users::session_check();//check session
        if ($this->request->isAJAX()) {//check if request come from ajax
            $id=json_decode($this->request->getPost('id'));
            $id = esc($id, 'html');//escape cat data
            $rand_id = esc($user_rand_id, 'html');//escape rand_id
            $post_model = new PostModel();//get post model
            $commentExists = $post_model->getUserComment($id, $rand_id);//verify post using post_id and rand_id
            if($commentExists > 0) {
                    $affected_rows = $post_model->deleteComment($id);//delete post and cat_post relation row
                    if($affected_rows) {//if post deleted
                        return $this->response->setHeader(csrf_header(), csrf_hash())->setBody('{"msg":"comment deleted"}');
                    } else {
                        return $this->response->setHeader(csrf_header(), csrf_hash())->setBody('{"msg":"failed comment deleted"}');
                    }
                }
            }
        }
        #update comment
        public function update(){
            $user_rand_id =  Users::session_check();//check session
        if ($this->request->isAJAX()) {//check if request come from ajax
            $id=json_decode($this->request->getPost('id'));
            $comment=json_decode($this->request->getPost('comment'));
            $id = esc($id, 'html');
            $comment = esc($comment, 'html');
            $rand_id = esc($user_rand_id, 'html');//escape rand_id
            $post_model = new PostModel();//get post model
                $commentExists = $post_model->getUserComment($id, $rand_id);//verify post using post_id and rand_id
            if($commentExists > 0) {
                    $affected_rows = $post_model->updateComment($comment,$id,$rand_id);//delete post and cat_post relation row
                    if($affected_rows) {//if post deleted
                        return $this->response->setHeader(csrf_header(), csrf_hash())->setBody('{"msg":"comment update"}');
                    } else {
                        return $this->response->setHeader(csrf_header(), csrf_hash())->setBody('{"msg":"failed comment updated"}');
                    }
                }
            }
        }
}
