<html>
	<head>
		<style>
			* {
				font-family: Helvetica, Arial, Sans-Serif;
				text-decoration: none !important;
			}

			.text-center {
				text-align: center;
			}

			.password-text {
				color: #47a;
			}

		</style>
	</head>

	<body>
		<h1 class="text-center">Your password has been reset!</h1>
		<p class="text-center">
			Your new password is: <span class="password-text"><?php echo($templateVars['newPassword']) ?></span>.
			<br>Please change it as soon as possible.
		</p>

		<p class="text-center">
			Best regards,
			your Team at XY
		</p>

	</body>

</html>
