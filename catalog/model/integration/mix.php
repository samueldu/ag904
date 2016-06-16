<?php 
class ModelIntegrationMix extends Model {
	
	private $wsdl = '';
	private $namespace = '';
	private $client = '';
	
	public function getData(){
	
   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $starttime = $mtime;
	
		$mode = "teste";
		$modeFotos = "skyp";

        $produtosXML = simplexml_load_file("catalog/model/integration/haymax.xml",'SimpleXMLElement', LIBXML_NOCDATA) or die("Error: Cannot create object");

		$language_id = $this->getDefaultLanguageId();

        /*

                    [prod_name] => Divisor Antena VHF/UHF 1/2 GENÉRICO
                    [seg_name] => Eletrônicos##Antenas##Divisores & Misturadores
                     [link] => http://www.hayamax.com.br/divisor-antena-vhf-uhf-1-2-generico
                    [saleUnit] => DEZ
                    [shortname] => DIVISOR VHF/UHF 1/2 GENÉRICO
                    [EAN] => 7898221073490
                    [width] => 0.000
                    [height] => 0.000
                    [depth] => 0.000
                    [information] => SimpleXMLElement Object
        (
            [description] => Divisor antena 1 Entrada 2 Saídas   Indicado para VHF/UHF/   Frequência: 5~900 MHz
                            [characteristics] => SimpleXMLElement Object
        (
        )

        [technical] => SimpleXMLElement Object
        (
        )

        [included] => SimpleXMLElement Object
        (
        )

                        )

                    [PPB] => 0
                    [warrantyDays] => 06
                    [price] => 11.85
                    [IPI] => 0.00
                    [sourceFat] => SP
        */

        $produtos = array();

		foreach($produtosXML as $key=>$value)
		{

            $categorias = explode("##",$value->seg_name);

			$produtos[$key]['manufacturerId'] = $this->getManufactureId($value->brand);
			$produtos[$key]['catPrincipalId'] = $this->getCategoryId($categorias[0],0);
			$produtos[$key]['catSubId'] = $this->getCategoryId($categorias[1],$produtos[$key]['catPrincipalId']);
            if($categorias[2])
            $produtos[$key]['catSubSubId'] = $this->getCategoryId($categorias[2],$produtos[$key]['catSubId']);

            $produtos[$key]['estoque'] = $value->stock;
            $produtos[$key]['nome'] = str_replace("GENÉRICO","",$value->prod_name);
            $produtos[$key]['referencia'] = $value->NBM;
            $produtos[$key]['valor'] = $value->price+($value->price*20/100);
            $produtos[$key]['width'] = $value->width;
            $produtos[$key]['height'] = $value->height;
            $produtos[$key]['depth'] = $value->depth;
            $produtos[$key]['fornecedor'] = 1;
            $produtos[$key]['information']['description'] = $value->information->description;
            $produtos[$key]['weightValue'] = $value->weightValue;
            $produtos[$key]['weightUnit'] = $value->weightUnit;
            $produtos[$key]['image'] = $value->image;
            $produtos[$key]['modelo'] = $value->EAN;
            $produtos[$key]['image'] = $value->image;
            $produtos[$key]['productId'] = $this->getProductId($produtos[$key]);
            $produtos[$key]['product_id'] = $produtos[$key]['productId'];

            /*

			if($produtos[$key]['tamanho'] != "U")
			{
				$produtos[$key]['option'] = array("product_id"=>$produtos[$key]['productId'],"variacao"=>"Tamanho","variacao_value"=>$produtos[$key]['tamanho'],"estoque"=>$produtos[$key]['estoque'],"cod"=>$produtos[$key]['referenciatam']);
				$produtos[$key]['option']['optionId'] = $this->saveOption($produtos[$key]['option']);
				$this->saveOptionValue($produtos[$key]['option']);
			}

            */
		}
		

   $mtime = microtime();
   $mtime = explode(" ",$mtime);
   $mtime = $mtime[1] + $mtime[0];
   $endtime = $mtime;
   $totaltime = ($endtime - $starttime);
   echo "This page was created in ".$totaltime." seconds"; 
		
	}
		
