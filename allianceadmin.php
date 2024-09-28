<?php
include("includes/basicprivatephp.php");
include("includes/bbcode.php");


$sql = 'SELECT idalliance FROM autre WHERE login=\'' . $_SESSION['login'] . '\'';
$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
$idalliance = mysqli_fetch_array($ex);

$sql1 = 'SELECT * FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'';
$ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
$chef = mysqli_fetch_array($ex1);

$ex = mysqli_query($base, 'SELECT * FROM grades WHERE login=\'' . $_SESSION['login'] . '\' AND idalliance=\'' . $chef['id'] . '\'');
$grade = mysqli_fetch_array($ex);
$existeGrade = mysqli_num_rows($ex);

$ex = query('SELECT login FROM autre WHERE idalliance=\'' . $idalliance['idalliance'] . '\'');
$nombreJoueurs = mysqli_num_rows($ex);

if ($chef['chef'] != $_SESSION['login'] and $existeGrade < 1) {
?>
	<script LANGUAGE="JavaScript">
		window.location = "allianceprive.php";
	</script>
	<?php
	exit();
}

if ($_SESSION['login'] != $chef['chef']) {
	list($inviter, $guerre, $pacte, $bannir, $description) = explode('.', $grade['grade']);
	if ($inviter == 1) $inviter = true;
	if ($guerre == 1) $guerre = true;
	if ($bannir == 1) $bannir = true;
	if ($pacte == 1) $pacte = true;
	if ($description == 1) $description = true;
	$gradeChef = false;
} else {
	$inviter = true;
	$guerre = true;
	$bannir = true;
	$pacte = true;
	$description = true;
	$gradeChef = true;
}

