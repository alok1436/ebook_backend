<!DOCTYPE html>
<html>
	<head>
	<title>Reset Password</title>
	</head>
<body>
	<table style="border: 0; width: 600px; border-spacing: 0;" width="600">
	    <tr><td>
  		<h2>Dear {{ $user->name }},</h2>
		<p><b>Your reset password link:</b> <a href="{{$url}}">Click Here to Reset password</a></p>
		<p>Email: {websiteName} for any questions or assistance.</p>
		<p>Kind regards<br>
		Coach Web App</p>
		<!-- <p><img src="{{ asset('assets/images/gbi-mail-footer-new.jpg') }}" alt="" style="width:600px !important;" width="600"></p> -->
		</td></tr>
	</table>
</body>
</html>