	function saveOptionValue($option)
	{

		$language_id = $this->getDefaultLanguageId();   

		if(!is_numeric($option['variacao_value']))
		{        
			if($option['variacao_value'] == "PP")
				$sort = '0';
			if($option['variacao_value'] == "P")
				$sort = '1';
			if($option['variacao_value'] == "M")
				$sort = '2';
			if($option['variacao_value'] == "G")
				$sort = '3';
		}
		else
		{
			$sort = $option['variacao_value'];
		}

		$sql = "SELECT * FROM `".DB_PREFIX."product_option_value`
		inner join `".DB_PREFIX."product_option_value_description` on product_option_value.product_id = product_option_value_description.product_id 
		and product_option_value_description.language_id = '$language_id'
		and product_option_value.product_option_id = '".$option['optionId']."'
		and product_option_value_description.name = '".$option['variacao_value']."';";
		
		
		$result = $this->db->query( $sql );
		if ($result->rows) {
				return $result->row['product_option_id']; 
			}
			else
			{
				$sql2 = "INSERT INTO `".DB_PREFIX."product_option_value` 
				(`product_option_value_id`,`product_option_id`, `product_id`, `quantity`, `subtract`, `price`, `prefix`, `sort_order`) 
				VALUES 
				('','".$option['optionId']."','".$option['product_id']."', '".$option['estoque']."','1','0', '+', '$sort');";
				
				$this->db->query($sql2);
				$product_option_value_id = $this->db->getLastId();    
				
				$sql2 = "INSERT INTO `".DB_PREFIX."product_option_value_description` (`product_option_value_id`, `language_id`, `product_id`, `name`) 
				VALUES ('$product_option_value_id','$language_id','".$option['product_id']."','".$option['variacao_value']."');";
				$this->db->query($sql2);
				
				return $product_option_value_id;
			}
	}
	
	function saveOption($option)
	{

		$language_id = $this->getDefaultLanguageId();   
		// add option names, ids, and sort orders to the database
		$maxOptionId = 0;
		$sortOrder = 0;

		$sql = "SELECT * FROM `".DB_PREFIX."product_option`
		inner join `".DB_PREFIX."product_option_description` on product_option.product_id = product_option_description.product_id 
		and product_option_description.name = '".$option['variacao']."';";
		
		$result = $this->db->query( $sql );
		if ($result->rows) {
				return $result->row['product_option_id']; 
			}
			else
			{
				$sql2 = "INSERT INTO `".DB_PREFIX."product_option` (`product_option_id`,`product_id`,`sort_order`) VALUES ('','".$option['product_id']."','".$sortOrder."');";
				$this->db->query($sql2);
				$product_option_id = $this->db->getLastId();    
				
				$sql2 = "INSERT INTO `".DB_PREFIX."product_option_description` (`product_option_id`, `language_id`, `product_id`, `name`) 
				VALUES ('$product_option_id','$language_id','".$option['product_id']."','".$option['variacao']."');";
				$this->db->query($sql2);
				
				return $product_option_id;
			}
	}
	
