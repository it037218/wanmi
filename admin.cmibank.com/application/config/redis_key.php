<?php

#redis 存储 string 数据类型
define('_REDIS_DATATYPE_STRING' , 'redis:string');
#redis 存储 hash 数据类型
define('_REDIS_DATATYPE_HASH' , 'redis:hash');
#redis 存储 set 数据类型
define('_REDIS_DATATYPE_SET' , 'redis:set');
#redis 存储 set 数据类型
define('_REDIS_DATATYPE_LIST' , 'redis:list');

//系统上线活期产品列表
define("_KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_" , 'longproduct:online:list:');
//系统上线快乐宝产品列表
define("_KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_" , 'klproduct:online:list:');
//系统活期预告产品列表
define("_KEY_REDIS_SYSTEM_YUGAO_LONGPRODUCT_LIST_PREFIX_" , 'longproduct:yugao:list:');
//系统快乐宝预告产品列表
define("_KEY_REDIS_SYSTEM_YUGAO_KLPRODUCT_LIST_PREFIX_" , 'klproduct:yugao:list:');
//系统活期产品列表
define("_KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_" , 'longproduct:detail:');
//系统快乐宝产品列表
define("_KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_" , 'klproduct:detail:');
//活期列队
define("_KEY_REDIS_SYSTEM_LTYPE_LIST_PREFIX_", 'ltype:list:');

//系统上线产品列表
define("_KEY_REDIS_SYSTEM_ONLINE_PRODUCT_LIST_PREFIX_" , 'product:online:list:');
//系统上线等额产品列表
define("_KEY_REDIS_SYSTEM_ONLINE_EQUALPRODUCT_LIST_PREFIX_" , 'equalproduct:online:list:');

//系统售馨产品列表
define("_KEY_REDIS_SYSTEM_SELLOUT_PRODUCT_LIST_PREFIX_" , 'product:sellout:list:');

//系统售馨产品列表
define("_KEY_REDIS_SYSTEM_SELLOUT_EQUALPRODUCT_LIST_PREFIX_" , 'equalproduct:sellout:list:');


//系统预告产品列表
define("_KEY_REDIS_SYSTEM_YUGAO_PRODUCT_LIST_PREFIX_" , 'product:yugao:list:');
//系统预告等额产品列表
define("_KEY_REDIS_SYSTEM_YUGAO_EQUALPRODUCT_LIST_PREFIX_" , 'equalproduct:yugao:list:');

//系统产品列表
define("_KEY_REDIS_SYSTEM_PRODUCT_DETAIL_PREFIX_" , 'product:detail:');

define("_KEY_REDIS_SYSTEM_EQUALPRODUCT_DETAIL_PREFIX_", 'equalproduct:detail:');

define("_KEY_REDIS_SYSTEM_PTYPE_LIST_PREFIX_", 'ptype:list:');

define("_KEY_REDIS_SYSTEM_EQUALPTYPE_LIST_PREFIX_", 'equalptype:list:');

//推荐列表
define("_KEY_REDIS_SYSTEM_RECOMMEND_LIST_PREFIX_", 'recommend:homepage:');

//精品推荐
define("_KEY_REDIS_COMPETITIVE_PREFIX_", 'recommend:competitive');

//用户信息缓存
define("_KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_" , 'user:identity:');

//用户托管信息
define("_KEY_REDIS_TG_USER_IDENTITY_DETAIL_PREFIX_" , 'tg_user:identity:');

//用户日志
define("_KEY_REDIS_USER_LOG_PREFIX_", 'userlog:info:');

define("_KEY_REDIS_SYSTEM_NOTICE_LIST_PREFIX_", 'notice:list');

define("_KEY_REDIS_SYSTEM_NEWS_LIST_PREFIX_", 'news:list');

define("_KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX", 'banner:');

define("_KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_" , 'contract:detail:');

define("_KEY_REDIS_SYSTEM_EQUALAMOUNTCONTRACT_DETAIL_PREFIX_" , 'equalamountcontract:detail:');

define("_KEY_REDIS_SYSTEM_LONGPRODUCTCONTRACT_DETAIL_PREFIX_" , 'longproductcontract:detail:');

define("_KEY_REDIS_SYSTEM_KLPRODUCTCONTRACT_DETAIL_PREFIX_" , 'klproductcontract:detail:');

define("_KEY_REDIS_SYSTEM_SELLOUT_LONGPRODUCT_LIST_PREFIX_" , 'longproduct:sellout:list:');

define("_KEY_REDIS_SYSTEM_SELLOUT_KLPRODUCT_LIST_PREFIX_" , 'klproduct:sellout:list:');

define("_KEY_REDIS_SYSTEM_ABOUTUS_LIST_PREFIX_", 'aboutus:list:');

