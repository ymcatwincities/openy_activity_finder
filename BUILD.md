Welcome to Activity Finder

# How to build applications

When you use source code of Activity Finder you need to follow steps of how to compile latest versions of javascript applications, included within.


## Internals

Activity Finder contains three applications

- Activity Finder v3 - folder /openy_af_vue_app
- Activity Finder v4 - folder /openy_af4_vue_app
- Camp Finder - folder /openy_cf_vue_app

## Compillation

In order to compile any of abobe three apps, follow steps below, please

Go to respective folder

Run in shell

```sh
npm install
npm run build
```

After these are finished - you'd see in dist folders latest versions of compiled applications, which will be automatically used by Acitivity Finder on a site.
You would need to clear drupal and browser/cdn cache after obtaining new versions of compiled javascript files.
