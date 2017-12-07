<?php

#redis 存储 string 数据类型
define('_REDIS_DATATYPE_STRING' , 'redis:string');
#redis 存储 hash 数据类型
define('_REDIS_DATATYPE_HASH' , 'redis:hash');
#redis 存储 set 数据类型
define('_REDIS_DATATYPE_SET' , 'redis:set');
#redis 存储 set 数据类型
define('_REDIS_DATATYPE_LIST' , 'redis:list');

#测试数据
define("_KEY_REDIS_TEST_INFO_PREFIX_" , 'test:info:');
#账号信息
define("_KEY_REDIS_ACCOUNT_INFO_PREFIX_" , 'account:info:');
define("_KEY_REDIS_ACCOUNT_UID_PREFIX_" , 'account:uid:');
define("_KEY_REDIS_ACCOUNT_MOBILE_PREFIX_" , 'account:mobile:uid:');
//验证码前缀
define("_KEY_REDIS_VALIDATECODE_PREFIX_" , 'account:phonecode:');

define("_KEY_REDIS_VALIDATECODE_COUNT_PREFIX_" , 'account:phonecode:count:');

define("_KEY_REDIS_PAYCODE_COUNT_PREFIX_" , 'paycode:count:');

define("_KEY_REDIS_PAYCODE_PREFIX_" , 'paycode:');

define("_KEY_REDIS_BINDBANK_PREFIX_" , 'account:bindbank:');

define("_KEY_REDIS_BINDBANK_PHONE_PREFIX_" , 'account:bindbankphone:');

define("_KEY_REDIS_LOGINPWD_PREFIX_", 'find:loginpwd:');

define("_KEY_REDIS_LOGINPWD_COUNT_PREFIX_", 'find:loginpwd:count:');

//今日已还款产品列表
define("_KEY_REDIS_SYSTEM_REPAYMENT_PRODUCT_LIST_PREFIX_" , 'product:repayment:list:');
//系统在线产品列表
define("_KEY_REDIS_SYSTEM_ONLINE_PRODUCT_LIST_PREFIX_" , 'product:online:list:');
//系统售馨定期产品列表
define("_KEY_REDIS_SYSTEM_SELLOUT_PRODUCT_LIST_PREFIX_" , 'product:sellout:list:');
//系统售馨活期产品列表
define("_KEY_REDIS_SYSTEM_SELLOUT_LONGPRODUCT_LIST_PREFIX_" , 'longproduct:sellout:list:');

define("_KEY_REDIS_SYSTEM_SELLOUT_KLPRODUCT_LIST_PREFIX_" , 'klproduct:sellout:list:');


define("_KEY_REDIS_SYSTEM_YUGAO_PRODUCT_LIST_PREFIX_" , 'product:yugao:list:');
//系统产品列表
define("_KEY_REDIS_SYSTEM_PRODUCT_DETAIL_PREFIX_" , 'product:detail:');
//邀请我的
define("_KEY_REDIS_INVITE_MY" , 'invite:invite_my:');
//我邀请的
define("_KEY_REDIS_MY_INVITE" , 'invite:my_invite:');
//我邀请的--累计奖励
define("_KEY_REDIS_MY_INVITE_COUNT" , 'invite:my_invite_count:');
//我的邀请奖励
define("_KEY_REDIS_MY_INVITE_REWARD", 'invite:my_invitereward:');
//我的邀请奖励--累计奖励
define("_KEY_REDIS_MY_INVITE_REWARD_COUNT", 'invite:my_invitereward_count:');

define("_KEY_REDIS_SYSTEM_PTYPE_LIST_PREFIX_", 'ptype:list:');
//用户昨日收益
define("_KEY_REDIS_SYSTEM_YESTERDAY_PROFIT_PREFIX_", 'profit:yesterday:');

//用户利息收益
define("_KEY_REDIS_SYSTEM_COUNT_PROFIT_PREFIX_", 'profit:count:');

