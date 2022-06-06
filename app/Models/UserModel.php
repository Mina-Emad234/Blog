<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    public function __construct()
    {
        $helpers = ['cookie','date'];
        helper($helpers);
    }
    #get username from table if exists
    public function getUniqueUsername($user_name)
    {
        $db = db_connect();
        $builder = $db->table('users');
        $builder->select('user_name')->where(['user_name' => $user_name]);
        $query = $builder->get();
        return $query->getRowArray();
        $db->close();
    }
    #insert user registration data
    public function insertSignupData(array $data)
    {
        $db = db_connect();
        $builder = $db->table('users');
        $builder->insert($data);
        $db->close();
        return 1;
    }
    #data verification
    public function verify(array $auth_data)
    {
        $user_name = $auth_data['user_name'];
        $user_pass = $auth_data['user_pass'];
        $verify_bool = UserModel::retriveHash($user_name, $user_pass);
        return $verify_bool;
    }
    #retrieve and verifying password
    public static function retriveHash(string $username, string $userpass)
    {
        $db = db_connect();//open connection
        $builder = $db->table('users');//select table
        $query = $builder->select('user_pass')->where(['user_name' => $username])->limit(1)->get();//select data
        $row_count = $builder->countAllResults();//get number of selecting rows
        if ($row_count === 0) {//check if num of rows == 0
            return 0; //user does not exsist
        } else {//if num of rows == 0
            $hash = $query->getRowArray();//get row in array
            if (password_verify($userpass, $hash['user_pass'])) {//verifying password
                return 1; //user is validated
            } else {
                return 0; // invalid password
            }
        }
        $db->close();//close connection
    }
    #get user_rand_id from users table
    public function getUserId(string $usernm)
    {
        $db = db_connect();
        $builder = $db->table('users');
        $query = $builder->select(['user_rand_id','admin','user_mail','email_verified_at','verify_send_time'])->where(['user_name' => $usernm])->limit(1)->get();//get user_rand_id
        return $query->getRowArray();//row in array
        $db->close();
    }

    public function getUserName(string $user_rand_id)
    {
        $db = db_connect();
        $builder = $db->table('users');
        $query = $builder->select('user_name')->where(['user_rand_id' => $user_rand_id])->limit(1)->get();
        return $query->getRowArray();
        $db->close();
    }
    #get users data using rand_id
    public function getUserDetails($user_rand_id):array
    {
        $db = db_connect();
        $builder = $db->table('users');
        $query = $builder->select('user_name,user_fname, user_lname, user_mail,image,user_about')->where('user_rand_id' , $user_rand_id)->get();
        return $query->getRowArray();//single row as an array.
        $db->close();
    }
    #update user data
    public function setUserData(string $user_rand_id, array $user_data)
    {
        $db = db_connect();
        $builder = $db->table('users');
        $builder->update($user_data, ['user_rand_id' => $user_rand_id]);
        return $db->affectedRows();
        $db->close();
    }
    #password verification using rand_id
    public function verify_old_pass(string $user_rand_id, string $pass)
    {
        $db = db_connect();
        $builder = $db->table('users');
        $query = $builder->select('user_pass')->where(['user_rand_id' => $user_rand_id])->limit(1)->get();
        $result = $query->getRowArray();//get row as array
        $hash = $result['user_pass'];
        if (password_verify($pass, $hash)) {//if password verified
            return 1;
        } else {
            return 0;
        }
    }
    #update password using rand_id
    public function updatePassword(string $user_rand_id, string $hashed_pass)
    {
        $db = db_connect();
        $builder = $db->table('users');
        $query = $builder->update(['user_pass' => $hashed_pass], ['user_rand_id' => $user_rand_id]);//update password
        return $db->affectedRows();//get num of row affected
        $db->close();
    }
    #get category data[[id,name].[id,name]]
    public function getCategoryDetails()
    {
        $db = db_connect();
        $builder = $db->table('category');
        return $builder->select(['*'])->join('users','cat_created_by = user_rand_id')->get()->getResultarray();//get cat id & name
    }
    #insert new category if not exists
    public function setCategory(array $category)
    {
        $db = db_connect();
        $builder = $db->table('category');//category table
        $lower = strtolower($category['cat_name']);//lowercase cat_name
        $query = $builder->selectCount('cat_name')->where(['cat_name ' => $lower])->get();//get count of cat name if exists on table
        $res = $query->getRowArray();//get num of rows as array
        $results = (int)$res['cat_name'];//cat_name count
        if ($results > 0) {//if category exists
            return 0;
        } else {//if category not exists
            $builder->insert($category);//insert
            return 1;
        }
        $db->close();
    }

    public function verifyEmailAddress($user_rand_id){
        $db = db_connect();
        $builder = $db->table('users');
        $query = $builder->update(['email_verified_at'=> now()] , ['user_rand_id' => $user_rand_id]);
        return $db->affectedRows();//get num of row affected
        $db->close();
    }

    public function getEmail($email){
        $db = db_connect();
        $builder = $db->table('users');
        $query = $builder->select('user_mail,user_rand_id')->where('user_mail' , $email)->get();
        if($builder->countAllResults() === 0){
            return false;
        }else{
            return $query->getRowArray();//single row as an array.
        }
    }
    public function getCode($user_rand_id){
        $db = db_connect();
        $builder = $db->table('users');
        $query = $builder->select('verification_code')->where('user_rand_id' , $user_rand_id)->get();
        if($builder->countAllResults() === 0){
            return false;
        }else{
            return $query->getRowArray();//single row as an array.
        }
    }

    public function deleteCategory($id)
    {
        $db = db_connect();
        $rows = $db->table('category')->selectCount('*','catCount')->where('cat_id',$id)->get()->getRowArray();
        if($rows){
            $db->table('category')->delete(['cat_id'=>$id]);
        }
        return $db->affectedRows();//row num
    }

    public function updateCategory(array $data)
    {
        $db = db_connect();
        $rows = $db->table('category')->selectCount('*','catCount')->where('cat_id',$data['cat_id'])->get()->getRowArray();
        if($rows){
            $db->table('category')->update($data,['cat_id'=> $data['cat_id']]);
        }
        return $db->affectedRows();//row num
    }
}
