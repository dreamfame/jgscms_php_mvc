/*
Navicat MySQL Data Transfer

Source Server         : jgs
Source Server Version : 50554
Source Host           : localhost:3306
Source Database       : jgs

Target Server Type    : MYSQL
Target Server Version : 50554
File Encoding         : 65001

Date: 2018-12-19 17:35:33
*/

SET FOREIGN_KEY_CHECKS=0;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES ('1', 'liuliu', '', '夏日凉风', '$2y$10$RWyn8DbyRKGhpf934ugYEexcSA1BDFtoL6WBs7Ui1R2709WL.zpyi', '', '0', '', '', '', '0', '0', '1', '系统管理员');
INSERT INTO `admin` VALUES ('2', 'admin', 'default.jpg', '不合格的程序员66', '$2y$10$zfjXwiXE2Xvy8DZZHhXKZOoZZeMrWk6tt5InorkMCIPlam1982JNC', null, '0', '13628635884', 'f6efbbc7da7381c0f8e1644ca1b003d1', '406384958@qq.com', '1545095005', '1545095005', '1', '照片审核管理员');

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `time` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('1', null, '2', 'css3用transition实现边框动画效果', 0xE5B7A5E4BD9CE4B98BE4BD99E68A8AE588ABE4BABAE794A8E69DA5E588B7E5B18FE38081E8B088E8AEBAE585ABE58DA6E79A84E697B6E997B4E794A8E4BA8EE5ADA6E4B9A0E4BC9AE694B6E588B0E6848FE683B3E4B88DE588B0E79A84E69588E69E9CEFBC9AE6898BE69CBAE9878CE5AD98E4B88AE4B880E69CACE4B9A6E7B18DEFBC8CE59CA8E7AD89E5BE85E5ADA9E5AD90E4B88AE585B4E8B6A3E78FADE79A84E697B6E997B4E58FAFE4BBA5E79C8BE4B880E79C8BEFBC9BE5A682E69E9CE697B6E997B4E58581E8AEB8E58FAFE4BBA5E6ADA5E8A18CE4B88AE4B88BE78FADEFBC8CE994BBE782BCE8BAABE4BD93E79A84E5908CE697B6E9A1BAE4BEBFE5AFB9E5BD93E5A4A9E79A84E7949FE6B4BBE5819AE4B880E795AAE8A784E58892E68896E88085E680BBE7BB93EFBC9BE68ABDE7A9BAE69FA5E79C8BE4B880E4B88BE69C80E8BF91E79A84E6B688E8B4B9E6988EE7BB86EFBC8CE5819AE588B0E5BF83E4B8ADE69C89E695B0EFBC8CE5878FE68E89E4B88DE5BF85E8A681E79A84E694AFE587BAEFBC8CE5ADA6E4B9A0E4B880E4BA9BE5AE9EE794A8E79A84E79086E8B4A2E68A80E5B7A7E8AEA9E8B4A2E5AF8CE5A29EE580BCEFBC8CE588A9E794A8E5A5BDE7A28EE78987E58C96E79A84E697B6E997B4E4BABAE7949FE4BC9AE69C89E6848FE683B3E4B88DE588B0E79A84E694B6E88EB7E38082, '1', '0', '2017-7-14', '2017-7-14', '0', '夏日凉风', '', '');
INSERT INTO `news` VALUES ('4', null, '1', '今天天气超级好的', 0x3C696D67207372633D222F696D616765732F3135343531333335393346464244373935374630314646313246343946384543394631374341443936382E706E672220616C743D22223EE5A5BDE79C8BE79A84E59097, '1', '1', '2018-12-18', '2018-12-18', '0', '夏日凉风', '今天', '天气');
INSERT INTO `news` VALUES ('5', 'http://localhost/images/15452059900A8A77494B990E0345BD1AD97B9A3A2B.png', '2', '房贷', 0xE589AFE9A39FE5BA97, '1', '0', '2018-12-19', '2018-12-19', '0', '夏日凉风', '方式', '东方不败');

-- ----------------------------
-- Table structure for news_type
-- ----------------------------
DROP TABLE IF EXISTS `news_type`;
CREATE TABLE `news_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of news_type
-- ----------------------------
INSERT INTO `news_type` VALUES ('1', '领导关怀');
INSERT INTO `news_type` VALUES ('2', '景区新闻');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `order` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', '系统管理员', null);
INSERT INTO `role` VALUES ('2', '内容管理员', null);
INSERT INTO `role` VALUES ('3', '照片审核管理员', null);

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
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of scenic
-- ----------------------------
