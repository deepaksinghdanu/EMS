<?php

/**
 * @file
 * Contains \Drupal\at_core\File\DirectoryOperations
 */

namespace Drupal\at_core\File;

class DirectoryOperations {

  /**
   * Build, prepare and return the path for generated files.
   *
   * @param array $file_path
   *   Numeric array of path parts (directories).
   * @return string
   *   Path to the prepared directory/s.
   */
  public function directoryPrepare($file_path) {
    $directory_path = implode('/', $file_path);
    if (!file_exists($directory_path)) {
      file_prepare_directory($directory_path, FILE_CREATE_DIRECTORY);
    }

    return $directory_path;
  }

  /**
   * Copy a directory recursively.
   *
   * @param $source
   *   The source directory.
   * @param $target
   *   The target directory.
   * @param $ignore
   *   Regex to filter out unwanted files and directories.
   */
  public function directoryRecursiveCopy($source, $target, $ignore = '/^(\.(\.)?|CVS|\.sass-cache|\.svn|\.git|\.DS_Store)$/') {
    $dir = opendir($source);
    @mkdir($target);
    while($file = readdir($dir)) {
      if (!preg_match($ignore, $file)) {
        if (is_dir($source . '/' . $file)) {
          self::directoryRecursiveCopy($source . '/' . $file, $target . '/' . $file, $ignore);
        }
        else {
          copy($source . '/' . $file, $target . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

  /**
   * Delete a folder and all files recursively.
   *
   * @param $directory
   * @return bool Returns TRUE on success, FALSE on failure
   * Returns TRUE on success, FALSE on failure
   */
  public function directoryRemove($directory) {
    if (!file_exists($directory)) {
      return false;
    }
    if (is_file($directory)) {
      return unlink($directory);
    }

    $dir = dir($directory);
    while (false !== $entry = $dir->read()) {
      if ($entry == '.' || $entry == '..') {
        continue;
      }
      self::directoryRemove("$directory/$entry");
    }
    $dir->close();

    return rmdir($directory);
  }

  /**
   * Scan directorys.
   *
   * @return array
   *   Directories below the path.
   */
  public function directoryScan($path) {
    $scan_directories = array();
    if (file_exists($path)) {
      $scan_directories = preg_grep('/^([^.])/', scandir($path));
    }

    return $scan_directories;
  }

  /**
   * Recursively glob files below the path
   * of a specified type.
   *
   * @return array globbed files
   */
  public function directoryGlob($path, array $types) {
    $files = array();
    $scan_directories = self::directoryScan($path);
    if (isset($scan_directories)) {
      foreach ($scan_directories as $directory) {
        $glob_path = $types . $directory;
        if (is_dir($glob_path)) {
          if (isset($types)) {
            foreach ($types as $type) {
              $files[$directory][$type] = array_filter(glob($glob_path . "/*.$type"), 'is_file');
            }
          }
        }
      }
    }

    return $files;
  }

}