if ($gradeChef) {
	if (isset($_POST['supprimeralliance1'])) {
		supprimerAlliance($idalliance['idalliance']);
	?>
		<script LANGUAGE="JavaScript">
			window.location = "allianceprive.php";
		</script>
		<?php
		exit();
	}

	if (isset($_POST['changernom'])) {
		if (!empty($_POST['changernom'])) {
			$_POST['changernom'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['changernom'])));
			$sql2 = 'SELECT nom FROM alliances WHERE nom=\'' . $_POST['changernom'] . '\' ';
			$ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
			$nballiance = mysqli_num_rows($ex2);

			if ($nballiance == 0) {
				$sql3 = 'UPDATE alliances SET nom=\'' . $_POST['changernom'] . '\' WHERE id=\'' . $idalliance['idalliance'] . '\'';
				$ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysqli_error($base));

				$information = 'Le nom de l\'équipe a bien été changé et est devenu ' . $_POST['changernom'] . '.';
			} else {
				$erreur = "Une équipe avec ce nom existe déjà.";
			}
		} else {
			$erreur = "Le nom de votre équipe doit au moins comporter un caractère.";
		}
	}

	if (isset($_POST['nomgrade']) and isset($_POST['personnegrade'])) {
		$_POST['nomgrade'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['nomgrade'])));
		$_POST['personnegrade'] = ucfirst(mysqli_real_escape_string($base, stripslashes(antihtml($_POST['personnegrade']))));
		if (!empty($_POST['nomgrade']) and !empty($_POST['personnegrade'])) {
			$ex = mysqli_query($base, 'SELECT login FROM grades WHERE login=\'' . $_POST['personnegrade'] . '\' AND idalliance=\'' . $chef['id'] . '\'');
			$gradee = mysqli_num_rows($ex);
			if ($_POST['personnegrade'] != $chef['chef'] and $gradee < 1) {
				$ex = mysqli_query($base, 'SELECT login FROM membre WHERE login=\'' . $_POST['personnegrade'] . '\'');
				$existe = mysqli_num_rows($ex);
				if ($existe >= 1) {
					if (isset($_POST['inviterDroit']) and $_POST['inviterDroit']) $droit_inviter = 1;
					else $droit_inviter = 0;
					if (isset($_POST['guerreDroit']) and $_POST['guerreDroit']) $droit_guerre = 1;
					else $droit_guerre = 0;
					if (isset($_POST['pacteDroit']) and $_POST['pacteDroit']) $droit_pacte = 1;
					else $droit_pacte = 0;
					if (isset($_POST['bannirDroit']) and $_POST['bannirDroit']) $droit_bannir = 1;
					else $droit_bannir = 0;
					if (isset($_POST['descriptionDroit']) and $_POST['descriptionDroit']) $droit_description = 1;
					else $droit_description = 0;

					mysqli_query($base, 'INSERT INTO grades VALUES("' . $_POST['personnegrade'] . '", "' . $droit_inviter . '.' . $droit_guerre . '.' . $droit_pacte . '.' . $droit_bannir . '.' . $droit_description . '", "' . $chef['id'] . '", "' . $_POST['nomgrade'] . '")') or die('Erreur SQL !<br />' . mysqli_error($base));
					$information = "" . $_POST['personnegrade'] . " a été gradé " . $_POST['nomgrade'] . ".";
				} else {
					$erreur = "Cette personne n'existe pas";
				}
			} else {
				$erreur = "Cette personne est déjà gradée.";
			}
		} else {
			$erreur = "Tout les champs ne sont pas remplis";
		}
	}

	if (isset($_POST['joueurGrade']) and !empty($_POST['joueurGrade'])) {
		$_POST['joueurGrade'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['joueurGrade'])));
		$ex = mysqli_query($base, 'SELECT count(*) AS gradeExiste FROM grades WHERE login=\'' . $_POST['joueurGrade'] . '\' AND idalliance=\'' . $chef['id'] . '\'');
		$gradeExiste = mysqli_fetch_array($ex);

		if ($gradeExiste['gradeExiste'] > 0) {
			mysqli_query($base, 'DELETE FROM grades WHERE login=\'' . $_POST['joueurGrade'] . '\' AND idalliance=\'' . $chef['id'] . '\'');
			$information = "Vous avez supprimé le grade de " . $_POST['joueurGrade'] . ".";
		} else {
			$erreur = "Cette guerre n'existe pas.";
		}
	}

	if (isset($_POST['changertag'])) {
		if (!empty($_POST['changertag'])) {
			$_POST['changertag'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['changertag'])));
			$sql2 = 'SELECT tag FROM alliances WHERE tag=\'' . $_POST['changertag'] . '\'';
			$ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
			$nballiance = mysqli_num_rows($ex2);

			if ($nballiance == 0) {
				$sql3 = 'UPDATE alliances SET tag=\'' . $_POST['changertag'] . '\' WHERE id=\'' . $idalliance['idalliance'] . '\'';
				$ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysqli_error($base));

				$information = 'Le tag de l\'équipe a bien été changé et est devenu ' . $_POST['changertag'] . '.';
			} else {
				$erreur = "Une équipe avec ce tag existe déjà.";
			}
		} else {
			$erreur = "Le tag de votre équipe doit au moins comporter un caractère.";
		}
	}

	if (isset($_POST['changerchef'])) {
		if (!empty($_POST['changerchef'])) {
			$_POST['changerchef'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['changerchef'])));
			$sql = 'SELECT login FROM autre WHERE idalliance=\'' . $idalliance['idalliance'] . '\' AND login=\'' . $_POST['changerchef'] . '\'';
			$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
			$dansLAlliance = mysqli_num_rows($ex);
			if ($dansLAlliance > 0) {
				$sql1 = 'UPDATE alliances SET chef=\'' . $_POST['changerchef'] . '\' WHERE id=\'' . $idalliance['idalliance'] . '\'';
				mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));

		?>
				<script LANGUAGE="JavaScript">
					window.location = "allianceprive.php";
				</script>
	<?php
			} else {
				$erreur = "Le joueur que vous essayez de mettre en chef n'existe pas ou n'est pas dans votre équipe.";
			}
		} else {
			$erreur = "Aucun chef n'a été séléctionné";
		}
	}
}



