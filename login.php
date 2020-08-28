<?php
// See all errors and warnings
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

$server = "localhost";
$username = "root";
$password = "";
$database = "dbUser";
$mysqli = mysqli_connect($server, $username, $password, $database);

$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
?>

<!DOCTYPE html>
<html>


<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Devashan Naicker">
	<!-- Replace Name Surname with your name and surname -->
</head>


<body>
	<div class="container">
		<?php
		if ($email && $pass) {
			$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
			$res = $mysqli->query($query);
			if ($row = mysqli_fetch_array($res)) {
				echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";

				echo 	"<form method='POST' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple='multiple'/><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
									<input type='hidden' class='form-control' name='loginEmail' id='loginEmail' value='"  . $email .  "' /><br/>
									<input type='hidden' class='form-control' name='loginPass' id='loginPass' value='"  . $pass .  "' /><br/>
								</div>
						  	</form>";
			} else {
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
			}
		} else {
			echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
		}
		?>

		<?php


		if (isset($_POST["loginEmail"]) && isset($_POST["loginPass"]) && isset($_FILES["picToUpload"])) {
			// variable initialization
			$userID = $row["user_id"];
			$emailAddress = $_POST["loginEmail"];
			$password = $_POST["loginPass"];
			$fileToUpload = $_FILES["picToUpload"];
			$total = count($_FILES['picToUpload']['name']);
			for( $i=0 ; $i < $total ; $i++ ) {
				$fileType = $fileToUpload["type"][$i];
				$fileName = $fileToUpload['name'][$i];
				$tempFile = $fileToUpload['tmp_name'][$i];
				$fileSize = $fileToUpload['size'][$i];
				$allowedExtensions = array('image/jpeg', 'image/jpg');
				$targetDirectory = "gallery/";
				$finalFilePath = $targetDirectory . $fileName;
				if (!$fileName) {
					$errors[] = "Error: No file uploaded, please choose a file to upload.";
				}
				if ((in_array($fileType,  $allowedExtensions)) === false) {
					$errors[] = "Error: Extension not allowed, please choose a file with a .jpg or .jpeg extension.";
				}
				if ($file_size > 1000000) {
					$errors[]  =  'Error: File is too big, the file size must be less than 1 MB';
				}
				if (empty($errors)  ==  true) {
					move_uploaded_file($tempFile,  $finalFilePath);
					$query = "INSERT INTO tbgallery(user_id,filename) VALUES ('$userID','$fileName')";
					$res = $mysqli->query($query);
					if (!$res) {
						echo '<div class="alert alert-danger mt-3" role="alert">Error: Image could not be added - '  .  $mysqli->error  .  ' </div>';
					}
				} else {
					for ($i  =  0; $i < sizeof($errors); $i++) {
						echo '<div class="alert alert-danger mt-3" role="alert">
									'  . $errors[$i] .
							'</div>';
					}
				}
			}
		}

		?>

		<h2> Image Gallery </h2>

		<div class="row imageGallery" id="row1">
			<?php
			$query = "SELECT user_id FROM tbusers WHERE email = '$email' AND password = '$pass'";
			$user = $mysqli->query($query);
			if ($row = mysqli_fetch_array($user)) {
				$userID = $row['user_id'];
				$readQuery = "SELECT filename FROM tbgallery WHERE user_id = '$userID'";
				$result = $mysqli->query($readQuery);

				if (mysqli_num_rows($result) > 0) {
					while ($row = mysqli_fetch_array($result)) {
						echo "<div class='col-3'";
						echo "style='background-image: url(gallery/";
						echo  $row['filename'];
						echo  "'>";
						echo "</div>";
					}
				}
			}
			?>

		</div>
	</div>
</body>


</html>