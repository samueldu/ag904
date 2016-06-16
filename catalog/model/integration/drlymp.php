<?php 
class ModelIntegrationDrlymp extends Model {
	
	private $wsdl = '';
	private $namespace = '';
	private $client = '';
	
	public function getData(){

        $url = 'http://177.101.148.52/vendabemweb/ws/integracao_site_produtos/';

        $fields = array('id_usuario' => urlencode('drlympsite'),
            'chave_acesso' => urlencode('UfaG43D2Sc4567HkloRtYA12Av'),
            'filial'=>urlencode('001;002'),
            'data_inicial'=>urlencode('1900-01-31'),
            'data_final'=>urlencode('2016-01-01'));

        $fields_string ='';
//url-ify the data for the POST
        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
        rtrim($fields_string, '&');

//open connection
        $ch = curl_init();

        $produtos = "";

//set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

//execute post
        $produtos = curl_exec($ch);
       // $produtos = str_Replace(array("[","]"),"",$produtos);

//close connection
        curl_close($ch);


        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;
	
		$mode = "inserir";
		$modeFotos = "xxx";

        $produtosx = json_decode($produtos,true);

        unset($produtos);

        $produtos = $produtosx;

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                echo ' - No errors';
                break;
            case JSON_ERROR_DEPTH:
                echo ' - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                echo ' - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                echo ' - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                echo ' - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                echo ' - Unknown error';
                break;
        }


		if($mode == "teste")
		{
			print "<pre>";
			print_R($produtos);
			print "</pre>";
			exit;
		}
		
		$language_id = $this->getDefaultLanguageId();
		
