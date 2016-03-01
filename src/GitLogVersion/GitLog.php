<?php
/**
 * Created by PhpStorm.
 * User: yonman
 * Date: 28/01/2016
 * Time: 6:20 PM
 */

namespace GitLogVersion;


class GitLog {
    /**
     * @param $startRange
     * @param $endRange
     * @return array
     */
    public function report($startRange, $endRange) {
        $console = `git log --no-merges --pretty=format:%s {$startRange}...{$endRange} | uniq | tr -s '*'`;

        $entries = explode("\n", $console);

        $entries = array_filter($entries, function($item){
            return preg_match('/^\[\d+\]/', $item) > 0;
        });

        return $entries;
    }
}