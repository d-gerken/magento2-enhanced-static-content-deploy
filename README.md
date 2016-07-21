# Magento 2 Enhanced static file deploy

The enhanced static file deploy module allows a faster static file deployment.
After initial file deploy, thhis module only deploy new oder changed files.

## Usage

```
bin/magento setup:static-content:deploy
```

## Problems

This is an alpha version. If any problems occur flush the static-content cache and redeploy the static-content
 
```
bin/magento setup:static-content:flush
bin/magento setup:static-content:deploy
```