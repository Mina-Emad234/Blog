<?php


namespace App\Models;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use CodeIgniter\Email;
use DateTime;
use DateTimeZone;

class EmailModel extends Model {



    function sendVerificatinEmail($mail,$user_rand_id){
        $db=db_connect();
        $db->table('users')->update(['verify_send_time'=>date('y-m-d H:i:sP',strtotime('next day'))],['user_mail'=>$mail,'user_rand_id'=>$user_rand_id]);
        $rows=$db->affectedRows();
        if($rows > 0) {
            $config = array(
                'protocol' => 'smtp',
                'SMTPHost' => 'smtp.gmail.com.',
                'SMTPPort' => 587,
                'SMTPUser' => 'mina.emad.em1998@gmail.com', // change it to yours
                'SMTPPass' => 'aznmenpjgknqodwm', // change it to yours
                'SMTPCrypto' => 'tls',
                'mailType' => 'html',
                'charset' => 'utf-8',
                'wordWrap' => TRUE
            );

            $email = new Email\Email($config);
            $email->setNewline("\r\n");
            $email->setFrom('mina.emad.em1998@gmail.com', "Admin Team");
            $email->setTo($mail);
            $email->setSubject("Email Verification");
            $email->setMessage("Dear User,\nPlease click on below URL or paste into your browser to verify your Email Address\n\n" . base_url() . "/verify/" . $user_rand_id . "\n" . "\n\nThanks\nAdmin Team");
            $email->send();
            return true;
        }else{
            return false;
        }
    }

    public function sendResetPasswordCode($mail,$user_rand_id,$verifyCode)
    {
        $db=db_connect();
        $db->table('users')->update(['verification_code'=>$verifyCode],['user_mail'=>$mail,'user_rand_id'=>$user_rand_id]);
        $rows=$db->affectedRows();
        if($rows > 0) {
            $config = Array(
            'protocol' => 'smtp',
            'SMTPHost' => 'smtp.gmail.com.',
            'SMTPPort' => 587,
            'SMTPUser' => 'mina.emad.em1998@gmail.com', // change it to yours
            'SMTPPass' => 'aznmenpjgknqodwm', // change it to yours
            'SMTPCrypto'=>'tls',
            'mailType' => 'html',
            'charset' => 'utf-8',
            'wordWrap' => TRUE
        );
        $email = new Email\Email($config);
        $email->setNewline("\r\n");
        $email->setFrom('mina.emad.em1998@gmail.com', "Admin Team");
        $email->setTo($mail);
        $email->setSubject("Password Reset");
        $email->setMessage("Dear User,\n{$verifyCode}\nis your verification code to reset your password");
        $email->send();
            return true;
        }else{
            return false;
        }
    }
}
