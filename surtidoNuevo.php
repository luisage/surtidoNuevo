<?php
include("conectaBase.php");
$sd =new conexion();
$sd->conectar(); 

$codigo=$_GET["codC"];
$solicitud=$_GET["solx"];
$cantidad=$_GET["canx"];
$usuarioID=$_GET["usuarioidx"];
$ubicacion=$_GET["codUbi"];
$necSerie = $_GET["necSeriex"];
$subcentro = $_GET["subcentro"];
if($necSerie=='1'){$noSerieEnvia = $_GET["noSerieEnviax"];}
$tipoSurtido=$_GET["tipoSurtidox"];
if($tipoSurtido=='1'){$noSerie = $_GET["noSeriex"];$canPed = $_GET["canPedx"];}
$ubicacion = $_GET["ubicacionx"];
$separar = explode('x',$codigo); 
$cod=$separar[0];
$consecutivo=$separar[1];
$codCompleto=$cod.' '.$consecutivo;
$n=0;
$estSol=0;
$est=47;
$almacenDestino='2';
$canArea='';
$estParcial='0';
$banderaSub=0; $IDalmacen=0; $pedidoVenta=""; $tipodeVenta="";
$conBDI = odbc_connect("Driver={SQL Server}; Server=10.10.0.7; Database=NAVODN;","SiscoNavision","MovNav1s10n3");
$numHis["suma"]=0;
$valNPx=mssql_query("select r.refaccionidfk from rubicacion_refaccion r inner join crefaccion re on (re.refaccionid=r.refaccionidfk) where clave='".$cod."' ");
$valNP=mssql_num_rows($valNPx);
if($valNP==0){$valNPComx=mssql_query("select top(1)c.refaccionidfk from ccomponente c inner join crefaccion re on (re.refaccionid=c.refaccionidfk) where clave='".$cod."' and (c.ubicacionidfk is not null or cajaidfk is not null)");
$valNP=mssql_num_rows($valNPComx);
}

//if (in_array($cod, $codigosNuevoP)) {//echo"NUEVO";
//if ($valNP>0) {
	/////////////////////////////// P R O C E S O  N U E V O //////////////////////////////
if($subcentro==4){$canDes="canStockTijuana=canStockTijuana-".$cantidad.""; $campo="canStockTijuana"; $banderaSub=1; $IDalmacen=3;}
if($subcentro==5){$canDes="canStockMonterrey=canStockMonterrey-".$cantidad.""; $campo="canStockMonterrey"; $banderaSub=1; $IDalmacen=4;}
if($subcentro==3){$canDes="canStockGuadalajara=canStockGuadalajara-".$cantidad.""; $campo="canStockGuadalajara"; $banderaSub=1; $IDalmacen=5;}
if($subcentro==1){$canDes="canStockLeon=canStockLeon-".$cantidad.""; $campo="canStockLeon"; $banderaSub=1; $IDalmacen=6;}
if($subcentro==2){$canDes="canStockCancun=canStockCancun-".$cantidad.""; $campo="canStockCancun"; $banderaSub=1; $IDalmacen=7;}
if($subcentro==9){$canDes="canStockPuebla=canStockPuebla-".$cantidad.""; $campo="canStockPuebla"; $banderaSub=1; $IDalmacen=42;}
if($subcentro==0){$canDes="cantidadStock=cantidadStock-".$cantidad.""; $campo="cantidadStock"; $banderaSub=1; $IDalmacen=1; }
if($banderaSub==0){$canDes="cantidadStock=cantidadStock-".$cantidad.""; $campo="cantidadStock";}

