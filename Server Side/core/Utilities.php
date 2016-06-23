<?php
class Utilities
{
	//M�todos est�ticos
	public static function formatCnpj($cnpj)
	{
		return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . 
                '.' . substr($cnpj, 5, 3) . '/' . 
                substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
	}
}
?>