if ($description) {
	if (isset($_POST['changerdescription'])) {
		if (!empty($_POST['changerdescription'])) {
			$_POST['changerdescription'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['changerdescription'])));
			$sql3 = 'UPDATE alliances SET description=\'' . $_POST['changerdescription'] . '\' WHERE id=\'' . $idalliance['idalliance'] . '\'';
			$ex3 = mysqli_query($base, $sql3) or die('Erreur SQL !<br />' . $sql3 . '<br />' . mysqli_error($base));
			$information = 'La description de l\'équipe a bien été changée.';
		} else {
			$erreur = "La description de votre équipe doit au moins comporter un caractère.";
		}
	}
}

if ($bannir) {
	if (isset($_POST['bannirpersonne'])) {
		if (!empty($_POST['bannirpersonne'])) {
			$_POST['bannirpersonne'] = ucfirst(mysqli_real_escape_string($base, stripslashes(antihtml($_POST['bannirpersonne']))));
			$sql = 'SELECT login FROM autre WHERE idalliance=\'' . $idalliance['idalliance'] . '\' AND login=\'' . $_POST['bannirpersonne'] . '\'';
			$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
			$dansLAlliance = mysqli_num_rows($ex);
			if ($dansLAlliance > 0) {
				$sql1 = 'UPDATE autre SET idalliance=0 WHERE login=\'' . $_POST['bannirpersonne'] . '\'';
				mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
				mysqli_query($base, 'DELETE FROM grades WHERE idalliance=\'' . $idalliance['idalliance'] . '\' AND login=\'' . $_POST['bannirpersonne'] . '\'');
				$information = 'Vous avez banni ' . $_POST['bannirpersonne'] . '.';
			} else {
				$erreur = "Le joueur que vous essayez de bannir n'existe pas ou n'est pas dans votre équipe.";
			}
		} else {
			$erreur = "Aucune personne n'a été séléctionné";
		}
	}
}

