# ペチャクチャ

## 環境について

### staging
* http://ec2-18-176-93-3.ap-northeast-1.compute.amazonaws.com/
  * ID: pechakucha
  * Password: pechakucha

### production
* 現状なし

## 環境構築について
TODO: 記載する

## デプロイについて

[deployer](https://deployer.org)を使っていて、検証環境へは次の方法でデプロイできます。
デプロイ用ユーザー用の鍵が必要なので、関係者からもらってください。

```
$ ./vendor/bin/dep deploy staging
```

