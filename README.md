# git-log-version build

cli tool to create git log based reports and label pivotal stories according to the report

```git-log-version build ```
Creates the git-log-version.phar package file that is executable

```git-log-version report <git-commit> [delimiter] <git-commit> ```
Report action returns the git log entries that have an opening pivotal id of the form [<story-id>] or [<project-id>-<story-id>]. Merges and other messages are ignored.

```git-log-version label <pivotal-label>```
Label action labels all stories retrieved from STDIN. Stories are expected in the report format above.

Common use case:
```git-log-version report tag tag2 | git-log-version label my-release-v1.0```
Piping the result from the Report action will feed directly in the label command
