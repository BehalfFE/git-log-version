<?php
/**
 * Created by PhpStorm.
 * User: yonman
 * Date: 04/02/2016
 * Time: 4:47 PM
 */

namespace GitLogVersion;


class StoriesExtractor {

    public function collect() {
        $entries = [];
        while ($entry = fgets(STDIN)) {
            $entries[] = $entry;
        }

        $entries = array_filter($entries, function ($item) {
            return preg_match('/^\[\d+\]/', $item) > 0;
        });

        if (count($entries) == 0) {
            throw new \Exception("No input found, use report command to generate input");
        }

        return array_map(function ($item) {
            $matches = array();
            /// with projects
            if (preg_match('/^\[(\d+)-(\d+)\]/', $item, $matches) > 1) {
                return $matches[2];
            }
            /// no project IDs
            if (preg_match('/^\[(\d+)\]/', $item, $matches) > 0) {
                return $matches[1];
            }
            return null;
        }, $entries);
    }
}