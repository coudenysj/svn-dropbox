#!/usr/bin/env php
<?php
/**
 * @see README.md
 * @author jachimcoudenys@gmail.com
 * @license DWTFYWWI
 */
define('SVN', '/usr/bin/env svn');
define('LOG', '/var/log/svn-dropbox.log');

$directories = array(
    '/tmp/svn-repository-checkout'
);

function write($msg)
{
    file_put_contents(
        LOG, date('Y-m-d H:i:s') . ' ' . $msg . PHP_EOL, FILE_APPEND
    );
}

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        throw new \InvalidArgumentException(
            'Directory "' . $directory . '" does not exist'
        );
    }

    // update and keep my paths
    $update = array();
    exec(SVN . ' update "' . $directory . '" --accept mine-full', $update);
    if (count($update) > 1) {
        foreach ($update as $msg) {
            write('[update] ' . trim($msg));
        }
    }

    // figure out the status of every path
    $status = array();
    exec(SVN . ' status "' . $directory . '" 2>/dev/null', $status);
    if (!empty($status)) {
        foreach ($status as $line) {
            $flag = substr($line, 0, 1);
            $path = trim(substr($line, 5));
            write('[status] ' . $flag . ' ' . $path);
            switch ($flag) {
                case '?':
                    exec(SVN . ' add -q --force "' . $path . '"');
                    write('[add] ' . $path);
                    break;

                case '!':
                    exec(SVN . ' rm -q --force "' . $path . '"');
                    write('[rm] ' . $path);
                    break;

                case 'X':
                case 'C':
                case '~':
                    // not possible, log
                    break;
            }
        }
        $commit = array();
        exec(SVN . ' commit -m "" "' . $directory . '"', $commit);
        foreach ($commit as $msg) {
            write('[commit] ' . trim($msg));
        }
    }
}