	function getProductId($product)
	{

		$languageId = $this->getDefaultLanguageId();    
	
		$sql = "SELECT `product_id` FROM `".DB_PREFIX."product` where model = '".$product['referencia']."' and fornecedor = '".$product['fornecedor']."';";
		$result = $this->db->query( $sql );
		if ($result->rows) {

			$productName = $product['nome'];
			$model_2 = $product['modelo'];
			$price = $product['valor'];

            $productId = $result->row['product_id'];
			
			$sql = "update `".DB_PREFIX."product` set price = '$price', model_2 = '$model_2' where product_id = '".$result->row['product_id']."'";
			
			$this->db->query($sql); 
			
			$sql2 = "update `".DB_PREFIX."product_description` set `name` = '".$product['information']['description']."' ";
			//`description`,`meta_description`,`meta_keywords`) VALUES ";
			//$sql2 .= "('$productId',$languageId,'$productName','$productDescription','$meta_description','$meta_keywords') 
			$sql2 .="where product_id = '$productId' and language_id = '2';";
			
			$this->db->query($sql2);
			
			return $result->row['product_id'];
			
		}
		else
		{

            //file_put_contents("data/fotosMix/".$product['referencia'].".jpg", file_get_contents($product['image']));

			$productId = '';
			$productName = $product['nome'];
			$categories[] = $product['catPrincipalId'];
			$categories[] = $product['catSubId'];

            if($product['catSubSubId'])
            $categories[] = $product['catSubSubId'];

            $quantity = $product['estoque'];
			$model = $product['referencia'];
			$model_2 = $product['modelo'];
            $fornecedor = $product['fornecedor'];
			$manufacturerId = $product['manufacturerId'];
			$imageName = "data/fotosMix/".$product['referencia'].".jpg";
			$shipping = 1;
			$price = $product['valor'];
			$dateAdded = date("Y-m-d H:s:i");
			$dateModified = date("Y-m-d H:s:i");
			$dateAvailable = date("Y-m-d");
			$weight = $product['weightValue'];
			$unit = $product['weightUnit'];
			$weightClassId = 1;
			$status = 1;
			$taxClassId = 0;
			$viewed = 0;
			$productDescription = $product['information']['description'];
			$stockStatusId = '5';
			$meta_description = '';
			$length = $product['depth'];
			$width = $product['width'];
			$height = $product['height'];

            if($length <= 0)
                $length = 10;

            if($width <= 0)
                $width = 10;

            if($height <= 0)
                $height = 10;

			$keyword = '';
			$lengthUnit = 'cm';
			$lengthClassId = 1;
			$sku = '0';
			$location = 0;
			$storeIds[] = "0";
			$related = null;
			$tags = '';
			$subtract = 1;
			$minimum = 1;
			$cost = '';
			$meta_keywords = '';
			$sort_order = 0;
			$sql  = "INSERT INTO `".DB_PREFIX."product` (`product_id`,`quantity`,`sku`,`location`,";
			$sql .= "`stock_status_id`,`model`,`model_2`,`fornecedor`,`manufacturer_id`,`image`,`shipping`,`price`,`date_added`,`date_modified`,`date_available`,`weight`,`weight_class_id`,`status`,";
			$sql .= "`tax_class_id`,`viewed`,`length`,`width`,`height`,`length_class_id`,`sort_order`,`subtract`,`minimum`,`cost`) VALUES ";
			$sql .= "('$productId',$quantity,'$sku','$location',";
			$sql .= "'$stockStatusId','$model','$model_2','$fornecedor','$manufacturerId','$imageName','$shipping','$price',";
			$sql .= ($dateAdded=='NOW()') ? "$dateAdded," : "'$dateAdded',";
			$sql .= ($dateModified=='NOW()') ? "$dateModified," : "'$dateModified',";
			$sql .= ($dateAvailable=='NOW()') ? "$dateAvailable," : "'$dateAvailable',";
			$sql .= "$weight,$weightClassId,$status,";
			$sql .= "$taxClassId,$viewed,$length,$width,$height,'$lengthClassId','$sort_order','$subtract','$minimum','$cost');";
			
			$sql2 = "INSERT INTO `".DB_PREFIX."product_description` (`product_id`,`language_id`,`name`,`description`,`meta_description`,`meta_keywords`) VALUES ";
			$sql2 .= "('$productId',$languageId,'$productName','$productDescription','$meta_description','$meta_keywords');";
			
			$this->db->query($sql) or die(mysql_error());
			
			$this->db->query($sql2);
			
			$productId = $this->db->getLastId();  
			
			if (count($categories) > 0) {
				$sql = "INSERT INTO `".DB_PREFIX."product_to_category` (`product_id`,`category_id`) VALUES ";
				$first = TRUE;
				foreach ($categories as $categoryId) {
					$sql .= ($first) ? "\n" : ",\n";
					$first = FALSE;
					$sql .= "('$productId','$categoryId')";
				}
				$sql .= ";";
				
				//print $sql."<BR>";
				//exit;
				
				$this->db->query($sql);
			}
			
			if ($keyword) {
				$sql4 = "INSERT INTO `".DB_PREFIX."url_alias` (`query`,`keyword`) VALUES ('product_id=$productId','$keyword');";
				$this->db->query($sql4);
			}
			
			foreach ($storeIds as $storeId) {
				$sql6 = "INSERT INTO `".DB_PREFIX."product_to_store` (`product_id`,`store_id`) VALUES ('$productId',$storeId);";
				$this->db->query($sql6);
			}
			
			if (count($related) > 0) {
				$sql = "INSERT INTO `".DB_PREFIX."product_related` (`product_id`,`related_id`) VALUES ";
				$first = TRUE;
				foreach ($related as $relatedId) {
					$sql .= ($first) ? "\n" : ",\n";
					$first = FALSE;
					$sql .= "('$productId',$relatedId)";
				}
				$sql .= ";";
				$this->db->query($sql);
			}
			
			/*
			
			if (count($tags) > 0) {
				$sql = "INSERT INTO `".DB_PREFIX."product_tags` (`product_id`,`tag`,`language_id`) VALUES ";
				$first = TRUE;
				$inserted_tags = array();
				foreach ($tags as $tag) {
					if ($tag == '') {
						continue;
					}
					if (in_array($tag,$inserted_tags)) {
						continue;
					}
					$sql .= ($first) ? "\n" : ",\n";
					$first = FALSE;
					$sql .= "('$productId','".addslashes($tag)."',$languageId)";
					$inserted_tags[] = $tag;
				}
				$sql .= ";";
				if (count($inserted_tags)>0) {
					$this->db->query($sql);
				}
			}
			*/
		}
		
		return $productId;
	}
	
