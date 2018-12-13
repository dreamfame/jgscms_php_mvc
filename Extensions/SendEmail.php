<?php
	include '../PHPMailer-master/class.phpmailer.php';
	include '../PHPMailer-master/class.smtp.php';
	
	Class SendEmail
	{
		public function emailValidate($sendto_email)
		{
			$mail = new PHPMailer();
			$mail->isSMTP();
			$mail->Host = "smtp.163.com";
			$mail->SMTPAuth = true;
			$mail->Username = "liuliuonlai@163.com";
			$mail->Password = "liuliuon@";
			$mail->From = "liuliuonlai@163.com";
			$mail->Port = 25;
			$mail->FromName = "管理员";
			$mail->CharSet = "UTF-8";
			$mail->Encoding = "base64";
			$mail->addAddress($sendto_email);
			$mail->WordWrap = 50;
			$mail->isHTML(true);
			$mail->Subject = "酒吧后台管理忘记密码";
			$mail->Body="重置密码";
			/*<!DOCTYPE html>
			<html> 
			<body>   
				<span>您好，请点击以下链接重置您的密码：</span>
				<a href='192.168.2.101:8080/minipapba/Extensions/ResetCode.php?email="+$sendto_email+" target='_blank'>重置密码</a>
			</body>   
			</html>";*/       
    		$mail->AltBody ="text/html";
    		$result = $mail->send();
    		if($result)
    		{
    			$row["success"] = true;
    			$row["reason"] = "邮箱发送成功！";
    			$com[] = $row;
    			echo json_encode($com);
    		}
    		else{
    			$row["success"] = false;
    			$row["reason"] = "邮件发送有误，邮件错误信息：".$mail->ErrorInfo;
    			$com[] = $row;
    			echo json_encode($com);
    			exit;
    		}
		}
	}
	
	$email = $_REQUEST['email'];
	$s = new SendEmail();
	$s->emailValidate($email);
?>