if ($pacte) {
	if (isset($_POST['pacte'])) {
		$_POST['pacte'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['pacte'])));
		$sql = 'SELECT id FROM alliances WHERE tag=\'' . $_POST['pacte'] . '\' AND id!=\'' . $idalliance['idalliance'] . '\'';
		$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
		$existeAlliance = mysqli_num_rows($ex);
		if ($existeAlliance > 0) {
			$ex = mysqli_query($base, 'SELECT * FROM alliances WHERE tag=\'' . $_POST['pacte'] . '\'');
			$allianceAllie = mysqli_fetch_array($ex);

			$ex = mysqli_query($base, 'SELECT count(*) AS nbDeclarations FROM declarations WHERE alliance1=\'' . $allianceAllie['id'] . '\' AND alliance2=\'' . $chef['id'] . '\' AND fin=0') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
			$nbDeclarations = mysqli_fetch_array($ex);

			$ex = mysqli_query($base, 'SELECT count(*) AS nbDeclarations FROM declarations WHERE alliance2=\'' . $allianceAllie['id'] . '\' AND alliance1=\'' . $chef['id'] . '\' AND fin=0') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
			$nbDeclarations1 = mysqli_fetch_array($ex);

			if ($nbDeclarations['nbDeclarations'] == 0 and $nbDeclarations1['nbDeclarations'] == 0) {
				mysqli_query($base, 'INSERT INTO declarations VALUES(default, 1, "' . $chef['id'] . '", "' . $allianceAllie['id'] . '", "' . time() . '", default, default, default, default, default)') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
				$ex = mysqli_query($base, 'SELECT id FROM declarations WHERE type=1 AND valide=0 AND alliance1=\'' . $chef['id'] . '\' AND alliance2=\'' . $allianceAllie['id'] . '\'');
				$idDeclaration = mysqli_fetch_array($ex);

				mysqli_query($base, 'INSERT INTO rapports VALUES(default, "' . time() . '", "L\'alliance ' . $chef['tag'] . ' vous propose un pacte.", "L\'alliance <a href=\"alliance.php?id=' . $chef['tag'] . '\">' . $chef['tag'] . '</a> vous propose un pacte. 
				<form action=\"validerpacte.php\" method=\"post\">
				<input type=\"submit\" value=\"Accepter\" name=\"accepter\"/>
				<input type=\"submit\" value=\"Refuser\" name=\"refuser\"/>
				<input type=\"hidden\" value=\"' . $idDeclaration['id'] . '\" name=\"idDeclaration\"/>
				</form>", "' . $allianceAllie['chef'] . '", default)') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
				$information = "Vous avez proposé un pacte à l'alliance " . $_POST['pacte'] . ".";
			} else {
				$erreur = "Soit vous êtes déjà allié avec cette équipe, soit vous êtes en guerre avec elle.";
			}
		} else {
			$erreur = "Cette équipe n'existe pas.";
		}
	}

	if (isset($_POST['allie']) and !empty($_POST['allie'])) {
		$_POST['allie'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['allie'])));
		$ex = mysqli_query($base, 'SELECT count(*) AS pacteExiste FROM declarations WHERE (alliance1=\'' . $_POST['allie'] . '\' OR alliance2=\'' . $_POST['allie'] . '\') AND type=1');
		$pacteExiste = mysqli_fetch_array($ex);

		if ($pacteExiste['pacteExiste'] > 0) {
			$ex = mysqli_query($base, 'SELECT * FROM alliances WHERE id=\'' . $_POST['allie'] . '\'');
			$allianceAdverse = mysqli_fetch_array($ex);
			mysqli_query($base, 'DELETE FROM declarations WHERE (alliance1 =\'' . $chef['id'] . '\' AND alliance2=\'' . $allianceAdverse['id'] . '\') OR ((alliance2 =\'' . $chef['id'] . '\' AND alliance1=\'' . $allianceAdverse['id'] . '\')) AND type=1');
			mysqli_query($base, 'INSERT INTO rapports VALUES(default, "' . time() . '", "L\'alliance ' . $chef['tag'] . ' met fin au pacte qui vous alliait.", "L\'alliance <a href=\"alliance.php?id=' . $chef['tag'] . '\">' . $chef['tag'] . '</a> met fin au pacte qui vous alliait.", "' . $allianceAdverse['chef'] . '", default)') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
			$information = "Le pacte avec " . $allianceAdverse['tag'] . " est bien rompu.";
		} else {
			$erreur = "Ce pacte n'existe pas.";
		}
	}
}

