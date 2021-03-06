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

			.button {
				padding: 0.6em 5em;
				background-color: #fff;
				border: 1px solid #000;
				color: #000;
				font-size: 14pt;

				transition: 
					background-color 0.2s ease,
					color 0.2s ease,
					transform 0.2s ease;
			}

			.button:hover {
				background-color: #000;
				transform: scale(1.05);
				color: #fff;
			}

			.button-wrapper {
				display: flex;
				justify-content: center;
				width: 100%;
				margin: 3em 0;
			}

		</style>
	</head>

	<body>
		<h1 class="text-center">Thanks for your registration!</h1>
		<p class="text-center">
			To complete your registration for <?php echo($templateVars['userEmail']) ?>, please confirm your email, by clicking on this button!
		</p>

		<div class="button-wrapper">
			<a href="<?php echo($templateVars['confirmationLink']) ?>" target="_blank" class="button">
				<p>
					Confirm
				</p>
			</a>
		</div>

		<p class="text-center">
			Best regards,
			your Team at XY
		</p>

	</body>

</html>
