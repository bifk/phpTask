<?php

namespace Sprint\Migration;

interface ParseMigrationInterface 
{
	function parse($csvFile);
	function setOutCalls($outCall, $outErrorCall);
}