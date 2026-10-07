<?php

namespace Drupal\views_fieldsets\Hook;

use Drupal\Core\Render\Element;
use Drupal\views_ui\ViewUI;
use Drupal\views_fieldsets\Plugin\views\field\Fieldset;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Hook implementations for views_fieldsets.
 */
class ViewsFieldsetsHooks {
  use StringTranslationTrait;

  /**
   * Implements hook_help().
   */
  #[Hook('help')]
  public function help($route_name, RouteMatchInterface $route_match) {
    if ($route_name == 'help.page.views_fieldsets') {
      $output = '<p>' . $this->t('Creates fieldset (and details and div) in Views fields output, to group fields,
      by adding a new field: "Global: Fieldset" and a few preprocessors. Also
      introduces a new template: views-fieldsets-fieldset.tpl.php where you can
      customize your fieldset output.
      ') . '</p>';
      $output .= '<h3>' . $this->t('For a full description of the module, visit the project page:') . '</h3>';
      $output .= $this->t('https://www.drupal.org/project/views_fieldsets') . '<br />';
      $output .= '<h3>' . $this->t('To submit bug reports and feature suggestions, or to track changes:') . '</h3>';
      $output .= $this->t('https://www.drupal.org/project/issues/views_fieldsets') . '<br />';
      return $output;
    }
  }

  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public static function theme() {
    $vars = [
      'fields' => [],
      'attributes' => [],
      'show_fieldset' => FALSE,
    ];
    $path = \Drupal::service('extension.list.module')->getPath('views_fieldsets');
    $hooks['views_fieldsets_fieldset'] = [
      'variables' => array_merge($vars, [
        'legend' => '',
        'collapsible' => TRUE,
        'collapsed' => FALSE,
      ]),
      'template' => 'templates/views-fieldsets-fieldset',
      'path' => $path,
    ];
    $hooks['views_fieldsets_details'] = [
      'variables' => array_merge($vars, [
        'legend' => '',
        'collapsed' => FALSE,
      ]),
      'template' => 'templates/views-fieldsets-details',
      'path' => $path,
    ];
    $hooks['views_fieldsets_div'] = [
      'variables' => $vars,
      'template' => 'templates/views-fieldsets-div',
      'path' => $path,
    ];
    return $hooks;
  }

  /**
   * Implements hook_views_data().
   */
  #[Hook('views_data')]
  public function viewsData() {
    $data['views']['fieldset'] = [
      'title' => $this->t('Fieldset'),
      'help' => $this->t('Create a group of fields.'),
      'field' => [
        'id' => 'fieldset',
      ],
    ];
    return $data;
  }

  /**
   * Implements hook_preprocess_views_view_fields().
   */
  #[Hook('preprocess_views_view_fields')]
  public static function preprocessViewsViewFields(&$vars) {
    $view = $vars['view'];
    Fieldset::replaceFieldsetHandlers($view, $vars['fields'], $vars['row']);
  }

  /**
   * Implements hook_views_ui_display_tab_alter().
   */
  #[Hook('views_ui_display_tab_alter')]
  public static function viewsUiDisplayTabAlter(&$build, ViewUI $ui_view, $display_id) {
    $view = $ui_view->getExecutable();
    // Re-init handlers.
    $view->inited = FALSE;
    $view->build($display_id);
    $ui_view->set('executable', $view);
    if (Fieldset::isFieldsetView($view)) {
      $fieldsets = Fieldset::getAllFieldsets($view);
      foreach ($build['details']['columns']['first']['fields']['fields'] as $field_name => &$renderable) {
        // Noticeable fieldsets.
        if (isset($fieldsets[$field_name])) {
          $renderable['#class'][] = 'views-fieldsets-fieldset';
        }
        // Indentation for all fields.
        $renderable['#class'][] = 'views-fieldsets-level-' . count(Fieldset::getFieldParents($view, $field_name));
        unset($renderable);
      }
      $build['details']['#attached']['library'][] = 'views_fieldsets/admin';
    }
  }

  /**
   * Implements hook_preprocess_views_ui_display_tab_setting().
   */
  #[Hook('preprocess_views_ui_display_tab_setting')]
  public static function preprocessViewsUiDisplayTabSetting(&$vars) {
    // Copy #class from views_fieldsets_views_ui_display_tab_alter()
    // to renderable #attributes.
    if (!empty($vars['class'])) {
      $vars['attributes'] += [
        'class' => [],
      ];
      $vars['attributes']['class'] = array_merge($vars['attributes']['class'], $vars['class']);
    }
  }

  /**
   * Implements hook_form_FORM_ID_alter() for views_ui_rearrange_form().
   */
  #[Hook('form_views_ui_rearrange_form_alter')]
  public static function formViewsUiRearrangeFormAlter(&$form, &$form_state) {
    $field_display = $form_state->getStorage()['type'];
    if ($field_display === 'field') {
      $ui_view = $form_state->get('view');
      $display_id = $form_state->get('display_id');
      $view = $ui_view->getExecutable();
      $view->inited = FALSE;
      $view->build($display_id);
      $ui_view->set('executable', $view);
      $fieldsets = Fieldset::getAllFieldsets($view);
      $debug_tabledrag = [];
      foreach (Element::children($form['fields']) as $field_name) {
        $row =& $form['fields'][$field_name];
        if (isset($fieldsets[$field_name])) {
          $row['#attributes']['class'][] = 'views-fieldsets-fieldset';
        }
        else {
          $row['#attributes']['class'][] = 'tabledrag-leaf';
        }
        $depth = count(Fieldset::getFieldParents($view, $field_name));
        $row['name'] = [
          'indent' => $depth > 0 ? [
            '#theme' => 'indentation',
            '#size' => $depth,
          ] : [],
          'name' => $row['name'],
          'field_name' => [
            '#type' => 'hidden',
            '#value' => $field_name,
            '#attributes' => [
              'class' => [
                'field-name',
              ],
            ],
          ],
          'hierarchy' => array_merge($debug_tabledrag, [
            '#type' => 'hidden',
            '#default_value' => Fieldset::getFieldParent($view, $field_name),
            '#attributes' => [
              'class' => [
                'hierarchy',
              ],
            ],
          ]),
          'depth' => array_merge([
            '#type' => 'hidden',
            '#default_value' => $depth,
            '#attributes' => [
              'class' => [
                'depth',
              ],
            ],
          ]),
        ];
        unset($row);
      }
      $form['fields']['#tabledrag'] = [];
      $form['fields']['#tabledrag'][] = [
        'action' => 'match',
        'relationship' => 'parent',
        'group' => 'hierarchy',
        'subgroup' => 'hierarchy',
        'source' => 'field-name',
        'hidden' => FALSE,
      ];
      $form['fields']['#tabledrag'][] = [
        'action' => 'depth',
        'relationship' => 'group',
        'group' => 'depth',
        'hidden' => FALSE,
      ];
      $form['fields']['#tabledrag'][] = [
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'weight',
      ];
      $form['actions']['submit']['#submit'][] = 'views_fieldsets_views_ui_rearrange_form_submit';
    }
  }

  /**
   * Implements hook_theme_suggestions_HOOK().
   */
  #[Hook('theme_suggestions_paragraph')]
  public static function themeSuggestionsParagraph(array $variables) {
    $suggestions = [];
    $paragraph = $variables['elements']['#paragraph'];
    $sanitized_view_mode = strtr($variables['elements']['#view_mode'], '.', '_');
    $suggestions[] = 'paragraph__' . $sanitized_view_mode;
    $suggestions[] = 'paragraph__' . $paragraph->bundle();
    $suggestions[] = 'paragraph__' . $paragraph->bundle() . '__' . $sanitized_view_mode;
    return $suggestions;
  }

}
