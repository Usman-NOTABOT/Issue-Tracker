<?php

namespace Drupal\issue_tracking_system\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a 'User Issues' Block.
 *
 * @Block(
 *   id = "user_issues",
 *   admin_label = @Translation("User Issues"),
 *   category = @Translation("User Issues"),
 * )
 */
class UserIssues extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    $resultArray = [];
    $currentUserId = \Drupal::currentUser()->id();
    
    $query = \Drupal::database()->select('node__field_assignee', 'nfa');
    $query->fields('nfa', ['entity_id']);
    $query->condition ('nfa.field_assignee_target_id', $currentUserId);
    $query->range(0, 3);
    $query->orderBy('entity_id', 'DESC');
    $result = $query->execute()->fetchCol();

    //Get Issues assigned to current user and get fields
    if(!empty($result)){
      $nodes =  Node::loadMultiple($result);
      foreach ($nodes as $node) {
        $title = $node->label();
        $description = $node->get('field_description')->getValue()[0]['value'];
        $resultArray[$node->id()] = ['title' => $title, 'description' => $description];
      }
    }

    return [
      '#theme' => 'issue_tracker',
      '#cache' => ['max-age' => 0],
      '#data' => $resultArray,
    ];
  }

}