//用户信息缓存
define("_KEY_REDIS_USER_IDENTITY_DETAIL_PREFIX_" , 'user:identity:');
define("_KEY_REDIS_TG_USER_IDENTITY_DETAIL_PREFIX_" , 'tg_user:identity:');
//用户体验金累计收益
define("_KEY_REDIS_SYSTEM_COUNT_EXPMONEY_PROFIT_PREFIX_", 'exp_profit:count:');
//用户体验金昨日收益
define("_KEY_REDIS_SYSTEM_YESTERDAY_EXPMONEY_PROFIT_PREFIX_", 'exp_profit:yesterday:');

//用户体验金日志
define("_KEY_REDIS_SYSTEM_EXPMONEY_LOG_PROFIT_LIST_PREFIX_", 'exp_log::profit');

//新用户体验金累计收益
define("_KEY_REDIS_TOTAL_EXPMONEY_PROFIT_PREFIX_", 'exp:total:');
//新用户体验金昨日收益
define("_KEY_REDIS_YESTERDAY_EXPMONEY_PROFIT_PREFIX_", 'exp:yesterday:');

//新用户体验金日志
define("_KEY_REDIS_EXPMONEY_LOG_PROFIT_LIST_PREFIX_", 'exp:profit:');

define("_KEY_REDIS_EXPMONEY_LOG_PROFIT_SET_PREFIX_", 'exp:profit:set');

//
define("_KEY_REDIS_SYSTEM_LONGPRODUCT_LOG_PROFIT_LIST_PREFIX_", 'longproduct_log::profit');

define("_KEY_REDIS_SYSTEM_KLPRODUCT_LOG_PROFIT_LIST_PREFIX_", 'klproduct_log::profit');
//活动
define("_KEY_REDIS_SYSTEM_ACTIVITY_PREFIX_" , 'activity:user:');

//用户排名  活动KEY
define("_KEY_REDIS_SYSTEM_ACTIVITY_RANK_PREFIX_" , 'activity:rank:');

define("_KEY_REDIS_SYSTEM_ACTIVITY_WEEKRANK_PREFIX_" , 'activity:weekrank:');


define("_KEY_REDIS_SYSTEM_CONTRACT_DETAIL_PREFIX_" , 'contract:detail:');
define("_KEY_REDIS_SYSTEM_LONGPRODUCTCONTRACT_DETAIL_PREFIX_" , 'longproductcontract:detail:');


define("_KEY_REDIS_SYSTEM_KLRODUCTCONTRACT_DETAIL_PREFIX_" , 'klproductcontract:detail:');
//验证码保存时间
define("VALIDATECODE_TTL" , 600);

//交易密码修改 短信验证码
define("_KEY_REDIS_SMSTPWD_PREFIX_", 'sms:step2:');

define("_KEY_REDIS_MOBIFYTPWD_PREFIX_", 'sms:step3:');


define("_KEY_REDIS_MOBIFYTPWD_COUNT_PREFIX_", 'sms:step3:count:');


//系统上线活期产品列表
define("_KEY_REDIS_SYSTEM_ONLINE_LONGPRODUCT_LIST_PREFIX_" , 'longproduct:online:list:');

define("_KEY_REDIS_SYSTEM_ONLINE_KLPRODUCT_LIST_PREFIX_" , 'klproduct:online:list:');
//系统活期预告产品列表
define("_KEY_REDIS_SYSTEM_YUGAO_LONGPRODUCT_LIST_PREFIX_" , 'longproduct:yugao:list:');

define("_KEY_REDIS_SYSTEM_YUGAO_KLPRODUCT_LIST_PREFIX_" , 'klproduct:yugao:list:');
//系统活期产品列表
define("_KEY_REDIS_SYSTEM_LONGPRODUCT_DETAIL_PREFIX_" , 'longproduct:detail:');
define("_KEY_REDIS_SYSTEM_KLPRODUCT_DETAIL_PREFIX_" , 'klproduct:detail:');
//活期列队
define("_KEY_REDIS_SYSTEM_LTYPE_LIST_PREFIX_", 'ltype:list:');

