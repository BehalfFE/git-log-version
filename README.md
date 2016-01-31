# git-log-version build

cli tool to create git log based reports and label pivotal stories according to the report

```php index.php build ```

Creates the git-log-version.phar package file that is executable. Note that you will need a PHP executable and the git-log-version.phar file will be created in the current directory.

```git-log-version report <git-commit> [delimiter] <git-commit> ```

Report action returns the git log entries that have an opening pivotal id of the form [<story-id>] or [<project-id>-<story-id>]. Merges and other messages are ignored. This operation references the current working directory only.

```git-log-version label <pivotal-label>```

Label action labels all stories retrieved from STDIN. Stories are expected in the report format above. This operation references the current working directory only.

# Common use cases:

```git-log-version report tag tag2 > report.log```

Will generate a git-log messges report and write it into the report.log file

```git-log-version label my-release-v1.0 < report.log```

Will label all git log entries found in report.log

```git-log-version report tag tag2 | git-log-version label my-release-v1.0```

Piping the result from the Report action will feed directly in the label command
