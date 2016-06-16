<?php
class ControllerCommonSeoUrl extends Controller {
	public function index() {

		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			foreach ($parts as $part) {

                $sql = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "' or keyword = '" . $this->db->escape($part) . "' or keyword = '" . $this->request->get['_route_'] . "'";
				$query = $this->db->query($sql);
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($part) . "'");

           			if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

                    if ($url[0] == 'portfolio_category_id') {
                        if (!isset($this->request->get['portfolio_category_id'])) {
                            $this->request->get['portfolio_category_id'] = $url[1];
                        } else {
                            $this->request->get['portfolio_category_id'] .= '_' . $url[1];
                        }
                    }

                    if ($url[0] == 'portfolio_article_id') {
                        $this->request->get['portfolio_article_id'] = $url[1];
                    }

                    if($url[0] == 'portfolio_author_id') {
                        $this->request->get['portfolio_author_id'] = $url[1];
                    }

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

                    if($url[0] == 'simple_blog_article_id') {
                        $this->request->get['simple_blog_article_id'] = $url[1];
                    }

                    if($url[0] == 'simple_blog_author_id') {
                        $this->request->get['simple_blog_author_id'] = $url[1];
                    }

                    if ($url[0] == 'simple_blog_category_id') {
                        if (!isset($this->request->get['simple_blog_category_id'])) {
                            $this->request->get['simple_blog_category_id'] = $url[1];
                        } else {
                            $this->request->get['simple_blog_category_id'] .= '_' . $url[1];
                        }
                    }

					if ($query->row['query']
                        && $url[0] != 'information_id'
                        && $url[0] != 'manufacturer_id'
                        && $url[0] != 'category_id'
                        && $url[0] != 'product_id'
                        && $url[0] != 'simple_blog_author_id'
                        && $url[0] != 'simple_blog_category_id'
                        && $url[0] != 'simple_blog_article_id'
						&& $url[0] != 'portfolio_article_id'
                        && $url[0] != 'portfolio_author_id'
						&& $url[0] != 'portfolio_category_id') {

						$this->request->get['route'] = $query->row['query'];

					}
				} else {
			/*		$this->request->get['route'] = 'error/not_found'; */

                    if(($this->config->has('portfolio_seo_keyword')) && ($this->db->escape($part) == str_replace("/","",$this->config->get('portfolio_seo_keyword')))) {
                        if(!substr_count($this->request->get['_route_'],"/"))
                            $this->request->get['route'] = "portfolio";

                    } else if($this->db->escape($part) == 'portfolio') {

                       // $this->request->get['route'] = $this->config->get('portfolio_seo_keyword');

                    }elseif(($this->config->has('simple_blog_seo_keyword')) && ($this->db->escape($part) == $this->config->get('simple_blog_seo_keyword'))) {
                        if(!substr_count($this->request->get['_route_'],"/"))
                            $this->request->get['route'] = "blog";

                    } else if($this->db->escape($part) == 'simple_blog') {
                        $this->request->get['route'] = 'simple-blog';

                    } else {
                        $this->request->get['route'] = 'error/not_found';

                        break;
                    }

					//break;
				}
			}

			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
                } elseif (isset($this->request->get['portfolio_article_id'])) {
                    $this->request->get['route'] = 'portfolio/article/view';
                } elseif (isset($this->request->get['portfolio_author_id'])) {
                    $this->request->get['route'] = 'portfolio/author';
                } elseif (isset($this->request->get['portfolio_category_id'])) {
                    $this->request->get['route'] = 'portfolio/category';
				} elseif (isset($this->request->get['simple_blog_article_id'])) {
                    $this->request->get['route'] = 'simple_blog/article/view';
                } elseif (isset($this->request->get['simple_blog_author_id'])) {
                    $this->request->get['route'] = 'simple_blog/author';
                } elseif (isset($this->request->get['simple_blog_category_id'])) {
                    $this->request->get['route'] = 'simple_blog/category';
                }
            }

                else {

                if(($this->config->has('simple_blog_seo_keyword'))) {
                    if($this->request->get['_route_'] == $this->config->get('simple_blog_seo_keyword')) {
                        $this->request->get['route'] = 'simple_blog/article';
                    }
                } if($this->request->get['_route_'] == 'simple_blog') {
                    $this->request->get['route'] = 'simple_blog/article';
                }

                    if(($this->config->has('portfolio_seo_keyword'))) {
                        if($this->request->get['_route_'] == $this->config->get('portfolio_seo_keyword')) {
                            $this->request->get['route'] = 'portfolio/article';
                        }
                    } if($this->request->get['_route_'] == 'portfolio') {
                        $this->request->get['route'] = 'portfolio/article';
                    }
			}

			if (isset($this->request->get['route'])) {
				return new Action($this->request->get['route']);
			}
		}
	}

	public function rewrite($link) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		foreach ($data as $key => $value) {

			if (isset($data['route'])) {
				if (($data['route'] == 'product/product' && $key == 'product_id')
                    || (($data['route'] == 'product/manufacturer/info'
                    || $data['route'] == 'product/product') && $key == 'manufacturer_id')
                    || ($data['route'] == 'information/information' && $key == 'information_id'))
                {

                    $select = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'";

					$query = $this->db->query($select);

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}

                /* modificacao simple_blog */

                } else if($data['route'] == 'simple_blog/article/view' && $key == 'simple_blog_article_id') {

                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

                    if ($query->num_rows) {
                        $url .= '/' . $query->row['keyword'];
                        unset($data[$key]);
                    } else {
                        $url .= '/simple_blog/' . (int)$value;
                        unset($data[$key]);
                    }

                } else if($data['route'] == 'simple_blog/author' && $key == 'simple_blog_author_id') {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

                    if ($query->num_rows) {
                        $url .= '/' . $query->row['keyword'];
                        unset($data[$key]);
                    } else {
                        $url .= '/simple_blog/' . (int)$value;
                        unset($data[$key]);
                    }
                } else if($data['route'] == 'simple_blog/category' && $key == 'simple_blog_category_id') {

                    $blog_categories = explode("_", $value);

                    foreach ($blog_categories as $blog_category) {
                        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'simple_blog_category_id=" . (int)$blog_category . "'");

                        if ($query->num_rows) {
                            $url .= '/' . $query->row['keyword'];
                        } else {
                            $url .= '/simple-category' . $blog_category;
                        }
                    }
                    unset($data[$key]);

                } else if($data['route'] == 'simple_blog/search') {
                    //echo $data['route'];
                    if(isset($key) && ($key == 'blog_search')) {
                        $url .= '/search&blog_search=' . $value;
                    } else {
                        $url .= '/search';
                    }
                    //echo $url;
                } else if(isset($data['route']) && $data['route'] == 'simple_blog/article' && $key != 'simple_blog_article_id' && $key != 'simple_blog_author_id' && $key != 'simple_blog_category_id' && $key != 'page') {

                    if($this->config->has('simple_blog_seo_keyword')) {
                        $url .=  '/' . $this->config->get('simple_blog_seo_keyword');
                    } else {
                        $url .=  '/simple-blog';
                    }

                /* modificacao portfolio */

                } else if($data['route'] == 'portfolio/article/view' && $key == 'portfolio_article_id') {

                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

                    if ($query->num_rows) {
                        $url .= '/' . $query->row['keyword'];
                        unset($data[$key]);
                    } else {
                        $url .= '/portfolio/' . (int)$value;
                        unset($data[$key]);
                    }

                } else if($data['route'] == 'portfolio/author' && $key == 'portfolio_author_id') {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "'");

                    if ($query->num_rows) {
                        $url .= '/' . $query->row['keyword'];
                        unset($data[$key]);
                    } else {
                        $url .= '/portfolio/' . (int)$value;
                        unset($data[$key]);
                    }
                } else if($data['route'] == 'portfolio/category' && $key == 'portfolio_category_id') {

                    $blog_categories = explode("_", $value);

                    foreach ($blog_categories as $blog_category) {
                        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'portfolio_category_id=" . (int)$blog_category . "'");

                        if ($query->num_rows) {
                            $url .= '/' . $query->row['keyword'];
                        } else {
                            $url .= '/simple-category' . $blog_category;
                        }
                    }
                    unset($data[$key]);

                } else if($data['route'] == 'portfolio/search') {
                    //echo $data['route'];
                    if(isset($key) && ($key == 'blog_search')) {
                        $url .= '/search&blog_search=' . $value;
                    } else {
                        $url .= '/search';
                    }
                    //echo $url;
                } else if(isset($data['route']) && $data['route'] == 'portfolio/article' && $key != 'portfolio_article_id' && $key != 'portfolio_author_id' && $key != 'portfolio_category_id' && $key != 'page') {

                    if ($this->config->has('portfolio_seo_keyword')) {
                        $url .= '/' . $this->config->get('portfolio_seo_keyword');
                    } else {
                        $url .= '/portfolio';
                    }

                    /* fim mod */
                }
                elseif( $data['route'] == 'information/contact') {
                {
                    $select = "SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = '" . $data['route'] . "'";

                    $query = $this->db->query($select);

                    if ($query->num_rows && $query->row['keyword']) {
                        $url .= '/' . $query->row['keyword'];

                        unset($data[$key]);
                }

                }

                } elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE `query` = 'category_id=" . (int)$category . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		if ($url) {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((string)$value);
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

            $link = str_replace('index.php?route=common/home', '', $link);

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}
}