define("_KEY_REDIS_SYSTEM_KLTYPE_LIST_PREFIX_", 'kltype:list:');


//推荐列表
define("_KEY_REDIS_SYSTEM_RECOMMEND_LIST_PREFIX_", 'recommend:homepage:');
//精品推荐
define("_KEY_REDIS_COMPETITIVE_PREFIX_", 'recommend:competitive');
//用户已购买产品
define("_KEY_REDIS_USER_PRODUCT_PREFIX_", 'userproduct:info:');

//用户已购买产品
define("_KEY_REDIS_USER_PRODUCT_DETAIL_PREFIX_", 'userproduct:detail:');

define("_KEY_REDIS_CORPORATION_DETAIL_PREFIX_", 'corporation:');

define("_KEY_REDIS_USER_PRODUCT_MONEY_PREFIX_", 'userproduct:money:');
//用户已购买活期产品
define("_KEY_REDIS_USER_LONGPRODUCT_PREFIX_", 'userlongproduct:info:');
define("_KEY_REDIS_USER_KLPRODUCT_PREFIX_", 'userklproduct:info:');
//用户单个产品购买限额
define("_KEY_REDIS_USER_LONGPRODUCT_MAX_PREFIX_", 'userlongproduct:pmax:');
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

define("_KEY_REDIS_USER_LOG_PREFIX_LONGTOBALANCE", 'userlog:info:longtobalance:');

define("_KEY_REDIS_USER_LOG_PREFIX_LONGALL", 'userlog:info:longall:');

define("_KEY_REDIS_USER_LOG_PREFIX_KLPRODUCT", 'userlog:info:klproduct:');

define("_KEY_REDIS_USER_LOG_PREFIX_KLTOBALANCE", 'userlog:info:kltobalance:');

define("_KEY_REDIS_USER_LOG_PREFIX_CASHOUT", 'userlog:info:cashout:');

define("_KEY_REDIS_USER_EXPPRODUCT_PREFIX_", 'userexpproduct:info:');

//在使用的体验金总数
define("_KEY_REDIS_USER_SUM_EXPPRODUCT_PREFIX_", 'userexpproduct:sum_using:');
//已过期的体验金总数
define("_KEY_REDIS_USER_SUM_ALL_EXPPRODUCT_PREFIX_", 'userexpproduct:sum_all:');


//用户日志   收支明细
define("_KEY_REDIS_USER_LOG_PREFIX_", 'userlog:info:');

define("_KEY_REDIS_EXPMONEY_LOG_PREFIX_ALL", 'expmoneylog:user:');

//用户收益
define("_KEY_REDIS_USER_PROFIT_PREFIX_", 'userprofit:info:');
define("_KEY_REDIS_USER_NOT_SQUARE_PROFIT_PREFIX_", 'userprofit:notsquareinfo:');


//用户日志   收支明细
define("_KEY_REDIS_LONGMONEY_INCOME_LOG_PREFIX_", 'lmoneyincomelog:info:');
define("_KEY_REDIS_KLMONEY_INCOME_LOG_PREFIX_", 'klmoneyincomelog:info:');

define("_KEY_REDIS_LONGMONEY_INCOME_ALL_LOG_PREFIX_", 'lmoneyincomelog:all:');
define("_KEY_REDIS_KLMONEY_INCOME_ALL_LOG_PREFIX_", 'klmoneyincomelog:all:');
//用户公告
define("_KEY_REDIS_SYSTEM_NOTICE_LIST_PREFIX_", 'notice:list');

define("_KEY_REDIS_SYSTEM_NEWS_LIST_PREFIX_", 'news:list');

define("_KEY_REDIS_SYSTEM_BANNER_HASH_PREFIX", 'banner:');

