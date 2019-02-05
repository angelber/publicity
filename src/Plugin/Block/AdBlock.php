<?php 
namespace Drupal\publicity\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Component\Utility\Xss;
/** 
 * Providea a 'Render Publicity' block
 * 
 * @Block(
 *  id = "publicity_block",
 *  admin_label = @translation("Custom Ad Block"),
 * )
 */
class AdBlock extends BlockBase{
    /**
     * {@inheritdoc}
     */
    public function build(){
        $build = [];
        $build['ad']=[
            '#theme'=>'publicity_render',
                'library'=>[
                    'publicity/renderAd',
                ]
        ];
        return $build;
    }
}