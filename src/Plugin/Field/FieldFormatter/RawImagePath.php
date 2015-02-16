<?php /**
 * @file
 * Contains \Drupal\manati_custom_formatters\Plugin\Field\FieldFormatter\RawImagePath.
 */

namespace Drupal\manati_custom_formatters\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * @FieldFormatter(
 *  id = "raw_image_path",
 *  label = @Translation("Raw Path Image"),
 *  description = @Translation("Display the absolute url of the image file"),
 *  field_types = {"image"}
 * )
 */
class RawImagePath extends ImageFormatter {


  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    unset($element['image_link']);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    return parent::settingsSummary();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items) {
    $elements = array();
    $image_style_setting = $this->getSetting('image_style');
    // Collect cache tags to be added for each item in the field.
    $cache_tags = array();
    if (!empty($image_style_setting)) {
      $image_style = entity_load('image_style', $image_style_setting);
      $cache_tags = $image_style->getCacheTags();
    }
    foreach ($items as $delta => $item) {
      if ($item->entity) {
        $image_uri = $item->entity->getFileUri();
        if (!empty($image_style)) {
          $path = $image_style->buildUrl($image_uri);
          if (!file_exists($path)) {
            $image_style->createDerivative($image_uri, $path);
          }
        }
        else {
          $path = file_create_url($image_uri);
        }
        $elements[$delta] = array(
          '#type' => 'markup',
          '#markup' => $path,
          '#cache' => array(
            'tags' => $cache_tags,
          ),
        );
      }
    }
    return $elements;
  }

}
