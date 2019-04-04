# flaravel-generator

## 下载

```
composer require flaravel/generator
```

## 示例

> php artisan flaravel:make Members --migrate="name:string(100):index,description:text:nullable,subscriber_count:integer:unsigned:default(0)" --f="System"

```
----------- flaravel: Member--START -----------

+ ./app/Models//Member.php
+ ./app/Http/Controllers//MembersController.php
+ ./app/Http/Requests/Request.php
+ ./app/Http/Requests/MemberRequest.php
x ./routes/api.php (Skipped)

----------- flaravel: Member--END -------------
dump autoload successfully!
```