if ($guerre) {
	if (isset($_POST['guerre'])) {
		$_POST['guerre'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['guerre'])));
		$sql = 'SELECT id FROM alliances WHERE tag=\'' . $_POST['guerre'] . '\' AND id!=\'' . $idalliance['idalliance'] . '\'';
		$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
		$existeAlliance = mysqli_num_rows($ex);
		if ($existeAlliance > 0) {
			$ex = mysqli_query($base, 'SELECT * FROM alliances WHERE tag=\'' . $_POST['guerre'] . '\'');
			$allianceAdverse = mysqli_fetch_array($ex);
			$ex = mysqli_query($base, 'SELECT count(*) AS nbDeclarations FROM declarations WHERE alliance1=\'' . $allianceAdverse['id'] . '\' AND alliance2=\'' . $chef['id'] . '\' AND ((fin=0 AND type=0) OR (type=1 AND valide!=0))') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
			$nbDeclarations = mysqli_fetch_array($ex);
			echo $nbDeclarations['nbDeclarations'];

			$ex = mysqli_query($base, 'SELECT count(*) AS nbDeclarations FROM declarations WHERE alliance2=\'' . $allianceAdverse['id'] . '\' AND alliance1=\'' . $chef['id'] . '\' AND ((fin=0 AND type=0) OR (type=1 AND valide!=0))') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
			$nbDeclarations1 = mysqli_fetch_array($ex);

			if ($nbDeclarations['nbDeclarations'] == 0 and $nbDeclarations1['nbDeclarations'] == 0) {
				mysqli_query($base, 'DELETE FROM declarations WHERE alliance1=\'' . $allianceAdverse['id'] . '\' AND alliance2=\'' . $chef['id'] . '\' AND fin=0 AND valide=0');
				mysqli_query($base, 'DELETE FROM declarations WHERE alliance2=\'' . $allianceAdverse['id'] . '\' AND alliance1=\'' . $chef['id'] . '\' AND fin=0 AND valide=0');
				mysqli_query($base, 'INSERT INTO declarations VALUES(default, 0, "' . $chef['id'] . '", "' . $allianceAdverse['id'] . '", "' . time() . '", default, default, default, default, default)') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
				mysqli_query($base, 'INSERT INTO rapports VALUES(default, "' . time() . '", "L\'alliance ' . $chef['tag'] . ' vous déclare la guerre.", "L\'alliance <a href=\"alliance.php?id=' . $chef['tag'] . '\">' . $chef['tag'] . '</a> vous déclare la guerre.", "' . $allianceAdverse['chef'] . '", default)') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
				$information = "Vous avez déclaré la guerre à l'équipe " . $_POST['guerre'] . ".";
			} else {
				$erreur = "Soit une guerre est déjà déclarée contre cette équipe, soit vous êtes alliés avec elle.";
			}
		} else {
			$erreur = "Cette équipe n'existe pas.";
		}
	}

	if (isset($_POST['adversaire']) and !empty($_POST['adversaire'])) {
		$_POST['adversaire'] = mysqli_real_escape_string($base, stripslashes(antihtml($_POST['adversaire'])));
		$ex = mysqli_query($base, 'SELECT count(*) AS guerreExiste FROM declarations WHERE alliance2=\'' . $_POST['adversaire'] . '\' AND type=0');
		$guerreExiste = mysqli_fetch_array($ex);

		if ($guerreExiste['guerreExiste'] > 0) {
			$ex = mysqli_query($base, 'SELECT * FROM alliances WHERE id=\'' . $_POST['adversaire'] . '\'');
			$allianceAdverse = mysqli_fetch_array($ex);

			mysqli_query($base, 'UPDATE declarations SET fin=\'' . time() . '\' WHERE alliance1 =\'' . $chef['id'] . '\' AND alliance2=\'' . $allianceAdverse['id'] . '\' AND fin=0 AND type=0');
			mysqli_query($base, 'INSERT INTO rapports VALUES(default, "' . time() . '", "L\'alliance ' . $chef['tag'] . ' met fin à la guerre qui vous opposait.", "L\'alliance <a href=\"alliance.php?id=' . $chef['tag'] . '\">' . $chef['tag'] . '</a> met fin à la guerre qui vous opposait.", "' . $allianceAdverse['chef'] . '", default)') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
			$information = "La guerre contre " . $allianceAdverse['tag'] . " a pris fin.";
		} else {
			$erreur = "Cette guerre n'existe pas.";
		}
	}
}

if ($inviter) {
	if (isset($_POST['inviterpersonne'])) {
		if (!empty($_POST['inviterpersonne'])) {
			if ($nombreJoueurs < $joueursEquipe) {
				$_POST['inviterpersonne'] = ucfirst(mysqli_real_escape_string($base, stripslashes(antihtml($_POST['inviterpersonne']))));
				$sql = 'SELECT login FROM autre WHERE login=\'' . $_POST['inviterpersonne'] . '\'';
				$ex = mysqli_query($base, $sql) or die('Erreur SQL !<br />' . $sql . '<br />' . mysqli_error($base));
				$joueurExiste = mysqli_num_rows($ex);

				$sql1 = 'SELECT invite FROM invitations WHERE invite=\'' . $_POST['inviterpersonne'] . '\' AND idalliance=\'' . $idalliance['idalliance'] . '\'';
				$ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
				$invitationDejaEnvoye = mysqli_num_rows($ex1);
				if ($invitationDejaEnvoye == 0) {
					if ($joueurExiste > 0) {
						$sql2 = 'INSERT INTO invitations VALUES (default, "' . $idalliance['idalliance'] . '", "' . $chef['tag'] . '", "' . $_POST['inviterpersonne'] . '")';
						mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));

						$information = 'Vous avez invité ' . $_POST['inviterpersonne'] . '';
					} else {
						$erreur = "Ce joueur n'existe pas.";
					}
				} else {
					$erreur = "Vous avez déja envoyé une invitation à ce joueur.";
				}
			} else {
				$erreur = "Le nombre maximal de joueurs est atteint dans l'équipe";
			}
		} else {
			$erreur = "Je crois qu'une personne sans nom, ça n'existe pas.";
		}
	}
}

