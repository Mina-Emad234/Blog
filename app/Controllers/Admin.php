<?php


namespace App\Controllers;


use App\Models\BlogModel;
use App\Models\CommentModel;
use App\Models\PostModel;

class Admin extends BaseController
{

    public function post($action,$id=null){
        if ($action == 'all') {
            $post_model = new BlogModel();
            $posts = $post_model->paginate(10);
            $pager = $post_model->pager;
            $data['posts'] = $posts;
            $data['pager'] = $pager;
            echo view('templates/header');
            echo view('admin/posts', $data);
            echo view('templates/footer');
        }elseif ($action == 'activate' && isset($id)){
            $post_model = new PostModel();
            $done=$post_model->activatePost($id);
            if($done > 0){
                return redirect()->to('/admin/posts/all')->with('activated','Post activated successfully!');
            }
        }elseif ($action == 'deactivate' && isset($id)){
            $post_model = new PostModel();
            $done=$post_model->deactivatePost($id);
            if($done > 0){
                return redirect()->to('/admin/posts/all')->with('deactivated','Post deactivated successfully!');
            }
        }elseif ($action == 'delete' && isset($id)){
            $post_model = new PostModel();
            $done=$post_model->adminDeeletePost($id);
            if($done > 0){
                return redirect()->to('/admin/posts/all')->with('deleted','Post deleted successfully!');
            }
        }
    }

    public function comment($do,$id=null){
        if ($do == 'all') {
            $comment_model = new CommentModel();
            $comments=$comment_model->paginate(10);
            $pager=$comment_model->pager;
            $data['comments']=$comments;
            $data['pager']=$pager;
            echo view('templates/header');
            echo view('admin/comments',$data);
            echo view('templates/footer');
        }elseif ($do == 'activate' && isset($id)){
            $post_model = new PostModel();
            $done=$post_model->activateComment($id);
            if($done > 0){
                return redirect()->to('/admin/comments/all')->with('activated','Comment activated successfully!');
            }
        }elseif ($do == 'deactivate' && isset($id)){
            $post_model = new PostModel();
            $done=$post_model->deactivateComment($id);
            if($done > 0){
                return redirect()->to('/admin/comments/all')->with('deactivated','Comment deactivated successfully!');
            }
        }elseif ($do == 'delete' && isset($id)){
            $post_model = new PostModel();
            $done=$post_model->adminDeeleteComment($id);
            if($done > 0){
                return redirect()->to('/admin/comments/all')->with('deleted','Comment deleted successfully!');
            }
        }
    }
}