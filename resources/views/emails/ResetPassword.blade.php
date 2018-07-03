<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Engrafi</title>
</head>
<body style="margin:0;">
    <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
        <tbody>
            <tr>
                <td align="center" valign="top" >
                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                            <tr>
                                <td align="center" bgcolor="#384148" valign="top" style="background-color:#384148;padding-right:30px;padding-left:30px">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:400px">
                                        <tbody>
                                            <tr>
                                                <td align="center" valign="top" style="padding-top:40px;padding-bottom:40px">
                                                    <img alt="Engrafi" src="http://engrafi.tk/icon/engrafi_circle.png" height="120" style="color:#ffffff;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:12px;font-weight:400;letter-spacing:-1px;padding:0;margin:0;text-align:center">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#52BAD5" valign="top" style="background-color:#384148;padding-right:30px;padding-left:30px">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="center" valign="top">
                                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px">
                                                        <tbody>
                                                            <tr>
                                                                <td align="center" valign="top">
                                                                    <table align="center" bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#ffffff;border-collapse:separate;border-top-left-radius:4px;border-top-right-radius:4px">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="center" valign="top" width="100%" style="padding-top:40px;padding-bottom:0">&nbsp;</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="center" valign="top">
                                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:700px">
                                                        <tbody>
                                                            <tr>
                                                                <td align="right" valign="top" width="30">
                                                                    <img src="https://ci4.googleusercontent.com/proxy/L5tUh1OMjnXZ7pML9wTqD_Lpb0aqOQtEIxQdlBZbzz44clEn-1MlZdakDu17kOGBQjhw0qROA1em_M8TlKw1efwKeQyVgVfiWnCSel6n2HkLGJvN_NbbMg=s0-d-e1-ft#http://cdn-images.mailchimp.com/template_images/tr_email/arrow.jpg" width="30" style="display:block" class="CToWUd">
                                                                </td>
                                                                <td valign="top" width="100%" style="padding-right:70px;padding-left:40px;border-left:0.5px solid #f2f2f2;">
                                                                    <table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="left" valign="top" style="padding-bottom:40px">
                                                                                    <h1 style="color:#737373;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:30px;font-style:normal;font-weight:600;line-height:42px;letter-spacing:normal;margin:0;padding:0;text-align:center">Forgot your password? <br> Let's get you a new one.</h1>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td style="padding-bottom:20px" valign="top">
                                                                                    <p style="color:#606060;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:24px;padding-top:0;margin-top:0;text-align:left">We've got a request to change the password for the account linked to this email.</p>
                                                                                    <p style="color:#606060;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:24px;padding-top:0;margin-top:0;text-align:left">If you don't want to reset your password, you can ignore this email.</p>
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td align="center" valign="top">
                                                                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                                                        <tbody>

                                                                                            <tr>
                                                                                                <td align="center" valign="middle">
                                                                                                    <a href="{{ env('APP_FRONTEND').'/password/reset/'. $token }}" style="background-color:#00B0FF;border-collapse:separate;margin-bottom:50px;padding:20px 40px;background-color:#00B0FF;border-radius:3px;color:#ffffff;display:inline-block;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:16px;font-weight:600;letter-spacing:.3px;text-decoration:none" target="_blank" data-saferedirecturl="{{ env('APP_FRONTEND').'/password/reset/'. $token }}">Reset Your Password</a>
                                                                                                </td>
                                                                                            </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top" style="padding-right:30px;padding-left:30px">
                                    <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px">
                                        <tbody>
                                            <tr>
                                                <td valign="top" style="border-top:2px solid #f2f2f2;color:#b7b7b7;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:12px;font-weight:400;line-height:24px;padding-top:40px;padding-bottom:20px;text-align:center">
                                                    <p style="color:#b7b7b7;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-size:12px;font-weight:400;line-height:24px;padding:0;margin:0;text-align:center">© 2018 Engrafi, All Rights Reserved.<br></p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>