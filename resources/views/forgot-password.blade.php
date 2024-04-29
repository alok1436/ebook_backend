<!DOCTYPE html>
<html>
	<head>
	<title>Coach Web App OTP Verification</title>
	</head>
<body>
	<table style="border: 0; width: 600px; border-spacing: 0;" width="600">
	    <tr><td>
  		<h2>Dear {{ $user->name }},</h2>
		<p><b>Your verification code:</b> {{ $user->otp }}</p>
		<p>This code is unique to you and won't work for anyone else.</p>
		<p>Email: {websiteName} for any questions or assistance.</p>
		<p></p>
		<p>Kind regards<br>
		Coach Web App</p>
		<!-- <p><img src="{{ asset('assets/images/gbi-mail-footer-new.jpg') }}" alt="" style="width:600px !important;" width="600"></p> -->
		</td></tr>
	</table>
</body>
</html>
