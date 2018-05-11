<?php

	$conf = new stdClass();
	$conf->db = new stdClass();

	$conf->db->location = "localhost";
	$conf->db->name = "heimstatusDB";
	$conf->db->user = "root";
	$conf->db->pass = "";

	$conf->file = new stdClass();
	$conf->file->dol_document_root = $_SERVER['DOCUMENT_ROOT'].'/Heim-Status/';

?>