<?php
namespace MQS\lib;

use PHPMailer;

/**
 * Classe que contém funções utilitárias de uso geral
 * 
 * @author Carlos Feitoza
 */
class Utilitarios {
    
        const EMAIL_HOSTNAME = "mail.grupomqs.com.br";
        const EMAIL_USERNAME = "contato@grupomqs.com.br";
        const EMAIL_PASSWORD = "lsQ44x5iDXTI";
        const EMAIL_PORT = 25;
        const EMAIL_FROM = "contato@solidarios.com.br";
        const EMAIL_FROM_NAME = "=?UTF-8?B?RXF1aXBlIFNvbGlkw6FyaW9z?=";// "Equipe Solidários";
        
        function __construct(){
            
        }
        
        function __destruct(){
            
        }
        
        public function sendMail($aEmailTo,$aEmailToName,$aSubject,$aBody) {
            $result = false;
            
            $mail = new PHPMailer(true);
            
            try {
                // Server settings
                // $mail->SMTPDebug = 2;                                        // Enable verbose debug output
                $mail->isSMTP();                                                // Set mailer to use SMTP
                $mail->Host = Self::EMAIL_HOSTNAME;                             // Specify main and backup SMTP servers
                $mail->SMTPAuth = true;                                         // Enable SMTP authentication
                $mail->Username = Self::EMAIL_USERNAME;                         // SMTP username
                $mail->Password = Self::EMAIL_PASSWORD;                         // SMTP password
                // $mail->SMTPSecure = "tls";                                   // Enable TLS encryption, `ssl` also accepted
                $mail->Port = 25;                                               // TCP port to connect to

                //Recipients
                $mail->setFrom(Self::EMAIL_FROM, Self::EMAIL_FROM_NAME);
                $mail->addAddress($aEmailTo, $aEmailToName);                    // Add a recipient
                // $mail->addAddress('ellen@example.com');                      // Name is optional
                // $mail->addReplyTo('info@example.com', 'Information');
                // $mail->addCC('cc@example.com');
                // $mail->addBCC('bcc@example.com');
            
                // Attachments
                // $mail->addAttachment('/var/tmp/file.tar.gz');                // Add attachments
                // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');           // Optional name

                // Content
                $mail->isHTML(true);                                            // Set email format to HTML
                $mail->Subject = "=?UTF-8?B?" . base64_encode($aSubject) . "?=";
                $mail->Body = $aBody;
                // $mail->AltBody = 'Este é o texto plano do corpo do e-mail, caso o bocó não não tenha um leitor de e-mail que aceite html';

                $mail->send();
                $result = true;
            } catch (\Exception $e) {
                // Não faz nada, porque por padrão $result = false e isso basta.
                // No funturo, talvez seja interessante guardar um logo 
                return false;
            } finally {
                unset($mail);
            }
            
            return $result;
        }

        public function templateName($aFileName) {
            return basename($aFileName,".php") . ".tpl";
        }
}//class