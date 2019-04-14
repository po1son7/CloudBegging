# CloudBegging
云讨饭，基于 TrimePay

## 搭建
Example:
```
cd /www/wwwroot/donate.example.com/
git clone -b master https://github.com/WooMai/CloudBegging ./tmp
mv ./tmp/.git ./
git reset --hard HEAD
rm -rf ./tmp
chmod 777 ./data
cp config.example.php config.php
vi config.php
```
