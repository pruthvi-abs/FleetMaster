<?php

/**
 * @file
 * Provides an additional config form for theme settings.
 */

use Drupal\Core\Form\FormStateInterface;

function yg_logistics_form_system_theme_settings_alter(array &$form, FormStateInterface $form_state) {
  $form['yg_logistics_settings']= array(
    '#type' => 'details',
    '#title' => t('YG Logistics Settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#group' => 'bootstrap',
    '#weight' => 10,
  );

#social links    
  $form['yg_logistics_settings']['social_links'] = array(
    '#type' => 'details',
    '#title' => t('Social Links'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['yg_logistics_settings']['social_links']['social_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Social Links Title'),
    '#description' => t('Please enter footer social title'),
    '#default_value' => theme_get_setting('social_title'),
    '#required' => TRUE,
  ); 
  $form['yg_logistics_settings']['social_links']['facebook_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Facebook'),
    '#description' => t('Please enter your facebook url'),
    '#default_value' => theme_get_setting('facebook_url'),
  );
   $form['yg_logistics_settings']['social_links']['twitter_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Twitter'),
    '#description' => t('Please enter your twitter url'),
    '#default_value' => theme_get_setting('twitter_url'),
  );
  $form['yg_logistics_settings']['social_links']['google_plus_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Google Plus'),
    '#description' => t('Please enter your google-plus url'),
    '#default_value' => theme_get_setting('google_plus_url'),
  );
#Footer-contact-details
  $form['yg_logistics_settings']['contact'] = array(
    '#type' => 'details',
    '#title' => t('Contact Info'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  );
  $form['yg_logistics_settings']['contact']['contact_title'] = array(
    '#type' => 'textfield',
    '#title' => t('Contact Title'),
    '#description' => t('Please enter footer contact title'),
    '#default_value' => theme_get_setting('contact_title'),
    '#required' => TRUE,
  ); 
  $news_post_desc = theme_get_setting('address');
  $form['yg_logistics_settings']['contact']['address'] = array(
    '#type' => 'text_format',
    '#title' => t('Address'),
    '#description' => t('Please enter address'),
    '#default_value' => $news_post_desc['value'],
    '#foramt'        => $news_post_desc['format'],
  );
  $form['yg_logistics_settings']['contact']['email'] = array(
    '#type' => 'email',
    '#title' => t('Email'),
    '#description' => t('Please enter email-id'),
    '#default_value' => theme_get_setting('email'),
  );
  $form['yg_logistics_settings']['contact']['phone'] = array(
    '#type' => 'textfield',
    '#title' => t('Phone Number'),
    '#description' => t('Please enter phone number'),
    '#default_value' => theme_get_setting('phone'),
  );
    
}
 