	function getCategoryId($name,$parent)
	{
	
		$language_id = $this->getDefaultLanguageId();  
		
		$sql = "SELECT category_description.category_id, category_description.name FROM category_description 
		inner join category on category.category_id = category_description.category_id";

		$sql .= " and category.parent_id = '$parent'";
		
		$sql .=" where category_description.name = '".$name."'";
		
		//print $sql."<BR>";
		
		$result = $this->db->query( $sql );
		if ($result->rows) {
			return $result->row['category_id'];
		}
		else
		{
			$sql2 = "INSERT INTO `".DB_PREFIX."category` (`parent_id`,`status`) VALUES ($parent,1);";
			$this->db->query($sql2);
			$category_id = $this->db->getLastId();    
			
			$sql2 = "INSERT INTO `".DB_PREFIX."category_description` (`category_id`, `language_id`, `name`) VALUES ('$category_id','$language_id','$name');";
			$this->db->query($sql2);
		
			$sql2 = "INSERT INTO `".DB_PREFIX."category_to_store` (`category_id`,`store_id`) VALUES ($category_id,0);";
			$this->db->query($sql2);
			
			return $category_id;
		}
	}
	
	function getManufactureId($name)
	{
	
		$language_id = $this->getDefaultLanguageId();
		
		$sql = "SELECT `manufacturer_id`, `name` FROM `".DB_PREFIX."manufacturer` where name = '$name';";
		$result = $this->db->query( $sql );
		if ($result->rows) {
			return $result->row['manufacturer_id'];
		}
		else
		{
			$sql2 = "INSERT INTO `".DB_PREFIX."manufacturer` (`name`) VALUES ('$name');";
			$this->db->query( $sql2 );
			$manufacturerId = $this->db->getLastId();
			
			$sql2 = "INSERT INTO `".DB_PREFIX."manufacturer_description` (`manufacturer_id`, `language_id`, `name`) VALUES ('$manufacturerId','$language_id','$name');";
			$this->db->query( $sql2 );
		
			$sql2 = "INSERT INTO `".DB_PREFIX."manufacturer_to_store` (`manufacturer_id`,`store_id`) VALUES ($manufacturerId,0);";
			$this->db->query( $sql2 );
			
			return $manufacturerId;
		}
	}
	
