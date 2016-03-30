# OneThink
One Think based on Think PHP 3.2.3, remove the articles, only keep RBAC module.

引用Think PHP 3.2.3，并且删除OT对原有TP的库的引用。没有保留数据库备份等功能。

使用指南：

1. 如果需要修改**数据库配置**相关信息，需要修改`Common/Conf/db.php`,以及`User/Conf/config.php`，根据环境变量设置不同的数据库切换。
2. 如果要修改数据库的**表前缀**，需要替换`.sql`文件中的前缀，并修改`Application/User/Conf/config.php`下表前缀配置。
3. 如果需要修改后台LOGO，替换相应尺寸的图片到对应的目录。
4. 后台新增控制器尽量继承GlobalController，里面实现了通用的增删改查等。大部分情况都只需要写模板和模型。
5. 在项目根目录有`password.php`文件，线上需删除此文件，在部署时可利用此文件对初始用户`admin`进行密码修改。
6. 可修改用户加密密钥信息，修改`Common/Conf/config.php`和`User/Conf/config.php`两个文件。
