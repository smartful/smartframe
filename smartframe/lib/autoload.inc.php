<?php

function chargerClasse($classe)
{
	require ucfirst($classe).'.class.php';
}

spl_autoload_register('chargerClasse');
