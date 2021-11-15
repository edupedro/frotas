<?php

$CORPO_EMAIL_RECUPERA = "
<!doctype html>
<html xmlns='https://www.w3.org/1999/xhtml' xmlns:v='urn:schemas-microsoft-com:vml' xmlns:o='urn:schemas-microsoft-com:office:office'>
    <head>
    	<!-- NAME: INVITATION BY TERRIS KREMER -->
        <!--[if gte mso 15]>
		<xml>
			<o:OfficeDocumentSettings>
			<o:AllowPNG/>
			<o:PixelsPerInch>96</o:PixelsPerInch>
			</o:OfficeDocumentSettings>
		</xml>
		<![endif]-->
		<meta charset='UTF-8'>
        <meta http-equiv='X-UA-Compatible' content='IE=edge'>
        <meta name='viewport' content='width=device-width, initial-scale=1'>
		<title>{ASSUNTO}</title>
        
    <style type='text/css'>
		p{
			margin:10px 0;
			padding:0;
		}
		table{
			border-collapse:collapse;
		}
		h1,h2,h3,h4,h5,h6{
			display:block;
			margin:0;
			padding:0;
		}
		img,a img{
			border:0;
			height:auto;
			outline:none;
			text-decoration:none;
		}
		body,#bodyTable,#bodyCell{
			height:100%;
			margin:0;
			padding:0;
			width:100%;
		}
		.mcnPreviewText{
			display:none !important;
		}
		#outlook a{
			padding:0;
		}
		img{
			-ms-interpolation-mode:bicubic;
		}
		table{
			mso-table-lspace:0pt;
			mso-table-rspace:0pt;
		}
		.ReadMsgBody{
			width:100%;
		}
		.ExternalClass{
			width:100%;
		}
		p,a,li,td,blockquote{
			mso-line-height-rule:exactly;
		}
		a[href^=tel],a[href^=sms]{
			color:inherit;
			cursor:default;
			text-decoration:none;
		}
		p,a,li,td,body,table,blockquote{
			-ms-text-size-adjust:100%;
			-webkit-text-size-adjust:100%;
		}
		.ExternalClass,.ExternalClass p,.ExternalClass td,.ExternalClass div,.ExternalClass span,.ExternalClass font{
			line-height:100%;
		}
		a[x-apple-data-detectors]{
			color:inherit !important;
			text-decoration:none !important;
			font-size:inherit !important;
			font-family:inherit !important;
			font-weight:inherit !important;
			line-height:inherit !important;
		}
		a.mcnButton{
			display:block;
		}
		.mcnImage,.mcnRetinaImage{
			vertical-align:bottom;
		}
		.mcnTextContent{
			word-break:break-word;
		}
		.mcnTextContent img{
			height:auto !important;
		}
		.mcnDividerBlock{
			table-layout:fixed !important;
		}
		.borderBar{
			background-image:url('https://cdn-images.mailchimp.com/template_images/gallery/diagborder.png');
			background-position:top left;
			background-repeat:repeat;
			height:5px !important;
		}
		body,#bodyTable{
			background-color:#DEE0E2;
		}
		#bodyCell{
			border-top:0;
		}
		#templateContainer{
			border:0;
		}
		h1{
			color:#43404D !important;
			font-family:Georgia;
			font-size:32px;
			font-style:normal;
			font-weight:normal;
			line-height:125%;
			letter-spacing:-1px;
			text-align:left;
		}
		h2{
			color:#43404D !important;
			font-family:Georgia;
			font-size:26px;
			font-style:normal;
			font-weight:normal;
			line-height:125%;
			letter-spacing:-.75px;
			text-align:left;
		}
		h3{
			color:#ED5E29 !important;
			font-family:Georgia;
			font-size:18px;
			font-style:italic;
			font-weight:normal;
			line-height:125%;
			letter-spacing:-.5px;
			text-align:left;
		}
		h4{
			color:#43404D !important;
			font-family:Georgia;
			font-size:12px;
			font-style:normal;
			font-weight:normal;
			line-height:125%;
			letter-spacing:normal;
			text-align:left;
		}
		#templatePreheader{
			background-color:#DEE0E2;
			border-top:0;
			border-bottom:0;
		}
		.preheaderContainer .mcnTextContent,.preheaderContainer .mcnTextContent p{
			color:#43404D;
			font-family:Georgia;
			font-size:11px;
			line-height:125%;
			text-align:left;
		}
		.preheaderContainer .mcnTextContent a{
			color:#43404D;
			font-weight:normal;
			text-decoration:underline;
		}
		#templateHeader{
			background-color:#F8F8F8;
			border-top:10px solid #A6BFC9;
			border-bottom:0;
		}
		.headerContainer .mcnTextContent,.headerContainer .mcnTextContent p{
			color:#43404D;
			font-family:Georgia;
			font-size:15px;
			line-height:150%;
			text-align:left;
		}
		.headerContainer .mcnTextContent a{
			color:#ED5E29;
			font-weight:normal;
			text-decoration:underline;
		}
		#templateUpperBody,.borderBar{
			background-color:#F8F8F8;
		}
		#templateUpperBody{
			border-top:0;
			border-bottom:0;
		}
		.upperBodyContainer .mcnTextContent,.upperBodyContainer .mcnTextContent p{
			color:#43404D;
			font-family:Georgia;
			font-size:15px;
			line-height:150%;
			text-align:left;
		}
		.upperBodyContainer .mcnTextContent a{
			color:#ED5E29;
			font-weight:normal;
			text-decoration:underline;
		}
		#templateLowerBody{
			background-color:#F8F8F8;
			border-top:0;
			border-bottom:0;
		}
		.lowerBodyContainer .mcnTextContent,.lowerBodyContainer .mcnTextContent p{
			color:#43404D;
			font-family:Georgia;
			font-size:15px;
			line-height:150%;
			text-align:left;
		}
		.lowerBodyContainer .mcnTextContent a{
			color:#ED5E29;
			font-weight:normal;
			text-decoration:underline;
		}
		#templateColumns{
			background-color:#F8F8F8;
			border-top:0;
			border-bottom:0;
		}
		.leftColumnContainer .mcnTextContent,.leftColumnContainer .mcnTextContent p{
			color:#43404D;
			font-family:Georgia;
			font-size:15px;
			line-height:150%;
			text-align:left;
		}
		.leftColumnContainer .mcnTextContent a{
			color:#ED5E29;
			font-weight:normal;
			text-decoration:underline;
		}
		.rightColumnContainer .mcnTextContent,.rightColumnContainer .mcnTextContent p{
			color:#43404D;
			font-family:Georgia;
			font-size:15px;
			line-height:150%;
			text-align:left;
		}
		.rightColumnContainer .mcnTextContent a{
			color:#ED5E29;
			font-weight:normal;
			text-decoration:underline;
		}
		#templateFooter{
			background-color:#F8F8F8;
			border-top:0;
			border-bottom:10px solid #A6BFC9;
		}
		.footerContainer .mcnTextContent,.footerContainer .mcnTextContent p{
			color:#43404D;
			font-family:Georgia;
			font-size:11px;
			line-height:125%;
			text-align:left;
		}
		.footerContainer .mcnTextContent a{
			color:#43404D;
			font-weight:normal;
			text-decoration:underline;
		}
	@media only screen and (max-width: 480px){
		body,table,td,p,a,li,blockquote{
			-webkit-text-size-adjust:none !important;
		}

}	@media only screen and (max-width: 480px){
		body{
			width:100% !important;
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		#templateContainer,#templatePreheader,#templateHeader,#templateColumns,#templateUpperBody,#templateLowerBody,#templateFooter{
			max-width:600px !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.columnsContainer{
			display:block!important;
			max-width:600px !important;
			width:100%!important;
		}

}	@media only screen and (max-width: 480px){
		.mcnRetinaImage{
			max-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImage{
			height:auto !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCartContainer,.mcnCaptionTopContent,.mcnRecContentContainer,.mcnCaptionBottomContent,.mcnTextContentContainer,.mcnBoxedTextContentContainer,.mcnImageGroupContentContainer,.mcnCaptionLeftTextContentContainer,.mcnCaptionRightTextContentContainer,.mcnCaptionLeftImageContentContainer,.mcnCaptionRightImageContentContainer,.mcnImageCardLeftTextContentContainer,.mcnImageCardRightTextContentContainer,.mcnImageCardLeftImageContentContainer,.mcnImageCardRightImageContentContainer{
			max-width:100% !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer{
			min-width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupContent{
			padding:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnCaptionLeftContentOuter .mcnTextContent,.mcnCaptionRightContentOuter .mcnTextContent{
			padding-top:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardTopImageContent,.mcnCaptionBottomContent:last-child .mcnCaptionBottomImageContent,.mcnCaptionBlockInner .mcnCaptionTopContent:last-child .mcnTextContent{
			padding-top:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardBottomImageContent{
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockInner{
			padding-top:0 !important;
			padding-bottom:0 !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageGroupBlockOuter{
			padding-top:9px !important;
			padding-bottom:9px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnTextContent,.mcnBoxedTextContentColumn{
			padding-right:18px !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnImageCardLeftImageContent,.mcnImageCardRightImageContent{
			padding-right:18px !important;
			padding-bottom:0 !important;
			padding-left:18px !important;
		}

}	@media only screen and (max-width: 480px){
		.mcpreview-image-uploader{
			display:none !important;
			width:100% !important;
		}

}	@media only screen and (max-width: 480px){
		h1{
			font-size:24px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		h2{
			font-size:20px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		h3{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		h4{
			font-size:16px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		.mcnBoxedTextContentContainer .mcnTextContent,.mcnBoxedTextContentContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		#templatePreheader{
			display:block !important;
		}

}	@media only screen and (max-width: 480px){
		.preheaderContainer .mcnTextContent,.preheaderContainer .mcnTextContent p{
			font-size:14px !important;
			line-height:115% !important;
		}

}	@media only screen and (max-width: 480px){
		.headerContainer .mcnTextContent,.headerContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		.bodyContainer .mcnTextContent,.bodyContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		.leftColumnContainer .mcnTextContent,.leftColumnContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		.rightColumnContainer .mcnTextContent,.rightColumnContainer .mcnTextContent p{
			font-size:18px !important;
			line-height:125% !important;
		}

}	@media only screen and (max-width: 480px){
		.footerContainer .mcnTextContent,.footerContainer .mcnTextContent p{
			font-size:14px !important;
			line-height:115% !important;
		}

}</style></head>
    <body leftmargin='0' marginwidth='0' topmargin='0' marginheight='0' offset='0' style='height: 100%;margin: 0;padding: 0;width: 100%;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #DEE0E2;'>
        <!---->
        <center>
            <table align='center' border='0' cellpadding='0' cellspacing='0' height='100%' width='100%' id='bodyTable' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height: 100%;margin: 0;padding: 0;width: 100%;background-color: #DEE0E2;'>
                <tr>
                    <td align='center' valign='top' id='bodyCell' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;height: 100%;margin: 0;padding: 0;width: 100%;border-top: 0;'>
                        <!-- BEGIN TEMPLATE // -->
                        <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateContainer' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;border: 0;'>
                            <tr>
                                <td align='center' valign='top' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                    <!-- BEGIN PREHEADER // -->
                                    <table border='0' cellpadding='0' cellspacing='0' width='600' id='templatePreheader' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #DEE0E2;border-top: 0;border-bottom: 0;'>
                                        <tr>
                                        	<td valign='top' class='preheaderContainer' style='padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><table class='mcnTextBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnTextBlockOuter'>
        <tr>
            <td class='mcnTextBlockInner' style='padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' valign='top'>
              	<!--[if mso]>
				<table align='left' border='0' cellspacing='0' cellpadding='0' width='100%' style='width:100%;'>
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign='top' width='390' style='width:390px;'>
				<![endif]-->
                <table style='max-width: 390px;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnTextContentContainer' width='100%' cellspacing='0' cellpadding='0' border='0' align='left'>
                    <tbody><tr>
                        
                        <td class='mcnTextContent' style='padding-top: 0;padding-left: 18px;padding-bottom: 9px;padding-right: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #43404D;font-family: Georgia;font-size: 11px;line-height: 125%;text-align: left;' valign='top'>
                        
                            Recuperação de senha
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				<td valign='top' width='210' style='width:210px;'>
				<![endif]-->
                <table style='max-width: 210px;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnTextContentContainer' width='100%' cellspacing='0' cellpadding='0' border='0' align='left'>
                    <tbody><tr>
                        
                        <td class='mcnTextContent' style='padding-top: 0;padding-left: 18px;padding-bottom: 9px;padding-right: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #43404D;font-family: Georgia;font-size: 11px;line-height: 125%;text-align: left;' valign='top'>
                        
                            <a href='#' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;color: #43404D;font-weight: normal;text-decoration: underline;'>&nbsp;</a>
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
                                        </tr>
                                    </table>
                                    <!-- // END PREHEADER -->
                                </td>
                            </tr>
                            <tr>
                                <td align='center' valign='top' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                    <!-- BEGIN HEADER // -->
                                    <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateHeader' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #F8F8F8;border-top: 10px solid #A6BFC9;border-bottom: 0;'>
                                        <tr>
                                            <td valign='top' class='headerContainer' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><table class='mcnTextBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnTextBlockOuter'>
        <tr>
            <td class='mcnTextBlockInner' style='padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' valign='top'>
              	<!--[if mso]>
				<table align='left' border='0' cellspacing='0' cellpadding='0' width='100%' style='width:100%;'>
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign='top' width='600' style='width:600px;'>
				<![endif]-->
                <table style='max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnTextContentContainer' width='100%' cellspacing='0' cellpadding='0' border='0' align='left'>
                    <tbody><tr>
                        
                        <td class='mcnTextContent' style='padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #43404D;font-family: Georgia;font-size: 15px;line-height: 150%;text-align: left;' valign='top'>
                        
                            <h1 style='display: block;margin: 0;padding: 0;font-family: Georgia;font-size: 32px;font-style: normal;font-weight: normal;line-height: 125%;letter-spacing: -1px;text-align: left;color: #43404D !important;'>Bem-vindo(a)</h1>

<h3 style='display: block;margin: 0;padding: 0;font-family: Georgia;font-size: 18px;font-style: italic;font-weight: normal;line-height: 125%;letter-spacing: -.5px;text-align: left;color: #ED5E29 !important;'><span style='color:#ff0000'>{NOME}</span></h3>

                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
                                        </tr>
                                    </table>
                                    <!-- // END HEADER -->
                                </td>
                            </tr>
                            <tr>
                                <td class='borderBar' background='https://cdn-images.mailchimp.com/template_images/gallery/diagborder.png' style='background:url('https://cdn-images.mailchimp.com/template_images/gallery/diagborder.png') repeat top left;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-image: url(https://cdn-images.mailchimp.com/template_images/gallery/diagborder.png);background-position: top left;background-repeat: repeat;background-color: #F8F8F8;height: 5px !important;'>
                                    <img src='https://cdn-images.mailchimp.com/template_images/gallery/blank.gif' height='5' width='5' style='display: block;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;'>
                                </td>
                            </tr>
                            <tr>
                                <td align='center' valign='top' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                    <!-- BEGIN UPPER BODY // -->
                                    <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateUpperBody' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #F8F8F8;border-top: 0;border-bottom: 0;'>
                                        <tr>
                                            <td valign='top' class='upperBodyContainer' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><table class='mcnImageBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnImageBlockOuter'>
            <tr>
                <td style='padding: 0px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnImageBlockInner' valign='top'>
                    <table class='mcnImageContentContainer' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0' align='left'>
                        <tbody><tr>
                            <td class='mcnImageContent' style='padding-right: 0px;padding-left: 0px;padding-top: 0;padding-bottom: 0;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' valign='top'>
                                
                                    
                                        <img alt='' src='https://gallery.mailchimp.com/8a44c4f8a121e66f3c842fdfd/images/a8057c3d-0629-4220-985e-d01e9a8dc26a.jpg' style='max-width: 600px;padding-bottom: 0;display: inline !important;vertical-align: bottom;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;' class='mcnImage' width='600' align='left'>
                                    
                                
                            </td>
                        </tr>
                    </tbody></table>
                </td>
            </tr>
    </tbody>
</table></td>
                                        </tr>
                                    </table>
                                    <!-- // END UPPER BODY -->
                                </td>
                            </tr>
                            <tr>
                                <td class='borderBar' background='https://cdn-images.mailchimp.com/template_images/gallery/diagborder.png' style='background:url('https://cdn-images.mailchimp.com/template_images/gallery/diagborder.png') repeat top left;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-image: url(https://cdn-images.mailchimp.com/template_images/gallery/diagborder.png);background-position: top left;background-repeat: repeat;background-color: #F8F8F8;height: 5px !important;'>
                                    <img src='https://cdn-images.mailchimp.com/template_images/gallery/blank.gif' height='5' width='5' style='display: block;border: 0;height: auto;outline: none;text-decoration: none;-ms-interpolation-mode: bicubic;'>
                                </td>
                            </tr>
                            <tr>
                                <td align='center' valign='top' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                    <!-- BEGIN COLUMNS // -->
                                    <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateColumns' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #F8F8F8;border-top: 0;border-bottom: 0;'>
                                        <tr>
                                            <td align='left' valign='top' class='columnsContainer' width='50%' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                <table border='0' cellpadding='0' cellspacing='0' width='100%' class='templateColumn' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                    <tr>
                                                        <td valign='top' class='leftColumnContainer' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><table class='mcnTextBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnTextBlockOuter'>
        <tr>
            <td class='mcnTextBlockInner' style='padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' valign='top'>
              	<!--[if mso]>
				<table align='left' border='0' cellspacing='0' cellpadding='0' width='100%' style='width:100%;'>
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign='top' width='300' style='width:300px;'>
				<![endif]-->
                <table style='max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnTextContentContainer' width='100%' cellspacing='0' cellpadding='0' border='0' align='left'>
                    <tbody><tr>
                        
                        <td class='mcnTextContent' style='padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #43404D;font-family: Georgia;font-size: 15px;line-height: 150%;text-align: left;' valign='top'>
                        
                            <h4 style='display: block;margin: 0;padding: 0;font-family: Georgia;font-size: 12px;font-style: normal;font-weight: normal;line-height: 125%;letter-spacing: normal;text-align: left;color: #43404D !important;'>&nbsp;</h4>

<h2 style='display: block;margin: 0;padding: 0;font-family: Georgia;font-size: 26px;font-style: normal;font-weight: normal;line-height: 125%;letter-spacing: -.75px;text-align: left;color: #43404D !important;'>Senha gerada com sucesso!</h2>

                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td align='left' valign='top' class='columnsContainer' width='50%' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                <table border='0' cellpadding='0' cellspacing='0' width='100%' class='templateColumn' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                                    <tr>
                                                        <td valign='top' class='rightColumnContainer' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><table class='mcnTextBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnTextBlockOuter'>
        <tr>
            <td class='mcnTextBlockInner' style='padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' valign='top'>
              	<!--[if mso]>
				<table align='left' border='0' cellspacing='0' cellpadding='0' width='100%' style='width:100%;'>
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign='top' width='300' style='width:300px;'>
				<![endif]-->
                <table style='max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnTextContentContainer' width='100%' cellspacing='0' cellpadding='0' border='0' align='left'>
                    <tbody><tr>
                        
                        <td class='mcnTextContent' style='padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #43404D;font-family: Georgia;font-size: 15px;line-height: 150%;text-align: left;' valign='top'>
                        
                            
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table><table class='mcnButtonBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnButtonBlockOuter'>
        <tr>
            <td style='padding-top: 0;padding-right: 18px;padding-bottom: 18px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnButtonBlockInner' valign='top' align='center'>
                <table class='mcnButtonContentContainer' style='border-collapse: separate !important;border: 2px solid #707070;border-radius: 3px;background-color: #FF0000;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
                    <tbody>
                        <tr>
                            <td class='mcnButtonContent' style='font-family: Arial;font-size: 20px;padding: 15px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' valign='middle' align='center'>
                                <a class='mcnButton ' title='Acessar' href='https://www.habiliter.com.br/admin' target='_self' style='font-weight: bold;letter-spacing: normal;line-height: 100%;text-align: center;text-decoration: none;color: #FFFFFF;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;display: block;'>Acessar</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <!-- // END COLUMNS -->
                                </td>
                            </tr>
                            <tr>
                                <td align='center' valign='top' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                    <!-- BEGIN LOWER BODY // -->
                                    <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateLowerBody' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #F8F8F8;border-top: 0;border-bottom: 0;'>
                                        <tr>
                                            <td valign='top' class='lowerBodyContainer' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><table class='mcnTextBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnTextBlockOuter'>
        <tr>
            <td class='mcnTextBlockInner' style='padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' valign='top'>
              	<!--[if mso]>
				<table align='left' border='0' cellspacing='0' cellpadding='0' width='100%' style='width:100%;'>
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign='top' width='600' style='width:600px;'>
				<![endif]-->
                <table style='max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnTextContentContainer' width='100%' cellspacing='0' cellpadding='0' border='0' align='left'>
                    <tbody><tr>
                        
                        <td class='mcnTextContent' style='padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #43404D;font-family: Georgia;font-size: 15px;line-height: 150%;text-align: left;' valign='top'>
                        
                            Seus dados para acesso são:
<ul>
	<li style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>E-mail: {LOGIN}</li>
	<li style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>Senha: <strong>{SENHA}</strong></li>
</ul>
Após realizar o seu primeiro acesso, para sua segurança, recomendamos que troque sua senha. Utilize a opção 'Perfil' do menu superior.
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table></td>
                                        </tr>
                                    </table>
                                    <!-- // END LOWER BODY -->
                                </td>
                            </tr>
                            <tr>
                                <td align='center' valign='top' style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                                    <!-- BEGIN FOOTER // -->
                                    <table border='0' cellpadding='0' cellspacing='0' width='600' id='templateFooter' style='border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;background-color: #F8F8F8;border-top: 0;border-bottom: 10px solid #A6BFC9;'>
                                        <tr>
                                            <td valign='top' class='footerContainer' style='padding-bottom: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'><table class='mcnTextBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnTextBlockOuter'>
        <tr>
            <td class='mcnTextBlockInner' style='padding-top: 9px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' valign='top'>
              	<!--[if mso]>
				<table align='left' border='0' cellspacing='0' cellpadding='0' width='100%' style='width:100%;'>
				<tr>
				<![endif]-->
			    
				<!--[if mso]>
				<td valign='top' width='600' style='width:600px;'>
				<![endif]-->
                <table style='max-width: 100%;min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' class='mcnTextContentContainer' width='100%' cellspacing='0' cellpadding='0' border='0' align='left'>
                    <tbody><tr>
                        
                        <td class='mcnTextContent' style='padding-top: 0;padding-right: 18px;padding-bottom: 9px;padding-left: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;word-break: break-word;color: #43404D;font-family: Georgia;font-size: 11px;line-height: 125%;text-align: left;' valign='top'>
                        
                            <em>Copyright © 2020 Habiliter, Todos os direitos reservados.</em><br>
<br>
<strong>Dúvidas</strong><br>
contato@habiliter.com.br<br>
&nbsp;
                        </td>
                    </tr>
                </tbody></table>
				<!--[if mso]>
				</td>
				<![endif]-->
                
				<!--[if mso]>
				</tr>
				</table>
				<![endif]-->
            </td>
        </tr>
    </tbody>
</table><table class='mcnDividerBlock' style='min-width: 100%;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;table-layout: fixed !important;' width='100%' cellspacing='0' cellpadding='0' border='0'>
    <tbody class='mcnDividerBlockOuter'>
        <tr>
            <td class='mcnDividerBlockInner' style='min-width: 100%;padding: 18px;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                <table class='mcnDividerContent' style='min-width: 100%;border-top: 1px solid #CCCCCC;border-collapse: collapse;mso-table-lspace: 0pt;mso-table-rspace: 0pt;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;' width='100%' cellspacing='0' cellpadding='0' border='0'>
                    <tbody><tr>
                        <td style='mso-line-height-rule: exactly;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;'>
                            <span></span>
                        </td>
                    </tr>
                </tbody></table>
<!--            
                <td class='mcnDividerBlockInner' style='padding: 18px;'>
                <hr class='mcnDividerContent' style='border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;' />
-->
            </td>
        </tr>
    </tbody>
</table></td>
                                        </tr>
                                    </table>
                                    <!-- // END FOOTER -->
                                </td>
                            </tr>
                        </table>
                        <!-- // END TEMPLATE -->
                    </td>
                </tr>
            </table>
        </center>
	</body>
</html>
";


?>