// On actualise les informations qui ont pu être changées
$sql1 = 'SELECT * FROM alliances WHERE id=\'' . $idalliance['idalliance'] . '\'';
$ex1 = mysqli_query($base, $sql1) or die('Erreur SQL !<br />' . $sql1 . '<br />' . mysqli_error($base));
$chef = mysqli_fetch_array($ex1);

include("includes/tout.php");
debutCarte('Paramètres de l\'équipe');
debutListe();
if ($chef) {
	item(['form' => ["allianceadmin.php", "changerNom"], 'floating' => true, 'titre' => "Nom de l'alliance", 'input' => '<input type="text" name="changernom" id="changernom" value="' . stripslashes($chef['nom']) . '" class="form-control"/>', 'after' => submit(['titre' => 'Changer', 'form' => 'changerNom'])]);

	item(['form' => ["allianceadmin.php", "changerTAG"], 'floating' => true, 'titre' => "TAG", 'input' => '<input maxlength=10 type="text" name="changertag" id="changertag" value="' . stripslashes($chef['tag']) . '" class="form-control"/>', 'after' => submit(['titre' => 'Changer', 'form' => 'changerTAG'])]);
}
if ($description) {
	creerBBcode("changerdescription", $chef['description']);
	item(['form' => ["allianceadmin.php", "description"], 'floating' => false, 'titre' => "Description", 'input' => '<textarea name="changerdescription" id="changerdescription" rows="10" cols="50">' . $chef['description'] . '</textarea>', 'after' => submit(['titre' => 'Changer', 'form' => 'description'])]);
}
if ($chef) {
	item(['form' => ["allianceadmin.php", "supprimerAlliance"], 'floating' => false, 'input' => '<input type="hidden" name="supprimeralliance1"/>' . submit(['titre' => 'Supprimer l\'équipe', 'form' => 'supprimerAlliance', 'style' => 'background-color:red'])]);
}
finListe();
finCarte();

debutCarte('Gestion des membres');
debutContent();
debutListe();
if ($gradeChef) {
	$options = '';
	$sql2 = 'SELECT login FROM autre WHERE idalliance=\'' . $idalliance['idalliance'] . '\'';
	$ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
	while ($chef1 = mysqli_fetch_array($ex2)) {
		$options = $options . '<option value=' . $chef1['login'] . '>' . $chef1['login'] . '</option>';
	}
	item(['form' => ["allianceadmin.php", "formChangerChef"], 'select' => ['changerchef', $options], 'titre' => 'Chef']);
	item(['input' => submit(['titre' => 'Changer', 'form' => 'formChangerChef'])]);
	echo '<hr/>';
}

if ($bannir) {
	$options = '';
	$sql2 = 'SELECT login FROM autre WHERE idalliance=\'' . $idalliance['idalliance'] . '\'';
	$ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
	while ($chef1 = mysqli_fetch_array($ex2)) {
		$options = $options . '<option value=' . $chef1['login'] . '>' . $chef1['login'] . '</option>';
	}
	item(['form' => ["allianceadmin.php", "bannir"], 'select' => ['bannirpersonne', $options], 'titre' => 'Bannir un membre']);
	item(['input' => submit(['titre' => 'Bannir', 'form' => 'bannir'])]);
	echo '<hr/>';
}

if ($inviter) {
	if ($nombreJoueurs < $joueursEquipe) {
		item(['form' => ["allianceadmin.php", "inviterPersonne"], 'titre' => "Inviter", 'ajax' => true, 'autocomplete' => 'labelInviter', 'input' => ' <input type="hidden" name="inviterpersonne" id="inviterpersonne" class="form-control"/>', 'after' => 'Nom du joueur']);
		item(['input' => submit(['titre' => 'Inviter', 'form' => 'inviterPersonne'])]);
	} else {
		echo 'Le nombre de joueurs maximal est déjà atteint dans l\'équipe.';
	}
}
finListe();
finContent();
finCarte();


