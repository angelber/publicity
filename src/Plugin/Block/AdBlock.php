<?php 
namespace Drupal\publicity\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\node\Entity;
use Drupal\Core\Config;
/** 
 * Providea a 'Render Publicity' block
 * 
 * @Block(
 *  id = "publicity_block",
 *  admin_label = @translation("Ad Block"),
 * )
 */
class AdBlock extends BlockBase{
    /**
     * {@inheritdoc}
     */
    public function build(){
    $build['adBlock']= [
        '#type'=>'container',
        '#attributes'=>[
            'class'=>[
                'renderAd',
            ]
        ]
    ];
    return $build;
    }
}
