<?php
/*
 * Run this once at the very end, after your last push, with OpenCode closed:
 *
 *   php tools/finish.php
 *
 * It checks your repo is ready, exports your OpenCode session(s) into a zip file NEXT TO
 * this folder (not inside it), and prints a summary to paste into the submission form.
 * It never changes your code, your commits or NOTES.md.
 */

const OPENCODE_JSON_SHA256 = 'de49a6e97a7d6a287c9557c54b8f247c9adaae85d30f2eaa8f7b3dc5316c41f8';
const EXPECTED_OPENCODE = '2.0.29';

$root = realpath(__DIR__ . '/..');
chdir($root);
$problems = array();
$warnings = array();

function run($cmd, &$code = NULL)
{
	$proc = proc_open($cmd, array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
	if ( ! is_resource($proc)) { $code = 127; return ''; }
	$out = stream_get_contents($pipes[1]);
	stream_get_contents($pipes[2]);
	fclose($pipes[1]); fclose($pipes[2]);
	$code = proc_close($proc);
	return rtrim((string) $out);
}

echo "FeeLedger: finishing up\n\n";

// 1. Everything committed?
$status = run('git status --porcelain', $code);
if ($code !== 0) {
	fwrite(STDERR, "This doesn't look like a git repository. Run it from your assignment folder.\n");
	exit(1);
}
if ($status !== '') {
	$files = array_map(function ($l) { return trim(substr($l, 3)); }, explode("\n", $status));
	$problems[] = "You have changes that aren't committed:\n      " . implode("\n      ", $files) . "\n    Commit them (git add, git commit), push, then run this again.";
}

// 2. Everything pushed?
run('git fetch --quiet', $fetch_code);
$upstream = run('git rev-parse --abbrev-ref --symbolic-full-name @{u}', $code);
if ($code !== 0 || $upstream === '') {
	$problems[] = "Your branch isn't connected to GitHub yet. Run: git push -u origin HEAD";
} else {
	$ahead = (int) run('git rev-list --count @{u}..HEAD');
	if ($ahead > 0) {
		$problems[] = "$ahead commit(s) aren't on GitHub yet. Run: git push";
	}
	if ($fetch_code !== 0) {
		$warnings[] = "Couldn't reach GitHub to double-check your push (offline?).";
	}
}

// 3. NOTES.md
if ( ! is_file('NOTES.md') || trim(file_get_contents('NOTES.md')) === '') {
	$problems[] = "NOTES.md is missing or empty. Add it (see the README for the headings), commit, push, run this again.";
} else {
	$notes = strtolower(file_get_contents('NOTES.md'));
	foreach (array('fixed' => 'What I fixed and how I found it', 'not to change' => 'What I chose not to change',
	               'own feature' => 'My own feature', 'risk' => 'Biggest remaining risk') as $needle => $heading) {
		if (strpos($notes, $needle) === FALSE) {
			$warnings[] = "NOTES.md doesn't seem to have the section \"$heading\".";
		}
	}
}

// 4. opencode.json untouched
// compare with Windows line endings normalised (Git for Windows checks files out with CRLF by default)
$opencode_json = is_file('opencode.json') ? str_replace("\r\n", "\n", file_get_contents('opencode.json')) : NULL;
if ($opencode_json === NULL || hash('sha256', $opencode_json) !== OPENCODE_JSON_SHA256) {
	$problems[] = "opencode.json has been changed or deleted. Restore it with: git checkout origin/HEAD -- opencode.json  (then commit and push).";
}

// 5. OpenCode sessions for this folder
$version = run('opencode --version', $code);
if ($code !== 0) {
	$problems[] = "Can't run 'opencode' from here. Use the same terminal you used for OpenCode.";
	$sessions = array();
} else {
	if (strpos($version, EXPECTED_OPENCODE) === FALSE) {
		$warnings[] = "OpenCode version is " . preg_replace('/^opencode\s+v?/i', '', $version) . " (the guide asks for " . EXPECTED_OPENCODE . "). Mention this in the form.";
	}
	$sessions = array();
	foreach (explode("\n", run('opencode session list')) as $line) {
		if (preg_match('/^(ses_[A-Za-z0-9]+)/', trim($line), $m)) {
			$sessions[] = $m[1];
		}
	}
	if (empty($sessions)) {
		$problems[] = "No OpenCode session found for this folder. Did you start OpenCode inside it?";
	}
}

if ($problems) {
	echo "Not ready yet:\n\n";
	foreach ($problems as $i => $p) {
		echo '  ' . ($i + 1) . ". $p\n\n";
	}
	foreach ($warnings as $w) {
		echo "  Note: $w\n";
	}
	exit(1);
}

// OpenCode can cut its output short when writing into a pipe, so the export goes straight into a file
function export_session($id)
{
	$tmp = tempnam(sys_get_temp_dir(), 'feeledger-export-');
	$proc = proc_open('opencode session export ' . escapeshellarg($id),
		array(0 => array('file', DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null', 'r'), 1 => array('file', $tmp, 'w'), 2 => array('file', $tmp . '.err', 'w')), $pipes);
	$code = is_resource($proc) ? proc_close($proc) : 127;
	$json = (string) @file_get_contents($tmp);
	@unlink($tmp); @unlink($tmp . '.err');
	return array($code, $json);
}

// 6. Export the sessions next to the repo folder
$name = basename($root) . '-submission';
$outdir = dirname($root);
$exports = array();
$elsewhere = array();
$failed = array();
$root_real = rtrim(str_replace('\\', '/', $root), '/');
foreach ($sessions as $s) {
	list($code, $json) = export_session($s);
	$data = json_decode($json, TRUE);
	if ($code !== 0 || $data === NULL) {
		// often a session from another clone that is still running; only fatal if nothing from this folder exports
		$failed[] = $s;
		continue;
	}
	// OpenCode groups sessions by repository, so other clones of this repo show up too. Keep this folder's only.
	$dir = isset($data['info']['location']['directory']) ? $data['info']['location']['directory'] : '';
	$dir_real = rtrim(str_replace('\\', '/', (string) realpath($dir)), '/');
	if ($dir_real !== '' && ($dir_real === $root_real || strpos($dir_real . '/', $root_real . '/') === 0)) {
		$exports[$s . '.json'] = $json;
	} else {
		$elsewhere[] = "$s ($dir)";
	}
}
if (empty($exports)) {
	fwrite(STDERR, "No OpenCode session from this folder could be exported.\n"
		. ($failed ? "Couldn't export: " . implode(', ', $failed) . " (quit OpenCode and run this again, or export by hand: opencode session export <ID> > ../<ID>.json)\n" : '')
		. ($elsewhere ? "Sessions from other folders: " . implode(', ', $elsewhere) . "\n" : ''));
	exit(1);
}
if ($failed) {
	$warnings[] = "Couldn't export session(s) " . implode(', ', $failed) . ". If one of them is yours, quit OpenCode and run this again.";
}
$sessions = array_map(function ($f) { return substr($f, 0, -5); }, array_keys($exports));
if ($elsewhere) {
	$warnings[] = "Left out session(s) started in other folders: " . implode(', ', $elsewhere) . ". If you worked there too, say so in the form.";
}

$repo_url = run('git remote get-url origin');
$repo_url = preg_replace('#^git@github\.com:#', 'https://github.com/', $repo_url);
$repo_url = preg_replace('#\.git$#', '', $repo_url);
$summary = "Repo URL: $repo_url\n"
	. "Last commit: " . run('git log -1 --format=%cI') . "\n"
	. "Session ID(s): " . implode(', ', $sessions) . "\n"
	. "OpenCode version: $version\n"
	. "PHP version: " . PHP_VERSION . "\n"
	. "OS: " . php_uname('s') . ' ' . php_uname('r') . "\n";

if (class_exists('ZipArchive')) {
	$zip_path = $outdir . DIRECTORY_SEPARATOR . $name . '.zip';
	@unlink($zip_path);
	$zip = new ZipArchive();
	if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
		fwrite(STDERR, "Couldn't create $zip_path\n");
		exit(1);
	}
	foreach ($exports as $file => $json) {
		$zip->addFromString($file, $json);
	}
	$zip->addFromString('summary.txt', $summary);
	$zip->close();
	$saved = "Upload this file in the form:\n  $zip_path";
} else {
	$dir = $outdir . DIRECTORY_SEPARATOR . $name;
	@mkdir($dir);
	foreach ($exports as $file => $json) {
		file_put_contents($dir . DIRECTORY_SEPARATOR . $file, $json);
	}
	file_put_contents($dir . DIRECTORY_SEPARATOR . 'summary.txt', $summary);
	$saved = "Your PHP can't make zip files, so the export is in this folder:\n  $dir\nZip that folder yourself and upload the zip in the form.";
}

echo "Ready.\n\n$saved\n\nPaste this into the form:\n\n$summary\n";
foreach ($warnings as $w) {
	echo "Note: $w\n";
}
exit(0);
