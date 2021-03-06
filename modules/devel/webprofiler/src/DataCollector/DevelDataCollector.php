<?php

/**
 * Contains \Drupal\webprofiler\DataCollector\DevelDataCollector;
 */

namespace Drupal\webprofiler\DataCollector;

use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\webprofiler\DrupalDataCollectorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class DevelDataCollector
 */
class DevelDataCollector extends DataCollector implements DrupalDataCollectorInterface {

  use StringTranslationTrait, DrupalDataCollectorTrait;

  /**
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  private $menuLinkTree;

  /**
   * @param \Drupal\Core\Menu\MenuLinkTreeInterface $menuLinkTree
   */
  public function __construct(MenuLinkTreeInterface $menuLinkTree) {
    $this->menuLinkTree = $menuLinkTree;
  }

  /**
   * {@inheritdoc}
   */
  public function collect(Request $request, Response $response, \Exception $exception = NULL) {
    $this->data['links'] = $this->develMenuLinks();
  }

  /**
   * @return string
   */
  public function getLinks() {
    return $this->data['links'];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'devel';
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->t('Development');
  }

  /**
   * {@inheritdoc}
   */
  public function hasPanel() {
    return FALSE;
  }

  /**
   * @return array
   *    Array containing Devel Menu links
   */
  protected function develMenuLinks() {
    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(1)->onlyEnabledLinks();
    $tree = $this->menuLinkTree->load('devel', $parameters);

    $manipulators = array(
      array('callable' => 'menu.default_tree_manipulators:checkAccess'),
      array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
    );
    $tree = $this->menuLinkTree->transform($tree, $manipulators);

    $links = array();

    foreach ($tree as $item) {
      if ($item->access->isAllowed()) {
        $links[] = array(
          'title' => $item->link->getTitle(),
          'description' => $item->link->getDescription(),
          'url' => $item->link->getUrlObject()->toString(),
        );
      }
    }

    return $links;
  }

  /**
   * Returns the collector icon in base64 format.
   *
   * @return string
   *   The collector icon.
   */
  public function getIcon() {
    return 'iVBORw0KGgoAAAANSUhEUgAAABYAAAAcCAYAAABlL09dAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNS4xIE1hY2ludG9zaCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2RTdCREU2NUVFQUUxMUU1QTc4Q0Q0OEU5RUY0N0YwMyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo2RTdCREU2NkVFQUUxMUU1QTc4Q0Q0OEU5RUY0N0YwMyI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjZFN0JERTYzRUVBRTExRTVBNzhDRDQ4RTlFRjQ3RjAzIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjZFN0JERTY0RUVBRTExRTVBNzhDRDQ4RTlFRjQ3RjAzIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+6sNOKAAAAPVJREFUeNpi/P//PwMtABMDjcCowbQ3mNHe3h6XnBUQTwJiTRzy14E4+8CBAyexSbLgsZQbiI2BOBCH/HqoGgZSDYaBDXjk/uMzuBaLzeVI7FV4DN7n4OAAojvRxL+CwvgnkMFG5bj7BUoVv4lUfBWIrxGp9jcoKM4DsQABhZ+A2BPqs61AzEVA/XuQwT1AzElA4Q8g/gJN951EBN13kMFLgJiHgMJvQCwNxOxAvJwIg7+wEBnGv4D4M1TtLyIMBocxMQUyKExnAzEzEHMQof4/C5GxDHJh4qAp3VhpYC4rKCieAbEQlQ1+yzhamY4aTD+DAQIMAFv+MFaJEyYhAAAAAElFTkSuQmCC';
  }
}
