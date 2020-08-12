<?php

/**
 * Função responsável por formatar o valor para o banco de dados
 * @Autor Mauricio
 * @Parâmetos $valor
 * @Retorno o valor formatado
 */
function formatValueForMysql($valor) {

	if (strlen($valor) <= 6) {
		return str_replace(',', '.', $valor);
	}

	return str_replace(',', '.', str_replace('.', '', $valor));
}

/**
 * Função responsável por formatar o valor para exibicao
 * @Autor Mauricio
 * @Parâmetos $valor
 * @Retorno o valor formatado
 */
function formatValueForUser($valor) {
	if (empty($valor)) {
		return '0,00';
	}
	return number_format($valor, 2, ',', '.');
}

function converteMaiusculo($string) {
	$string = strtoupper($string);
	$string = str_replace("â", "Â", $string);
	$string = str_replace("á", "Á", $string);
	$string = str_replace("ã", "Ã", $string);
	$string = str_replace("à", "A", $string);
	$string = str_replace("ê", "Ê", $string);
	$string = str_replace("é", "É", $string);
	$string = str_replace("î", "I", $string);
	$string = str_replace("í", "Í", $string);
	$string = str_replace("ó", "Ó", $string);
	$string = str_replace("õ", "Õ", $string);
	$string = str_replace("ô", "Ô", $string);
	$string = str_replace("ú", "Ú", $string);
	$string = str_replace("û", "U", $string);
	$string = str_replace("ç", "Ç", $string);
	return $string;
}

function converteMinusculo($string) {
	$string = str_replace("Â", "â", $string);
	$string = str_replace("Á", "á", $string);
	$string = str_replace("Ã", "ã", $string);
	$string = str_replace("A", "à", $string);
	$string = str_replace("Ê", "ê", $string);
	$string = str_replace("É", "é", $string);
	$string = str_replace("I", "î", $string);
	$string = str_replace("Í", "í", $string);
	$string = str_replace("Ó", "ó", $string);
	$string = str_replace("Õ", "õ", $string);
	$string = str_replace("Ô", "ô", $string);
	$string = str_replace("Ú", "ú", $string);
	$string = str_replace("U", "û", $string);
	$string = str_replace("Ç", "ç", $string);
	return $string;
}