if ($gradeChef) {
	debutCarte('Grades');
	echo important('Créer un grade');
	debutListe();
	?>
	<form method="post" action="allianceadmin.php" name="creerGrade">
		<?php
		item(['floating' => true, 'titre' => "Nom du grade", 'input' => '<input type="text" name="nomgrade" id="nomgrade" class="form-control"/>']);

		$options = '';
		$sql2 = 'SELECT login FROM autre WHERE idalliance=\'' . $idalliance['idalliance'] . '\'';
		$ex2 = mysqli_query($base, $sql2) or die('Erreur SQL !<br />' . $sql2 . '<br />' . mysqli_error($base));
		while ($chef1 = mysqli_fetch_array($ex2)) {
			$options = $options . '<option value=' . $chef1['login'] . '>' . $chef1['login'] . '</option>';
		}
		item(['select' => ['personnegrade', $options], 'titre' => 'Login du gradé']);
		finListe();
		echo checkbox([['name' => 'inviterDroit', 'titre' => 'Inviter des joueurs', 'noList' => true], ['name' => 'guerreDroit', 'titre' => 'Déclarer/finir la guerre', 'noList' => true], ['name' => 'pacteDroit', 'titre' => 'Demander/finir un pacte', 'noList' => true], ['name' => 'bannirDroit', 'titre' => 'Bannir un joueur', 'noList' => true], ['name' => 'descriptionDroit', 'titre' => 'Changer la description', 'noList' => true]]);
		echo '<br/>';
		item(['input' => submit(['titre' => 'Créer', 'form' => 'creerGrade']), 'noList' => true]);
		?> </form>
	<br />
	<?php echo important('Liste des grades'); ?>
	<form method="post" action="allianceadmin.php" name="supprimerGrade">
		<?php
		$ex = mysqli_query($base, 'SELECT * FROM grades WHERE idalliance=\'' . $chef['id'] . '\'') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
		?>
		<div class="table-responsive">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Login</th>
						<th>Nom du grade</th>
						<th>Supprimer</th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ($listeGrades = mysqli_fetch_array($ex)) {
						echo '<tr>
                            <td><a href="joueur.php?id=' . $listeGrades['login'] . '">' . $listeGrades['login'] . '</a></td>
                            <td>' . $listeGrades['nom'] . '</td>
                            <td>
                            <input type="hidden" name="joueurGrade" value="' . $listeGrades['login'] . '"/>
                            <input src="images/croix.png" alt="suppr" type="image" name="Supprimer"></td>
                            </tr>';
					}
					?>
				</tbody>
			</table>
		</div>
	</form>
<?php
	finCarte();
} ?>

