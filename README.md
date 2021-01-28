# Open Y Activity Finder

## Versions
There are currently 2 major versions: 3 and 4. Open Y distribution has
`^3.1 || ^4.0` version constraint allowing you to choose which version you want
to use depending on the project requirements.

### Deprecations
Outdated implementations are not removed immediately allowing you to update
your projects and migrate to new components without breaking your site pages.
They are marked with `[deprecated]` notices in the next version and planned to
be removed in the future releases.

### New projects
All the new projects should get the latest available Open Y Activity Finder
release.

### Existing projects
You have a choice of either staying on the same version you use or to update to
the next version. It depends on your project requirements and customizations.
We recommend updating to the latest release if you have resources for it.

### Update from version 3.x to version 4.x
Activity Finder is a complex functionality, it connects together many different
pieces and might require additional steps to make it working. The list of
actions below outlines the major steps to get Activity Finder updated to
version 4.
- Update the codebase using composer command:
  `composer require ymcatwincities/openy_activity_finder:"^4.0"`
- Run database updates `drush -y updb`
  - Verify there were no errors and updates went fine
- Install the new "Open Y Paragraph Activity Finder" module,
  Machine name: `openy_prgf_activity_finder_4`:
  `drush en openy_prgf_activity_finder_4`
- Create new or update existing Landing Page with Activity Finder
- Add Activity Finder paragraph (replace the deprecated paragraph), configure
  it and save the page
  - Verify the page and Activity Finder functionality is working fine
- The previous version of Activity Finder used 2 landing pages with 2 paragraph
  types - one for wizard and another one for results - find and remove these
  pages
- Uninstall "OpenY Paragraph Activity Finder" module,
  Machine name: `openy_prgf_activity_finder`
- Uninstall "OpenY Paragraph Activity Finder Search" module,
  Machine name: `openy_paragraph_activity_finder_search`

## Trusted redirect host patterns
Activity Finder has a feature to track redirects to 3rd party systems. In order
to control the URLs to redirect to you should use the trusted host patterns.
This feature works similar to Drupal core trusted_host_patterns setting.

Example - add this section to the settings.php:
```
// Trusted hosts to redirect to for Activity Finder.
$settings['activity_finder_trusted_redirect_host_patterns'] = [
  '^apm\.activecommunities\.com$',
];
```
It is also recommended to disallow these paths in robots.txt:
```
# Activity Finder redirects
Disallow: /af/register-redirect/
Disallow: /index.php/af/register-redirect/
```

## Development

### How to override processResults in Activity Finder

See `openy_activity_finder.api.php`

```php
/**
 * Implements hook_activity_finder_program_process_results_alter().
 */
function custom_module_activity_finder_program_process_results_alter(&$data, NodeInterface $entity) {
  // Get formatted session data from some custom service.
  $formatted_session = \Drupal::service('ymca_class_page.data_provider')
    ->formatSessions([$entity], FALSE);
  $formatted_session = reset($formatted_session);

  // Fix pricing according to YMCA price customization.
  $data['price'] = '';
  if (!empty($formatted_session['prices'])) {
    foreach ($formatted_session['prices'] as $price) {
      $data['price'] .= implode(' ', $price) . '<br>';
    }
  }

  // Fix availability and registration according to YMCA customization.
  $messages = [
    'begun' => t('This class has begun.'),
    'will_open' => t('Registration for this class opens shortly. Please check back.'),
    'inperson' => t('Online registration is closed. Visit a YMCA branch to register.'),
    'included_in_membership' => t('Included in Membership'),
  ];

  if (isset($messages[$formatted_session['reg_state']])) {
    $data['availability_note'] = $messages[$formatted_session['reg_state']];
  }
}
```
