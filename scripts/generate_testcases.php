<?php

/**
 * Restore/export the generated testcase workbook.
 * The workbook is kept under documents so it can be downloaded without Excel.
 */
$source = dirname(__DIR__) . '/documents/restaurant-form-testcases-fixed.xlsx';
$target = dirname(__DIR__) . '/documents/restaurant-form-testcases.xlsx';

if (!is_file($source)) {
	fwrite(STDERR, "Missing source workbook: $source\n");
	exit(1);
}

if (!copy($source, $target)) {
	fwrite(STDERR, "Could not restore workbook: $target\n");
	exit(1);
}

echo "Restored $target\n";
