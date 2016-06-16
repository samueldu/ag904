<?php 
    class ModelportfolioInstall extends Model {
        public function addExtensionTables() {
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS ". DB_PREFIX . "portfolio_article (
                    `portfolio_article_id` int(16) NOT NULL AUTO_INCREMENT,
                    `portfolio_author_id` int(16) NOT NULL,
                    `allow_comment` tinyint(1) NOT NULL,
                    `image` text NOT NULL,
                    `featured_image` text NOT NULL,
                    `article_related_method` varchar(64) NOT NULL,
                    `article_related_option` text NOT NULL,
                    `sort_order` int(8) NOT NULL,
                    `status` tinyint(1) NOT NULL,
                    `date_added` datetime NOT NULL,
                    `date_modified` datetime NOT NULL,
                    PRIMARY KEY (`portfolio_article_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_article_description (
                  `portfolio_article_description_id` int(16) NOT NULL AUTO_INCREMENT,
                  `portfolio_article_id` int(16) NOT NULL,
                  `language_id` int(16) NOT NULL,
                  `article_title` varchar(256) NOT NULL,
                  `description` text NOT NULL,
                  `meta_description` varchar(256) NOT NULL,
                  `meta_keyword` varchar(256) NOT NULL,
                  PRIMARY KEY (`portfolio_article_description_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_article_description_additional (
                  `portfolio_article_id` int(16) NOT NULL,
                  `language_id` int(16) NOT NULL,
                  `additional_description` text NOT NULL,
                  `title` text NOT NULL,
                  `sort_order` int(8) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_article_product_related (
                  `portfolio_article_id` int(16) NOT NULL,
                  `product_id` int(16) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_article_to_category (
                  `portfolio_article_id` int(16) NOT NULL,
                  `portfolio_category_id` int(16) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_article_to_layout (
                  `portfolio_article_id` int(16) NOT NULL,
                  `store_id` int(16) NOT NULL,
                  `layout_id` int(16) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_article_to_store (
                  `portfolio_article_id` int(16) NOT NULL,
                  `store_id` int(16) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
            );
            
             $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_author (
                  `portfolio_author_id` int(16) NOT NULL AUTO_INCREMENT,
                  `name` varchar(256) NOT NULL,
                  `image` text NOT NULL,
                  `status` tinyint(1) NOT NULL,
                  `date_added` datetime NOT NULL,
                  `date_modified` datetime NOT NULL,
                  PRIMARY KEY (`portfolio_author_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_author_description (
                  `portfolio_author_description_id` int(16) NOT NULL AUTO_INCREMENT,
                  `portfolio_author_id` int(16) NOT NULL,
                  `language_id` int(16) NOT NULL,
                  `description` text NOT NULL,
                  `meta_description` varchar(256) NOT NULL,
                  `meta_keyword` varchar(256) NOT NULL,
                  `date_added` datetime NOT NULL,
                  PRIMARY KEY (`portfolio_author_description_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
            
             $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_category (
                  `portfolio_category_id` int(16) NOT NULL AUTO_INCREMENT,
                  `image` text NOT NULL,
                  `parent_id` int(16) NOT NULL,
                  `top` tinyint(1) NOT NULL,
                  `blog_category_column` int(16) NOT NULL,
                  `column` int(8) NOT NULL,
                  `sort_order` int(8) NOT NULL,
                  `status` tinyint(1) NOT NULL,
                  `date_added` datetime NOT NULL,
                  `date_modified` datetime NOT NULL,
                  PRIMARY KEY (`portfolio_category_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_category_description (
                  `portfolio_category_description_id` int(16) NOT NULL AUTO_INCREMENT,
                  `portfolio_category_id` int(16) NOT NULL,
                  `language_id` int(16) NOT NULL,
                  `name` varchar(256) NOT NULL,
                  `description` text NOT NULL,
                  `meta_description` varchar(256) NOT NULL,
                  `meta_keyword` varchar(256) NOT NULL,
                  PRIMARY KEY (`portfolio_category_description_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_category_to_layout (
                  `portfolio_category_id` int(16) NOT NULL,
                  `store_id` int(16) NOT NULL,
                  `layout_id` int(16) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_category_to_store (
                  `portfolio_category_id` int(16) NOT NULL,
                  `store_id` int(16) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_comment (
                  `portfolio_comment_id` int(16) NOT NULL AUTO_INCREMENT,
                  `portfolio_article_id` int(16) NOT NULL,
                  `portfolio_article_reply_id` int(16) NOT NULL,
                  `author` varchar(64) NOT NULL,
                  `comment` text NOT NULL,
                  `status` tinyint(1) NOT NULL,
                  `date_added` datetime NOT NULL,
                  `date_modified` datetime NOT NULL,
                  PRIMARY KEY (`portfolio_comment_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_related_article (
                  `portfolio_related_article_id` int(16) NOT NULL AUTO_INCREMENT,
                  `portfolio_article_id` int(16) NOT NULL,
                  `portfolio_article_related_id` int(16) NOT NULL,
                  `sort_order` int(8) NOT NULL,
                  `status` tinyint(1) NOT NULL,
                  `date_added` datetime NOT NULL,
                  PRIMARY KEY (`portfolio_related_article_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
            
            $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "portfolio_view (
                  `portfolio_view_id` int(16) NOT NULL AUTO_INCREMENT,
                  `portfolio_article_id` int(16) NOT NULL,
                  `view` int(16) NOT NULL,
                  `date_added` datetime NOT NULL,
                  `date_modified` datetime NOT NULL,
                  PRIMARY KEY (`portfolio_view_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1"
            );
        }
    }
?>