define("_KEY_REDIS_SYSTEM_EQUALPTYPE_LIST_PREFIX_", 'equalptype:list:');
define("_KEY_REDIS_SYSTEM_EQUALAMOUNTCONTRACT_DETAIL_PREFIX_" , 'equalamountcontract:detail:');
define("_KEY_REDIS_SYSTEM_ONLINE_EQUALPRODUCT_LIST_PREFIX_" , 'equalproduct:online:list:');
define("_KEY_REDIS_SYSTEM_SELLOUT_EQUALPRODUCT_LIST_PREFIX_" , 'equalproduct:sellout:list:');
define("_KEY_REDIS_SYSTEM_YUGAO_EQUALPRODUCT_LIST_PREFIX_" , 'equalproduct:yugao:list:');
define("_KEY_REDIS_SYSTEM_EQUALPRODUCT_DETAIL_PREFIX_", 'equalproduct:detail:');
//今日还款等额产品
define("_KEY_REDIS_SYSTEM_REPAYMENT_EQUALPRODUCT_LIST_PREFIX_" , 'equalproduct:repayment:list:');

define("_KEY_REDIS_USER_EQUALPRODUCT_PREFIX_", 'userequalproduct:info:');

define("_KEY_REDIS_USER_EQUAL_PROFIT_PREFIX_", 'userequalprofit:info:');
define("_KEY_REDIS_USER_EQUAL_NOT_SQUARE_PROFIT_PREFIX_", 'userequalprofit:notsquareinfo:');

//红包产品数据
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_LIST_PREFIX_" , 'luckmoney:luckmoneylist:');        //红包队列 以日期为分割
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_DETAIL_PREFIX_" , 'luckmoney:luckmoneydetail:');    //红包详情

//单个红包金额数据
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_INCR_PREFIX_" , 'luckmoney:luckmoney_incr:');        //红包已出金额

//用户红包数据
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_USERCD_PREFIX_" , 'luckmoney:luckmoneyusercd:');     //红包详情

//用户单个红包手气排名
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_RANK_PREFIX_" , 'luckmoney:luckmoneyuserRank:');

//单个红包参与人数
define("_KEY_REDIS_SYSTEM_ACTIVITY_LUCKMONEY_JOIN_PREFIX_" , 'luckmoney:luckmoneyjoin:');


//微信红包详情
define("_KEY_REDIS_REDBAG_DETAIL_PREFIX_" , 'redbag:detail:');

//微信红包领到者记录
define("_KEY_REDIS_REDBAG_USER_PREFIX_" , 'redbag:user:');

//微信红包个数计数
define("_KEY_REDIS_REDBAG_COUNT_PREFIX_" , 'redbag:count:');

define("_KEY_REDIS_REDBAG_TOTAL_COUNT_PREFIX_" , 'redbag:total:count:');

define("_KEY_REDIS_REDBAG_LOG_PREFIX_" , 'redbag:log:');

define("_KEY_REDIS_REDBAG_LIST_PREFIX_" , 'redbag:list:');

define("_KEY_REDIS_COUPON_REGEDIT_INFO_PREFIX_" , 'coupon:regedit:info');
define("_KEY_REDIS_COUPON_VALIDATE_INFO_PREFIX_" , 'coupon:validate:info');
define("_KEY_REDIS_COUPON_BUY_INFO_PREFIX_" , 'coupon:buy:info');
define("_KEY_REDIS_COUPON_FIRSTBUY_INFO_PREFIX_" , 'coupon:firstbuy:info');

define("_KEY_REDIS_COUPON_REGEDIT_DETAIL_PREFIX_" , 'coupon:regedit:detail:');
define("_KEY_REDIS_COUPON_VALIDATE_DETAIL_PREFIX_" , 'coupon:validate:detail:');
define("_KEY_REDIS_COUPON_FIRSTBUY_DETAIL_PREFIX_" , 'coupon:firstbuy:detail:');
define("_KEY_REDIS_COUPON_BUY_DETAIL_PREFIX_" , 'coupon:buy:detail:');
define("_KEY_REDIS_COUPON_JIFENG_DETAIL_PREFIX_" , 'coupon:jifeng:detail:');

define("_KEY_REDIS_USER_CONPON_PREFIX_" , 'coupon:list:');
define("_KEY_REDIS_USER_USED_CONPON_PREFIX_" , 'coupon:used:list:');
define("_KEY_REDIS_USER_EXPIRED_CONPON_PREFIX_" , 'coupon:expired:list:');

