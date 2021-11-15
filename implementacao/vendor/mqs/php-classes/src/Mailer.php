<?php
namespace MQS;

use Rain\Tpl;

class Mailer{
    
    private $mail;

    public function __construct($arrayHost, $arrayMsg, $tplName, $data = array()){
        
        // config
        $config = array(
            "tpl_dir"       => $_SERVER['DOCUMENT_ROOT'].'/views/email/',
            "cache_dir"     => $_SERVER['DOCUMENT_ROOT']."/views/views-cache/",
            "auto_escape"   => false,
            "debug"         => false // set to false to improve the speed
        );
        Tpl::configure( $config );
        $tpl = new Tpl;
        
        foreach ($data as $key => $value) {
            $tpl->assign($key, $value);
        }        
        $html = $tpl->draw($tplName, true);
        
        $this->mail = new \PHPMailer;
        $this->mail->IsSMTP();
        $this->mail->SMTPDebug 	  = 0; // enables SMTP debug information (for testing) 1 = errors and messages 2 = messages only
        $this->mail->Debugoutput  = 'html'; // HTML-friendly debug        
        $this->mail->Host 		= $arrayHost['HOST'];
        $this->mail->Port 		= $arrayHost['PORT'];
        $this->mail->Username 	= $arrayHost['USER'];
        $this->mail->Password 	= $arrayHost['PASS'];        
        $this->mail->SMTPAuth 	= true; // enable SMTP authentication
        $this->mail->SetFrom($arrayHost['USER'], $arrayMsg['FROM']);
        
        $this->mail->Subject = $arrayMsg['SuBJECT'];
        $this->mail->AddAddress($arrayMsg['TO'], $arrayMsg['NAME']);        
        $this->mail->MsgHTML($html);  
        $this->mail->AltBody = 'This is a plain-text message body';
    }
    
    public function send(){
        return $this->mail->Send();
    }

    function __destruct(){
        
    }
}

