# composer 

## composer init  后续步骤详解

    root@dduan:/home/dduan/test_composer# php composer.phar init
    Do not run Composer as root/super user! See https://getcomposer.org/root for details
    
      Welcome to the Composer config generator
      
    This command will guide you through creating your composer.json config.
    
    # 1. 输入项目命名空间
    # 注意<vendor>/<name> 必须要符合 [a-z0-9_.-]+/[a-z0-9_.-]+
    Package name (<vendor>/<name>) [root/test_composer]:yourname/projectname
    
    # 2. 项目描述
    Description []:这是一个测试composer init 项目
    
    # 3. 输入作者信息，直接回车可能出现如下提示，有的系统可以直接回车，具体为什么？这里不详细介绍
     Invalid author string.  Must be in the format: John Smith <john@example.com>
    # 3.1. 注意必须要符合 John Smith <john@example.com>
    Author [, n to skip]: John Smith <john@example.com>
    
    # 4. 输入最低稳定版本，stable, RC, beta, alpha, dev
    Minimum Stability []:dev
    
    # 5. 输入项目类型
    Package Type (e.g. library, project, metapackage, composer-plugin) []:library
    
    # 6. 输入授权类型
    License []:
    
    Define your dependencies.
    
    # 7. 输入依赖信息
    Would you like to define your dependencies (require) interactively [yes]?
    
    # 7.1. 如果需要依赖，则输入要安装的依赖
    Search for a package:php
    
    # 7.2. 输入版本号
    Enter the version constraint to require (or leave blank to use the latest version): >=5.4.0
    
    #  如需多个依赖，则重复以上两个步骤(7.1/7.2)
    Search for a package:
    
    # 8. 是否需要require-dev，
    Would you like to define your dev dependencies (require-dev) interactively [yes]?
    
    
    {
        "name": "dduan/test_compser",
        "description": "这是一个测试composer init 项目",
        "type": "library",
        "require": {
            "php": ">=5.4.0"
        },
        "authors": [
            {
                "name": "John Smith",
                "email": "john@example.com"
            }
        ],
        "minimum-stability": "dev"
    }
    # 9. 是否生成composer.json
    Do you confirm generation [yes]?
    
    # 现在安装依赖项吗
    Would you like to install dependencies now [yes]?
    
 # composer 生成包步骤
 
 1. 输入项目命名空间
 2. 项目描述
 3. 输入作者信息
 4. 输入最低稳定版本
 5. 输入项目类型
 6. 输入授权类型
 
 
 # linux
 
    linux查看端口号命令lsof -i:9501
    
    
# reactor模型

    reactor ->异步IO（属于）
    异步IO->reactor(包括)
    
# 可读可写事件

    数据包进入linux内核之后，linux内核像资源流写入数据，这个时候资源流的文件称为可读，因为有数据可以被读了
    