<?php if ($pacte) {
	debutCarte('Pactes');
	debutListe();
	item(['form' => ["allianceadmin.php", "declarerPacte"], 'floating' => false, 'titre' => "Demander un pacte", 'input' => '<input type="text" name="pacte" id="pacte" placeholder="TAG de l\'alliance" class="form-control"/>', 'after' => submit(['titre' => 'Demander', 'form' => 'declarerPacte'])]);
	echo '<li>';
	$ex = mysqli_query($base, 'SELECT * FROM declarations WHERE alliance1=\'' . $chef['id'] . '\' AND type=1 AND valide!=0') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
	echo '
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                        <th>Allié</th>
                        <th>Début</th>
                        <th>Fin</th>
                        </tr></thead><tbody>';
	while ($pacte = mysqli_fetch_array($ex)) {
		$ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $pacte['alliance2'] . '\'');
		$tagAlliance = mysqli_fetch_array($ex1);

		echo '<tr>
                            <td><a href="alliance.php?id=' . $tagAlliance['tag'] . '">' . $tagAlliance['tag'] . '</a></td>
                            <td>' . date('d/m/Y à H\hi', $pacte['timestamp']) . '</td>
                            <td><form action="allianceadmin.php" method="post">
                            <input type="hidden" name="allie" value="' . $pacte['alliance2'] . '"/>
                            <input src="images/croix.png" alt="stop" type="image" name="stoppacte"></form></td>
                            </tr>';
	}
	$ex = mysqli_query($base, 'SELECT * FROM declarations WHERE alliance2=\'' . $chef['id'] . '\' AND type=1 AND valide!=0') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
	while ($pacte = mysqli_fetch_array($ex)) {
		$ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $pacte['alliance1'] . '\'');
		$tagAlliance = mysqli_fetch_array($ex1);

		echo '<tr>
                            <td><a href="alliance.php?id=' . $tagAlliance['tag'] . '">' . $tagAlliance['tag'] . '</a></td>
                            <td>' . date('d/m/Y à H\hi', $pacte['timestamp']) . '</td>
                            <td><form action="allianceadmin.php" method="post">
                            <input type="hidden" name="allie" value="' . $pacte['alliance1'] . '"/>
                            <input src="images/croix.png" alt="stop" type="image" name="stoppacte"></form></td>
                            </tr>';
	}
?>
	</tbody>
	</table>
	</div>
	</li>
<?php
	finListe();
	finCarte();
}
if ($guerre) {
	debutCarte('Guerres');
	debutListe();
	item(['form' => ["allianceadmin.php", "declarerGuerre"], 'floating' => false, 'titre' => "Déclarer une guerre", 'input' => '<input type="text" name="guerre" id="guerre" placeholder="TAG de l\'alliance" class="form-control"/>', 'after' => submit(['titre' => 'Déclarer', 'form' => 'declarerGuerre'])]);
	echo '<li>';
	$ex = mysqli_query($base, 'SELECT * FROM declarations WHERE alliance1=\'' . $chef['id'] . '\' AND type=0 AND fin=0') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
	echo '
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                        <thead>
                        <tr>
                        <th>Adversaire</th>
                        <th>Début</th>
                        <th>Pertes</th>
                        <th>Fin</th>
                        </tr></thead><tbody>';
	while ($guerre = mysqli_fetch_array($ex)) {
		$ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $guerre['alliance2'] . '\'');
		$tagAlliance = mysqli_fetch_array($ex1);

		echo '<tr>
                            <td><a href="alliance.php?id=' . $tagAlliance['tag'] . '">' . $tagAlliance['tag'] . '</a></td>
                            <td>' . date('d/m/Y à H\hi', $guerre['timestamp']) . '</td>
                            <td>' . $guerre['pertes1'] . '</td>
                            <td><form action="allianceadmin.php" method="post">
                            <input type="hidden" name="adversaire" value="' . $guerre['alliance2'] . '"/>
                            <input src="images/croix.png" alt="stop" type="image" name="stopguerre"></form></td>
                            </tr>';
	}
	$ex = mysqli_query($base, 'SELECT * FROM declarations WHERE alliance2=\'' . $chef['id'] . '\' AND type=0 AND fin=0') or die('Erreur SQL !<br /><br />' . mysqli_error($base));
	while ($guerre = mysqli_fetch_array($ex)) {
		$ex1 = mysqli_query($base, 'SELECT tag FROM alliances WHERE id=\'' . $guerre['alliance1'] . '\'');
		$tagAlliance = mysqli_fetch_array($ex1);

		echo '<tr>
                            <td><a href="alliance.php?id=' . $tagAlliance['tag'] . '">' . $tagAlliance['tag'] . '</a></td>
                            <td>' . date('d/m/Y à H\hi', $guerre['timestamp']) . '</td>
                            <td>' . $guerre['pertes1'] . '</td>
                            <td>Déclarée par ' . $tagAlliance['tag'] . '</td>
                            </tr>';
	}
?>
	</tbody>
	</table>
	</div>
	</li>
<?php
	finListe();
	finCarte();
}

include("includes/copyright.php"); ?>