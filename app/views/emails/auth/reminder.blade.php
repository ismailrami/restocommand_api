<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>modification de mot de passe</h2>

		<div>
			Pour modifier votre mot de passe, compléter ce formulaire: http://localhost/restocommand-bo2/#/reset/{{$token}}
			 .<br/>
			Se lien sera expiré dans  {{ Config::get('auth.reminder.expire', 60) }} minutes.
		</div>
	</body>
</html>
