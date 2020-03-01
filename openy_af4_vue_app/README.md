## Vue CLI

Installation `npm install -g @vue/cli`

Check version `vue --version` => @vue/cli 4.0.5

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
```

# vue default readme

## Project setup
```
npm install
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```

### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).
