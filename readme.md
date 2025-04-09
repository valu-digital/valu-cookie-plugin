# Valu Cookie Plugin

## Description
A plugin to view cookies and visitor opt-out percentages using Cookiebot API.

This plugin is made to help Wordpress editors observing Cookiebot opt-outs and cookie descriptions.

## Installation

### Set  API key

API key can be found in [Cookiebot admin panel](https://manage.cookiebot.com/en/manage) under `Settings -> Your scripts -> API key -> Show`.

Install plugin using git clone. Cookiebot API Key can be set in wp-config.php using constant `VALU_COOKIEPLUGIN_API_KEY`.

```
define( 'VALU_COOKIEPLUGIN_API_KEY', '<API_KEY_FROM_COOKIEBOT>' );
```

### Apply settings

`wp-admin/admin.php?page=Valu_Cookie_Plugin_settings`

**Cookiebot Domain group ID:** [Cookiebot admin panel](https://manage.cookiebot.com/en/manage) `Settings -> Your scripts -> Domain Group ID`

**Cookiebot domain:** [Cookiebot admin panel](https://manage.cookiebot.com/en/manage) `Settings -> Domains`
