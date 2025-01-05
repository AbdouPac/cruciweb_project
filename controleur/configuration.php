<?php

session_start();

header('Content-type: application/json');

require_once '../model/databaseConfig.php';
require_once '../model/utilisateurModel.php';
require_once '../model/grillesModel.php';
require_once '../model/sauvegardeModel.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	$errors = array();
        
    // Validation des champs
    if (empty($_POST['serveur'])) {
        $errors[] = " Le serveur est obligatoire";
    }
    if (empty($_POST['bdd'])) {
        $errors[] = " Le nom de la base de données est obligatoire";
    }
    if (empty($_POST['utilisateur'])) {
        $errors[] = " Le nom d'utilisateur est obligatoire";
    }
    // Si erreurs, renvoyer la réponse
    if (!empty($errors)) {
        echo json_encode(array('response' => 'error', 'message' => $errors));
        exit;
    }

    $params['serveur'] = $_POST['serveur'];
    $params['bdd'] = $_POST['bdd'];
    $params['utilisateur'] = $_POST['utilisateur'];
    $params['mdp'] = $_POST['mdp'];

    try {
        $pdo = connectToDatabaseParams($params['serveur'], $params['bdd'], $params['utilisateur'], $params['mdp']);
        saveDatabaseConfig($params);
        initDatabase($pdo);
        // Création de l'utilisateur admin par défaut si il n'existe pas
        if (!isAdminExist($pdo)) {
            // Créer l'utilisateur admin par défaut
            createUser($pdo, 'admin@admin.com', 'admin', 'admin');

            $admin_id = getUserIdByEmail($pdo, 'admin@admin.com')['id'];

            if (!getUserByEmail($pdo, 'abdoulaye.fofana1@univ-rouen.fr')) {
                createUser($pdo, 'abdoulaye.fofana1@univ-rouen.fr', 'Password1234', 'inscrit');
            }
            if (!getUserByEmail($pdo, 'rachid.anjorin@univ-rouen.fr')) {
                createUser($pdo, 'rachid.anjorin@univ-rouen.fr', 'Pwd1234', 'inscrit');
            }

            if (!getUserByEmail($pdo, 'kimia.golbazkhanian@univ-rouen.fr')) {
                createUser($pdo, 'kimia.golbazkhanian@univ-rouen.fr', 'Pwd1234', 'inscrit');
            }
        
          // Ajouter des grilles par défaut associées à l'administrateur
        createGrid(
            $pdo,
            $admin_id,
            "Mots enigmatiques",
            10,
            10,
            '{"1":"Rendu moins sauvage","2":"Courtois # Mesure en Asie # Vieux do","3":"Un joli temps","4":"île de France # Disposée au milieu","5":"Saule # Un triste appel","6":"Vedette","7":"Oublie # Informé","8":"Vers marins","9":"A sa clairette # Septième art","10":"Fin d\'infinitif # Direction # Symbole chimique","A":"Etudié avec précision","B":"Trous d\'évacuation # Femme politique israélienne","C":"Lettre # Célèbre en 38","D":"Pousse-café","E":"Préfixe multiplicateur # Glace anglaise","F":"Exprime un bruit violent # Plante potagère","G":"Frottés et bénis # Mistral","H":"Dit avec raillerie","I":"Transpirations # Personnel","J":"Un individu # Une façon d\'être"}',
            '{"1":"APPRIVOISE","2":"POLI#LI#UT","3":"PRINTANIER","4":"RE#CENTREE","5":"OSIER#SOS#","6":"F#STAR#N#S","7":"OMET#AVISE","8":"NEREIDES#R","9":"DIE#CINEMA","10":"IR##EST#ES"}',
            'expert'
        );

        createGrid(
            $pdo,
            $admin_id,
            "Janvier de l’Astronomie",
            10,
            10,
            '{"1":"Les outils des astronomes","2":"Louis XIV, selon Louis XIV # Chasseur équatorial","3":"Que ce soit le dieu ou le métal, il irradie # Sam ou Tom","4":"Porteuse de messages célestes # A une liaison avec Jupiter","5":"Aurochs # Bout de bois","6":"Court # Religieuse","7":"A la mode # Technique utilisée par les virtuoses de l\'imagerie électronique","8":"Petits ensembles, battus d\'une tête par celui de Stéphan.","9":"Local industriel","10":"On les trouve surtout dans le nord de l\'Italie","A":"Telle notre bonne vieille planète","B":"Les astronomes sont toujours à sa recherche # Le cadeau","C":"Brouillent la vision # Odeur méridionale","D":"Pour éviter que le ciel ne nous tombe sur la tête # En Moravie.","E":"Article","F":"Boréale,australe ou solaire","G":"Décoreront","H":"Celui du Midi est un haut lieu de l\'astronomie française # Les instruments astronomiques renversent les images, mais ce n\'est pas une raison pour tourner de l\'oeil de cette façon.","I":"Pour faire quelque chose avec du vent.","J":"Aux quatre coins de la rose # Un gamin vraiment désordonné"}',
            '{"1":"TELESCOPES","2":"ETAT#ORION","3":"L#RA#UNCLE","4":"LUMIERE#IO","5":"URES#OREE#","6":"RAS##NONNE","7":"IN#BINNING","8":"QUARTETTES","9":"USINE##E#O","10":"E#LOMBARDS"}',
            'expert'
        );

        createGrid(
            $pdo,
            $admin_id,
            "Maux-Croizé",
            5,
            5,
            '{"1":"Danse","2":"Bleu peint","3":"Grands frères","4":"Génisse","5":"Crochets","A":"Planche","B":"Pseudo","C":"Chétifs","D":"Mousse","E":"Poignées"}',
            '{"1":"SAMBA","2":"KLEIN","3":"AINES","4":"TAURE","5":"ESSES"}',
            'intermediaire'
        );

        createGrid(
            $pdo,
            $admin_id,
            "Galactiques",
            10,
            10,
            '{"1":"Chasseurs","2":"Table d\'église # Intelligence artificielle","3":"Mèches rebelles # Résidu céréalier","4":"Champion - Amérindien","5":"Tissu léger","6":"Théories # Crochet de boucher","7":"Connu # Homme vertueux # Règle","8":"Deux romain # Discrètes quand elles sont basses","9":"Dresser # Titane","10":"Retraits chirurgicaux de tumeurs","A":"Oiseau de Nouvelle-Guinée","B":"Maigre filet # Charmer","C":"Saison chaude # Négation # Est anglais","D":"Déléguée # Terre mère","E":"Contraints de rester couchés # Gars","F":"Esprit # Lance la balle","G":"École polytechnique # Mauvais services","H":"Chef-lieu de canton de l\'Orne # Travail forcé","I":"Son carnaval est célèbre # Femme de lettres américaine","J":"Bien attachées"}',
            '{"1":"PREDATEURS","2":"AUTEL#P#IA","3":"R#EPIS#SON","4":"AS#UTE#E#G","5":"DENTELLE#L","6":"IDEES#ESSE","7":"SU#E#ST#TE","8":"II##MESSES","9":"ERIGER#TI#","10":"RESECTIONS"}',
            'expert'
        );


        createGrid(
            $pdo,
            $admin_id,
            "Défis linguistiques et culturels",
            10,
            10,
            '{"1":"Relatifs à l\'hygiène","2":"Jeune équidé # Devoirs universitaires","3":"Il met les ouvrages en vente","4":"Affrétées","5":"Allège la souffrance","6":"Ingrédient pour la fabrication de la bière # Drame japonais","7":"Soyeuse # Pour citer textuellement","8":"Lichen # Société","9":"Relatif à un des cinq sens","10":"Transpirer # Rembourse sa dette","A":"Commune de la Réunion","B":"Pige # Mœurs","C":"Père très généreux # Consomme","D":"Informateur","E":"Recluses","F":"Espérance # Possessif","G":"Elles passent en tête # Direction","H":"Utilisons # Sujet familier","I":"Activité récréative","J":"Marque la condition # Négation # Boisson du petit déjeuner"}',
            '{"1":"SANITAIRES","2":"ANON#TD##I","3":"I#EDITEUR#","4":"NOLISEES#N","5":"T##CONSOLE","6":"L#MALT#NO#","7":"OUATEE#SIC","8":"USNEE#C#SA","9":"I#GUSTATIF","10":"SUER#APURE"}',
            'expert'
        );
        
}
        
        closeDatabaseConnection($pdo);
        echo json_encode(array('response'=>'success', 'message' => "Configuration enregistrée avec succès : " . json_encode($params) . ", base de données initialisée et utilisateur admin créé."));
    } catch(Exception $e) {
        echo json_encode(array('response' => 'error', 'message' => $e->getMessage()));
        exit;
    }

} else {
	$arrResult = array ('response'=>'error', 'message' => "SERVER REQUEST_METHOD doit être POST");
	echo json_encode($arrResult);
}
?>
