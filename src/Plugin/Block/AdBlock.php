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
    return ['#theme'=> 'node_adelelement'];
    }
}