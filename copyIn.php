<?php

/**
 * Joomla Install -> GIT
 *
 * This file allows you to copy changes from a deployed version of the component
 * back to the git repo!
 * This means that you can develop on your live test code!
 *
 * The location of the joomlaRoot to copy from is taken from .joomlaRoot
 *     For example: X:\web\pub\joomla
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lurker\Event\FilesystemEvent;
use Lurker\ResourceWatcher;

$joomlaRoot = __DIR__ . '/.docker/www';

$watcher = new ResourceWatcher;
$watcher->track('administrator', $joomlaRoot . '/administrator/components/com_swa' );
$watcher->track('site', $joomlaRoot . '/components/com_swa' );

$watcher->addListener('administrator', function (FilesystemEvent $event) use ( $joomlaRoot ) {
	$file = $event->getResource();
	$eventType = $event->getType();
	echo $file . " was " . $eventType;
	echo "Copying administrator...";
	recurse_copy($joomlaRoot . '/administrator/components/com_swa', __DIR__ . '/src/administrator');
	echo "Done!\n";
});
$watcher->addListener('site', function (FilesystemEvent $event) use ( $joomlaRoot ) {
	echo "Copying site...\n";
	recurse_copy($joomlaRoot . '/components/com_swa', __DIR__ . '/src/site');
	echo "Done!\n";
});


echo "Lurking...\n";
$watcher->start();

/**
 * Taken from the doc page of copy
 * http://php.net/manual/en/function.copy.php#91010
 */
function recurse_copy($src, $dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while (false !== ( $file = readdir($dir))) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if (is_dir($src . '/' . $file)) {
				recurse_copy($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

/**
 * Taken from https://stackoverflow.com/a/2638272
 */
function getRelativePath($from, $to)
{
	// some compatibility fixes for Windows paths
	$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
	$to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
	$from = str_replace('\\', '/', $from);
	$to   = str_replace('\\', '/', $to);

	$from     = explode('/', $from);
	$to       = explode('/', $to);
	$relPath  = $to;

	foreach($from as $depth => $dir) {
		// find first non-matching dir
		if($dir === $to[$depth]) {
			// ignore this directory
			array_shift($relPath);
		} else {
			// get number of remaining dirs to $from
			$remaining = count($from) - $depth;
			if($remaining > 1) {
				// add traversals up to first matching dir
				$padLength = (count($relPath) + $remaining - 1) * -1;
				$relPath = array_pad($relPath, $padLength, '..');
				break;
			} else {
				$relPath[0] = './' . $relPath[0];
			}
		}
	}
	return implode('/', $relPath);
}
