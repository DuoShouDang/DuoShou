所有POST请求均使用在body中放置JSON String的方式，返回值也是JSON格式，默认格式如下：
`{success: true/false, successInfo[, errorInfo]}`

术语定义：
- 返回xxx是指successInfo=xxx
- 额外返回xxx是指在跟successInfo并列的层级上返回的额外键值对

模型定义：
`GOOD = {gid, name, cid/*类别*/, info = {id:{name, image, price, remains/*剩余库存*/}}, abstract, description, timestamp/*上架时间*/}`
`FILE = {fid, fhash, size, file_name, ext}`

其他说明：

- 身份认证：在HTTP Header中携带Authorization字段，值为登录或注册时颁发的token

#用户账户操作

##用户注册
`POST /api/account/register`
`{email, password, username}`
`如果成功，返回token`
##用户登录
`POST /api/account/login`
`{email, password}`
`如果成功，返回{token, type}`
##退出登录
`POST /api/account/logout`

#浏览商品

##展示所有商品
`GET /api/goods/all/{page}/{num_per_page}`
`返回{goods:[{GOOD-GOOD.description}], total_page}`

##按类别查看商品
`GET /api/goods/category/{cid}/{page}/{num_per_page}`
`返回{goods:[{GOOD-GOOD.description}], total_page}`

##搜索商品
`GET /api/goods/search/{keyword}/{page}/{num_per_page}`
`返回{goods:[{GOOD-GOOD.description}], total_page}`

##查看商品详情
`GET /api/goods/{good_id}`
`返回{GOOD-GOOD.abstract}`

#类别

##查看所有类别
`GET /api/category`
`返回[{cid, name}]`

#购物车

##浏览购物车商品
`GET /api/shoppingcart`
`返回[{good_id, number, sort_identifier, info:{sort_identifier:{name, image, price}}}, product_info:{name, image, price, product_name}]`
##添加商品
`POST /api/shoppingcart/{good_id}/{sort_id}`
`Body可选：{number}，默认为1`
##修改商品数量
`PUT /api/shoppingcart/{good_id}/{sort_id}`
`{number}`
##删除商品
`DELETE /api/shoppingcart/{good_id}/{sort_id}`

#管理员

##管理员注册
`POST /api/admin/register`
`{email, password, username}`
`如果成功，返回token`
##管理员登录
`POST /api/admin/login`
`{email, password}`
`如果成功，返回{token, type}`
##退出登录
`POST /api/admin/logout`
##增加商品
`POST /api/admin/goods`
`{GOOD/*info字段使用 JSON 编码的字符串*/-GOOD.gid}`
`如果成功，返回{gid}`
##修改商品
`PUT /api/admin/goods`
`{GOOD/*info字段使用 JSON 编码的字符串*/}`
##删除商品
`DELETE /api/admin/goods`
`{gid}`

#文件操作
`POST_FILE /api/file/upload`
`返回{FILE}`

`GET /api/file/{fhash}`
`{binary}`

`DELETE /api/file/{fhash}`

`PUT /api/file/{flash}`

#订单操作
##创建订单
`POST /api/bill`
`{info:[{gid, sid, number, price/*单价*/}]}`
`如果成功，返回bill_id`

##查看所有订单
`GET /api/bill`
`返回[{info:[{gid, sid, number, price/*单价*/}]}, user_id, bill_id, paid/*是否已付款*/, deal/*是否已成交*/, comment/*评价*/, timestamp]`

##创建支付
`POST /api/bill/pay`
`{bill_id}`
`如果成功，返回{price}，表明支付总价`