define("_KEY_REDIS_USER_EXPMONEY_PREFIX_" , 'exp:list:');
define("_KEY_REDIS_EXPMONEY_REG_DETAIL_PREFIX_" , 'exp:reg:detail:');
define("_KEY_REDIS_EXPMONEY_JIFENG_DETAIL_PREFIX_" , 'exp:jifeng:detail:');

define("_KEY_REDIS_USER_LUCKYBAG_LIST_PREFIX_" , 'luckybag:list:');
define("_KEY_REDIS_LUCKYBAG_DETAIL_PREFIX_" , 'luckybag:detail:');
define("_KEY_REDIS_LUCKYBAG_CACHE_DETAIL_PREFIX_" , 'luckybag:cache:');
define("_KEY_REDIS_USER_LUCKYBAG_ACCEPTED_LIST_PREFIX_" , 'luckybag:acctped:');
define("_KEY_REDIS_LUCKYBAG_COUNT_PREFIX_" , 'luckybag:count:');

define("_KEY_REDIS_USER_NOTICE_PREFIX_" , 'notice:list:');

define("_KEY_REDIS_USER_WITHDRAW_RESTRICT_PREFIX_" , 'withdraw:restrict:');

define("_KEY_REDIS_REDBAG_MONEYARRAY_PREFIX_" , 'redbag:moneyarray:');

define("_KEY_REDIS_USER_WITHDRAW_DEFAULT_PREFIX_" , 'withdraw:default:');

define("_KEY_REDIS_USER_JIFENG_LOG_PREFIX_" , 'jifeng:log:');
define("_KEY_REDIS_USER_QIANDAO_LIST_PREFIX_" , 'qiandao:list');
define("_KEY_REDIS_USER_QIANDAO_MONTH_PREFIX_" , 'qiandao:month:');
define("_KEY_REDIS_USER_QIANDAO_LIANXU_COUNT_PREFIX_" , 'qiandao:count:');
define("_KEY_REDIS_USER_QIANDAO_SINGLE_COUNT_PREFIX_" , 'qiandao:day:count:');
define("_KEY_REDIS_USER_QIANDAO_MONTH_COUNT_PREFIX_" , 'qiandao:month:count:');
define("_KEY_REDIS_SYSTEM_TOTAL_JIFENG_PREFIX_" , 'jifeng:total:');
define("_KEY_REDIS_SYSTEM_GOODS_DETAIL_" , 'goods:detail:');
define("_KEY_REDIS_SYSTEM_TOTAL_DUIHUAN_LIST_PREFIX_" , 'duihuan:list:');
define("_KEY_REDIS_SYSTEM_TOTAL_ONLINE_GOODS_LIST_PREFIX_" , 'online:goods:list:');
define("_KEY_REDIS_SYSTEM_TOTAL_GOODS_SOLD_COUNT_PREFIX_" , 'goods:sold:');
define("_KEY_REDIS_SYSTEM_TOTAL_GOODS_SOLD_LOCK_PREFIX_" , 'goods:sold:lock:');
define("_KEY_REDIS_LUCKYBAG_BUY_INFO_PREFIX_" , 'luckybag:buyinfo');
define("_KEY_REDIS_LUCKYBAG_BUY_INFO_DETAIL_PREFIX_" , 'luckybag:buyinfo_detail:');
//后台用户资产排名
define("_KEY_REDIS_USER_RANK_MARK_DETAIL_INFO_" , 'rank:user_rank_mark:');
//复投活动发奖记录
define("_KEY_REDIS_USER_FUTOU_ACTIVITY_MARK_INFO_" , 'activity:user_mark_info:');
//双十二单笔投资活动一
define("_KEY_REDIS_USER_DOUBLE_TWELVE_ACTIVITY_ONE_INFO_" , 'activity:double_twelve_one:');
define("_KEY_REDIS_USER_DOUBLE_TWELVE_ACTIVITY_TWO_INFO_" , 'activity:double_twelve_two:');