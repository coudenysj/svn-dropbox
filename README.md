# svn-dropbox

This little script can be used to simulate dropbox behaviour with Subversion.

The script can run every 2 minutes (or so) and will check for changes in the
supplied directories and add/delete/commit those to Subversion.

`svn status` is used a lot to figure out what to do and the missing status is
used to delete a file on the server as wel.

## workflow

* svn up --accept mine-full (with conflict resolution: use mine, because their version is already in Subversion)
* svn status
** A: ok
** D: ok
** M: ok
** C: not possile (log)
** X: not possile (log)
** I: ok (ignored)
** !: mark deleted (missing)
** ?: add (non-versioned)
** ~: not possible (log)
* svn commit

## notes

* Make sure the svn:ignore list is complete (lock files)
* Keep an eye on your process list

## todo

* See if a file in the status list is locked (os level) and abort everything