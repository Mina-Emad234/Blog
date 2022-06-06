<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    public function __construct()
    {
        $helpers = array('text');
        helper($helpers);
    }

    public static function insertCategory(string $blog_id, array $cat_values)
    {
        $db = db_connect();
        $builder = $db->table('cat_blog');
        $cat_insert = array();
        foreach ($cat_values as $key => $value) {
            $cat_insert_single = array(
                'blog_id' => $blog_id,
                'cat_name' => $value
            );
            array_push($cat_insert, $cat_insert_single);
        }
        $builder->insertBatch($cat_insert);
        $db->close();
    }

    public function insertPost(array $values)
    {
        $db = db_connect();
        $builder = $db->table('blog');
        $random_blog_id = random_string('alnum', 16);
        $blog_insert = array(
            'blog_id'    => $random_blog_id,
            'blog_title' => $values['blog_title'],
            'blog_body'  => $values['blog_body'],
            'blog_created_time' => $values['blog_created_time'],
            'user_rand_id' => $values['user_rand_id']
        );
        $builder->insert($blog_insert);
        $db->close();
        PostModel::insertCategory($random_blog_id, $values['post_category']);
        return $db->affectedRows();
    }

    #get all posts or user's posts
    public function getAllPost(string $user_rand_id = 'default')
    {
        $db = db_connect();
        $builder = $db->table('blog');
        $blog_ids = array();
        if ($user_rand_id === 'default') {//if rand_id == default
            $query = $builder->select(['blog_id', 'blog_title', 'blog_body', 'blog_created_time'])->where('active',1)->get();//get all blogs
        } else {//if rand_id == user_rand_id
            $query = $builder->select(['blog_id', 'blog_title', 'blog_body', 'blog_created_time'])->where(['user_rand_id' => $user_rand_id,'active'=>1])->get();
            //get all blogs related with user
        }
        $blogs = $query->getResultArray();//get data in array
        if (is_null($blogs)) {//if no blogs
            return null;
        } else {//if blogs array not null
            foreach ($blogs as $key => $value) {
                array_push($blog_ids, $blogs[$key]['blog_id']);//push blog_ids in array
            }
            $categories = PostModel::returnCategories($blog_ids);//get categories using blog_ids-->categories [$blog_id=>$cat_name]
            $cat_id_name = array();
            foreach ($categories as $key => $values) {
                $blog_id_cat = $key;//get blog id
                foreach ($values as $key => $value) {
                    $cat_id_name[$blog_id_cat][] = $value['cat_name'];
                }//multi dimensional array[blog_id=>[cat_name,cat_name]]-->assign array of collection of categories to it's blog_id
            }
            return array($cat_id_name, $blogs);//return categories & blogs
        }
    }
    #get categories using blog_ids
    public static function returnCategories(array $blog_id)
    {
        $db = db_connect();
        $builder = $db->table('cat_blog');//cat blog relation
        $blog_id_cat = array();
        foreach ($blog_id as $key => $value) {
            $query = $builder->select(['cat_name'])->where(['blog_id' => $value])->get();
            $blog_id_cat[$value] = $query->getResultArray();//categories [$blog_id=>$cat_name]
        }
        $db->close();
        return $blog_id_cat;
    }
    #verify post using post_id and rand_id
    public function verfiyPostUser($blog_id, $user_rand_id)
    {
        $db = db_connect();
        $builder = $db->table('blog');
        $query = $builder->select(['blog_id'])->where(['user_rand_id' => $user_rand_id, 'blog_id' => $blog_id])->get();
        return $db->affectedRows();//row num
    }
    #delete post and cat_post relation row
    public function deletePost($blog_id)
    {
        $db = db_connect();
        $builder = $db->table('blog');
        $query = $builder->delete(['blog_id' => $blog_id]);
        $builders = $db->table('cat_blog');
        $query = $builders->delete(['blog_id' => $blog_id]);
        return $db->affectedRows();//row num
    }
    #get post_data and categories related
    public function getBlog($blog_id)
    {
        $db = db_connect();
        $builder = $db->table('blog');
        $query = $builder->select(['blog_id', 'blog_title', 'blog_body', 'blog_created_time'])->where(["blog_id" => $blog_id,'active'=>1])->get();
        $result = $builder->countAllResults();//row count
        if ($result === 0) {
            return null;
        } else {
            $blog = $query->getResultArray();//get array
            $category = PostModel::returnCategories(["$blog_id"]);//get categories using blog_ids

            return array($blog, $category);
        }
    }
    #add comment
    public function insertComment($comment){
        $db = db_connect();
        $builder = $db->table('comments');
        $query = $builder->insert($comment);
        return $db->affectedRows();
        $db->close();
    }
    #get all comments
    public function getAllComments($blog_id):array
    {
        $db = db_connect();
        $builder = $db->table('comments');
        $query = $builder->select('*')->join('users','comments.user_rand_id=users.user_rand_id')->where(['post_id'=> $blog_id,'active'=>1])->get();
        return $query->getResultArray();
    }
    #verify user comment
    public function getUserComment($id,$user_rand_id){
        $db = db_connect();
        $builder = $db->table('comments');
        $query = $builder->select(['comment_id'])->where(['comment_id' => $id,'user_rand_id' => $user_rand_id])->get();
        return $db->affectedRows();//row num
        $db->close();
    }
    #delete comment
   public function deleteComment($id){
       $db = db_connect();
       $builder = $db->table('comments');
       $query = $builder->delete(['comment_id' => $id]);
       return $db->affectedRows();//row num
       $db->close();
   }
   #update comment
   public function updateComment($comment,$id,$user_rand_id){
       $db = db_connect();
       $builder = $db->table('comments');
       $query = $builder->update(['comment_text' => $comment],['comment_id'=>$id,'user_rand_id'=>$user_rand_id]);
       return $db->affectedRows();//row num
       $db->close();
   }
    #get posts with author data
    public function getAllPostsData(){
        $db = db_connect();
        $builder = $db->table('blog');
        $query = $builder->select('*')->join('users','blog.user_rand_id=users.user_rand_id')->get();
        return $query->getResultArray();
    }

    public function activatePost($id){
        $db = db_connect();
        $blog = $db->table('blog')->selectCount('blog_id')->where('blog_id',$id)->get()->getRow();
        if($blog){
            $query = $db->table('blog')->update(['active'=>1],['blog_id'=>$id]);
        }
        return $db->affectedRows();//row num
        $db->close();
    }

    public function deactivatePost($id){
        $db = db_connect();
        $blog = $db->table('blog')->selectCount('blog_id')->where('blog_id',$id)->get()->getRow();
        if($blog){
            $query = $db->table('blog')->update(['active'=>0],['blog_id'=>$id]);
        }
        return $db->affectedRows();//row num
        $db->close();
    }
    public function adminDeeletePost($id){
        $db = db_connect();
        $blog = $db->table('blog')->selectCount('blog_id')->where('blog_id',$id)->get()->getRow();
        if($blog){
            $query = $db->table('blog')->delete(['blog_id'=>$id]);
        }
        return $db->affectedRows();//row num
        $db->close();
    }
    public function getAllCommentsData(){
        $db = db_connect();
        $builder = $db->table('comments');
        $query = $builder->select('*')->join('users','comments.user_rand_id=users.user_rand_id')->get();
        return $query->getResultArray();
    }
    public function activateComment($id){
        $db = db_connect();
        $blog = $db->table('comments')->selectCount('comment_id')->where('comment_id',$id)->get()->getRow();
        if($blog){
            $query = $db->table('comments')->update(['active'=>1],['comment_id'=>$id]);
        }
        return $db->affectedRows();//row num
        $db->close();
    }

    public function deactivateComment($id){
        $db = db_connect();
        $blog = $db->table('comments')->selectCount('comment_id')->where('comment_id',$id)->get()->getRow();
        if($blog){
            $query = $db->table('comments')->update(['active'=>0],['comment_id'=>$id]);
        }
        return $db->affectedRows();//row num
        $db->close();
    }
    public function adminDeeleteComment($id){
        $db = db_connect();
        $blog = $db->table('comments')->selectCount('comment_id')->where('comment_id',$id)->get()->getRow();
        if($blog){
            $query = $db->table('comments')->delete(['comment_id'=>$id]);
        }
        return $db->affectedRows();//row num
        $db->close();
    }
}
