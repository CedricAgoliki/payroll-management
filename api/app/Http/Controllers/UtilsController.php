<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UtilsController extends Controller
{

    public function test(Request $request)
    {
	$file = file_get_contents($request->file('file')->getRealPath());
	//return response('{"success": true, "reason": "saved"}', 200);
	return response('{"content": '.$file.'}', 200);
    }
    /*
     * Le code suivant parse le fichier xml generer par le logiciel Perfect v6.3.0.1 de CAGECFI
     * Les échances commence à la ligne 79 et ce repete chaque 9 ligne jusqu'au dernier
     * l'appel à array_pop à la fin permet de retirer le dernier element qui n'est pas valide
     * Un tableau $property est utilisé pour gerer les noms des differents attributs
     * au lieu d'un "swith case" ou d'un "if else" parce que trop long à ecrire :)
     * EX: 
     *
	$property = "";

	switch ($key) {
	    case $current: 
		$property = "date";
		break;
	    case $current + 1 :
		$property = "interets";
		break;
	    case $current + 2 :
		$property = "capital";
		break;
	    case $current + 3 :
		$property = "epargne";
		break;
	    case $current + 4 :
		$property = "commiss";
		break;
	    case $current + 5 :
		$property = "tot. ech";
		break;
	    case $current + 6 :
		$property = "cap rest du";
		break;
	    case $current + 7 :
		$property = "sold rest du";
		break;
	}
	if ($property !== "") {
	    //$value = preg_replace('/\s+/','',$values[$item]["value"]);
	    //$value = str_replace('\u00a0','',$values[$item]["value"]);
	    $value = str_replace(chr(194).chr(160),'',$values[$item]["value"]);
	    $res["echeances"][$count][$property] = $value;
	}
     * */
    public function parsexml(Request $request)
    {
/*<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>*/
	/*$file = "
<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>
<DOCUMENT>
  <TEXT>ASSILASSIME SIEGE</TEXT>
  <TEXT>Imprimé le</TEXT>
  <TEXT>:</TEXT>
  <TEXT>07/12/2018</TEXT>
  <TEXT>Imprimé par</TEXT>
  <TEXT>PATRICK FOLIGAH</TEXT>
  <TEXT>:</TEXT>
  <TEXT>Tél.</TEXT>
  <TEXT>Journée</TEXT>
  <TEXT>:</TEXT>
  <TEXT>03/12/2018</TEXT>
  <TEXT>Devise</TEXT>
  <TEXT>:</TEXT>
  <TEXT>F.CFA</TEXT>
  <TEXT>BP.</TEXT>
  <TEXT> LOME</TEXT>
  <TEXT>TABLEAU D'AMORTISSEMENT</TEXT>
  <TEXT>Demande</TEXT>
  <TEXT>:</TEXT>
  <TEXT>A00D201800012</TEXT>
  <TEXT>N°manuel</TEXT>
  <TEXT>:</TEXT>
  <TEXT>Périodicité                      : </TEXT>
  <TEXT>:</TEXT>
  <TEXT>Mensuelle</TEXT>
  <TEXT>Adhérent</TEXT>
  <TEXT>:</TEXT>
  <TEXT>A0000012 - M. FOLIGAH Adadé Lébéné</TEXT>
  <TEXT>Nbre éché.</TEXT>
  <TEXT>:</TEXT>
  <TEXT>10</TEXT>
  <TEXT>Dossier</TEXT>
  <TEXT>:</TEXT>
  <TEXT>A00PRT201800014</TEXT>
  <TEXT>Différé       </TEXT>
  <TEXT>:</TEXT>
  <TEXT>0</TEXT>
  <TEXT>Péri. grâce</TEXT>
  <TEXT>:</TEXT>
  <TEXT>0</TEXT>
  <TEXT>Produit de crédit</TEXT>
  <TEXT>:</TEXT>
  <TEXT>PERSONNEL PRET SOCIAL</TEXT>
  <TEXT>Durée crd.</TEXT>
  <TEXT>:</TEXT>
  <TEXT>10 mois</TEXT>
  <TEXT>Taux mensuel</TEXT>
  <TEXT>:</TEXT>
  <TEXT>0,2917</TEXT>
  <TEXT>Taux effectif  mensuel</TEXT>
  <TEXT>:</TEXT>
  <TEXT>0,2416</TEXT>
  <TEXT>Montant              </TEXT>
  <TEXT>:</TEXT>
  <TEXT>350 000</TEXT>
  <TEXT>Type de tableau</TEXT>
  <TEXT>:</TEXT>
  <TEXT>Dégressif</TEXT>
  <TEXT>Date effet</TEXT>
  <TEXT>:</TEXT>
  <TEXT>24/09/2018</TEXT>
  <TEXT>Gestionnaire</TEXT>
  <TEXT>:</TEXT>
  <TEXT>EDAH Antoine</TEXT>
  <TEXT>Numéro contrat</TEXT>
  <TEXT>:</TEXT>
  <TEXT>Zone géographique</TEXT>
  <TEXT>:</TEXT>
  <TEXT>AGBALEPEDO</TEXT>
  <TEXT>N°</TEXT>
  <TEXT>ECHEANCE</TEXT>
  <TEXT>INTERETS</TEXT>
  <TEXT>CAPITAL</TEXT>
  <TEXT>EPARGNE</TEXT>
  <TEXT>COMMISSIONS</TEXT>
  <TEXT>TOTAL ECHEANCE</TEXT>
  <TEXT>CAPITAL REST. DU</TEXT>
  <TEXT>SOLDE RESTANT DÛ</TEXT>
  <TEXT>1</TEXT>
  <TEXT>24/10/2018</TEXT>
  <TEXT>1 000</TEXT>
  <TEXT>34 600</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>315 400</TEXT>
  <TEXT>320 000</TEXT>
  <TEXT>2</TEXT>
  <TEXT>26/11/2018</TEXT>
  <TEXT>1 000</TEXT>
  <TEXT>34 600</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>280 800</TEXT>
  <TEXT>284 400</TEXT>
  <TEXT>3</TEXT>
  <TEXT>24/12/2018</TEXT>
  <TEXT>800</TEXT>
  <TEXT>34 800</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>246 000</TEXT>
  <TEXT>248 800</TEXT>
  <TEXT>4</TEXT>
  <TEXT>24/01/2019</TEXT>
  <TEXT>700</TEXT>
  <TEXT>34 900</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>211 100</TEXT>
  <TEXT>213 200</TEXT>
  <TEXT>5</TEXT>
  <TEXT>25/02/2019</TEXT>
  <TEXT>600</TEXT>
  <TEXT>35 000</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>176 100</TEXT>
  <TEXT>177 600</TEXT>
  <TEXT>6</TEXT>
  <TEXT>25/03/2019</TEXT>
  <TEXT>500</TEXT>
  <TEXT>35 100</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>141 000</TEXT>
  <TEXT>142 000</TEXT>
  <TEXT>7</TEXT>
  <TEXT>24/04/2019</TEXT>
  <TEXT>400</TEXT>
  <TEXT>35 200</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>105 800</TEXT>
  <TEXT>106 400</TEXT>
  <TEXT>8</TEXT>
  <TEXT>24/05/2019</TEXT>
  <TEXT>300</TEXT>
  <TEXT>35 300</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>70 500</TEXT>
  <TEXT>70 800</TEXT>
  <TEXT>9</TEXT>
  <TEXT>24/06/2019</TEXT>
  <TEXT>200</TEXT>
  <TEXT>35 400</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 600</TEXT>
  <TEXT>35 100</TEXT>
  <TEXT>35 200</TEXT>
  <TEXT>10</TEXT>
  <TEXT>24/07/2019</TEXT>
  <TEXT>100</TEXT>
  <TEXT>35 100</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>35 200</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>TOTAL</TEXT>
  <TEXT>5 600</TEXT>
  <TEXT>350 000</TEXT>
  <TEXT>0</TEXT>
  <TEXT>0</TEXT>
  <TEXT>355 600</TEXT>
  <TEXT>PERFECT V6.2</TEXT>
  <TEXT>1 / 1</TEXT>
</DOCUMENT>
";*/
	$file = file_get_contents($request->file('file')->getRealPath());
	$parser = xml_parser_create();
	$r = xml_parse_into_struct($parser, $file, $values, $index);
	xml_parser_free($parser);
	// return response('{"result": "'.$r.'"}', 200);
	$res = [];
	foreach ($index as $key => $val) {
	    if ($key === "TEXT") {
		//return $values;
		// return ["periodicité" => $values[25]["value"]];
		// 79 -- 9
		// 64 -- 12
		$current = -1; $next = -1; $count = 0;
		$start = -1; $step = -1;
		foreach ($val as $key => $item) {
		    // on verifie le champ date pour savoir quelle type de pret est effectué
		    if ($start == -1 && ($key == 64 || $key == 79)) {
			$matched = preg_match('#^\d\d/\d\d/\d{4}$#', $values[$item]["value"]);
			if ($matched) {
			    $start = $key;
			    if ($key == 64) $step = 12;
			    else if ($key == 79) $step = 9;
			}
		    }
		    if ($key < $start) {
			$property = array(
			    24 => "periodicite",
			    30 => "nombreEcheancier",
			    51 => "tauxEffectifMensuelle",
			    54 => "montant",
			    57 => "typeDeTableau",
			    60 => "dateEffet",
			    63 => "gestionnaire",
			);
			if (isset($property[$key])) {
			    //$value = str_replace(chr(194).chr(160),'', $values[$item]["value"]);
			    $value = str_replace('','', $values[$item]["value"]);
			    $res[$property[$key]] = $value;
			}
		    } else {
			if ($key === $start) {
			    $current = $key; // 79 + 9
			    $next = $current + $step;
			}
			if ($key === $next) {
			    $current = $next;
			    $next += $step;
			    $count++;
			}
			$property = array(
			    0 => "date",
			    1 => "interets",
			    2 => "capital",
			    3 => "epargne",
			    4 => "commission",
			    5 => "totalEcheancier",
			    6 => "capitalRestantDu",
			    7 => "soldeRestantDu",
			);
			if (isset($property[$key % $current])) {
			    // on enleve les caracteres \u00a0
			    $value = str_replace(chr(194).chr(160),'',$values[$item]["value"]);
			    $res["echeances"][$count][$property[$key % $current]] = $value;
			}
		    }
		}
	    }
	}

	array_pop($res["echeances"]);

	return $res;
    }
}
