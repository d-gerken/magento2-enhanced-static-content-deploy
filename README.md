# Magento 2 Enhanced static contentdeploy

The enhanced static file deploy module allows a faster static content deployment. It will speed up initial the content deployment by 60%.

After initial content deploy, this module only deploy new or changed files. This will speed up your content-deploy to a few seconds. 

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