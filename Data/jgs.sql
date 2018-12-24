/*
Navicat MySQL Data Transfer

Source Server         : jgs
Source Server Version : 50554
Source Host           : localhost:3306
Source Database       : jgs

Target Server Type    : MYSQL
Target Server Version : 50554
File Encoding         : 65001

Date: 2018-12-24 17:29:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for activity
-- ----------------------------
DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `date` varchar(255) DEFAULT NULL,
  `join` varchar(255) DEFAULT NULL,
  `intro` varchar(255) DEFAULT NULL,
  `prize_way` varchar(255) DEFAULT NULL,
  `prize` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `enable` int(2) DEFAULT NULL,
  `num` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of activity
-- ----------------------------
INSERT INTO `activity` VALUES ('1', '元旦', '/images/15453792651545308976default.png', '2018年12月22日-12月24日', '短视频', '庆祝元旦佳节', '转载30次，点赞100次', '井冈山门票1张', 'xxx-1234567', '1', '2');
INSERT INTO `activity` VALUES ('2', '测试活动', '/images/15453807661545309744alipay.jpg', '2018年12月21日-12月29日', '测试', '测试', '测试', 'iphone一台', '13628635884', '1', '0');

-- ----------------------------
-- Table structure for activity_person
-- ----------------------------
DROP TABLE IF EXISTS `activity_person`;
CREATE TABLE `activity_person` (
  `id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `prize` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of activity_person
-- ----------------------------
INSERT INTO `activity_person` VALUES ('1', '1', '13628635884', '夏日凉风', '0', '0');
INSERT INTO `activity_person` VALUES ('2', '1', '15072558596', 'dreamfame', '0', '0');

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `head_pic` varchar(255) NOT NULL,
  `nickname` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `auth_key` varchar(255) DEFAULT NULL,
  `age` int(8) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `updated_at` int(255) NOT NULL,
  `created_at` int(255) NOT NULL,
  `status` int(2) NOT NULL,
  `role` varchar(50) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'liuliu', '/images/face.jpg', '夏日凉风', '$2y$10$RWyn8DbyRKGhpf934ugYEexcSA1BDFtoL6WBs7Ui1R2709WL.zpyi', '', '0', '', '', '', '0', '0', '1', '系统管理员');
INSERT INTO `admin` VALUES ('2', 'admin', 'default.jpg', '不合格的程序员66', '$2y$10$zfjXwiXE2Xvy8DZZHhXKZOoZZeMrWk6tt5InorkMCIPlam1982JNC', null, '0', '13628635884', 'f6efbbc7da7381c0f8e1644ca1b003d1', '406384958@qq.com', '1545095005', '1545095005', '1', '照片审核管理员');

-- ----------------------------
-- Table structure for area
-- ----------------------------
DROP TABLE IF EXISTS `area`;
CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `brief` longblob,
  `intro` longblob,
  `area_map` varchar(255) DEFAULT NULL,
  `recommend` varchar(255) DEFAULT NULL,
  `isshow` int(2) DEFAULT NULL,
  `top` int(2) DEFAULT NULL,
  `see` int(11) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of area
-- ----------------------------
INSERT INTO `area` VALUES ('1', '井冈山景区', 0xE69882E8B4B5E79A84E5B9BFE6B39BE59CB0, 0xE998BFE8BEBEE998B2E781ABE998B2E79B97E5928CE8A686E79B96, '/images/15453108821545308976default.png', '4', '1', '1', '0', '2018-12-20', '2018-12-20');
INSERT INTO `area` VALUES ('2', '黄洋界景区', 0xE5958A4756E79A84E6A2B5E8B0B7, 0xE5958AE59BBDE998B2E7949FE79A84, '/images/15453130941545309744alipay.jpg', '4', '1', '0', '0', '2018-12-20', '2018-12-20');

-- ----------------------------
-- Table structure for images
-- ----------------------------
DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `scenic_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `picStr` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of images
-- ----------------------------

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of log
-- ----------------------------

-- ----------------------------
-- Table structure for menu
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `spread` varchar(11) DEFAULT NULL,
  `icon` varchar(128) DEFAULT NULL,
  `target` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', '后台首页', null, 'page/main.html', 'false', 'icon-computer', null);
INSERT INTO `menu` VALUES ('2', '权限管理', null, '', 'false', '&#xe613;', null);
INSERT INTO `menu` VALUES ('3', '管理员信息', '2', 'page/admin/adminList.html', 'false', '&#xe612;', null);
INSERT INTO `menu` VALUES ('4', '角色信息', '2', 'page/admin/roleList.html', 'false', '&#xe673;', null);
INSERT INTO `menu` VALUES ('5', '景区内容管理', null, '', 'false', '&#xe705;', null);
INSERT INTO `menu` VALUES ('6', '文章信息', '5', 'page/news/newsList.html', 'false', 'icon-text', null);
INSERT INTO `menu` VALUES ('7', '文章分类', '5', 'page/news/newsType.html', 'false', '&#xe653;', null);
INSERT INTO `menu` VALUES ('8', '参考指南管理', null, null, 'false', '&#xe609;', null);
INSERT INTO `menu` VALUES ('9', '景区路线', '8', 'page/activity/routeList.html', 'false', '&#xe670;', null);
INSERT INTO `menu` VALUES ('10', '景区活动', '8', 'page/activity/activityList.html', 'false', '&#xe66c;', null);
INSERT INTO `menu` VALUES ('11', '分享内容管理', null, null, 'false', null, null);
INSERT INTO `menu` VALUES ('12', '照片信息', '11', 'page/share/picList.html', 'false', '&#xe60d;', null);
INSERT INTO `menu` VALUES ('13', '明信片信息', '11', 'page/share/postcardList.html', 'false', '&#xe64a;', null);
INSERT INTO `menu` VALUES ('14', '区域设置', null, 'page/area/areasetting.html', 'false', '&#xe715;', null);

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `msg` varchar(255) DEFAULT NULL,
  `reply` varchar(255) DEFAULT NULL,
  `msg_time` datetime DEFAULT NULL,
  `reply_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of message
-- ----------------------------
INSERT INTO `message` VALUES ('1', '1', '1', '你好，这个系统是干嘛的？', '景区专用', '2018-12-03 13:05:44', '2018-12-24 13:05:44', '1');
INSERT INTO `message` VALUES ('2', '1', '1', '你好，请问你多大了', '26', '2018-12-19 13:06:37', '2018-12-24 16:00:48', '1');

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pic` varchar(255) DEFAULT NULL,
  `type` varchar(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longblob,
  `isshow` int(4) NOT NULL,
  `top` int(4) NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `updated_at` varchar(255) NOT NULL,
  `see` int(11) NOT NULL,
  `operator` varchar(50) NOT NULL,
  `abstract` varchar(150) DEFAULT NULL,
  `keyword` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('1', 'http://localhost/images/15456146031545570217default.png', '2', 'css3用transition实现边框动画效果', 0xE5B7A5E4BD9CE4B98BE4BD99E68A8AE588ABE4BABAE794A8E69DA5E588B7E5B18FE38081E8B088E8AEBAE585ABE58DA6E79A84E697B6E997B4E794A8E4BA8EE5ADA6E4B9A0E4BC9AE694B6E588B0E6848FE683B3E4B88DE588B0E79A84E69588E69E9CEFBC9AE6898BE69CBAE9878CE5AD98E4B88AE4B880E69CACE4B9A6E7B18DEFBC8CE59CA8E7AD89E5BE85E5ADA9E5AD90E4B88AE585B4E8B6A3E78FADE79A84E697B6E997B4E58FAFE4BBA5E79C8BE4B880E79C8BEFBC9BE5A682E69E9CE697B6E997B4E58581E8AEB8E58FAFE4BBA5E6ADA5E8A18CE4B88AE4B88BE78FADEFBC8CE994BBE782BCE8BAABE4BD93E79A84E5908CE697B6E9A1BAE4BEBFE5AFB9E5BD93E5A4A9E79A84E7949FE6B4BBE5819AE4B880E795AAE8A784E58892E68896E88085E680BBE7BB93EFBC9BE68ABDE7A9BAE69FA5E79C8BE4B880E4B88BE69C80E8BF91E79A84E6B688E8B4B9E6988EE7BB86EFBC8CE5819AE588B0E5BF83E4B8ADE69C89E695B0EFBC8CE5878FE68E89E4B88DE5BF85E8A681E79A84E694AFE587BAEFBC8CE5ADA6E4B9A0E4B880E4BA9BE5AE9EE794A8E79A84E79086E8B4A2E68A80E5B7A7E8AEA9E8B4A2E5AF8CE5A29EE580BCEFBC8CE588A9E794A8E5A5BDE7A28EE78987E58C96E79A84E697B6E997B4E4BABAE7949FE4BC9AE69C89E6848FE683B3E4B88DE588B0E79A84E694B6E88EB7E38082, '1', '0', '2017-7-14', '2017-7-14', '0', '夏日凉风', '', '');
INSERT INTO `news` VALUES ('4', null, '1', '今天天气超级好的', 0x3C696D67207372633D222F696D616765732F3135343531333335393346464244373935374630314646313246343946384543394631374341443936382E706E672220616C743D22223EE5A5BDE79C8BE79A84E59097, '1', '1', '2018-12-18', '2018-12-18', '0', '夏日凉风', '今天', '天气');
INSERT INTO `news` VALUES ('5', 'http://localhost/images/15452059900A8A77494B990E0345BD1AD97B9A3A2B.png', '2', '房贷', 0xE589AFE9A39FE5BA97, '1', '0', '2018-12-19', '2018-12-19', '0', '夏日凉风', '方式', '东方不败');

-- ----------------------------
-- Table structure for news_type
-- ----------------------------
DROP TABLE IF EXISTS `news_type`;
CREATE TABLE `news_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of news_type
-- ----------------------------
INSERT INTO `news_type` VALUES ('1', '领导关怀');
INSERT INTO `news_type` VALUES ('2', '景区新闻');

-- ----------------------------
-- Table structure for photo
-- ----------------------------
DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) DEFAULT NULL,
  `des` varchar(255) DEFAULT NULL COMMENT '描述',
  `created_at` varchar(255) DEFAULT NULL,
  `praise` int(255) DEFAULT NULL,
  `comment` int(255) DEFAULT NULL,
  `img1` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `img3` varchar(255) DEFAULT NULL,
  `img4` varchar(255) DEFAULT NULL,
  `img5` varchar(255) DEFAULT NULL,
  `img6` varchar(255) DEFAULT NULL,
  `img7` varchar(255) DEFAULT NULL,
  `img8` varchar(255) DEFAULT NULL,
  `img9` varchar(255) DEFAULT NULL,
  `verify` int(4) DEFAULT NULL,
  `operator` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of photo
-- ----------------------------
INSERT INTO `photo` VALUES ('1', 'dreamfame', '分享测试', '2018-12-21', '0', '0', '/images/default.jpg', '/images/default.jpg', '/images/default.jpg', '/images/default.jpg', '/images/default.jpg', '/images/default.jpg', '/images/default.jpg', '/images/default.jpg', '/images/default.jpg', '1', '-');

-- ----------------------------
-- Table structure for postcard
-- ----------------------------
DROP TABLE IF EXISTS `postcard`;
CREATE TABLE `postcard` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wx` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `wishes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of postcard
-- ----------------------------
INSERT INTO `postcard` VALUES ('1', 'dreamfame', '测试', '/images/default.png', '2018-12-04 22:48:56', '祝大家元旦快乐');
INSERT INTO `postcard` VALUES ('4', 'dreamfame', '测试2', '/images/default.png', '2018-12-22 23:31:05', '祝大家新年快乐');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `order` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', '系统管理员', null);
INSERT INTO `role` VALUES ('2', '内容管理员', null);
INSERT INTO `role` VALUES ('3', '照片审核管理员', null);

-- ----------------------------
-- Table structure for route
-- ----------------------------
DROP TABLE IF EXISTS `route`;
CREATE TABLE `route` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scenic_id` int(11) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of route
-- ----------------------------
INSERT INTO `route` VALUES ('1', '1', '黄洋界-大井', '一日游', '线路二', '上午', '2018-12-21');

-- ----------------------------
-- Table structure for scenic
-- ----------------------------
DROP TABLE IF EXISTS `scenic`;
CREATE TABLE `scenic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `brief` varchar(150) NOT NULL,
  `intro` longblob NOT NULL,
  `recommend` int(11) NOT NULL,
  `isshow` int(2) NOT NULL,
  `top` int(2) NOT NULL,
  `see` int(11) NOT NULL,
  `created_at` varchar(255) NOT NULL,
  `updated_at` varchar(255) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of scenic
-- ----------------------------
INSERT INTO `scenic` VALUES ('1', '井冈山', '井冈山：地处湘东-赣西边界，万洋山（为罗霄山脉中段）的北支，据传在东汉年间就已经有人在井冈山居住了。秦朝设郡县制时，井冈山为九江郡庐陵县属地。', '', '4', '0', '1', '0', '2018-12-18', '2018-12-18');
INSERT INTO `scenic` VALUES ('3', '发送', '被第三方', 0xE58886E6898BE5A4A7E5B888, '3', '1', '1', '0', '2018-12-19', '2018-12-19');
INSERT INTO `scenic` VALUES ('4', '井冈山景区', '工时费', 0xE585ACE58FB8, '4', '1', '1', '0', '2018-12-20', '2018-12-20');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `wx` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `gender` int(11) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`openid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', '1', 'double_fame', 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJSvOhfrsWluVbVZZgUxAdotuPgDibwPxCBhggX1azjO2Z9uAHnfWMHUicYibMNYo02mv0FBmEXZJhQg/132', '夏日凉风', '', 'Bermuda', '1', '2018-12-23');
