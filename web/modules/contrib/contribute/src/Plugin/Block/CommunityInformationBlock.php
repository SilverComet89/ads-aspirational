<?php

namespace Drupal\contribute\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Community Information' block.
 *
 * @Block(
 *   id = "community_information_block",
 *   admin_label = @Translation("Community Information block"),
 *   category = @Translation("Community Information Block")
 * )
 */
class CommunityInformationBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    /** @var \Drupal\contribute\ContributeManagerInterface $contribute_manager */
    $contribute_manager = \Drupal::service('contribute.manager');
    if ($contribute_manager->getStatus()) {
      $build['#theme'] = 'contribute_status_report_community_info';
      $build['#account'] = $contribute_manager->getAccount(FALSE);
      $build['#membership'] = $contribute_manager->getMembership();
      $build['#contribution'] = $contribute_manager->getContribution();
    }
    return $build;
  }

}
