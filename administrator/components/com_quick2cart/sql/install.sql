CREATE TABLE IF NOT EXISTS `#__kart_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `address` varchar(200) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `store_email` varchar(100) NOT NULL,
  `store_avatar` varchar(125) DEFAULT NULL,
  `fee` float(10,2) DEFAULT '0.00',
  `live` tinyint(1) DEFAULT '1',
  `cdate` datetime DEFAULT NULL,
  `mdate` datetime DEFAULT NULL,
  `extra` text,
  `company_name` varchar(100) DEFAULT NULL,
  `payment_mode` tinyint(1) NOT NULL DEFAULT '0',
  `pay_detail` varchar(225) NOT NULL,
  `vanityurl` varchar(255) DEFAULT NULL,
  `header` varchar(255) NOT NULL,
  `length_id` int(11) DEFAULT NULL COMMENT 'This will be default length unite for store. Primary key of kart_lengths table',
  `weight_id` int(11) DEFAULT NULL COMMENT 'This will be default weight unite for store. Primary key of kart_weights table',
  `taxprofile_id` int(11) DEFAULT NULL COMMENT 'This will be default tax profile id for store. Primary key of kart_shipprofile table table',
  `shipprofile_id` int(11) DEFAULT NULL COMMENT 'This will be default ship profile id for store. Primary key of _kart_shipprofile table table',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Store Information' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_role` (
  `id` int(11) NOT NULL auto_increment,
  `store_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart Store user role Information' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__order_item_fee` (
  `id` int(11) NOT NULL auto_increment,
  `order_item_id` int(11) NOT NULL,
  `fee` float(10,2) NOT NULL,
  `comment` varchar(125) default NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart item fee Information' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__kart_option_currency` (
  `id` int(11) NOT NULL auto_increment,
  `itemattributeoption_id` int(11) default NULL,
  `currency` varchar(16) NOT NULL,
  `price` float(10,2) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart base currency Information' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_base_currency` (
  `id` int(11) NOT NULL auto_increment,
  `item_id` int(11) default NULL,
  `currency` varchar(16) NOT NULL,
  `price` float(10,2) default NULL,
  `discount_price` float(10,2) DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart base currency Information' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_orders` (
  `id` int(11) NOT NULL auto_increment,
  `prefix` VARCHAR( 23 ) NOT NULL,
  `user_info_id` int(11) default NULL,
  `name` varchar(255) default NULL,
  `email` varchar(100) default NULL,
  `cdate` datetime default NULL,
  `mdate` datetime default NULL,
  `transaction_id` varchar(100) default NULL,
  `payee_id` varchar(100) default NULL,
  `original_amount` float(10,2) NOT NULL,
  `amount` float(10,2) NOT NULL,
  `coupon_code` varchar(100) NOT NULL,
  `couponDetails` text COMMENT 'Coupon price and used price flag- MRP price or field price etc is stored',
  `order_tax` float(10,2) default NULL,
  `order_tax_details` text NOT NULL,
  `order_shipping` float(10,2) default NULL,
  `order_shipping_details` text default NULL,
  `orderRuleDetails` text,
  `fee` float(10,2) default NULL,
  `customer_note` text default NULL,
  `status` varchar(100) default NULL,
  `processor` varchar(100) default NULL,
  `ip_address` varchar(50) default NULL,
  `ticketscount` int(11) NOT NULL,
   `currency` varchar(16) NOT NULL,
  `extra` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Order Information' AUTO_INCREMENT=1 ;
--
-- removed product_id and parent added item_id --
--
CREATE TABLE IF NOT EXISTS `#__kart_order_item` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `store_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `user_info_id` varchar(32) DEFAULT NULL,
  `item_id` int(11) NOT NULL,
  `product_attributes` text NOT NULL COMMENT 'A CSV of itemattributeoption_id values, always in numerical order',
  `product_attribute_names` text NOT NULL COMMENT 'A CSV of itemattributeoption_name values',
  `order_item_name` varchar(255) NOT NULL,
  `product_quantity` int(11) DEFAULT NULL,
  `product_item_price` float(10,2) DEFAULT NULL,
  `product_attributes_price` varchar(64) NOT NULL COMMENT 'The increase or decrease in price per item as a result of attributes. Includes + or - sign',
  `product_final_price` float(10,2) NOT NULL COMMENT 'Amount after applying the coupon code',
  `original_price` float(10,2) NOT NULL COMMENT 'Backup item price if coupon is expire',
  `originalBasePrice` float(10,2) NOT NULL COMMENT 'Product price without field discount',
  `item_tax` decimal(15,5) NOT NULL DEFAULT '0.00000',
  `item_tax_detail` text NOT NULL,
  `item_shipcharges` decimal(15,5) NOT NULL DEFAULT '0.00000',
  `item_shipDetail` text NOT NULL COMMENT 'store item shipping changes detail',
  `cdate` datetime DEFAULT NULL,
  `mdate` datetime DEFAULT NULL,
  `params` text COMMENT 'Coupon code and all is stored',
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`order_item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart Order Item Information' AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kart_order_itemattributes` (
  `orderitemattribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_item_id` int(11) NOT NULL,
  `itemattributeoption_id` int(11) NOT NULL,
  `orderitemattribute_name` varchar(255) NOT NULL,
  `orderitemattribute_price` float(10,2) NOT NULL,
  `orderitemattribute_prefix` varchar(1) NOT NULL,
  PRIMARY KEY (`orderitemattribute_id`),
  KEY `itemattribute_id` (`itemattributeoption_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart Order Item attributes' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__kart_cart` (
  `cart_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(200) NOT NULL,
 `last_updated` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`cart_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart Cart Information' AUTO_INCREMENT=1 ;

--
-- removed product_id and parent added item_id --
--

CREATE TABLE IF NOT EXISTS `#__kart_cartitems` (
  `cart_item_id` int(11) NOT NULL auto_increment,
  `cart_id` int(11) default NULL,
  `store_id` int(11) default NULL,
  `user_info_id` varchar(32) default NULL,
  `item_id` int(11) NOT NULL,
  `currency` varchar(16) NOT NULL,
	  `product_attributes` text NOT NULL COMMENT 'A CSV of itemattributeoption_id values, always in numerical order',
	  `product_attribute_names` text NOT NULL COMMENT 'A CSV of itemattributeoption_name values',
  `order_item_name` varchar(255) NOT NULL,
  `product_quantity` int(11) default NULL,
  `product_item_price` float(10,2) default NULL,
		  `product_attributes_price` varchar(64) NOT NULL COMMENT 'The increase or decrease in price per item as a result of attributes. Includes + or - sign',
  `product_final_price` float(10,2) NOT NULL ,
`original_price` float(10,2) NOT NULL ,
  `cdate` datetime default NULL,
  `mdate` datetime default NULL,
  `params` text default NULL,
  PRIMARY KEY  (`cart_item_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart Cart Items Information' AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `#__kart_cartitemattributes` (
  `cartitemattribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_item_id` int(11) NOT NULL,
  `itemattributeoption_id` int(11) NOT NULL,
  `cartitemattribute_name` varchar(255) NOT NULL,
  `cartitemattribute_price` float(10,2) NOT NULL,
  `cartitemattribute_prefix` varchar(1) NOT NULL,
  PRIMARY KEY (`cartitemattribute_id`),
  KEY `itemattribute_id` (`itemattributeoption_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart Cart Items attributes' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_items` (
	`item_id` int(11) NOT NULL AUTO_INCREMENT,
	`parent` varchar(255) NOT NULL,
	`product_id` int(11) NOT NULL,
	`store_id` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`price` float(10,2) NOT NULL,
	`stock` int(11) default NULL,
	`min_quantity` int(5) NOT NULL,
	`max_quantity` int(5) NOT NULL,
	`slab` int(11) NOT NULL DEFAULT '1',
	`category` varchar(200) DEFAULT NULL,
	`sku` varchar(200) DEFAULT NULL,
	`images` text,
	`description` text,
	`video_link` varchar(200) DEFAULT NULL,
	`cdate` datetime DEFAULT NULL,
	`mdate` datetime DEFAULT NULL,
	`state` tinyint(3) NOT NULL DEFAULT '1',
	`featured` tinyint(3) NOT NULL DEFAULT '0' ,
	`metakey` text NOT NULL,
	`metadesc` text NOT NULL,
	`item_length` decimal(15,8) NOT NULL,
	`item_width` decimal(15,8) NOT NULL,
	`item_height` decimal(15,8) NOT NULL,
	`item_length_class_id` int(11) NOT NULL,
	`item_weight` decimal(15,8) NOT NULL,
	`item_weight_class_id` int(11) NOT NULL,
	`taxprofile_id` int(11) NOT NULL,
	`shipProfileId` int(11) NOT NULL,
	PRIMARY KEY (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Quick2Cart Items' AUTO_INCREMENT=1;

--
-- removed product_id and parent added item_id --
--

CREATE TABLE IF NOT EXISTS `#__kart_itemattributes` (
  `itemattribute_id` int(11) NOT NULL AUTO_INCREMENT,
   `item_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `itemattribute_name` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL,
  `attribute_compulsary` BOOLEAN NOT NULL,
  `attributeFieldType` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`itemattribute_id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8  COMMENT='Quick2Cart Items attributes' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_itemattributeoptions` (
  `itemattributeoption_id` int(11) NOT NULL AUTO_INCREMENT,
  `itemattribute_id` int(11) NOT NULL,
  `itemattributeoption_name` varchar(255) NOT NULL,
  `itemattributeoption_price` float(10,2) NOT NULL,
  `itemattributeoption_code` varchar(255) NOT NULL,
  `itemattributeoption_prefix` varchar(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`itemattributeoption_id`),
  KEY `itemattribute_id` (`itemattribute_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart Item attribute options' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `address_type` varchar(11) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `vat_number` varchar(250) NOT NULL,
  `tax_exempt` tinyint(4) NOT NULL,
  `country_code` varchar(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state_code` varchar(11) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Quick2Cart User Information' AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kart_coupon` (
	`id` int(11) NOT NULL auto_increment,
	`store_id` int(11) NOT NULL,
	`item_id` VARCHAR(50) NULL,
	`user_id` VARCHAR(50) NULL,
	`published` tinyint(4) NOT NULL,
	`name` varchar(100) NOT NULL,
	`code` varchar(100) NOT NULL,
	`value` float(10,2) NOT NULL,
	`val_type` tinyint(4) NOT NULL,
	`max_use` int(11) NOT NULL,
	`max_per_user` int(11) NOT NULL,
	`description` text NOT NULL,
	`extra_params` text NOT NULL,
	`from_date` datetime default NULL,
	`exp_date` datetime default NULL,
	PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_ship_manager` (
  `id` int(11) NOT NULL auto_increment,
  `key` varchar(64) NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_ship_manager_currency` (
  `id` int(11) NOT NULL auto_increment,
  `ship_manager_id` int(11) NOT NULL ,
  `shipvalue` float(10,2) NOT NULL,
  `currency` varchar(16) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_payouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) NOT NULL,
  `payee_name` varchar(20) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `transaction_id` varchar(20) DEFAULT NULL,
  `date` date NOT NULL,
  `email_id` varchar(50) NOT NULL,
  `amount` double(11,2) NOT NULL,
  `status` int(11) NOT NULL,
  `comment` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_promotions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `operation` varchar(32) NOT NULL,
  `keyValue` float(10,2) NOT NULL,
  `discountApplied` float(10,2) NOT NULL,
  `discuntValType` tinyint(4) NOT NULL,
  `message` varchar(150) NOT NULL DEFAULT 'Cart Discount',
  `state` tinyint(3) NOT NULL DEFAULT '1',
  `fromDate` datetime DEFAULT NULL,
  `toDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `#__q2c_order_history` Added by Sneha
--

CREATE TABLE IF NOT EXISTS `#__q2c_order_history` (
  `order_id` int(11) NOT NULL,
  `status` varchar(1) NOT NULL,
  `mdate` datetime NOT NULL,
  `comment` varchar(100) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__kart_orderItemFiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_file_id` int(11) DEFAULT NULL,
  `order_item_id` int(11) DEFAULT NULL,
  `download_count` int(11) DEFAULT '0',
  `cdate` datetime NOT NULL,
  `expirary_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `download_limit` int(11) DEFAULT NULL,
  `expiration_mode` varchar(50) DEFAULT NULL COMMENT 'This is component option value for expiration mode eg Max Download, Date Expirary or both',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_itemfiles` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_display_name` varchar(255) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `purchase_required` tinyint(1) DEFAULT '1',
  `state` tinyint(1) DEFAULT '1',
  `download_limit` int(11) DEFAULT NULL,
  `filePath` varchar(255) DEFAULT NULL,
  `version` varchar(16) DEFAULT NULL,
  `expiry_mode` tinyint(1) DEFAULT '1' COMMENT '1 for months',
  `expiry_in` int(11) NOT NULL DEFAULT '0',
  `cdate` datetime NOT NULL,
  `mdate` datetime NOT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- since v2.2 --
--
CREATE TABLE IF NOT EXISTS `#__kart_zone` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `store_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_zonerules` (
  `zonerule_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`zonerule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_taxrates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `percentage` decimal(11,3) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1  ;

CREATE TABLE IF NOT EXISTS `#__kart_taxrules` (
  `taxrule_id` int(11) NOT NULL AUTO_INCREMENT,
  `taxprofile_id` int(11) NOT NULL,
  `taxrate_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL COMMENT 'Which address should be used to apply taxrates. Eg billin, shipping or store address',
  `ordering` int(11) NOT NULL,
  `state` int(11) NOT NULL,
  PRIMARY KEY (`taxrule_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_taxprofiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `store_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- default zone spcific plugin tables
--

CREATE TABLE IF NOT EXISTS `#__kart_zoneShipMethods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '1',
  `taxprofileId` int(11) NOT NULL,
  `state` tinyint(3) NOT NULL,
  `shipping_type` int(11) NOT NULL COMMENT 'It is type of shipping method eg. weight based, quantity based etc',
  `min_value` decimal(15,5) NOT NULL,
  `max_value` decimal(15,5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kart_zoneShipMethodRates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `methodId` int(11) NOT NULL COMMENT 'This is primary key of #__kart_shipMethods table',
  `zone_id` int(11) NOT NULL COMMENT 'This is primary key of #__kart_zones table.',
  `rangeFrom` int(11) DEFAULT '0',
  `rangeTo` int(11) DEFAULT '99999',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kart_zoneShipMethodRateCurr` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rateId` int(11) NOT NULL COMMENT 'This is primary key of #__kart_zoneShipMethodRates table.',
  `shipCost` float(15,8) NOT NULL DEFAULT '0.00000000',
  `handleCost` float(15,8) NOT NULL DEFAULT '0.00000000',
  `currency` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `#__kart_shipprofile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `store_id` int(11) NOT NULL,
  `state` tinyint(1) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__kart_shipProfileMethods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shipprofile_id` int(11) NOT NULL,
  `client` varchar(255) NOT NULL COMMENT 'Shipping Plugin Name',
  `methodId` int(11) NOT NULL COMMENT 'Extension specific, Shipping Plugin Method id.',
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__kart_zoneShipMethodCurr` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `methodId` int(11) NOT NULL,
  `currency` varchar(16) NOT NULL,
  `min_value` decimal(15,5) NOT NULL,
  `max_value` decimal(15,5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__kart_orders_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `creater_id` int(11) DEFAULT NULL,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_notified` tinyint(1) NOT NULL,
  `order_item_status` varchar(100) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `#__kart_orders_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `mdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `extra` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