		foreach($produtos as $key=>$value)
		{
			$produtos[$key]['manufacturerId'] = $this->getManufactureId($produtos[$key]['descricao_marca']);
			$produtos[$key]['catPrincipalId'] = $this->getCategoryId($produtos[$key]['descricao_grupo'],0);
			$produtos[$key]['catSubId'] = $this->getCategoryId($produtos[$key]['descricao_subgrupo'],$produtos[$key]['catPrincipalId']);
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
		
		if($modeFotos == "insert")
		{
		
			print "inserindo fotos...<BR>";
		
			foreach($produtos as $key=>$value)
			{
				$produtosFotos[$produtos[$key]['product_id']] =  array("referencia" => $produtos[$key]['referencia'], "product_id"=>$produtos[$key]['product_id']);
			}
		
			foreach($produtosFotos as $key=>$valor)
			{
				$this->associaFotos($produtosFotos[$key]);
			} 
			
			print "finalizado fotos...<BR>";  
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
	
		$sql = "SELECT `product_id` FROM `".DB_PREFIX."product` where model = '".$product['referencia']."';";
		$result = $this->db->query( $sql );
		if ($result->rows) {
		
			//$sql2 = "UPDATE `".DB_PREFIX."product` set `status` = '1' where model = '".$product['referencia']."'";
			
			//$this->db->query($sql2);	
			
			$productName = $product['descricao'];
			$model_2 = '';
			$price = $product['preco'];
			//$cor = $product['cor'];
			$productId = $result->row['product_id'];
			
			$sql = "update `".DB_PREFIX."product` set price = '$price' where product_id = '".$result->row['product_id']."'";
			
			$this->db->query($sql); 
			
			//$sql2 = "update `".DB_PREFIX."product_description` set `name` = '$productName' ";
			//`description`,`meta_description`,`meta_keywords`) VALUES ";
			//$sql2 .= "('$productId',$languageId,'$productName','$productDescription','$meta_description','$meta_keywords') 
			//$sql2 .="where product_id = '$productId' and language_id = '2';";
			
			//$this->db->query($sql2);
			
			return $result->row['product_id'];
			
		}
		else
		{	
			$productId = '';
			$productName = $product['descricao'];
			$categories[] = $product['catPrincipalId'];
			$categories[] = $product['catSubId'];			
			$quantity = $product['estoque_filial'];
			$model = $product['referencia'];
			//$model_2 = $product['modelo'];
			//$cor = $product['cor'];
			$manufacturerId = $product['manufacturerId'];
			$imageName = '';
			$shipping = 1;
			$price = $product['preco'];
			$dateAdded = date("Y-m-d H:s:i");
			$dateModified = date("Y-m-d H:s:i");
			$dateAvailable = date("Y-m-d");
			$weight = $product['peso'];
			$unit = 'kg';
			$weightClassId = 1;
			$status = 1;
			$taxClassId = 0;
			$viewed = 0;
			$productDescription = '';
			$stockStatusId = '5';
			$meta_description = '';
			$length = $product['comprimento'];
			$width = $product['largura'];
			$height = $product['altura'];
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
			$sql .= "`stock_status_id`,`model`,`manufacturer_id`,`image`,`shipping`,`price`,`date_added`,`date_modified`,`date_available`,`weight`,`weight_class_id`,`status`,";
			$sql .= "`tax_class_id`,`viewed`,`length`,`width`,`height`,`length_class_id`,`sort_order`,`subtract`,`minimum`) VALUES ";
			$sql .= "('$productId',$quantity,'$sku','$location',";
			$sql .= "'$stockStatusId','$model','$manufacturerId','$imageName','$shipping','$price',";
			$sql .= ($dateAdded=='NOW()') ? "$dateAdded," : "'$dateAdded',";
			$sql .= ($dateModified=='NOW()') ? "$dateModified," : "'$dateModified',";
			$sql .= ($dateAvailable=='NOW()') ? "$dateAvailable," : "'$dateAvailable',";
			$sql .= "$weight,$weightClassId,$status,";
			$sql .= "$taxClassId,$viewed,$length,$width,$height,'$lengthClassId','$sort_order','$subtract','$minimum');";
			

			$this->db->query($sql);

            $productId = $this->db->getLastId();

            $sql2 = "INSERT INTO `".DB_PREFIX."product_description` (`product_id`,`language_id`,`name`,`description`,`meta_description`,`meta_keyword`) VALUES ";
            $sql2 .= "('$productId',$languageId,'$productName','$productDescription','$meta_description','$meta_keywords');";

            $this->db->query($sql2);
			

			
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
		
		$sql = "SELECT ".DB_PREFIX."category_description.category_id, ".DB_PREFIX."category_description.name FROM ".DB_PREFIX."category_description
		inner join ".DB_PREFIX."category on ".DB_PREFIX."category.category_id = ".DB_PREFIX."category_description.category_id";

		$sql .= " and ".DB_PREFIX."category.parent_id = '$parent'";
		
		$sql .=" where ".DB_PREFIX."category_description.name = '".$name."'";
		
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
			
		/*	$sql2 = "INSERT INTO `".DB_PREFIX."manufacturer_description` (`manufacturer_id`, `language_id`, `name`) VALUES ('$manufacturerId','$language_id','$name');";
			$this->db->query( $sql2 ); */
		
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
				
				 
		
				if(is_file("/home/fernicom/public_html/catalog/view/theme/ferni/image/data/fotosFtp/Fotos/".$product['referencia'].".jpg"))
				{
					$sql2 = "update product  set image= 'data/fotosFtp/Fotos/".$product['referencia'].".jpg',`status`='1' where product_id = '".$product['product_id']."'";
					$this->db->query( $sql2 );
					print "tem foto no ftp - > ".$product['referencia']." ".$product['product_id']."  <BR>";     
					
				}
				else
				print "data/fotosFtp/Fotos/".$product['referencia'].".jpg n�o encontrada<BR><BR>";
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
			
			 $sql = "SELECT `product_id` FROM `".DB_PREFIX."product_image` where product_id = '".$product['product_id']."' and image = 'data/fotosFtp/Site/".$product['referencia']."-2.jpg';";
			$result = $this->db->query( $sql );
			if (!$result->rows) {

			
				if(is_file("/home/fernicom/public_html/catalog/view/theme/ferni/image/data/fotosFtp/Site/".$product['referencia']."-2.jpg"))
				{
					$sql2 = "insert into product_image (image,product_id) values ('data/fotosFtp/Site/".$product['referencia']."-2.jpg','".$product['product_id']."')";
					$this->db->query( $sql2 );
				}
			}
			
			 $sql = "SELECT `product_id` FROM `".DB_PREFIX."product_image` where product_id = '".$product['product_id']."' and image = 'data/fotosFtp/Site/".$product['referencia']."-3.jpg';";
			$result = $this->db->query( $sql );
			if (!$result->rows) {

				
				if(is_file("/home/fernicom/public_html/catalog/view/theme/ferni/image/data/fotosFtp/Site/".$product['referencia']."-3.jpg"))
				{
					$sql2 = "insert into product_image (image,product_id) values ('data/fotosFtp/Site/".$product['referencia']."-3.jpg','".$product['product_id']."')";
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
		
		$dir = "../Fotos";
		
		ftp_chdir($con,$dir);
		
		$dir = "Fotos";
		
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
		
		/*
		
		$dir = "Site"; 
		
		ftp_chdir($con,$dir);
		
		$contents = ftp_nlist($con, "."); 
		
		foreach ($contents as $cont) {
		echo "<a href=ftp.php?dir=$_GET[dir]/$cont>$cont</a> <br>";
		} 
		*/
									  

		/*

		if (ftp_get($con, $local_file, $server_file, FTP_BINARY)) {
			echo "Successfully written to $local_file\n";
		} else {
			echo "There was a problem\n";
		}
		*/

		//print_r(ftp_nlist($con, "Fotos"));
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