# 剁手党网购系统

## 后端环境说明与安装指南：

* 操作系统：Ubuntu 14.04 x64
* 服务器软件：Nginx + MySQL Server + PHP 7.0
* 安装：sudo apt-get install nginx mysql-server
* 安装数据库

 **下文中请将单引号及其中的内容替换为自己定义的名称**

* 在 MySQL 中新建数据库和用户，并分配权限
	* `CREATE DATABASE 'databasename' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
	* `GRANT ALL PRIVILEGES ON 'databasename'.* TO 'username'@localhost IDENTIFIED BY ''password'';`
* 刷新权限
	* `FLUSH PRIVILEGES;` 
* 在数据库中运行`database.sql`，创建数据库
	* `USE 'databasename';`
	* `SOURCE initialize.sql;`
* 网站 `_api/config` 目录下复制文件 `config.sample.php` 为 `config.php`
* 编辑 `config.php` 文件，输入 MySQL 用户名、密码和数据库名，完成数据库配置

* 安装website.conf中的Nginx配置，注意修改PHP监听路径
* 开始运行：service nginx restart

## 前端环境说明与安装指南：

* 操作系统：Ubuntu 14.04 x64
* 安装Node：sudo apt-get install npm
* 安装bower：npm install bower
* 项目文件夹执行bower install以安装前端依赖包polymer, webcomponentjs, paper-elements和iron-elements