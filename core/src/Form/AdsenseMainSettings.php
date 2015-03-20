<?php

/**
 * @file
 * Contains \Drupal\adsense\Form\AdsenseMainSettings.
 */

namespace Drupal\adsense\Form;

use Drupal\Component\Utility\String;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

use Drupal\adsense\AdsenseAdBase;

class AdsenseMainSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'adsense_main_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['adsense.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    module_load_include('inc', 'adsense', 'help/adsense.help');
    module_load_include('inc', 'adsense', 'includes/adsense.search_options');

    $config = \Drupal::config('adsense.settings');

    $form['help'] = [
      '#type' => 'details',
      '#open' => FALSE,
      '#title' => t('Help and instructions'),
    ];

    $form['help']['help'] = ['#markup' => adsense_help_text()];

    $form['adsense_unblock_ads'] = [
      '#type' => 'checkbox',
      '#title' => t('Display anti ad-block request?'),
      '#default_value' => $config->get('adsense_unblock_ads'),
      '#description' => t("EXPERIMENTAL! Enabling this feature will add a mechanism that tries to detect when adblocker software is in use, displaying a polite request to the user to enable ads on this site. [!moreinfo]", [
        '!moreinfo' => \Drupal::l(t('More information'), Url::fromUri('http://easylist.adblockplus.org/blog/2013/05/10/anti-adblock-guide-for-site-admins'))
        ]),
    ];

    $form['adsense_test_mode'] = [
      '#type' => 'checkbox',
      '#title' => t('Enable test mode?'),
      '#default_value' => $config->get('adsense_test_mode'),
      '#description' => t('This enables you to test the AdSense module settings. This can be useful in some situations: for example, testing whether revenue sharing is working properly or not without having to display real ads on your site. It is best to test this after you log out.'),
    ];

    $form['adsense_disable'] = [
      '#type' => 'checkbox',
      '#title' => t('Disable Google AdSense ads?'),
      '#default_value' => $config->get('adsense_disable'),
      '#description' => t('This disables all display of Google AdSense ads from your web site. This is useful in certain situations, such as site upgrades, or if you make a copy of the site for development and test purposes.'),
    ];

    $form['adsense_placeholder'] = [
      '#type' => 'checkbox',
      '#title' => t('Placeholder when ads are disabled?'),
      '#default_value' => $config->get('adsense_placeholder'),
      '#description' => t('This causes an empty box to be displayed in place of the ads when they are disabled.'),
    ];

    $form['adsense_placeholder_text'] = [
      '#type' => 'textarea',
      '#title' => t('Placeholder text to display'),
      '#default_value' => $config->get('adsense_placeholder_text'),
      '#rows' => 3,
      '#description' => t('Enter any text to display as a placeholder when ads are disabled.'),
    ];

    $form['secret'] = [
      '#type' => 'details',
      '#open' => FALSE,
      '#title' => t('Undocumented options'),
      '#description' => t("Warning: Use of these options is AT YOUR OWN RISK. Google will never generate an ad with any of these options, so using one of them is a violation of Google AdSense's Terms and Conditions. USE OF THESE OPTIONS MAY RESULT IN GETTING BANNED FROM THE PROGRAM. You may lose all the revenue accumulated in your account. FULL RESPONSIBILITY FOR THE USE OF THESE OPTIONS IS YOURS. In other words, don't complain to the authors about getting banned, even if using one of these options was provided as a solution to a reported problem."),
    ];

    $form['secret']['agreed'] = [
      '#type' => 'details',
      '#open' => FALSE,
      '#title' => t('I agree'),
    ];

    $form['secret']['agreed']['adsense_secret_language'] = [
      '#type' => 'select',
      '#title' => t('Language to display ads'),
      '#default_value' => $config->get('adsense_secret_language'),
      '#options' => array_merge([
        '' => 'Set by Google'
        ], AdsenseAdBase::adsenseLanguages()),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::configFactory()->getEditable('adsense.settings');
    $form_state->cleanValues();

    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, String::checkPlain($value));
    }
    $config->save();
  }

}