//用户日志   收支明细
define("_KEY_REDIS_USER_LOG_PREFIX_ALL", 'userlog:info:all:');
//用户日志   收入
define("_KEY_REDIS_USER_LOG_PREFIX_IN", 'userlog:info:in:');
//用户日志   支出
define("_KEY_REDIS_USER_LOG_PREFIX_OUT", 'userlog:info:out:');
//用户日志   定期明细
define("_KEY_REDIS_USER_LOG_PREFIX_PRODUCT", 'userlog:info:product:');
//用户日志   活期明细
define("_KEY_REDIS_USER_LOG_PREFIX_LONGPRODUCT", 'userlog:info:longproduct:');

//用户日志   快乐宝明细
define("_KEY_REDIS_USER_LOG_PREFIX_KLPRODUCT", 'userlog:info:klproduct:');

define("_KEY_REDIS_USER_LOG_PREFIX_KLTOBALANCE", 'userlog:info:kltobalance:');


define("_KEY_REDIS_SYSTEM_VERSION_LIST_PREFIX_", 'version:list:');

//用户日志
define("_KEY_REDIS_USER_PROFIT_PREFIX_", 'userprofit:info:');

//红包
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_LIST_PREFIX_" , 'luckmoney:luckmoneylist:');
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_DETAIL_PREFIX_" , 'luckmoney:luckmoneydetail:');
//单个红包参与人数
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_JOIN_PREFIX_" , 'luckmoney:luckmoneyjoin:');
//用户单个红包手气排名
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_RANK_PREFIX_" , 'luckmoney:luckmoneyuserRank:');

//单个红包金额数据
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_INCR_PREFIX_" , 'luckmoney:luckmoney_incr:');        //红包已出金额

define("_KEY_REDIS_ACCOUNT_INFO_PREFIX_" , 'account:info:');
define("_KEY_REDIS_ACCOUNT_UID_PREFIX_" , 'account:uid:');

define("_KEY_REDIS_COUPON_REGEDIT_INFO_PREFIX_" , 'coupon:regedit:info');
define("_KEY_REDIS_COUPON_VALIDATE_INFO_PREFIX_" , 'coupon:validate:info');
define("_KEY_REDIS_COUPON_FIRSTBUY_INFO_PREFIX_" , 'coupon:firstbuy:info');
define("_KEY_REDIS_COUPON_BUY_INFO_PREFIX_" , 'coupon:buy:info');

define("_KEY_REDIS_COUPON_REGEDIT_DETAIL_PREFIX_" , 'coupon:regedit:detail:');
define("_KEY_REDIS_COUPON_VALIDATE_DETAIL_PREFIX_" , 'coupon:validate:detail:');
define("_KEY_REDIS_COUPON_FIRSTBUY_DETAIL_PREFIX_" , 'coupon:firstbuy:detail:');
define("_KEY_REDIS_COUPON_BUY_DETAIL_PREFIX_" , 'coupon:buy:detail:');
define("_KEY_REDIS_USER_CONPON_PREFIX_" , 'coupon:list:');

define("_KEY_REDIS_USERPAY_CODE_PREFIX_" , 'userpay:');
define("_KEY_REDIS_WITHDRAW_CODE_PREFIX_" , 'withdraw:');
define("_KEY_REDIS_BUCHANG_CODE_PREFIX_" , 'buchang:');
define("_KEY_REDIS_RANKSEND_CODE_PREFIX_" , 'ranksend:');

define("_KEY_REDIS_USER_EXPMONEY_PREFIX_" , 'exp:list:');
define("_KEY_REDIS_EXPMONEY_REG_DETAIL_PREFIX_" , 'exp:reg:detail:');

define("_KEY_REDIS_SYSTEM_ACTIVITY_RANK_PREFIX_" , 'activity:rank:');

define("_KEY_REDIS_SYSTEM_ACTIVITY_WEEKRANK_PREFIX_" , 'activity:weekrank:');
define("_KEY_REDIS_USER_NOTICE_PREFIX_" , 'notice:list:');

define("_KEY_REDIS_USER_WITHDRAW_RESTRICT_PREFIX_" , 'withdraw:restrict:');
define("_KEY_REDIS_USER_WITHDRAW_DEFAULT_PREFIX_" , 'withdraw:default:');

define("_KEY_REDIS_USER_JIFENG_LOG_PREFIX_" , 'jifeng:log:');
define("_KEY_REDIS_SYSTEM_GOODS_DETAIL_" , 'goods:detail:');
define("_KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_" , 'online:goods:list:');

define("_KEY_REDIS_LUCKYBAG_BUY_INFO_PREFIX_" , 'luckybag:buyinfo');
define("_KEY_REDIS_LUCKYBAG_BUY_INFO_DETAIL_PREFIX_" , 'luckybag:buyinfo_detail:');

//打款到银行卡
define("_KEY_REDIS_FUIOU_PAY_OUT_INFO_DETAIL_PREFIX_" , 'fuiou:payinfo_detail:');
//11月28月庆活动排行
define("_KEY_REDIS_USER_RANK_ACTIVITY_MARK_INFO_" , 'activity:user_rank_info:');