# README

## Vue CLI

Installation `npm install -g @vue/cli`

Check version `vue --version` =&gt; @vue/cli 4.0.5

## Library build

Always build in production mode before committing changes.

Production mode:
```shell script
npm run library
```

Development mode - watching for changes:
```shell script
npm run library-watch
```

## Committing code

Always apply linting rules and build in production mode before committing changes.
```shell script
npm run lint
npm run library
````

## Customize configuration

See [Configuration Reference](https://cli.vuejs.org/config/).