// 1 revisa si tiene existencia, necesita serie, es a granel y subfamilia y ve si existe el sign
//$refx=mssql_query("select necesitaSerie,refaccionid,isnull(granel,'0')granel2,isnull(subfamiliaidfk,'0')subfamiliaidfk2 from crefaccion where clave='".$cod."' and ".$campo.">=".$cantidad." ");
$refx=mssql_query("select necesitaSerie,refaccionid,isnull(granel,'0')granel2,isnull(subfamiliaidfk,'0')subfamiliaidfk2 from crefaccion where clave='".$cod."' ");
$exiRef=mssql_num_rows($refx);
if($exiRef==0){$json['piezas2'][]=array( 'surtido'=> 'no2'); print (json_encode($json)); return -1;}
$ref=mssql_fetch_array($refx);
/*if($ref["granel2"]==1){
	if($ubicacion=='0'){
	$json['piezas2'][]=array( 'surtido'=> 'no6'); print (json_encode($json)); return -1;
	}
	else{$arrUbi=explode("|",$ubicacion);
         if($arrUbi[1]=="Gaveta"){$verUbx=mssql_query("select ubicacionidfk from rubicacion_refaccion where refaccionidfk=".$ref["refaccionid"]." and gavetaidfk=".$arrUbi[0]." and cantidad>0");
		  				$verUb=mssql_num_rows($verUbx);
						if($verUb==0){$json['piezas2'][]=array( 'surtido'=> 'no7'); print (json_encode($json)); return -1;}
		               }
		if($arrUbi[1]=="Caja"){$verUbx=mssql_query("select ubicacionidfk from rubicacion_refaccion where refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$arrUbi[0]." and bolsaidfk=0 and cantidad>0");
		  				$verUb=mssql_num_rows($verUbx);
						if($verUb==0){$json['piezas2'][]=array( 'surtido'=> 'no7'); print (json_encode($json)); return -1;}
						}
		if($arrUbi[1]=="Ubicacion"){$verUbx=mssql_query("select ubicacionidfk from rubicacion_refaccion where refaccionidfk=".$ref["refaccionid"]." and ubicacionidfk=".$arrUbi[0]." and cajaidfk=0 and bolsaidfk=0 and cantidad>0");
		  				$verUb=mssql_num_rows($verUbx);
						if($verUb==0){$json['piezas2'][]=array( 'surtido'=> 'no7'); print (json_encode($json)); return -1;}
						}
		}
}*/
/*$ref["refaccionid"]==3514 estaba en el if de abajo*/
if($ref["refaccionid"]==3843 || $ref["refaccionid"]==3938 || $ref["refaccionid"]==4108 || $ref["refaccionid"]==4110 || $ref["refaccionid"]==4111 || $ref["refaccionid"]==4120 || $ref["refaccionid"]==4188 || $ref["refaccionid"]==4189 || $ref["refaccionid"]==4231 || $ref["refaccionid"]==4233 || $ref["refaccionid"]==4293 || $ref["refaccionid"]==4294 || $ref["refaccionid"]==4295 || $ref["refaccionid"]==4296 || $ref["refaccionid"]==4297 || $ref["refaccionid"]==4298 || $ref["refaccionid"]==4353){
	//// $ref["refaccionid"]==3720 || $ref["refaccionid"]==3974 ||
	$datosx=mssql_query("select c.id,codigo,r.clave,r.refaccionid,r.nombre ref,isnull(c.serie,'')serie,isnull(c.componenteidfk,'0')componenteidfk from ccomponente c inner join crefaccion r on (r.refaccionid=c.refaccionidfk) where codigo='".$codCompleto."'");
$numR=mssql_num_rows($datosx);
if($numR!=1){
	$json['piezas2'][]=array( 'surtido'=> 'no17'); print (json_encode($json)); return -1; //No existe el QR
	}
$datos=mssql_fetch_array($datosx);
	
	/*if($ref["refaccionid"]==3514){//915502
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3387,3572,3718,3721,3726)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}*/
		
	/*if($ref["refaccionid"]==3720){//653486
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3812,3830,4187)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){ 
		$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}*/
		
	if($ref["refaccionid"]==3843){/*915505*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3529,3718,3721,3726,3761)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
				$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==3938){/*653004*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3279,3281,3806,3812,3951,3815)");
		$infNum=mssql_num_rows($infx);
		//$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3815)"); $infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 4){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
	
	/*if($ref["refaccionid"]==3974){//653499
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3812,3830,4186)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1;// Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}*/
		
	if($ref["refaccionid"]==4108){/*653022*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3812,3830,3925,4178)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728,4274)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4110){/*653024*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3812,3830,3928,4025,4178)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4111){/*653026*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3812,3830,4109,4178)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (4025)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1;//Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4120){/*653027*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3812,3830,3925,4178,4221)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728,4274)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 1){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}


		
	if($ref["refaccionid"]==4188){/*653495*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3728,3812,3830,4186)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4189){/*653496*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3728,3812,3830,4187)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4231){/*653034*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3812,3830,4221)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728,4178)");
		$infDos=mssql_num_rows($infDosx);
		$infTresx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728,4274)");
		$infTres=mssql_num_rows($infTresx);		
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 3 && $infDos < 1 && $infTres < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4233){/*653041*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3728,3812,3830,4178,4221,4274)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1;//Serie Incorrecta
			} 
			if($infNum < 5){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==3){/*643003*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3279,3281,3812,4102,4230,4281,3951,3815,3830)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (4237)");
		$infDos=mssql_num_rows($infDosx);
		$infTresx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3815)");
		$infTres=mssql_num_rows($infTresx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5 && ($infDos < 6 || $infTres < 6)){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4293){/*654020*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3812,3830,4178,4186)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728,4274)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4294){/*654022*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3812,3830,3974,4178)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728,4274)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4295){/*654023*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3720,3812,3830,4178)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3728,4274)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 4 && $infDos < 2){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4296){/*654024*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3728,3812,3830,4178,4186,4274)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4297){/*654025*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3728,3812,3830,3974,4178,4274)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4298){/*654026*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3410,3720,3728,3812,3830,4178,4274)");
		$infNum=mssql_num_rows($infx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
		
	if($ref["refaccionid"]==4353){/*643004*/
		$infx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (3279,3281,3812,4102,4230,4281,3951)");
		$infNum=mssql_num_rows($infx);
		$infDosx=mssql_query("select * from ccomponente where componenteidfk = ".$datos["id"]." and refaccionidfk in (4237,3815)");
		$infDos=mssql_num_rows($infDosx);
		$revSeriex=mssql_query("select * from cmaquinas where serieCompleta = '".$datos["serie"]."'");
		$revSerie=mssql_num_rows($revSeriex);
		if($revSerie != 1){
			$json['piezas2'][]=array( 'surtido'=> 'no15'); print (json_encode($json)); return -1; //Serie Incorrecta
			} 
			if($infNum < 5 && $infDos < 3){
				$json['piezas2'][]=array( 'surtido'=> 'no16'); print (json_encode($json)); return -1; //Kit Incompleto
				}
		}
	}//Fin IF Signs
	//fin de 1
// 2 verifica si necesita serie y regresa en caso de necesitar

if($necSerie=='0'){
	if($ref["necesitaSerie"]==1){
		$json['piezas2'][]=array( 'surtido'=> 'conSerie'); print (json_encode($json)); return -1;
		}
}
//fin 2
//3 verifica que pertenezca al pedido y que no se halla surtido
$existex=mssql_query("select top(1)* from dsolicitud_refaccion where solicitud_refaccionidfk=$solicitud and (refaccionidfk=".$ref["refaccionid"]." and similaridfk IS NULL) and (isnull(surtido,0)=0 or isnull(surtido,0)=1)");
$existe=mssql_num_rows($existex);

if($existe==0){
	$existex=mssql_query("select top(1) * from dsolicitud_refaccion where solicitud_refaccionidfk=$solicitud and similaridfk=".$ref["refaccionid"]." and (isnull(surtido,0)=0 or 	    isnull(surtido,0)=1)");
	$existe=mssql_num_rows($existex);
	if($existe==0){$json['piezas2'][]=array( 'surtido'=> 'no2'); print (json_encode($json)); return -1;}
	}
	//fin 3
	//4 tipo pedido=1 verifica que exista  la pieza a surtir con la cantidad y la serie
if($tipoSurtido=='1'){
	$existex=mssql_query("select *from dsolicitud_refaccion where solicitud_refaccionidfk=".$solicitud." and (refaccionidfk=".$ref["refaccionid"]." and similaridfk IS NULL) and (isnull(surtido,0)=0 or isnull(surtido,0)=1) and cantidad=".$canPed." and serie='".$noSerie."'");
	$existe=mssql_num_rows($existex);
	if($existe==0){
		$existex=mssql_query("select * from dsolicitud_refaccion where solicitud_refaccionidfk=".$solicitud." and similaridfk=".$ref["refaccionid"]." and (isnull(surtido,0)=0 or isnull(surtido,0)=1) and cantidad=".$canPed." and serie='".$noSerie."'");
		}
	}
// fin 4
//5 verifica si ya se surtio y si es parcial para enviar la cantidad restante 
$pie=mssql_fetch_array($existex);

$confx=mssql_query("select h.id from HistorialSurtido h inner join ccomponente c on (c.id=h.codigoidfk)
where pedido=".$solicitud." and h.serie='".$pie["serie"]."' and c.refaccionidfk=".$ref["refaccionid"]."");
$numConf=mssql_num_rows($confx);
if($numConf==1 && $pie["estatusidfk"]!=33){$json['piezas2'][]=array( 'surtido'=> 'no2'); print (json_encode($json)); return -1;}
//fin 5
//6 verifica si es similar y a granel
if($pie["estatusidfk"]==51){$ref["refaccionid"]=$pie["similaridfk"]; $est=51;}

if($ref["granel2"]=='1' && ($cantidad<=0 || $ubicacion==0)){$json['granel'][]=array( 'granel2'=> 'si-.-'.$pie["cantidad"]); $json['proceso']='nuevo'; print (json_encode($json)); return -1; }

if($ref["granel2"]=='0'){$cantidad=1;}
//fin 6
// 7 verifica que exista el QR y con estatus 1 
if($existe>=1){/////isnull(c.qrCompleto,0)qrCompleto
	$codigox=mssql_query("select c.id,c.refaccionidfk,r.clave,c.codigo,isnull(c.ubicacionidfk,0)ubicacionidfk from ccomponente c inner join crefaccion r on (r.refaccionid=c.refaccionidfk)
where codigo='".$codCompleto."' and estatus='1' ");
	$exiCod=mssql_num_rows($codigox);
	if($exiCod==0){$json['piezas2'][]=array( 'surtido'=> 'no3'); print (json_encode($json)); return -1;}
	$codigo=mssql_fetch_array($codigox);
	if($codigo["ubicacionidfk"]==0 && $ubicacion==0){$json['granel'][]=array( 'granel2'=> 'si-.-'.$pie["cantidad"]); $json['proceso']='nuevo'; print (json_encode($json)); return -1;}
//fin 7
//8 duda en que piezas verifica**********
				/*if($ref["refaccionid"]==1582 || $ref["refaccionid"]==1673 || $ref["refaccionid"]==3514 || $ref["refaccionid"]==3720 || $ref["refaccionid"]==3843 || $ref["refaccionid"]==3925 || $ref["refaccionid"]==3928 || $ref["refaccionid"]==3938 || $ref["refaccionid"]==3974){
					if($codigo["qrCompleto"]==0){$json['piezas2'][]=array( 'surtido'=> 'no5'); print (json_encode($json)); return -1;}
					}*/
//fin 8
//8.1 N U E V O    valida ubicacion que cantidad sea correcta
//if($ref["granel2"]=='1' && $cantidad>0 && $ubicacion!=0){
	$valEntra=0; $valEntra2=0;
	$ins=mssql_query("insert into registroSurtido values('".$ubicacion."',5,".$cantidad.",".$solicitud.",".$ref["refaccionid"].",getdate())");
if(($ref["granel2"]=='1' && $cantidad>0 && $ubicacion!=0) || ($ref["granel2"]=='0' && $codigo["ubicacionidfk"]==0 && $ubicacion!=0)){$valEntra=1;
$parteUbi = explode('|',$ubicacion);
if($parteUbi[1]!="Ubicacion" && $parteUbi[1]!="Caja" && $parteUbi[1]!="Bolsa" && $parteUbi[1]!="Gaveta"){
	$json['piezas2'][]=array( 'surtido'=> 'no12'); print (json_encode($json));
	$ins=mssql_query("insert into registroSurtido values('".$ubicacion."',3,".$cantidad.",".$solicitud.",".$ref["refaccionid"].",getdate())"); 
	return -1;
	}

if($parteUbi[1]=="Ubicacion"){$valCanUbix=mssql_query("select * from rubicacion_refaccion where ubicacionidfk=".$parteUbi["0"]." and refaccionidfk=".$ref["refaccionid"]." ");
$numValUbi=mssql_num_rows($valCanUbix);
if($numValUbi==1){$valCanUbi=mssql_fetch_array($valCanUbix);
if($valCanUbi["cantidad"]>$cantidad){$desInventario=mssql_query("update rubicacion_refaccion set cantidad=cantidad-".$cantidad." where ubicacionidfk=".$valCanUbi["ubicacionidfk"]." and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$valCanUbi["cajaidfk"]." and bolsaidfk=".$valCanUbi["bolsaidfk"]." and gavetaidfk=".$valCanUbi["gavetaidfk"]." ");
  $desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-".$cantidad." where refaccionid  = ".$ref["refaccionid"]." ");}
if($valCanUbi["cantidad"]==$cantidad){$desInventario=mssql_query("delete rubicacion_refaccion where ubicacionidfk=".$valCanUbi["ubicacionidfk"]." and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$valCanUbi["cajaidfk"]." and bolsaidfk=".$valCanUbi["bolsaidfk"]." and gavetaidfk=".$valCanUbi["gavetaidfk"]." ");
  $desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-".$cantidad." where refaccionid  = ".$ref["refaccionid"]." ");}
if($valCanUbi["cantidad"]<$cantidad){$json['piezas2'][]=array( 'surtido'=> 'no10'); print (json_encode($json)); return -1;}
}
else{$json['piezas2'][]=array( 'surtido'=> 'no12'); print (json_encode($json)); return -1;}
}
//caja
if($parteUbi[1]=="Caja"){
	$valCanUbix=mssql_query("select * from rubicacion_refaccion where cajaidfk=".$parteUbi["0"]." and refaccionidfk=".$ref["refaccionid"]." and bolsaidfk=0 ");
	$numValUbi=mssql_num_rows($valCanUbix);
	if($numValUbi==1){
		$valCanUbi=mssql_fetch_array($valCanUbix);//echo $valCanUbi["cantidad"]." -- ".$cantidad;
		if($valCanUbi["cantidad"]>$cantidad){$desInventario=mssql_query("update rubicacion_refaccion set cantidad=cantidad-".$cantidad." where ubicacionidfk=".$valCanUbi["ubicacionidfk"]." and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".						$valCanUbi["cajaidfk"]." and bolsaidfk=".$valCanUbi["bolsaidfk"]." and gavetaidfk=".$valCanUbi["gavetaidfk"]."   update ccaja set estatus='3' where cajaid=".$parteUbi["0"]."");
		
  		$desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-".$cantidad." where refaccionid  = ".$ref["refaccionid"]." ");
  		}
		if($valCanUbi["cantidad"]==$cantidad){$desInventario=mssql_query("delete rubicacion_refaccion where ubicacionidfk=".$valCanUbi["ubicacionidfk"]." and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$valCanUbi["cajaidfk"]." and bolsaidfk=".$valCanUbi["bolsaidfk"]." and gavetaidfk=".$valCanUbi["gavetaidfk"]."   update ccaja set estatus='4' where cajaid=".$parteUbi["0"]."");
  		$desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-".$cantidad." where refaccionid  = ".$ref["refaccionid"]." ");
		}
		if($valCanUbi["cantidad"]<$cantidad){
			$json['piezas2'][]=array( 'surtido'=> 'no12'); print (json_encode($json)); return -1;
		}
	}
//aqui entra
	else{
		$json['piezas2'][]=array( 'surtido'=> 'no12'); print (json_encode($json)); return -1;
		}
}
//bolsa
if($parteUbi[1]=="Bolsa"){$valCanUbix=mssql_query("select * from rubicacion_refaccion where bolsaidfk=".$parteUbi["0"]." and refaccionidfk=".$ref["refaccionid"]." ");
$numValUbi=mssql_num_rows($valCanUbix);
if($numValUbi==1){$valCanUbi=mssql_fetch_array($valCanUbix);//echo $valCanUbi["cantidad"]." -- ".$cantidad;
if($valCanUbi["cantidad"]>$cantidad){$desInventario=mssql_query("update rubicacion_refaccion set cantidad=cantidad-".$cantidad." where ubicacionidfk=".$valCanUbi["ubicacionidfk"]." and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$valCanUbi["cajaidfk"]." and bolsaidfk=".$valCanUbi["bolsaidfk"]." and gavetaidfk=".$valCanUbi["gavetaidfk"]." ");
$upcaja=mssql_query("update ccaja set estatus='3' where cajaid=".$valCanUbi["cajaidfk"]."");
  $desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-".$cantidad." where refaccionid  = ".$ref["refaccionid"]." ");}
if($valCanUbi["cantidad"]==$cantidad){$desInventario=mssql_query("delete rubicacion_refaccion where ubicacionidfk=".$valCanUbi["ubicacionidfk"]." and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$valCanUbi["cajaidfk"]." and bolsaidfk=".$valCanUbi["bolsaidfk"]." and gavetaidfk=".$valCanUbi["gavetaidfk"]." ");

$exisCajax=mssql_query("select cajaidfk from rubicacion_refaccion where cajaidfk=".$valCanUbi["cajaidfk"]." ");
$exisCaja=mssql_num_rows($exisCajax);
if($exisCaja==0){
$upcaja=mssql_query("update ccaja set estatus='4' where cajaid=".$valCanUbi["cajaidfk"]."");
}
if($exisCaja>0){
$upcaja=mssql_query("update ccaja set estatus='3' where cajaid=".$valCanUbi["cajaidfk"]."");
}

  $desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-".$cantidad." where refaccionid  = ".$ref["refaccionid"]." ");}
if($valCanUbi["cantidad"]<$cantidad){$json['piezas2'][]=array( 'surtido'=> 'no12'); print (json_encode($json)); return -1;}
}
else{$json['piezas2'][]=array( 'surtido'=> 'no12'); print (json_encode($json)); return -1;}
}
//gaveta
if($parteUbi[1]=="Gaveta"){$valCanUbix=mssql_query("select * from rubicacion_refaccion where gavetaidfk=".$parteUbi["0"]." and refaccionidfk=".$ref["refaccionid"]."");
$numValUbi=mssql_num_rows($valCanUbix);
if($numValUbi==1){$valCanUbi=mssql_fetch_array($valCanUbix);
if($valCanUbi["cantidad"]>$cantidad){$desInventario=mssql_query("update rubicacion_refaccion set cantidad=cantidad-".$cantidad." where ubicacionidfk=".$valCanUbi["ubicacionidfk"]." and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$valCanUbi["cajaidfk"]." and bolsaidfk=".$valCanUbi["bolsaidfk"]." and gavetaidfk=".$valCanUbi["gavetaidfk"]." ");
  $desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-".$cantidad." where refaccionid  = ".$ref["refaccionid"]." ");}
if($valCanUbi["cantidad"]==$cantidad){$desInventario=mssql_query("delete rubicacion_refaccion where ubicacionidfk=".$valCanUbi["ubicacionidfk"]." and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$valCanUbi["cajaidfk"]." and bolsaidfk=".$valCanUbi["bolsaidfk"]." and gavetaidfk=".$valCanUbi["gavetaidfk"]." ");
  $desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-".$cantidad." where refaccionid  = ".$ref["refaccionid"]." ");}
if($valCanUbi["cantidad"]<$cantidad){$json['piezas2'][]=array( 'surtido'=> 'no14'); print (json_encode($json)); return -1;}
}
else{$json['piezas2'][]=array( 'surtido'=> 'no12'); print (json_encode($json)); return -1;}
}
}//fin de if if($ref["granel2"]=='1' && $cantidad>0 && $ubicacion!=0){
else{if($ubicacion!=0){$ins=mssql_query("insert into registroSurtido values('".$ubicacion."',2,".$cantidad.",".$solicitud.",".$ref["refaccionid"].",getdate())");}  }

if($valEntra==1){$ins=mssql_query("insert into registroSurtido values('".$ubicacion."',1,".$cantidad.",".$solicitud.",".$ref["refaccionid"].",getdate())");}
// fin 8.1 
//9 trae datos del pedido estatus departamento tecnico ingresa a historialSolRefaccion y HistorialSurtido
	$estPedx=mssql_query("select estatusidfk,isnull(departamentoidfk,0)departamentoidfk,tecnicoidfk,estatus from tsolicitud_refaccion where solicitud_refaccionid=".$solicitud." ");
	$estPed=mssql_fetch_array($estPedx);
	
	$pedidoVenta=""; $tipodeVenta="";
	if($estPed["estatus"]=='Venta Zitro'){$pedidoVenta="SI"; $tipodeVenta="Venta Zitro";}
	if($estPed["estatus"]=='Venta ODN'){$pedidoVenta="SI"; $tipodeVenta="Venta ODN";}
	
	if($estPed["estatusidfk"]==30){mssql_query("update tsolicitud_refaccion set estatusidfk=115 where solicitud_refaccionid=$solicitud ");
	mssql_query("insert into historialSolRefaccion values(".$solicitud.",115,getdate(),".$usuarioID.") ");}
	$insert=mssql_query("insert into HistorialSurtido values(".$codigo["id"].",'Salida',".$solicitud.",NULL,'1','".$pie["serie"]."',".$cantidad.",'', NULL) ");
//fin 9
//10 trae datos de tsolicitud_refaccion salaid y nombre de la sala folio o si es de OS
	$salaCBx=mssql_query("select isnull(sa.nombre,'0') sala1,ISNULL(sa.salaid,'0') id1,isnull(sa2.nombre,'0') sala2,ISNULL(sa2.salaid,'0') id2, isnull(sa3.nombre,'0') sala3,ISNULL(sa3.salaid,'0')id3, isnull(sa4.nombre,'0') sala4, ISNULL(sa4.departamentoid,'0')id4
	from tsolicitud_refaccion t
left join tfolio_solicitud f on (f.folio_solicitudid=t.folio_solicitudidfk)
left join csala sa on (sa.salaid=f.salaidfk)
left join csala sa2 on (sa2.salaid=t.salaidfk)
left join torden_Servicio o on (cast(o.ordenid as varchar(10))=t.folio_solicitudidfk)
left join csala sa3 on (sa3.salaid=o.salaidfk)
left join cdepartamento sa4 on (sa4.departamentoid=t.departamentoidfk)
where solicitud_refaccionid=".$solicitud." ");
            $sala=mssql_fetch_array($salaCBx);
			$nomSala='';
			$salaid=0;
			if($sala["sala1"]!='0'){
				$nomSala=$sala["sala1"]; $salaid=$sala["id1"];  
				}
				else{ 
			        if($sala["sala2"]!='0'){
						$nomSala=$sala["sala2"]; $salaid=$sala["id2"];
						}
			            else{
							if($sala["sala3"]!='0'){
					           $nomSala=$sala["sala3"]; $salaid=$sala["id3"];
							}
			                else{
						        $nomSala=$sala["sala4"]; $salaid=$sala["id4"];
						        }
						}
				}
				$almacenDestino='2';
// fin 10
//11 verifica si es subcentro el origen y destino 
	   if($salaid=='188'){$canArea="canStockTijuana=canStockTijuana+".$cantidad."";$almacenDestino='3';}
	   if($salaid=='189'){$canArea="canStockMonterrey=canStockMonterrey+".$cantidad.""; $almacenDestino='4';}
	   if($salaid=='200'){$canArea="canStockGuadalajara=canStockGuadalajara+".$cantidad.""; $almacenDestino='5';}
	   if($salaid=='375'){$canArea="canStockLeon=canStockLeon+".$cantidad.""; $almacenDestino='6';}
	   if($salaid=='374'){$canArea="canStockCancun=canStockCancun+".$cantidad.""; $almacenDestino='7';}
	   if($salaid=='1403'){$canArea="canStockEUA=canStockEUA+".$cantidad.""; $almacenDestino='8';}
	   if($salaid=='1404'){$canArea="canStockLighting=canStockLighting+".$cantidad.""; $almacenDestino='9';}
	   if($salaid=='1136'){$canArea="canStockLV=canStockLV+".$cantidad.""; $almacenDestino='10';}
	   if($salaid=='2263'){$canArea="canStockPuebla=canStockPuebla+".$cantidad.""; $almacenDestino='42';}
	   
	   if($subcentro==4){$canDes="canStockTijuana=canStockTijuana-".$cantidad.""; $campo="canStockTijuana"; $banderaSub=1;}
if($subcentro==5){$canDes="canStockMonterrey=canStockMonterrey-".$cantidad.""; $campo="canStockMonterrey"; $banderaSub=1;}
if($subcentro==3){$canDes="canStockGuadalajara=canStockGuadalajara-".$cantidad.""; $campo="canStockGuadalajara"; $banderaSub=1;}
if($subcentro==1){$canDes="canStockLeon=canStockLeon-".$cantidad.""; $campo="canStockLeon"; $banderaSub=1;}
if($subcentro==2){$canDes="canStockCancun=canStockCancun-".$cantidad.""; $campo="canStockCancun"; $banderaSub=1;}
if($subcentro==0){$canDes="cantidadStock=cantidadStock-".$cantidad.""; $campo="cantidadStock"; $banderaSub=1;}
if($banderaSub==0){$canDes="cantidadStock=cantidadStock-".$cantidad.""; $campo="cantidadStock";}
if($estPed["departamentoidfk"]==16){$canArea="cantidadDesarrollo=cantidadDesarrollo+".$cantidad."";}

// fin 11
// 12 descuento en inventario e insert en tmovimiento
if($salaid=='200'){
	/////////////////////////
//	if($usu["departamento"]=='StockGuadalajara'){
		//if($encqr==0){
$exisUbix=mssql_query("select * from rubicacion_refaccion where ubicacionidfk=495 and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=0 and bolsaidfk=0 and gavetaidfk=0");
$numExisUbi = mssql_num_rows($exisUbix);
$exisUbi=mssql_fetch_array($exisUbix);
if($numExisUbi==1){$canNueva=$cantidad+$exisUbi["cantidad"];
mssql_query("update rubicacion_refaccion set cantidad=".$canNueva." where ubicacionidfk=495 and refaccionidfk=".$ref["refaccionid"]." and cajaidfk=".$exisUbi["cajaidfk"]." and bolsaidfk=".$exisUbi["bolsaidfk"]." and gavetaidfk=".$exisUbi["gavetaidfk"]." ");
$insertMov=mssql_query("insert into tmovimientoUbicaciones(fecha,tipo,origen,destino,observacion,usuarioidfk,cantidad)values(GETDATE(),'Envio a Guadalajara ".$solicitud."','Almacen Central','Ubicacion GDL1AA1','Envio a subcentro Guadalajara ".$solicitud."',".$usuarioID.",".$cantidad.")");
}
else{
$query2=mssql_query("insert into rubicacion_refaccion (ubicacionidfk,refaccionidfk,cajaidfk,bolsaidfk,gavetaidfk,estatus,cantidad)
values(495,".$ref["refaccionid"].",0,0,0,'Correcto',".$cantidad.")");
$insertMov=mssql_query("insert into tmovimientoUbicaciones(fecha,tipo,origen,destino,observacion,usuarioidfk,cantidad)values(GETDATE(),'Envio a Guadalajara ".$solicitud."','Almacen Central','Ubicacion GDL1AA1','Envio a subcentro Guadalajara ".$solicitud."',".$usuarioID.",".$cantidad.")");
}
	//}//encqr==0
//   }
////////////////////////
	}

if($canArea!=''){
	   $updatei="update crefaccion set ".$canArea." where clave  = '" .$cod. "' ";
		$resui = mssql_query($updatei);
}
if($tipodeVenta=='Venta ODN'){
	$movimiento=mssql_query("INSERT INTO tmovimiento(refaccionidfk,fecha,origen,destino,cantidad,observacion,usuarioidfk,paisidfk)VALUES
			(".$ref["refaccionid"].",getdate(),'Stock','Almacen venta',".$cantidad.",'Salida solicitada por CC No. ".$solicitud."',".$usuarioID.",1),
			(".$ref["refaccionid"].",getdate(),'Almacen venta','".$nomSala."',".$cantidad.",'Salida solicitada por CC No. ".$solicitud."',".$usuarioID.",1)");
			
}
else{
	$movimiento=mssql_query("INSERT INTO tmovimiento(refaccionidfk,fecha,origen,destino,cantidad,observacion,usuarioidfk,paisidfk)VALUES
			(".$ref["refaccionid"].",getdate(),'Stock','".$nomSala."',".$cantidad.",'Salida solicitada por CC No. ".$solicitud."',".$usuarioID.",1)");
}
//fin 12
//13 verifica si es de tecnico e incrementa el adeudo del material
if($estPed["departamentoidfk"]==0){
$canAdeudoPen=0;  
if($ref["subfamiliaidfk2"]!=0){
$exiAdeudox=mssql_query("select cantidad from tadeudo_material where tecnicoidfk=".$estPed["tecnicoidfk"]." and subfamiliaidfk=".$ref["subfamiliaidfk2"]."");
$numAdeudo = mssql_num_rows($exiAdeudox);
$exiAdeudoCan=mssql_fetch_array($exiAdeudox);
if($numAdeudo==1){mssql_query("update tadeudo_material set cantidad=cantidad+".$cantidad." where tecnicoidfk=".$estPed["tecnicoidfk"]." and subfamiliaidfk=".$ref["subfamiliaidfk2"]." ");  $canAdeudoPen=$exiAdeudoCan["cantidad"]+$cantidad; }
if($numAdeudo==0){mssql_query("insert into tadeudo_material values(".$estPed["tecnicoidfk"].",".$ref["subfamiliaidfk2"].",".$cantidad.") ");  $canAdeudoPen=$cantidad; $exiAdeudoCan["cantidad"]=0;}

mssql_query("insert into hisAdeudoMat (tecnicoidfk,subfamiliaidfk,canSalida,canPendiente,balance,fecha,pedidoidfk) values(".$estPed["tecnicoidfk"].",".$ref["subfamiliaidfk2"].",".$cantidad.",".$exiAdeudoCan["cantidad"].",".$canAdeudoPen.",getdate(),".$solicitud.") ");
mssql_query("insert into tecnicosComAdeudo (tecnicoidfk,componenteidfk,refaccionidfk,pedido,fecha,cantidad,estatus,devolucionidfk,comentarios)
values(".$estPed["tecnicoidfk"].",".$codigo["id"].",".$ref["refaccionid"].",".$solicitud.",GETDATE(),1,'0',NULL,'')");
}//fin de if subfallaidfk!=0
}//fin de if departamentoidfk==0
// fin 13
//14  N A V I S I O N
//////////////////////////////////////N A V I S I O N////////////////////////
$queryNav="select top(1)[No_ movimiento] movimiento from [Interf_ Input NAV - SISCO] order by [No_ movimiento] desc";
$queNAV = odbc_exec($conBDI, $queryNav);
$revMov = odbc_fetch_array($queNAV);
if($revMov["movimiento"]=='NULL' || $revMov["movimiento"] == ''){
$queNAV = odbc_exec($conBDI, $queryNav);
$revMov = odbc_fetch_array($queNAV);
}
if($revMov["movimiento"]=='NULL' || $revMov["movimiento"] == '' || $revMov["movimiento"] ==0){$incremental=1;
mssql_query("insert into regSurtido values('".$revMov["movimiento"]."',0,GETDATE(),1,'".$revMov["movimiento"]."','1')");
}else{$incremental = $revMov["movimiento"] + 1;}

$updateNav = "INSERT INTO [Interf_ Input NAV - SISCO]([Cod_ Producto],[Fecha registro],[Tipo movimiento],[No_ Documento],Descripcion,[ID Almacen SISCO],Cantidad,Estatus,Error,[No_ movimiento],[Fecha Alta],[Cod_ almacen])VALUES('".$cod."',CONVERT(varchar(10), GETDATE(), 103),3,'".$solicitud."','',".$IDalmacen.",".$cantidad.",1,'',".$incremental.",CONVERT(varchar(10), GETDATE(), 103),0) ";
//$updNAV = odbc_exec($conBDI, $updateNav);

if($updNAV = odbc_exec($conBDI, $updateNav)){/*mssql_query("insert into regSurtido values('".$cod."',".$solicitud.",GETDATE(),1,'".$updateNav."','1')");*/
    mssql_query("insert into logNavision(codigo,solicitud,IDalmacen,cantidad,incremental,movimiento,estatus,fecha)
values('".$cod."',".$solicitud.",".$IDalmacen.",".$cantidad.",".$incremental.",'3','1',GETDATE())");
}
else{/*mssql_query("insert into regSurtido values('".$cod."',".$solicitud.",GETDATE(),1,'".$updateNav."','0')");*/
      mssql_query("insert into logNavision(codigo,solicitud,IDalmacen,cantidad,incremental,movimiento,estatus,fecha)
values('".$cod."',".$solicitud.",".$IDalmacen.",".$cantidad.",".$incremental.",'3','0',GETDATE())");}

//inicio pedido venta
//ingreso a 47
if($tipodeVenta=='Venta ODN'){
	$incremental++;
$updateNav = "INSERT INTO [Interf_ Input NAV - SISCO]([Cod_ Producto],[Fecha registro],[Tipo movimiento],[No_ Documento],Descripcion,[ID Almacen SISCO],Cantidad,Estatus,Error,[No_ movimiento],[Fecha Alta],[Cod_ almacen])
	VALUES('".$cod."',CONVERT(varchar(10), GETDATE(), 103),2,'".$solicitud."','',47,".$cantidad.",1,'',".$incremental.",CONVERT(varchar(10), GETDATE(), 103),0) ";
	$incremental++;
$updateNav = $updateNav . " INSERT INTO [Interf_ Input NAV - SISCO]([Cod_ Producto],[Fecha registro],[Tipo movimiento],[No_ Documento],Descripcion,[ID Almacen SISCO],Cantidad,Estatus,Error,[No_ movimiento],[Fecha Alta],[Cod_ almacen])
VALUES('".$cod."',CONVERT(varchar(10), GETDATE(), 103),3,'".$solicitud."','',47,".$cantidad.",1,'',".$incremental.",CONVERT(varchar(10), GETDATE(), 103),0) ";
$updNAV = odbc_exec($conBDI, $updateNav);
}
//salida a 47
//fin pedido venta

if($salaid=='188' || $salaid=='189' || $salaid=='200' || $salaid=='375' || $salaid=='374' || $salaid=='1403' || $salaid=='1404' || $salaid=='1136'){
$queryNav="select top(1)[No_ movimiento] movimiento from [Interf_ Input NAV - SISCO] order by [No_ movimiento] desc";
$queNAV = odbc_exec($conBDI, $queryNav);
$revMov = odbc_fetch_array($queNAV);
if($revMov["movimiento"]=='NULL' || $revMov["movimiento"] == '' || $revMov["movimiento"] ==0){$incremental=1;}else
{$incremental = $revMov["movimiento"] + 1;}

$updateNav = "INSERT INTO [Interf_ Input NAV - SISCO]([Cod_ Producto],[Fecha registro],[Tipo movimiento],[No_ Documento],Descripcion,[ID Almacen SISCO],Cantidad,Estatus,Error,[No_ movimiento],[Fecha Alta],[Cod_ almacen])VALUES('".$cod."',CONVERT(varchar(10), GETDATE(), 103),2,'".$solicitud."','',".$almacenDestino.",".$cantidad.",1,'',".$incremental.",CONVERT(varchar(10), GETDATE(), 103),0) ";
//$updNAV = odbc_exec($conBDI, $updateNav);

if($updNAV = odbc_exec($conBDI, $updateNav)){
    mssql_query("insert into logNavision(codigo,solicitud,IDalmacen,cantidad,incremental,movimiento,estatus,fecha)
values('".$cod."',".$solicitud.",".$almacenDestino.",".$cantidad.",".$incremental.",'2','1',GETDATE())");
}
else{
      mssql_query("insert into logNavision(codigo,solicitud,IDalmacen,cantidad,incremental,movimiento,estatus,fecha)
values('".$cod."',".$solicitud.",".$almacenDestino.",".$cantidad.",".$incremental.",'2','0',GETDATE())");}

}//Fin if subcentros
//////////////////////////////////////////////////////F I N  N A V I S I O N
//fin 14
//15 cambia a estatus 2 el componente
	if($ref["granel2"]!='1'){	//mssql_query("update ccodigo set estatus='2' where codigoid=".$codigo["codigoid"]." ");
	mssql_query("update ccomponente set estatus='2',ubicacionidfk=NULL,cajaidfk=NULL,bolsaidfk=NULL,gavetaidfk=NULL where id=".$codigo["id"]." ");
    $desInventario=mssql_query("update crefaccion set cantidadStock=cantidadStock-1 where refaccionid  = ".$codigo["refaccionidfk"]." ");
	}
//fin 15 
//16 verifica la cantidad surtida en HistorialSurtido y coloca si se ha surtido parcial, si es a granel y se surte parcial esa partida la coloca como completa y crea otra partida con la cantidad pendiente
$numHisx=mssql_query("select SUM(isnull(cantidad,1))suma from HistorialSurtido h inner join ccomponente c on (c.id=h.codigoidfk)
where c.refaccionidfk='".$codigo["refaccionidfk"]."' and pedido=".$solicitud." and h.serie='".$pie["serie"]."' ");
    $numHis=mssql_fetch_array($numHisx);
    if($numHis["suma"]<$pie["cantidad"]){$estSol=1; $est=33;
	////////////
	if($ref["granel2"]=='1'){
	$canPendiente=$pie["cantidad"] - $numHis["suma"];
	$cambia=mssql_query("update dsolicitud_refaccion set estatusidfk=47,cantidad=".$cantidad.",surtido='2' where solicitud_refaccionidfk=".$solicitud." and (refaccionidfk=".$ref["refaccionid"]." or similaridfk=".$ref["refaccionid"].") and serie='".$pie["serie"]."' ");
   $estParcial='1';
for($i=0;$i<50;$i++){
$serExix=mssql_query("select * from dsolicitud_refaccion where solicitud_refaccionidfk=".$solicitud." and serie='SeriePendiente".$i."' ");
$conSerExi=mssql_num_rows($serExix);
if($conSerExi==0){$serieEnviaSP='SeriePendiente'.$i; $c=$i; $i=50;}
}
$insert2=mssql_query("INSERT INTO dsolicitud_refaccion(solicitud_refaccionidfk,refaccionidfk,cantidad,serie,view2,descripcion,folioRetorno,estatusidfk
,denominacion,licencia,version,ip,juegoidfk,entregaParcial,estatus_fr,observacion,similaridfk,salidaidfk,no_guia,patrimonioidfk
,localizacion,refaccionRetorno,serieEnvia,diagnostico)
 VALUES(".$solicitud.",".$ref["refaccionid"].",".$canPendiente.",'".$serieEnviaSP."','','','',43,'','','','',50,null,'','Creada por entrega parcial',null,null,'',null,'',null,'','')");
	}
		///////////
	}//fin if($numHis["suma"]<$pie["cantidad"])
	else{$estSol=2; if($est!=51){$est=47;}}
//fin 16
//17 verifica si no es parcial realiza update a dsolicitud_refaccion como surtida
	if($estParcial!='1'){
	$up=mssql_query("update dsolicitud_refaccion set surtido=".$estSol.", estatusidfk=".$est."  where solicitud_refaccionidfk=".$solicitud." and (refaccionidfk=".$ref["refaccionid"]." or similaridfk=".$ref["refaccionid"].") and serie='".$pie["serie"]."' ");
	}else{$estParcial='0';}
//fin 17
//18 verifica si faltan partidas por surtir, si no faltan, si es sdepartamentoidfk 7 o 8 le pone 119 si no le coloca 116 en estatus a la partida
	$falx=mssql_query("select * from dsolicitud_refaccion where solicitud_refaccionidfk=$solicitud and (isnull(surtido,'0')=0 or isnull(surtido,'0')=1) and estatusidfk!=31");
	$fal=mssql_num_rows($falx);

	if($fal==0){if($estPed["departamentoidfk"]==8 || $estPed["departamentoidfk"]==7){
		$cambia=mssql_query("update dsolicitud_refaccion set localizacion='calle',estatus_fr='Enviado',enviado='1',fechaEnvio=getdate(),estatusidfk=47 where solicitud_refaccionidfk=".$solicitud." and surtido='2' ");
			$up2=mssql_query("update tsolicitud_refaccion set estatusidfk=119,fecha_envio=getdate() where solicitud_refaccionid=".$solicitud." "); $n=1;
	mssql_query("insert into historialSolRefaccion values(".$solicitud.",119,getdate(),".$usuarioID.") ");
	}
	else{
	$up2=mssql_query("update tsolicitud_refaccion set estatusidfk=116 where solicitud_refaccionid=".$solicitud." "); $n=1;
	mssql_query("insert into historialSolRefaccion values(".$solicitud.",116,getdate(),".$usuarioID.") ");}
	$json['piezas'][]=array( 'ref'=> 'Completo');
	}
	}
//fin 18
// 19 entra si aún quedan partidas por surtir y regresa las partidas que faltan con las cantidades restantes a la tablet
if($n==0){
	$piezasx=mssql_query("select r.refaccionid,r.clave,r.clave+'  '+r.nombre+'  Existencia='+CONVERT(varchar, r.".$campo.")  ref, '\nJuego: '+ ju.nombre+ ', Version: '+d.version+ ', Denominacion: '+d.denominacion infoCPU,isnull(d.surtido,'0')surtido,serie,cantidad,CONVERT(varchar,solicitud_refaccionidfk)+','+CONVERT(varchar,refaccionid)+','+d.serie datos,isnull(similaridfk,'0')similar,CONVERT(varchar, r.".$campo.")canAnterior from dsolicitud_refaccion d inner join crefaccion r on (r.refaccionid=d.refaccionidfk)
inner join cjuego ju on (ju.juegoid=d.juegoidfk) where solicitud_refaccionidfk=$solicitud and d.estatusidfk!=31 order by surtido desc");	
$json=array();
while ($res=mssql_fetch_array($piezasx)){
	$ubix=mssql_query("select top 1 'B'+u.bodega+' '+RIGHT('00000000' +convert(varchar,rack),2)+lado+columna+convert(varchar,fila) ubicacion from ccomponente c left join cubicacion3 u on (u.ubicacionid=c.ubicacionidfk)
where refaccionidfk=".$res["refaccionid"]." and ubicacionidfk is not null ");
    $numComUbi=mssql_num_rows($ubix);
    $ubi=mssql_fetch_array($ubix);
	
	if($ubi["ubicacion"]=='' || $ubi["ubicacion"]==' '){
		$ubix=mssql_query("select top 1 'B'+u.bodega+' '+RIGHT('00000000' +convert(varchar,rack),2)+lado+columna+convert(varchar,fila) ubicacion from rubicacion_refaccion ru inner join cubicacion3 u on (u.ubicacionid=ru.ubicacionidfk) where refaccionidfk=".$res["refaccionid"]." and ubicacionidfk!=0");
		$numGranelUbi=mssql_num_rows($ubix);
        $ubi=mssql_fetch_array($ubix);
        }
		if($numComUbi!=0 || $numGranelUbi!=0){
	$canGranelx=mssql_query("select SUM(cantidad)cantidad from rubicacion_refaccion where refaccionidfk=".$res["refaccionid"]." and estatus='Correcto' ");
	$canGranel=mssql_fetch_array($canGranelx);
    $canComx=mssql_query("select id from ccomponente where refaccionidfk=".$res["refaccionid"]." and ubicacionidfk is not null and cajaidfk is not null and bolsaidfk is not null and bolsaidfk is not null ");
	$canCom=mssql_num_rows($canComx);
	$cantidad=$canGranel["cantidad"]+$canCom;
	$res["ref"]=$res["ref"]."  Existencia=".$cantidad;
	}
	if($numComUbi==0 && $numGranelUbi==0){$res["ref"]=$res["ref"]."  Existencia=".$res["canAnterior"];}
	
	if($res["clave"]=='601015' || $res["clave"]=='601019' || $res["clave"]=='601020' || $res["clave"]=='601023' || $res["clave"]=='601025' || $res["clave"]=='601026' || $res["clave"]=='601027' || $res["clave"]=='601028' || $res["clave"]=='601030' || $res["clave"]=='601035' || $res["clave"]=='601036' || $res["clave"]=='601040' || $res["clave"]=='601045' || $res["clave"]=='601046' || $res["clave"]=='601047' || $res["clave"]=='601048' || $res["clave"]=='601049' || $res["clave"]=='900035' || $res["clave"]=='601018' || $res["clave"]=='602005' || $res["clave"]=='602006' || $res["clave"]=='602007' || $res["clave"]=='602008' || $res["clave"]=='602009' || $res["clave"]=='602010' || $res["clave"]=='602011' || $res["clave"]=='602012' || $res["clave"]=='602013' || $res["clave"]=='201005' || $res["clave"]=='201008' || $res["clave"]=='201110' || $res["clave"]=='201115' || $res["clave"]=='201116' || $res["clave"]=='201120' || $res["clave"]=='201121' || $res["clave"]=='201122' || $res["clave"]=='201123' || $res["clave"]=='201125' || $res["clave"]=='201126' || $res["clave"]=='201130' || $res["clave"]=='201131' || $res["clave"]=='555033' || $res["clave"]=='555040'){
		if($res["clave"]=='601015' || $res["clave"]=='601019' || $res["clave"]=='601020' || $res["clave"]=='601023' || $res["clave"]=='601025' || $res["clave"]=='601026' || $res["clave"]=='601027' || $res["clave"]=='601028' || $res["clave"]=='601030' || $res["clave"]=='601035' || $res["clave"]=='601036' || $res["clave"]=='601040' || $res["clave"]=='601045' || $res["clave"]=='601046' || $res["clave"]=='601047' || $res["clave"]=='601048' || $res["clave"]=='900035' || $res["clave"]=='601018' || $res["clave"]=='602005' || $res["clave"]=='602006' || $res["clave"]=='602007' || $res["clave"]=='602008' || $res["clave"]=='602009' || $res["clave"]=='602010' || $res["clave"]=='602011' || $res["clave"]=='602012' || $res["clave"]=='602013' || $res["clave"]=='201008' || $res["clave"]=='555033' || $res["clave"]=='555040'){//2
			if($officeID["OfficeID"]=='7' || $officeID["OfficeID"]=='12' || $officeID["OfficeID"]=='26' || $officeID["OfficeID"]=='59' || $officeID["OfficeID"]=='67' || $officeID[		"OfficeID"]=='70' || $officeID["OfficeID"]=='81' || $officeID["OfficeID"]=='99' || $officeID["OfficeID"]=='102' || $officeID["OfficeID"]=='103' || $officeID["OfficeID"]=='108' || $officeID["OfficeID"]=='117' || $officeID["OfficeID"]=='121' || $officeID["OfficeID"]=='124' || $officeID["OfficeID"]=='126' || $officeID["OfficeID"]=='129' || $officeID["OfficeID"]=='130' || $officeID["OfficeID"]=='133' || $officeID["OfficeID"]=='135' || $officeID["OfficeID"]=='136' || $officeID["OfficeID"]=='138' || $officeID["OfficeID"]=='139' || $officeID["OfficeID"]=='140' || $officeID["OfficeID"]=='141' || $officeID["OfficeID"]=='142' || $officeID["OfficeID"]=='167' || $officeID["OfficeID"]=='174' || $officeID["OfficeID"]=='178' || $officeID["OfficeID"]=='182' || $officeID["OfficeID"]=='185' || $officeID["OfficeID"]=='189' || $officeID["OfficeID"]=='199' || $officeID["OfficeID"]=='203' || $officeID["OfficeID"]=='209' || $officeID["OfficeID"]=='219' || $officeID["OfficeID"]=='222' || $officeID["OfficeID"]=='226' || $officeID["OfficeID"]=='227' || $officeID["OfficeID"]=='230' || $officeID["OfficeID"]=='234' || $officeID["OfficeID"]=='258' || $officeID["OfficeID"]=='260' || $officeID["OfficeID"]=='265' || $officeID["OfficeID"]=='266' || $officeID["OfficeID"]=='292' || $officeID["OfficeID"]=='294' || $officeID["OfficeID"]=='304' || $officeID["OfficeID"]=='307' || $officeID["OfficeID"]=='308' || $officeID["OfficeID"]=='316' || $officeID["OfficeID"]=='317'){//3
			$res["infoCPU"]="\nGRABACION POR RED";
			}//3
			
		}//2
		$res["ref"]=$res["ref"].' '.$res["infoCPU"];
		
	}
		
	if($res["similar"]!='0'){$piezas2x=mssql_query("select clave,clave+'  '+nombre+'  Existencia:='+CONVERT(varchar, ".$campo.") ref
from crefaccion where refaccionid=".$res["similar"]." ");
    $piezas2=mssql_fetch_array($piezas2x);
	$res["clave"]=$piezas2["clave"];
	$res["ref"]=$piezas2["ref"];
	}
	$numHisx=mssql_query("select SUM(isnull(cantidad,0))suma from HistorialSurtido h inner join ccomponente c on (c.id=h.codigoidfk)
where refaccionidfk='".$ref["refaccionid"]."' and pedido=".$solicitud." and h.serie='".$res["serie"]."' ");
    $numHis=mssql_fetch_array($numHisx);
	if($numHis["suma"]==''){$numHis["suma"]=0;}
    if($numHis["suma"]<$res["cantidad"]){$surtido=''.$numHis["suma"].' de '.$res["cantidad"]; $n=1;}else{$surtido='Completo';}
	$json['piezas'][]=array('ref' => ''.$res["ref"].' Ubicacion: '.$ubi["ubicacion"], 'surtido'=> ''.$surtido.'','dat' => ''.$res["datos"].'','ubicacion' => ''.$ubi["ubicacion"].'');
}
}
// fin 19
//20 si necesita serie lo coloca en HistorialSurtido y en dsolicitud_refaccion la serie que se envía
if($necSerie=='1'){
	//$datox=mssql_query("select id from HistorialSurtido hs inner join ccodigo cc on (hs.codigoidfk=cc.codigoid) where codigo='".$cod."' and lote='".$lote."' and consecutivo='".$consecutivo."'");
	$datox=mssql_query("select hs.id from HistorialSurtido hs inner join ccomponente cc on (hs.codigoidfk=cc.id) where cc.id=".$codigo["id"]."");
	$dato=mssql_fetch_array($datox);
	mssql_query("update HistorialSurtido set serieEnvia='".$noSerieEnvia."' where id=".$dato["id"]."");
	mssql_query("update dsolicitud_refaccion set serieEnvia='".$noSerieEnvia."' where solicitud_refaccionidfk=".$solicitud." and refaccionidfk=".$ref["refaccionid"]." and serie='".$pie["serie"]."'");
	}
// fin 20
		print (json_encode($json));
	/////////////////////////////// F I N  P R O C E S O  N U E V O //////////////////////////////
/*}
else{//echo"ANTERIOR";
		/////////////////////////////// P R O C E S O  A N T E R I O R //////////////////////////////
		if($subcentro==4){$canDes="canStockTijuana=canStockTijuana-".$cantidad.""; $campo="canStockTijuana"; $banderaSub=1; $IDalmacen=3;}
if($subcentro==5){$canDes="canStockMonterrey=canStockMonterrey-".$cantidad.""; $campo="canStockMonterrey"; $banderaSub=1; $IDalmacen=4;}
if($subcentro==3){$canDes="canStockGuadalajara=canStockGuadalajara-".$cantidad.""; $campo="canStockGuadalajara"; $banderaSub=1; $IDalmacen=5;}
if($subcentro==1){$canDes="canStockLeon=canStockLeon-".$cantidad.""; $campo="canStockLeon"; $banderaSub=1; $IDalmacen=6;}
if($subcentro==2){$canDes="canStockCancun=canStockCancun-".$cantidad.""; $campo="canStockCancun"; $banderaSub=1; $IDalmacen=7;}
if($subcentro==9){$canDes="canStockPuebla=canStockPuebla-".$cantidad.""; $campo="canStockPuebla"; $banderaSub=1; $IDalmacen=42;}
if($subcentro==0){$canDes="cantidadStock=cantidadStock-".$cantidad.""; $campo="cantidadStock"; $banderaSub=1; $IDalmacen=1; }
if($banderaSub==0){$canDes="cantidadStock=cantidadStock-".$cantidad.""; $campo="cantidadStock";}


$refx=mssql_query("select necesitaSerie,refaccionid,isnull(granel,'0')granel2,isnull(subfamiliaidfk,'0')subfamiliaidfk2 from crefaccion where clave='".$cod."' and ".$campo.">=".$cantidad." ");
$exiRef=mssql_num_rows($refx);
if($exiRef==0){$json['piezas2'][]=array( 'surtido'=> 'no2'); print (json_encode($json)); return -1;}

$ref=mssql_fetch_array($refx);
if($necSerie=='0'){
	if($ref["necesitaSerie"]==1){
		$json['piezas2'][]=array( 'surtido'=> 'conSerie'); print (json_encode($json)); return -1;
		}
}
$existex=mssql_query("select top(1)* from dsolicitud_refaccion where solicitud_refaccionidfk=$solicitud and (refaccionidfk=".$ref["refaccionid"]." and similaridfk IS NULL) and (isnull(surtido,0)=0 or isnull(surtido,0)=1)");
$existe=mssql_num_rows($existex);

if($existe==0){
	$existex=mssql_query("select top(1)* from dsolicitud_refaccion where solicitud_refaccionidfk=$solicitud and similaridfk=".$ref["refaccionid"]." and (isnull(surtido,0)=0 or 	    isnull(surtido,0)=1)");
	$existe=mssql_num_rows($existex);
	if($existe==0){$json['piezas2'][]=array( 'surtido'=> 'no2'); print (json_encode($json)); return -1;}
	}
	
if($tipoSurtido=='1'){
	$existex=mssql_query("select *from dsolicitud_refaccion where solicitud_refaccionidfk=".$solicitud." and (refaccionidfk=".$ref["refaccionid"]." and similaridfk IS NULL) and (isnull(surtido,0)=0 or isnull(surtido,0)=1) and cantidad=".$canPed." and serie='".$noSerie."'");
	$existe=mssql_num_rows($existex);
	if($existe==0){
		$existex=mssql_query("select * from dsolicitud_refaccion where solicitud_refaccionidfk=".$solicitud." and similaridfk=".$ref["refaccionid"]." and (isnull(surtido,0)=0 or isnull(surtido,0)=1) and cantidad=".$canPed." and serie='".$noSerie."'");
		}
	}

$pie=mssql_fetch_array($existex);
/////////////////////////////////
//$confx=mssql_query("select id from HistorialSurtido where codigoidfk=133085 and pedido=126527");
$confx=mssql_query("select h.id from HistorialSurtido h inner join ccomponente c on (c.id=h.codigoidfk)
where pedido=".$solicitud." and h.serie='".$pie["serie"]."' and c.refaccionidfk=".$ref["refaccionid"]."");
$numConf=mssql_num_rows($confx);
if($numConf==1 && $pie["estatusidfk"]!=33){$json['piezas2'][]=array( 'surtido'=> 'no2'); print (json_encode($json)); return -1;}

if($pie["estatusidfk"]==51){$ref["refaccionid"]=$pie["similaridfk"]; $est=51;}

if($ref["granel2"]=='1' && $cantidad<=0){$json['granel'][]=array( 'granel2'=> 'si-.-'.$pie["cantidad"]); $json['proceso']='anterior'; print (json_encode($json)); return -1; }

if($ref["granel2"]=='0'){$cantidad=1;}

if($existe>=1){

	//$codigox=mssql_query("select * from ccodigo where codigoCom='".$codCompleto."' and estatus='1' ");
	//$codigox=mssql_query("select id,codigo from ccomponente where codigo='".$codCompleto."' ");
	$codigox=mssql_query("select c.id,c.refaccionidfk,r.clave codigo from ccomponente c inner join crefaccion r on (r.refaccionid=c.refaccionidfk)
where codigo='".$codCompleto."' and estatus='1' ");
	$exiCod=mssql_num_rows($codigox);
	if($exiCod==0){$json['piezas2'][]=array( 'surtido'=> 'no3'); print (json_encode($json)); return -1;}
	$codigo=mssql_fetch_array($codigox);
	////////////
	/////////////////////
	
	 
	$estPedx=mssql_query("select estatusidfk,isnull(departamentoidfk,0)departamentoidfk,tecnicoidfk from tsolicitud_refaccion where solicitud_refaccionid=".$solicitud." ");
	$estPed=mssql_fetch_array($estPedx);
	if($estPed["estatusidfk"]==30){mssql_query("update tsolicitud_refaccion set estatusidfk=115 where solicitud_refaccionid=$solicitud ");
	mssql_query("insert into historialSolRefaccion values(".$solicitud.",115,getdate(),".$usuarioID.") ");}
	$insert=mssql_query("insert into HistorialSurtido values(".$codigo["id"].",'Salida',".$solicitud.",NULL,'1','".$pie["serie"]."',".$cantidad.",'', NULL) ");

	$salaCBx=mssql_query("select isnull(sa.nombre,'0') sala1,ISNULL(sa.salaid,'0') id1,isnull(sa2.nombre,'0') sala2,ISNULL(sa2.salaid,'0') id2, isnull(sa3.nombre,'0') sala3,ISNULL(sa3.salaid,'0')id3, isnull(sa4.nombre,'0') sala4, ISNULL(sa4.departamentoid,'0')id4
	from tsolicitud_refaccion t
left join tfolio_solicitud f on (f.folio_solicitudid=t.folio_solicitudidfk)
left join csala sa on (sa.salaid=f.salaidfk)
left join csala sa2 on (sa2.salaid=t.salaidfk)
left join torden_Servicio o on (cast(o.ordenid as varchar(10))=t.folio_solicitudidfk)
left join csala sa3 on (sa3.salaid=o.salaidfk)
left join cdepartamento sa4 on (sa4.departamentoid=t.departamentoidfk)
where solicitud_refaccionid=".$solicitud." ");
            $sala=mssql_fetch_array($salaCBx);
			$nomSala='';
			$salaid=0;
			if($sala["sala1"]!='0'){
				$nomSala=$sala["sala1"]; $salaid=$sala["id1"];  
				}
				else{ 
			        if($sala["sala2"]!='0'){
						$nomSala=$sala["sala2"]; $salaid=$sala["id2"];
						}
			            else{
							if($sala["sala3"]!='0'){
					           $nomSala=$sala["sala3"]; $salaid=$sala["id3"];
							}
			                else{
						        $nomSala=$sala["sala4"]; $salaid=$sala["id4"];
						        }
						}
				}
				$almacenDestino='2';
	   if($salaid=='188'){$canArea=",canStockTijuana=canStockTijuana+".$cantidad."";$almacenDestino='3';}
	   if($salaid=='189'){$canArea=",canStockMonterrey=canStockMonterrey+".$cantidad.""; $almacenDestino='4';}
	   if($salaid=='200'){$canArea=",canStockGuadalajara=canStockGuadalajara+".$cantidad.""; $almacenDestino='5';}
	   if($salaid=='375'){$canArea=",canStockLeon=canStockLeon+".$cantidad.""; $almacenDestino='6';}
	   if($salaid=='374'){$canArea=",canStockCancun=canStockCancun+".$cantidad.""; $almacenDestino='7';}
	   if($salaid=='1403'){$canArea=",canStockEUA=canStockEUA+".$cantidad.""; $almacenDestino='8';}
	   if($salaid=='1404'){$canArea=",canStockLighting=canStockLighting+".$cantidad.""; $almacenDestino='9';}
	   if($salaid=='1136'){$canArea=",canStockLV=canStockLV+".$cantidad.""; $almacenDestino='10';}
	   if($salaid=='2263'){$canArea=",canStockPuebla=canStockPuebla+".$cantidad.""; $almacenDestino='42';}
	   
   	   if($subcentro==4){$canDes="canStockTijuana=canStockTijuana-".$cantidad.""; $campo="canStockTijuana"; $banderaSub=1; $IDalmacen=3;}
       if($subcentro==5){$canDes="canStockMonterrey=canStockMonterrey-".$cantidad.""; $campo="canStockMonterrey"; $banderaSub=1; $IDalmacen=4;}
       if($subcentro==3){$canDes="canStockGuadalajara=canStockGuadalajara-".$cantidad.""; $campo="canStockGuadalajara"; $banderaSub=1; $IDalmacen=5;}
       if($subcentro==1){$canDes="canStockLeon=canStockLeon-".$cantidad.""; $campo="canStockLeon"; $banderaSub=1; $IDalmacen=6;}
       if($subcentro==2){$canDes="canStockCancun=canStockCancun-".$cantidad.""; $campo="canStockCancun"; $banderaSub=1; $IDalmacen=7;}
	   if($subcentro==9){$canDes="canStockPuebla=canStockPuebla-".$cantidad.""; $campo="canStockPuebla"; $banderaSub=1; $IDalmacen=42;}
       if($subcentro==0){$canDes="cantidadStock=cantidadStock-".$cantidad.""; $campo="cantidadStock"; $banderaSub=1; $IDalmacen=1;}
       if($banderaSub==0){$canDes="cantidadStock=cantidadStock-".$cantidad.""; $campo="cantidadStock";}
	   if($estPed["departamentoidfk"]==16){$canArea=",cantidadDesarrollo=cantidadDesarrollo+".$cantidad."";}
	   
	   $updatei="update crefaccion set ".$canDes." ".$canArea." where clave  = '" .$cod. "' ";
		$resui = mssql_query($updatei);
   
	$movimiento=mssql_query("INSERT INTO tmovimiento(refaccionidfk,fecha,origen,destino,cantidad,observacion,usuarioidfk,paisidfk)VALUES
			(".$ref["refaccionid"].",getdate(),'Stock','".$nomSala."',".$cantidad.",'Salida solicitada por CC No. ".$solicitud."',".$usuarioID.",1)");

if($estPed["departamentoidfk"]==0){
$canAdeudoPen=0;  
if($ref["subfamiliaidfk2"]!=0){
$exiAdeudox=mssql_query("select cantidad from tadeudo_material where tecnicoidfk=".$estPed["tecnicoidfk"]." and subfamiliaidfk=".$ref["subfamiliaidfk2"]."");
$numAdeudo = mssql_num_rows($exiAdeudox);
$exiAdeudoCan=mssql_fetch_array($exiAdeudox);
if($numAdeudo==1){mssql_query("update tadeudo_material set cantidad=cantidad+".$cantidad." where tecnicoidfk=".$estPed["tecnicoidfk"]." and subfamiliaidfk=".$ref["subfamiliaidfk2"]." ");  $canAdeudoPen=$exiAdeudoCan["cantidad"]+$cantidad; }
if($numAdeudo==0){mssql_query("insert into tadeudo_material values(".$estPed["tecnicoidfk"].",".$ref["subfamiliaidfk2"].",".$cantidad.") ");  $canAdeudoPen=$cantidad; $exiAdeudoCan["cantidad"]=0;}

mssql_query("insert into hisAdeudoMat (tecnicoidfk,subfamiliaidfk,canSalida,canPendiente,balance,fecha,pedidoidfk) values(".$estPed["tecnicoidfk"].",".$ref["subfamiliaidfk2"].",".$cantidad.",".$exiAdeudoCan["cantidad"].",".$canAdeudoPen.",getdate(),".$solicitud.") ");
mssql_query("insert into tecnicosComAdeudo (tecnicoidfk,componenteidfk,refaccionidfk,pedido,fecha,cantidad,estatus,devolucionidfk,comentarios)
values(".$estPed["tecnicoidfk"].",".$codigo["id"].",".$ref["refaccionid"].",".$solicitud.",GETDATE(),1,'0',NULL,'')");
}//fin de if subfallaidfk!=0
}//fin de if departamentoidfk==0

//////////////////////////////////////////////////////// NAVISION
$queryNav="select top(1)[No_ movimiento] movimiento from [Interf_ Input NAV - SISCO] order by [No_ movimiento] desc";
$queNAV = odbc_exec($conBDI, $queryNav);
$revMov = odbc_fetch_array($queNAV);
if($revMov["movimiento"]=='NULL' || $revMov["movimiento"] == ''){
$queNAV = odbc_exec($conBDI, $queryNav);
$revMov = odbc_fetch_array($queNAV);
}
if($revMov["movimiento"]=='NULL' || $revMov["movimiento"] == '' || $revMov["movimiento"] ==0){$incremental=1;
mssql_query("insert into regSurtido values('".$revMov["movimiento"]."',0,GETDATE(),1,'".$revMov["movimiento"]."','1')");
}else{$incremental = $revMov["movimiento"] + 1;}

$updateNav = "INSERT INTO [Interf_ Input NAV - SISCO]([Cod_ Producto],[Fecha registro],[Tipo movimiento],[No_ Documento],Descripcion,[ID Almacen SISCO],Cantidad,Estatus,Error,[No_ movimiento],[Fecha Alta],[Cod_ almacen])VALUES('".$cod."',CONVERT(varchar(10), GETDATE(), 103),3,'".$solicitud."','',".$IDalmacen.",".$cantidad.",1,'',".$incremental.",CONVERT(varchar(10), GETDATE(), 103),0) ";
//$updNAV = odbc_exec($conBDI, $updateNav);

if($updNAV = odbc_exec($conBDI, $updateNav)){
    mssql_query("insert into logNavision(codigo,solicitud,IDalmacen,cantidad,incremental,movimiento,estatus,fecha)
values('".$cod."',".$solicitud.",".$IDalmacen.",".$cantidad.",".$incremental.",'3','1',GETDATE())");
}
else{
      mssql_query("insert into logNavision(codigo,solicitud,IDalmacen,cantidad,incremental,movimiento,estatus,fecha)
values('".$cod."',".$solicitud.",".$IDalmacen.",".$cantidad.",".$incremental.",'3','0',GETDATE())");}

if($salaid=='188' || $salaid=='189' || $salaid=='200' || $salaid=='375' || $salaid=='374' || $salaid=='1403' || $salaid=='1404' || $salaid=='1136'){
$queryNav="select top(1)[No_ movimiento] movimiento from [Interf_ Input NAV - SISCO] order by [No_ movimiento] desc";
$queNAV = odbc_exec($conBDI, $queryNav);
$revMov = odbc_fetch_array($queNAV);
if($revMov["movimiento"]=='NULL' || $revMov["movimiento"] == '' || $revMov["movimiento"] ==0){$incremental=1;}else
{$incremental = $revMov["movimiento"] + 1;}

$updateNav = "INSERT INTO [Interf_ Input NAV - SISCO]([Cod_ Producto],[Fecha registro],[Tipo movimiento],[No_ Documento],Descripcion,[ID Almacen SISCO],Cantidad,Estatus,Error,[No_ movimiento],[Fecha Alta],[Cod_ almacen])VALUES('".$cod."',CONVERT(varchar(10), GETDATE(), 103),2,'".$solicitud."','',".$almacenDestino.",".$cantidad.",1,'',".$incremental.",CONVERT(varchar(10), GETDATE(), 103),0) ";
//$updNAV = odbc_exec($conBDI, $updateNav);

if($updNAV = odbc_exec($conBDI, $updateNav)){
    mssql_query("insert into logNavision(codigo,solicitud,IDalmacen,cantidad,incremental,movimiento,estatus,fecha)
values('".$cod."',".$solicitud.",".$almacenDestino.",".$cantidad.",".$incremental.",'2','1',GETDATE())");
}
else{
      mssql_query("insert into logNavision(codigo,solicitud,IDalmacen,cantidad,incremental,movimiento,estatus,fecha)
values('".$cod."',".$solicitud.",".$almacenDestino.",".$cantidad.",".$incremental.",'2','0',GETDATE())");}

}//Fin if subcentros
///////////////////////////////////////////////////////// FIN NAVISION
	if($ref["granel2"]!='1'){	//mssql_query("update ccodigo set estatus='2' where codigoid=".$codigo["codigoid"]." ");
	mssql_query("update ccomponente set estatus='2' where id=".$codigo["id"]." ");
	}
	
//	$numHisx=mssql_query("select SUM(isnull(cantidad,1))suma from HistorialSurtido h inner join ccodigo c on (c.codigoid=h.codigoidfk)
//where codigo='".$codigo["codigo"]."' and pedido=".$solicitud." and serie='".$pie["serie"]."' ");
$numHisx=mssql_query("select SUM(isnull(cantidad,1))suma from HistorialSurtido h inner join ccomponente c on (c.id=h.codigoidfk)
where c.refaccionidfk='".$codigo["refaccionidfk"]."' and pedido=".$solicitud." and h.serie='".$pie["serie"]."' ");
    $numHis=mssql_fetch_array($numHisx);
    if($numHis["suma"]<$pie["cantidad"]){$estSol=1; $est=33;
	////////////
	if($ref["granel2"]=='1'){
	$canPendiente=$pie["cantidad"] - $numHis["suma"];
	$cambia=mssql_query("update dsolicitud_refaccion set estatusidfk=47,cantidad=".$cantidad.",surtido='2' where solicitud_refaccionidfk=".$solicitud." and (refaccionidfk=".$ref["refaccionid"]." or similaridfk=".$ref["refaccionid"].") and serie='".$pie["serie"]."' ");
   $estParcial='1';
for($i=0;$i<50;$i++){
$serExix=mssql_query("select * from dsolicitud_refaccion where solicitud_refaccionidfk=".$solicitud." and serie='SeriePendiente".$i."' ");
$conSerExi=mssql_num_rows($serExix);
if($conSerExi==0){$serieEnviaSP='SeriePendiente'.$i; $c=$i; $i=50;}
}
$insert2=mssql_query("INSERT INTO dsolicitud_refaccion(solicitud_refaccionidfk,refaccionidfk,cantidad,serie,view2,descripcion,folioRetorno,estatusidfk
,denominacion,licencia,version,ip,juegoidfk,entregaParcial,estatus_fr,observacion,similaridfk,salidaidfk,no_guia,patrimonioidfk
,localizacion,refaccionRetorno,serieEnvia,diagnostico)
 VALUES(".$solicitud.",".$ref["refaccionid"].",".$canPendiente.",'".$serieEnviaSP."','','','',43,'','','','',50,null,'','Creada por entrega parcial',null,null,'',null,'',null,'','')");
	}
		///////////
	}else{$estSol=2; if($est!=51){$est=47;}}
	if($estParcial!='1'){
	$up=mssql_query("update dsolicitud_refaccion set surtido=".$estSol.", estatusidfk=".$est."  where solicitud_refaccionidfk=".$solicitud." and (refaccionidfk=".$ref["refaccionid"]." or similaridfk=".$ref["refaccionid"].") and serie='".$pie["serie"]."' ");
	}else{$estParcial='0';}
	$falx=mssql_query("select * from dsolicitud_refaccion where solicitud_refaccionidfk=$solicitud and (isnull(surtido,'0')=0 or isnull(surtido,'0')=1) and estatusidfk!=31");
	$fal=mssql_num_rows($falx);

	if($fal==0){if($estPed["departamentoidfk"]==8 || $estPed["departamentoidfk"]==7){
		$cambia=mssql_query("update dsolicitud_refaccion set localizacion='calle',estatus_fr='Enviado',enviado='1',fechaEnvio=getdate(),estatusidfk=47 where solicitud_refaccionidfk=".$solicitud." and surtido='2' ");
			$up2=mssql_query("update tsolicitud_refaccion set estatusidfk=119,fecha_envio=getdate() where solicitud_refaccionid=".$solicitud." "); $n=1;
	mssql_query("insert into historialSolRefaccion values(".$solicitud.",119,getdate(),".$usuarioID.") ");
	}
	else{
	$up2=mssql_query("update tsolicitud_refaccion set estatusidfk=116,fecha_surtido=GETDATE() where solicitud_refaccionid=".$solicitud." "); $n=1;
	mssql_query("insert into historialSolRefaccion values(".$solicitud.",116,getdate(),".$usuarioID.") ");}
	$json['piezas'][]=array( 'ref'=> 'Completo');
	}
	}
if($n==0){
	$piezasx=mssql_query("select r.clave,r.clave+'  '+r.nombre ref, '\nJuego: '+ ju.nombre+ ', Version: '+d.version+ ', Denominacion: '+d.denominacion infoCPU,isnull(d.surtido,'0')surtido,serie,cantidad,CONVERT(varchar,solicitud_refaccionidfk)+','+CONVERT(varchar,refaccionid)+','+d.serie datos,isnull(similaridfk,'0')similar,CONVERT(varchar, r.".$campo.")canAnterior from dsolicitud_refaccion d inner join crefaccion r on (r.refaccionid=d.refaccionidfk)
inner join cjuego ju on (ju.juegoid=d.juegoidfk) where solicitud_refaccionidfk=$solicitud and d.estatusidfk!=31 order by surtido desc");	
$json=array();
while ($res=mssql_fetch_array($piezasx)){
	$ubix=mssql_query("select top 1 'B'+u.bodega+' '+RIGHT('00000000' +convert(varchar,rack),2)+lado+columna+convert(varchar,fila) ubicacion from ccomponente c left join cubicacion3 u on (u.ubicacionid=c.ubicacionidfk)
where refaccionidfk=".$res["refaccionid"]." and ubicacionidfk is not null ");
    $numComUbi=mssql_num_rows($ubix);
    $ubi=mssql_fetch_array($ubix);
	
	if($ubi["ubicacion"]=='' || $ubi["ubicacion"]==' '){
		$ubix=mssql_query("select top 1 'B'+u.bodega+' '+RIGHT('00000000' +convert(varchar,rack),2)+lado+columna+convert(varchar,fila) ubicacion from rubicacion_refaccion ru inner join cubicacion3 u on (u.ubicacionid=ru.ubicacionidfk) where refaccionidfk=".$res["refaccionid"]." and ubicacionidfk!=0");
		$numGranelUbi=mssql_num_rows($ubix);
        $ubi=mssql_fetch_array($ubix);
        }
		
	if($res["similar"]!='0'){$piezas2x=mssql_query("select clave,clave+'  '+nombre+'  Existencia:='+CONVERT(varchar, ".$campo.") ref
from crefaccion where refaccionid=".$res["similar"]." ");
    $piezas2=mssql_fetch_array($piezas2x);
	$res["clave"]=$piezas2["clave"];
	$res["ref"]=$piezas2["ref"];
	}
	if($numComUbi!=0 || $numGranelUbi!=0){
	$canGranelx=mssql_query("select SUM(cantidad)cantidad from rubicacion_refaccion where refaccionidfk=".$res["refaccionid"]." and estatus='Correcto' ");
	$canGranel=mssql_fetch_array($canGranelx);
    $canComx=mssql_query("select id from ccomponente where refaccionidfk=".$res["refaccionid"]." and ubicacionidfk is not null and cajaidfk is not null and bolsaidfk is not null and bolsaidfk is not null ");
	$canCom=mssql_num_rows($canComx);
	$cantidad=$canGranel["cantidad"]+$canCom;
	$res["ref"]=$res["ref"]."  Existencia=".$cantidad;
	}
	if($numComUbi==0 && $numGranelUbi==0){$res["ref"]=$res["ref"]."  Existencia=".$res["canAnterior"];}
	
	if($res["clave"]=='601015' || $res["clave"]=='601019' || $res["clave"]=='601020' || $res["clave"]=='601023' || $res["clave"]=='601025' || $res["clave"]=='601026' || $res["clave"]=='601027' || $res["clave"]=='601028' || $res["clave"]=='601030' || $res["clave"]=='601035' || $res["clave"]=='601036' || $res["clave"]=='601040' || $res["clave"]=='601045' || $res["clave"]=='601046' || $res["clave"]=='601047' || $res["clave"]=='601048' || $res["clave"]=='601049' || $res["clave"]=='900035' || $res["clave"]=='601018' || $res["clave"]=='602005' || $res["clave"]=='602006' || $res["clave"]=='602007' || $res["clave"]=='602008' || $res["clave"]=='602009' || $res["clave"]=='602010' || $res["clave"]=='602011' || $res["clave"]=='602012' || $res["clave"]=='602013' || $res["clave"]=='201005' || $res["clave"]=='201008' || $res["clave"]=='201110' || $res["clave"]=='201115' || $res["clave"]=='201116' || $res["clave"]=='201120' || $res["clave"]=='201121' || $res["clave"]=='201122' || $res["clave"]=='201123' || $res["clave"]=='201125' || $res["clave"]=='201126' || $res["clave"]=='201130' || $res["clave"]=='201131' || $res["clave"]=='555033' || $res["clave"]=='555040'){
		if($res["clave"]=='601015' || $res["clave"]=='601019' || $res["clave"]=='601020' || $res["clave"]=='601023' || $res["clave"]=='601025' || $res["clave"]=='601026' || $res["clave"]=='601027' || $res["clave"]=='601028' || $res["clave"]=='601030' || $res["clave"]=='601035' || $res["clave"]=='601036' || $res["clave"]=='601040' || $res["clave"]=='601045' || $res["clave"]=='601046' || $res["clave"]=='601047' || $res["clave"]=='601048' || $res["clave"]=='900035' || $res["clave"]=='601018' || $res["clave"]=='602005' || $res["clave"]=='602006' || $res["clave"]=='602007' || $res["clave"]=='602008' || $res["clave"]=='602009' || $res["clave"]=='602010' || $res["clave"]=='602011' || $res["clave"]=='602012' || $res["clave"]=='602013' || $res["clave"]=='201008' || $res["clave"]=='555033' || $res["clave"]=='555040'){//2
			if($officeID["OfficeID"]=='7' || $officeID["OfficeID"]=='12' || $officeID["OfficeID"]=='26' || $officeID["OfficeID"]=='59' || $officeID["OfficeID"]=='67' || $officeID[		"OfficeID"]=='70' || $officeID["OfficeID"]=='81' || $officeID["OfficeID"]=='99' || $officeID["OfficeID"]=='102' || $officeID["OfficeID"]=='103' || $officeID["OfficeID"]=='108' || $officeID["OfficeID"]=='117' || $officeID["OfficeID"]=='121' || $officeID["OfficeID"]=='124' || $officeID["OfficeID"]=='126' || $officeID["OfficeID"]=='129' || $officeID["OfficeID"]=='130' || $officeID["OfficeID"]=='133' || $officeID["OfficeID"]=='135' || $officeID["OfficeID"]=='136' || $officeID["OfficeID"]=='138' || $officeID["OfficeID"]=='139' || $officeID["OfficeID"]=='140' || $officeID["OfficeID"]=='141' || $officeID["OfficeID"]=='142' || $officeID["OfficeID"]=='167' || $officeID["OfficeID"]=='174' || $officeID["OfficeID"]=='178' || $officeID["OfficeID"]=='182' || $officeID["OfficeID"]=='185' || $officeID["OfficeID"]=='189' || $officeID["OfficeID"]=='199' || $officeID["OfficeID"]=='203' || $officeID["OfficeID"]=='209' || $officeID["OfficeID"]=='219' || $officeID["OfficeID"]=='222' || $officeID["OfficeID"]=='226' || $officeID["OfficeID"]=='227' || $officeID["OfficeID"]=='230' || $officeID["OfficeID"]=='234' || $officeID["OfficeID"]=='258' || $officeID["OfficeID"]=='260' || $officeID["OfficeID"]=='265' || $officeID["OfficeID"]=='266' || $officeID["OfficeID"]=='292' || $officeID["OfficeID"]=='294' || $officeID["OfficeID"]=='304' || $officeID["OfficeID"]=='307' || $officeID["OfficeID"]=='308' || $officeID["OfficeID"]=='316' || $officeID["OfficeID"]=='317'){//3
			$res["infoCPU"]="\nGRABACION POR RED";
			}//3
			
		}//2
		$res["ref"]=$res["ref"].' '.$res["infoCPU"];
		
	}
	
	$numHisx=mssql_query("select SUM(isnull(cantidad,0))suma from HistorialSurtido h inner join ccomponente c on (c.id=h.codigoidfk)
where refaccionidfk='".$ref["refaccionid"]."' and pedido=".$solicitud." and h.serie='".$res["serie"]."' ");
    $numHis=mssql_fetch_array($numHisx);
	if($numHis["suma"]==''){$numHis["suma"]=0;}
    if($numHis["suma"]<$res["cantidad"]){$surtido=''.$numHis["suma"].' de '.$res["cantidad"]; $n=1;}else{$surtido='Completo';}
	$json['piezas'][]=array('ref' => ''.$res["ref"].' Ubicacion: '.$ubi["ubicacion"], 'surtido'=> ''.$surtido.'','dat' => ''.$res["datos"].'','ubicacion' => ''.$ubi["ubicacion"].'');
	//$json['piezas'][]=array('ref' => ''.$res["ref"].' Ubicacion: '.$ubi["ubicacion"], 'surtido'=> ''.$surtido.'','dat' => ''.$res["datos"].'','ubicacion' => ''.$ubi["ubicacion"].'');
}
}
if($necSerie=='1'){
	//$datox=mssql_query("select id from HistorialSurtido hs inner join ccodigo cc on (hs.codigoidfk=cc.codigoid) where codigo='".$cod."' and lote='".$lote."' and consecutivo='".$consecutivo."'");
	$datox=mssql_query("select hs.id from HistorialSurtido hs inner join ccomponente cc on (hs.codigoidfk=cc.id) where cc.id=".$codigo["id"]."");
	$dato=mssql_fetch_array($datox);
	mssql_query("update HistorialSurtido set serieEnvia='".$noSerieEnvia."' where id=".$dato["id"]."");
	mssql_query("update dsolicitud_refaccion set serieEnvia='".$noSerieEnvia."' where solicitud_refaccionidfk=".$solicitud." and refaccionidfk=".$ref["refaccionid"]." and serie='".$pie["serie"]."'");
	}
		print (json_encode($json));
 $sd->desconectar();
		/////////////////////////////// F I N  P R O C E S O  A N T E R I O R //////////////////////////////
}*/
$sd->desconectar();
?>