	public function associaFotos($product)
	{
		$sql = "SELECT `product_id`,`image` FROM `".DB_PREFIX."product` where model = '".$product['referencia']."';";
		$result = $this->db->query($sql);
		if ($result->rows) {
			$product['product_id'] = $result->row['product_id'];
			
			
			if($result->row['image'] != "data/fotosFtp/Fotos/".$product['referencia'].".jpg")
			{
			
				print "nao tem foto no site ".$product['referencia']."<BR>";  
				
				 
		
				if(is_file($product['image']))
				{
					$sql2 = "update product  set image= 'data/fotosFtp/Fotos/".$product['referencia'].".jpg',`status`='1' where product_id = '".$product['product_id']."'";
					$this->db->query( $sql2 );
					print "tem foto no ftp - > ".$product['referencia']." ".$product['product_id']."  <BR>";     
					
				}
				else
				print "data/fotosFtp/Fotos/".$product['referencia'].".jpg nao encontrada<BR><BR>";
			}
		
			$sql = "SELECT `product_id` FROM `".DB_PREFIX."product_image` where product_id = '".$product['product_id']."' and image = 'data/fotosFtp/Site/".$product['referencia']."-1.jpg';";
			$result = $this->db->query( $sql );
			if (!$result->rows) {
                if(is_file("/home/fernicom/public_html/catalog/view/theme/ferni/image/data/fotosFtp/Site/".$product['referencia']."-1.jpg"))
                {
                    $sql2 = "insert into product_image (image,product_id) values ('data/fotosFtp/Site/".$product['referencia']."-1.jpg','".$product['product_id']."')";
                    $this->db->query( $sql2 );
                }
			}
		}		
	}

    public function getFotos()
    {
        $server = '189.1.137.5';
        $username = 'ferni';
        $password = 'AN1234PB';

        $server_dir = '/';
        $local_dir = '/var/www/catalog/view/theme/ferni/image/data/fotosFtp/';

        $con = ftp_connect($server) or die("Couldn't connect");
        ftp_login($con,  $username,  $password);

        $dir = "Site";

        ftp_chdir($con,$dir);

        $contents = ftp_nlist($con, ".");

        foreach ($contents as $cont) {
            $tmp = explode('.', $cont);
            $ext = end($tmp);
            $contNovo = str_replace($ext,"",$cont);
            $contNovo = $contNovo.strtolower($ext);

            $local_file = $local_dir.$dir.'/'.$contNovo;
            $server_file = $server_dir.$dir.'/'.$cont;

            if(!is_file($local_file))
            {
                if (ftp_get($con, $local_file, $server_file, FTP_BINARY)) {
                    echo "Successfully written to $local_file\n <BR>";
                } else {
                    echo "There was a problem\n <BR>";
                }
            }
        }

        ftp_close($con);

    }

	protected function getDefaultLanguageId() {   
		$code = $this->config->get('config_language');
		$sql = "SELECT language_id FROM `".DB_PREFIX."language` WHERE code = '$code'";
		$result = $this->db->query( $sql );
		$languageId = 1;
		if ($result->rows) {
			foreach ($result->rows as $row) {
				$languageId = $row['language_id'];
				break;
			}
		}
		return $languageId;
	}
	
	protected function getDefaultWeightUnit() {
		$weightUnit = $this->config->get( 'config_weight_class' );
		return $weightUnit;
	}

	protected function getDefaultMeasurementUnit() {
		$measurementUnit = $this->config->get( 'config_length_class' );
		return $measurementUnit;
	}
	
}
?>